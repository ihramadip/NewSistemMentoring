<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MentorTraining;

/**
 * MentorTrainingController
 *
 * Controller untuk manage program pelatihan mentor (MODULE A #4: Training for Mentor & #5: Diklat)
 * Admin dapat create, read, update, delete program training untuk calon/mentor
 *
 * Fitur:
 * - Index: list semua program training dengan sorting by schedule_date desc
 * - Create: show form untuk create program training baru, input semua field
 * - Store: save program training baru, validate required fields & format
 * - Show: display detail program training (redirect ke index saat ini)
 * - Edit: show form untuk edit program training existing
 * - Update: update program training info (title, type, description, schedule, links)
 * - Delete: hapus program training dari database
 *
 * Data structure:
 * - MentorTraining: title, type, description, schedule_date, schedule_time, material_link, test_link
 * - Type: 'TFM' (Training for Mentor) atau 'Diklat' (Training continuation)
 * - Schedule: tanggal & waktu pelaksanaan training
 * - Links: external links untuk materi & test (Google Drive, Form, dll)
 *
 * Training types:
 * - TFM: Training for Mentor - pelatihan dasar untuk calon mentor
 * - Diklat: Diklat - pelatihan lanjutan untuk mentor aktif
 *
 * Field validation:
 * - title: required, string, max 255 chars
 * - type: required, enum (TFM, Diklat)
 * - description: optional, string (detail program)
 * - schedule_date: required, date format (tanggal pelaksanaan)
 * - schedule_time: optional, string (waktu pelaksanaan)
 * - material_link: optional, valid URL (link ke materi training)
 * - test_link: optional, valid URL (link ke pre/post test)
 *
 * Sorting:
 * - Order by schedule_date desc: training terdekat muncul di atas
 *
 * Flow:
 * 1. Admin create program training (isi semua info: title, type, schedule, links)
 * 2. Program training muncul di list dengan sorting by date
 * 3. Mentor dapat mengakses info training melalui dashboard mentor
 * 4. Admin edit/hapus program training jika perlu
 *
 * @package App\Http\Controllers\Admin
 */
class MentorTrainingController extends Controller
{
    /**
     * Menampilkan list semua program training mentor
     *
     * Proses:
     * 1. Query semua mentor trainings
     * 2. Sort by schedule_date desc (terdekat di atas)
     * 3. Return view dengan list trainings
     *
     * Sorting:
     * - orderBy('schedule_date', 'desc'): training terdekat muncul di atas
     * - Urutan kronologis untuk kemudahan admin lihat jadwal
     *
     * Note:
     * - View path: 'mentor.trainings.index' (untuk mentor lihat)
     * - Tapi controller di Admin namespace (admin manage)
     *
     * @return \Illuminate\View\View View list program training
     */
    public function index()
    {
        // Query semua trainings dengan sorting by schedule_date desc
        $trainings = MentorTraining::orderBy('schedule_date', 'desc')->get();

        // Return view dengan trainings list
        return view('mentor.trainings.index', compact('trainings'));
    }

    /**
     * Menampilkan form untuk create program training baru
     *
     * Proses:
     * 1. Return create view kosong untuk input program training baru
     *
     * Form fields:
     * - title: judul program training (required, max 255 chars)
     * - type: tipe training ('TFM' atau 'Diklat') (required)
     * - description: deskripsi/detail program (optional)
     * - schedule_date: tanggal pelaksanaan (required, date format)
     * - schedule_time: waktu pelaksanaan (optional)
     * - material_link: link ke materi training (optional, valid URL)
     * - test_link: link ke pre/post test (optional, valid URL)
     *
     * @return \Illuminate\View\View View form create program training
     */
    public function create()
    {
        // Return create view kosong
        return view('admin.mentor-trainings.create');
    }

    /**
     * Menyimpan program training baru ke database
     *
     * Proses:
     * 1. Validasi input:
     *    - title: required, string, max 255
     *    - type: required, in: TFM, Diklat
     *    - description: optional, string
     *    - schedule_date: required, date format
     *    - schedule_time: optional, string, max 255
     *    - material_link: optional, valid URL
     *    - test_link: optional, valid URL
     * 2. Create MentorTraining record dengan validated data
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - type: required, in:TFM,Diklat
     * - description: nullable, string
     * - schedule_date: required, date
     * - schedule_time: nullable, string, max:255
     * - material_link: nullable, url
     * - test_link: nullable, url
     *
     * @param Request $request Form request dengan training info
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:TFM,Diklat',
            'description' => 'nullable|string',
            'schedule_date' => 'required|date',
            'schedule_time' => 'nullable|string|max:255',
            'material_link' => 'nullable|url',
            'test_link' => 'nullable|url',
        ]);

        // Create new mentor training
        MentorTraining::create($validatedData);

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentor-trainings.index')
                        ->with('success', 'Pelatihan mentor berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail program training
     *
     * Proses:
     * 1. MentorTraining di-resolve via route model binding
     * 2. Saat ini redirect ke index (belum ada show view)
     *
     * Note:
     * - Fungsi ini redirect ke index karena belum ada show view
     * - Untuk detail, admin bisa edit langsung dari list
     * - Future enhancement: buat show view untuk detail training
     *
     * @param MentorTraining $training MentorTraining model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index
     */
    public function show(MentorTraining $training)
    {
        // Redirect ke index karena belum ada show view
        return redirect()->route('admin.mentor-trainings.index');
    }

    /**
     * Menampilkan form untuk edit program training
     *
     * Proses:
     * 1. MentorTraining di-resolve via route model binding
     * 2. Return edit view dengan training data
     *
     * Data:
     * - training: current training untuk populate form
     *
     * Admin dapat:
     * - Change title, type, description, schedule, links
     * - Update info program training tanpa buat baru
     *
     * @param MentorTraining $training MentorTraining model via route binding
     * @return \Illuminate\View\View View form edit program training
     */
    public function edit(MentorTraining $training)
    {
        // Return edit view dengan training data
        return view('admin.mentor-trainings.edit', compact('training'));
    }

    /**
     * Memperbarui program training di database
     *
     * Proses:
     * 1. Validasi input (sama seperti store):
     *    - title: required, string, max 255
     *    - type: required, in: TFM, Diklat
     *    - description: optional, string
     *    - schedule_date: required, date format
     *    - schedule_time: optional, string, max 255
     *    - material_link: optional, valid URL
     *    - test_link: optional, valid URL
     * 2. Update MentorTraining record dengan validated data
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - title: required, string, max:255
     * - type: required, in:TFM,Diklat
     * - description: nullable, string
     * - schedule_date: required, date
     * - schedule_time: nullable, string, max:255
     * - material_link: nullable, url
     * - test_link: nullable, url
     *
     * @param Request $request Form request dengan training info
     * @param MentorTraining $training MentorTraining model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, MentorTraining $training)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:TFM,Diklat',
            'description' => 'nullable|string',
            'schedule_date' => 'required|date',
            'schedule_time' => 'nullable|string|max:255',
            'material_link' => 'nullable|url',
            'test_link' => 'nullable|url',
        ]);

        // Update training info
        $training->update($validatedData);

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentor-trainings.index')
                        ->with('success', 'Pelatihan mentor berhasil diperbarui.');
    }

    /**
     * Menghapus program training dari database
     *
     * Proses:
     * 1. MentorTraining di-resolve via route model binding
     * 2. Delete MentorTraining record
     * 3. Redirect ke index dengan success message
     *
     * Cascade deletion:
     * - Hanya training record yang di-delete
     * - Tidak ada foreign key references ke mentor training
     * - Tidak mempengaruhi user atau data lain
     *
     * WARNING:
     * - Program training akan hilang sepenuhnya dari sistem
     * - Tidak ada soft delete atau archive mechanism saat ini
     * - Pastikan benar-benar ingin hapus sebelum konfirmasi
     *
     * @param MentorTraining $training MentorTraining model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(MentorTraining $training)
    {
        // Delete mentor training record
        $training->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentor-trainings.index')
                         ->with('success', 'Pelatihan mentor berhasil dihapus.');
    }
}
