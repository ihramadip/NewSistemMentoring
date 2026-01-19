<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use App\Mail\MentorApplicationApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

/**
 * MentorApplicationController
 *
 * Controller untuk mengelola aplikasi/pendaftaran pementor (MODULE A #2: Seleksi)
 * Admin dapat review, approve/reject, dan mengelola data aplikasi mentor
 *
 * Fitur:
 * - Daftar semua aplikasi mentor dengan pencarian & pagination
 * - View detail aplikasi mentor
 * - Edit status (pending, accepted, rejected) & notes reviewer
 * - Stream/download CV dan audio rekaman mentor
 * - Hapus aplikasi individual atau bulk delete
 * - Auto update User role ke 'mentor' saat aplikasi diterima
 * - Auto send email notifikasi ke mentor saat diterima
 *
 * @package App\Http\Controllers\Admin
 */
class MentorApplicationController extends Controller
{
    /**
     * Menampilkan daftar semua aplikasi mentor dengan pagination & pencarian
     *
     * Fitur:
     * - Support search by nama mentee atau email
     * - Pagination 10 items per halaman
     * - Preload relasi 'user' (eager loading)
     * - Sort by created_at descending (aplikasi terbaru di atas)
     *
     * @param Request $request HTTP request dengan optional 'search' parameter
     * @return \Illuminate\View\View View daftar aplikasi mentor
     */
    public function index(Request $request)
    {
        // Ambil parameter search dari query string
        $search = $request->input('search');

        // Build query dengan eager load user relation
        $applicationsQuery = MentorApplication::whereHas('user')
                                            ->with('user');

        // Jika ada search, filter by nama user atau email
        if ($search) {
            $applicationsQuery->where(function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                });
            });
        }

        // Order by created_at desc, paginate hasil
        $applications = $applicationsQuery->orderBy('created_at', 'desc')
                                        ->paginate(10);

        // Append search parameter ke pagination links
        $applications->appends(['search' => $search]);

        // Return view dengan data applications
        return view('admin.mentor-applications.index', compact('applications'));
    }

    /**
     * Method create - Not used for admin
     *
     * Admin tidak membuat aplikasi langsung
     * Aplikasi dibuat melalui public registration form (MentorRegistrationController)
     *
     * @return void Abort 404
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Method store - Not used for admin
     *
     * Admin tidak menyimpan aplikasi langsung
     * Aplikasi disimpan melalui public registration form (MentorRegistrationController)
     *
     * @param Request $request
     * @return void Abort 404
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Menampilkan detail aplikasi mentor tertentu
     *
     * Menampilkan info lengkap aplikasi termasuk:
     * - Data user (nama, email, npm, fakultas)
     * - Data aplikasi (CV, audio, status, notes)
     * - Option untuk streaming CV & audio
     *
     * @param MentorApplication $mentorApplication Aplikasi (di-inject via route model binding)
     * @return \Illuminate\View\View View detail aplikasi
     */
    public function show(MentorApplication $mentorApplication)
    {
        return view('admin.mentor-applications.show', compact('mentorApplication'));
    }

    /**
     * Menampilkan form edit untuk aplikasi mentor
     *
     * Form untuk:
     * - Ubah status (pending -> accepted/rejected)
     * - Tambah/edit notes reviewer
     *
     * @param MentorApplication $mentorApplication Aplikasi (di-inject via route model binding)
     * @return \Illuminate\View\View View form edit aplikasi
     */
    public function edit(MentorApplication $mentorApplication)
    {
        return view('admin.mentor-applications.edit', compact('mentorApplication'));
    }

    /**
     * Menyimpan update aplikasi mentor (status & notes)
     *
     * Proses:
     * 1. Validasi: status harus pending/accepted/rejected, notes opsional
     * 2. Update status & notes reviewer
     * 3. Jika status berubah ke 'accepted':
     *    - Update role user ke 'mentor'
     *    - Kirim email approval ke user
     * 4. Redirect dengan pesan sukses
     *
     * Validasi:
     * - status: required, in:[pending, accepted, rejected]
     * - notes_from_reviewer: nullable, string
     *
     * @param Request $request HTTP request dengan field status & notes_from_reviewer
     * @param MentorApplication $mentorApplication Aplikasi yang diupdate
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function update(Request $request, MentorApplication $mentorApplication)
    {
        // Validasi input
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'accepted', 'rejected'])],
            'notes_from_reviewer' => ['nullable', 'string'],
        ]);

        // Simpan status original untuk comparison
        $originalStatus = $mentorApplication->status;

        // Update status dan notes
        $mentorApplication->update($request->only('status', 'notes_from_reviewer'));

        // Jika status berubah ke 'accepted', update user role & kirim email
        if ($request->status == 'accepted' && $originalStatus != 'accepted') {
            // Cari role 'mentor'
            $mentorRole = \App\Models\Role::where('name', 'mentor')->first();

            if ($mentorRole) {
                // Update user role ke mentor
                $user = $mentorApplication->user;
                $user->role_id = $mentorRole->id;
                $user->save();

                // Kirim email approval ke user
                Mail::to($user->email)->send(new MentorApplicationApproved($user));
            }
        }

        // Redirect ke index dengan pesan sukses
        return redirect()->route('admin.mentor-applications.index')
                        ->with('success', 'Mentor application updated successfully.');
    }

    /**
     * Stream/download audio rekaman mentee dari aplikasi
     *
     * Fitur keamanan:
     * - Check jika path kosong (error 404)
     * - Check path traversal attack (error 403)
     * - Check file exists (error 404)
     * - Detect MIME type, support mp3/wav/m4a
     * - Log semua aktivitas untuk audit
     *
     * @param MentorApplication $mentorApplication Aplikasi dengan recording_path
     * @return \Illuminate\Http\Response Response file dengan header Content-Type
     */
    public function streamAudio(MentorApplication $mentorApplication)
    {
        // Ambil path rekaman dari aplikasi
        $path = $mentorApplication->recording_path;

        // Validasi: path tidak boleh kosong
        if (empty($path)) {
            Log::error("Audio streaming failed: recording_path is null or empty for application ID {$mentorApplication->id}");
            abort(404, 'Audio file path is missing.');
        }

        // Log attempt
        Log::info("Attempting to stream audio from path: {$path} for application ID {$mentorApplication->id}");

        // Security check: detect path traversal attack (..)
        if (str_contains($path, '..')) {
            Log::warning("Audio streaming blocked due to path traversal attempt: {$path}");
            abort(403, 'Invalid path specified.');
        }

        // Check file exists
        if (!Storage::exists($path)) {
            Log::error("Audio file not found at path: {$path} for application ID {$mentorApplication->id}");
            abort(404, 'Audio file not found.');
        }

        // Get file content, MIME type, size, extension
        $fileContents = Storage::get($path);
        $detectedMimeType = Storage::mimeType($path);
        $fileSize = Storage::size($path);
        $extension = Str::afterLast($path, '.');

        // Set MIME type berdasarkan extension (explicit mapping)
        $mimeType = match ($extension) {
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'm4a' => 'audio/mp4',
            default => $detectedMimeType ?: 'application/octet-stream',
        };

        // Log success
        Log::info("Streaming audio: Path={$path}, Exists=true, MIME={$mimeType}, Size={$fileSize} for application ID {$mentorApplication->id}");

        // Return file response dengan header
        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $fileSize)
            ->header('Accept-Ranges', 'bytes');
    }

    /**
     * Stream/download CV mentee dari aplikasi
     *
     * Fitur keamanan:
     * - Check jika path kosong (error 404)
     * - Check path traversal attack (error 403)
     * - Check file exists (error 404)
     * - Detect MIME type, default ke application/pdf
     * - Log semua aktivitas untuk audit
     *
     * @param MentorApplication $mentorApplication Aplikasi dengan cv_path
     * @return \Illuminate\Http\Response Response file dengan header Content-Disposition
     */
    public function streamCv(MentorApplication $mentorApplication)
    {
        // Ambil path CV dari aplikasi
        $path = $mentorApplication->cv_path;

        // Validasi: path tidak boleh kosong
        if (empty($path)) {
            Log::error("CV streaming failed: cv_path is null or empty for application ID {$mentorApplication->id}");
            abort(404, 'CV file path is missing.');
        }

        // Log attempt
        Log::info("Attempting to stream CV from path: {$path} for application ID {$mentorApplication->id}");

        // Security check: detect path traversal attack (..)
        if (str_contains($path, '..')) {
            Log::warning("CV streaming blocked due to path traversal attempt: {$path}");
            abort(403, 'Invalid path specified.');
        }

        // Check file exists
        if (!Storage::exists($path)) {
            Log::error("CV file not found at path: {$path} for application ID {$mentorApplication->id}");
            abort(404, 'CV file not found.');
        }

        // Get file content, MIME type, size
        $fileContents = Storage::get($path);
        $mimeType = Storage::mimeType($path) ?: 'application/pdf'; // Default ke PDF
        $fileSize = Storage::size($path);

        // Log success
        Log::info("Streaming CV: Path={$path}, Exists=true, MIME={$mimeType}, Size={$fileSize} for application ID {$mentorApplication->id}");

        // Return file response dengan header (inline display)
        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $fileSize)
            ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"');
    }

    /**
     * Menghapus satu aplikasi mentor dari database
     *
     * Proses:
     * 1. Hapus file CV & audio dari storage
     * 2. Hapus record aplikasi dari database
     * 3. Redirect dengan pesan sukses
     *
     * @param MentorApplication $mentorApplication Aplikasi yang dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function destroy(MentorApplication $mentorApplication)
    {
        // Hapus file CV & audio dari storage
        Storage::delete([
            $mentorApplication->cv_path,
            $mentorApplication->recording_path,
        ]);

        // Hapus record dari database
        $mentorApplication->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.mentor-applications.index')
                        ->with('success', 'Mentor application deleted successfully.');
    }

    /**
     * Menghapus multiple aplikasi mentor sekaligus (bulk delete)
     *
     * Proses:
     * 1. Validasi: ids harus array dan exist di database
     * 2. Loop setiap aplikasi:
     *    - Hapus file CV & audio dari storage
     *    - Hapus record dari database
     * 3. Redirect dengan pesan sukses menampilkan jumlah yang dihapus
     *
     * Validasi:
     * - ids: required, array
     * - ids.*: exists di mentor_applications table
     *
     * @param Request $request HTTP request dengan field 'ids' (array of application IDs)
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function bulkDestroy(Request $request)
    {
        // Validasi input
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mentor_applications,id',
        ]);

        // Fetch applications by ids
        $applications = MentorApplication::whereIn('id', $request->input('ids'))->get();
        $count = 0;

        // Loop & delete setiap aplikasi + filenya
        foreach ($applications as $application) {
            // Hapus file dari storage
            Storage::delete([
                $application->cv_path,
                $application->recording_path,
            ]);

            // Hapus record dari database
            $application->delete();
            $count++;
        }

        // Redirect dengan pesan sukses menampilkan jumlah deleted
        return redirect()->route('admin.mentor-applications.index')
                        ->with('success', "Successfully deleted {$count} selected mentor applications.");
    }
}

