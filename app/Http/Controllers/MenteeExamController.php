<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\PlacementTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\GradeExamSubmission; // Import the job
use Carbon\Carbon;

/**
 * MenteeExamController
 *
 * Controller untuk ujian akhir mentoring (MODULE C #6: Ujian Akhir Mentoring)
 * Mentee dapat melihat daftar ujian, mengikuti ujian, dan melihat status penyelesaian
 *
 * Fitur:
 * - Index: menampilkan daftar ujian yang tersedia dan yang sudah diselesaikan
 * - Show: menampilkan form ujian untuk dikerjakan
 * - Store: menyimpan jawaban ujian dan mengirim ke grading job
 * - Completed: halaman konfirmasi setelah submit ujian
 *
 * Data structure:
 * - Exam: informasi ujian (judul, deskripsi, durasi, level, dll)
 * - ExamSubmission: jawaban mentee untuk ujian tertentu
 * - SubmissionAnswer: jawaban per soal dalam submission
 *
 * Authorization:
 * - Hanya mentee yang bisa mengakses ujian mereka sendiri
 * - Admin juga bisa melihat semua ujian (untuk monitoring)
 *
 * Flow:
 * 1. Mentee lihat daftar ujian yang tersedia
 * 2. Mentee pilih ujian dan mulai mengerjakan
 * 3. Mentee submit jawaban
 * 4. Jawaban diproses oleh grading job secara asynchronous
 * 5. Mentee lihat status penyelesaian dan hasil
 *
 * @package App\Http\Controllers
 */
class MenteeExamController extends Controller
{
    /**
     * Menampilkan daftar ujian untuk mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Identifikasi apakah user adalah admin
     * 3. Jika admin:
     *    - Tampilkan semua ujian yang dipublish
     *    - Tampilkan ujian yang sudah pernah disubmit oleh mentee manapun
     * 4. Jika mentee:
     *    - Tampilkan ujian yang dipublish dan belum disubmit oleh mentee ini
     *    - Tampilkan ujian yang sudah disubmit oleh mentee ini
     * 5. Filter berdasarkan published_at (harus <= Carbon::now())
     * 6. Return view dengan availableExams, completedExams, dan isAdmin flag
     *
     * Data filtering:
     * - Available exams: whereNotIn('id', $submittedExamIds) untuk mentee
     * - Published exams: whereNotNull('published_at') and published_at <= now()
     * - Completed exams: exams that have submission records
     *
     * Pagination:
     * - Available exams di paginate 10 per halaman
     *
     * @return \Illuminate\View\View View daftar ujian untuk mentee
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Identifikasi apakah user adalah admin
        $isAdmin = $user->role->name === 'Admin';

        $availableExams = collect();
        $completedExams = collect();

        if ($isAdmin) {
            // Admin melihat semua ujian yang dipublish
            $availableExams = Exam::with('level')
                                ->whereNotNull('published_at')
                                ->where('published_at', '<=', Carbon::now())
                                ->orderBy('published_at', 'desc')
                                ->paginate(10);

            // Untuk admin, tampilkan semua ujian yang pernah disubmit oleh mentee manapun
            $submittedExamIds = ExamSubmission::pluck('exam_id')->unique();
            $completedExams = Exam::whereIn('id', $submittedExamIds)->with('level')->get();

        } else { // Mentee
            // Ambil ID ujian yang sudah disubmit oleh mentee ini
            $submittedExamIds = ExamSubmission::where('mentee_id', $user->id)->pluck('exam_id');

            // Tampilkan semua ujian yang dipublish dan belum disubmit oleh mentee ini
            $availableExams = Exam::with('level')
                                ->whereNotNull('published_at')
                                ->where('published_at', '<=', Carbon::now())
                                ->whereNotIn('id', $submittedExamIds)
                                ->orderBy('published_at', 'desc')
                                ->paginate(10);

            $completedExams = Exam::whereIn('id', $submittedExamIds)->with('level')->get();
        }

        // Return view dengan semua data ujian
        return view('mentee.exams.index', compact('availableExams', 'completedExams', 'isAdmin'));
    }

    /**
     * Menampilkan form ujian untuk dikerjakan (tidak digunakan untuk mentee)
     *
     * Proses:
     * 1. Fungsi ini tidak berlaku untuk mentee exams
     * 2. Akan mengembalikan 404 error
     *
     * Note:
     * - Fungsi ini tidak digunakan untuk mentee exams
     * - Ujian ditampilkan melalui show method
     *
     * @return void
     */
    public function create()
    {
        // Fungsi ini tidak berlaku untuk mentee exams
        abort(404);
    }

    /**
     * Menyimpan jawaban ujian dari mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Cek apakah mentee sudah submit ujian ini sebelumnya
     * 3. Validasi jawaban:
     *    - answers: required, array
     *    - answers.*.question_id: required, exists in questions table
     *    - answers.*.chosen_option_id: optional, exists in options table (for multiple choice)
     *    - answers.*.answer_text: optional, string (for essay/audio response)
     * 4. Load questions & options untuk scoring
     * 5. Dalam transaksi database:
     *    - Create ExamSubmission record
     *    - Create SubmissionAnswer records untuk setiap jawaban
     *    - Dispatch GradeExamSubmission job untuk grading asynchronous
     * 6. Redirect ke completed page dengan success message
     *
     * Validasi (inline):
     * - answers: required, array
     * - answers.*.question_id: required, exists:questions,id
     * - answers.*.chosen_option_id: nullable, exists:options,id
     * - answers.*.answer_text: nullable, string
     *
     * Asynchronous grading:
     * - Gunakan GradeExamSubmission job untuk grading
     * - Mencegah blocking HTTP request saat grading
     *
     * @param Request $request Form request dengan jawaban ujian
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke completed page
     */
    public function store(Request $request, Exam $exam)
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah mentee sudah submit ujian ini sebelumnya
        if (ExamSubmission::where('mentee_id', $user->id)->where('exam_id', $exam->id)->exists()) {
            return redirect()->route('mentee.exams.index')
                           ->with('warning', 'Anda sudah pernah mengikuti ujian ini.');
        }

        // Validasi jawaban
        $validatedData = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.chosen_option_id' => 'nullable|exists:options,id', // For multiple choice
            'answers.*.answer_text' => 'nullable|string', // For essay/audio response
        ]);

        // Load questions dan options untuk scoring
        $exam->load('questions.options');

        $submission = null; // Initialize submission outside the closure

        // Gunakan transaksi database untuk konsistensi data
        DB::transaction(function () use ($user, $exam, $validatedData, &$submission) {
            // Create ExamSubmission
            $submission = ExamSubmission::create([
                'mentee_id' => $user->id,
                'exam_id' => $exam->id,
                'submitted_at' => Carbon::now(),
                'status' => 'submitted', // Akan diupdate ke 'graded' setelah penilaian admin
                'total_score' => 0, // Inisialisasi skor ke 0; job akan update
            ]);

            // Create SubmissionAnswer records untuk setiap jawaban
            foreach ($validatedData['answers'] as $answerData) {
                $question = $exam->questions->find($answerData['question_id']);

                if ($question) {
                    $submission->answers()->create([
                        'question_id' => $question->id,
                        'chosen_option_id' => $answerData['chosen_option_id'] ?? null,
                        'answer_text' => $answerData['answer_text'] ?? null,
                        'score' => 0, // Skor diinisialisasi ke 0; job akan update
                    ]);
                }
            }

            // Dispatch job untuk grading submission secara asynchronous
            GradeExamSubmission::dispatch($submission);
        });

        // Redirect ke completed page dengan success message
        return redirect()->route('mentee.exams.completed')
                        ->with('success', 'Ujian Anda telah berhasil dikirimkan. Silakan tunggu hasil penilaian.');
    }

    /**
     * Menampilkan form ujian untuk dikerjakan oleh mentee
     *
     * Proses:
     * 1. Ambil user yang sedang login
     * 2. Cek apakah mentee sudah submit ujian ini sebelumnya
     * 3. Cek apakah ujian sudah dipublish (published_at <= Carbon::now())
     * 4. Jika ujian memiliki level, cek eligibility mentee berdasarkan placement test
     * 5. Load questions & options untuk ditampilkan di form
     * 6. Return view dengan exam data
     *
     * Eligibility checking:
     * - Jika exam->level_id ada, cek placement test mentee
     * - Pastikan final_level_id dari placement test cocok dengan level ujian
     *
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View Redirect ke index jika tidak eligible atau view form ujian
     */
    public function show(Exam $exam)
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah mentee sudah submit ujian ini sebelumnya
        if (ExamSubmission::where('mentee_id', $user->id)->where('exam_id', $exam->id)->exists()) {
            return redirect()->route('mentee.exams.index')
                           ->with('warning', 'Anda sudah pernah mengikuti ujian ini.');
        }

        // Cek apakah ujian sudah dipublish
        if (!$exam->published_at || $exam->published_at > Carbon::now()) {
            return redirect()->route('mentee.exams.index')
                           ->with('error', 'Ujian ini belum dipublikasikan atau sudah kadaluarsa.');
        }

        // Cek eligibility level mentee jika ujian memiliki level spesifik
        if ($exam->level_id) {
            $placementTest = PlacementTest::where('mentee_id', $user->id)
                                        ->where('final_level_id', $exam->level_id)
                                        ->first();
            if (!$placementTest) {
                return redirect()->route('mentee.exams.index')
                               ->with('error', 'Anda tidak memenuhi syarat untuk mengikuti ujian ini berdasarkan level Anda.');
            }
        }

        // Load questions dengan options untuk ditampilkan di form
        $exam->load('questions.options');

        // Return view form ujian
        return view('mentee.exams.show', compact('exam'));
    }

    /**
     * Menampilkan halaman konfirmasi setelah submit ujian
     *
     * Proses:
     * 1. Cek apakah session memiliki success message
     * 2. Jika tidak ada, redirect ke index untuk mencegah akses langsung
     * 3. Return view completed page
     *
     * Security:
     * - Cek session('success') untuk mencegah akses langsung ke halaman ini
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View Redirect ke index jika tidak valid atau view completed page
     */
    public function completed()
    {
        // Cek apakah session memiliki success message untuk mencegah akses langsung
        if (!session('success')) {
            return redirect()->route('mentee.exams.index');
        }

        // Return view completed page
        return view('mentee.exams.completed');
    }

    /**
     * Menampilkan form edit (tidak digunakan untuk mentee exams)
     *
     * Proses:
     * 1. Fungsi ini tidak berlaku untuk mentee exams
     * 2. Akan mengembalikan 404 error
     *
     * Note:
     * - Fungsi ini tidak digunakan untuk mentee exams
     *
     * @param string $id ID resource (tidak digunakan)
     * @return void
     */
    public function edit(string $id)
    {
        // Fungsi ini tidak berlaku untuk mentee exams
        abort(404);
    }

    /**
     * Memperbarui resource (tidak digunakan untuk mentee exams)
     *
     * Proses:
     * 1. Fungsi ini tidak berlaku untuk mentee exams
     * 2. Tidak ada implementasi
     *
     * Note:
     * - Fungsi ini tidak digunakan untuk mentee exams
     *
     * @param Request $request Form request
     * @param string $id ID resource
     * @return void
     */
    public function update(Request $request, string $id)
    {
        // Tidak ada implementasi untuk mentee exams
    }

    /**
     * Menghapus resource (tidak digunakan untuk mentee exams)
     *
     * Proses:
     * 1. Fungsi ini tidak berlaku untuk mentee exams
     * 2. Tidak ada implementasi
     *
     * Note:
     * - Fungsi ini tidak digunakan untuk mentee exams
     *
     * @param string $id ID resource
     * @return void
     */
    public function destroy(string $id)
    {
        // Tidak ada implementasi untuk mentee exams
    }
}
