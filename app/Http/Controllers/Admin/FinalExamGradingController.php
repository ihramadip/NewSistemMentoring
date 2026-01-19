<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSubmission;
use Illuminate\Http\Request;

/**
 * FinalExamGradingController
 *
 * Mengelola proses penilaian ujian akhir mentoring oleh admin.
 * Controller ini bertanggung jawab untuk:
 * - Menampilkan daftar submisi ujian yang perlu dinilai
 * - Menampilkan detail submisi untuk proses penilaian
 * - Menyimpan nilai (score) yang diberikan admin
 *
 * @package App\Http\Controllers\Admin
 */
class FinalExamGradingController extends Controller
{
    /**
     * Menampilkan daftar semua submisi ujian yang perlu penilaian.
     *
     * Fitur:
     * - Menampilkan data mentee & ujian mereka dengan pagination (30 items/halaman)
     * - Support pencarian berdasarkan nama mentee atau NPM
     * - Mengurutkan berdasarkan NPM (ascending)
     * - Preload relasi untuk optimasi query (eager loading)
     *
     * @param Request $request HTTP request dengan optional 'search' parameter
     * @return \Illuminate\View\View View dengan daftar submisi ujian
     */
    public function index(Request $request)
    {
        // Mulai build query dari model ExamSubmission
        $query = ExamSubmission::query()
            ->join('users', 'exam_submissions.mentee_id', '=', 'users.id')
            ->select('exam_submissions.*');

        // Jika ada parameter search, filter berdasarkan nama mentee atau NPM
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                // Search di nama mentee
                $q->where('users.name', 'like', "%{$search}%")
                  // OR search di NPM mentee
                ->orWhere('users.npm', 'like', "%{$search}%");
            });
        }
        
        // Load relasi mentee dan exam, urutkan by NPM, paginate hasil
        $submissions = $query->with(['mentee', 'exam'])
            ->orderBy('users.npm', 'asc')
            ->paginate(30)
            ->withQueryString(); // Preserve query string di pagination links

        // Return view dengan data submissions
        return view('admin.final-exam-grading.index', compact('submissions'));
    }


    /**
     * Menampilkan form penilaian untuk submisi ujian tertentu.
     *
     * Menampilkan detail lengkap:
     * - Data mentee (nama, NPM, fakultas)
     * - Informasi ujian (nama, deskripsi, durasi)
     * - Semua soal ujian dengan pilihan jawaban
     * - Jawaban mentee untuk setiap soal
     * - Form untuk input total_score dari admin
     *
     * Menggunakan eager loading untuk menghindari N+1 query problem:
     * - mentee: data peserta ujian
     * - exam.questions.options: soal + pilihan jawaban
     * - answers.question: jawaban yang sudah diberikan
     * - answers.option: pilihan yang dipilih mentee
     *
     * @param ExamSubmission $submission Submisi ujian (di-inject via route model binding)
     * @return \Illuminate\View\View View form penilaian dengan detail submisi
     */
    public function edit(ExamSubmission $submission)
    {
        // Eager load semua relasi yang dibutuhkan untuk menghindari query berulang
        $submission->load([
            'mentee',                    // Data mentee (nama, npm, etc)
            'exam.questions.options',    // Soal & pilihan jawaban untuk exam
            'answers.question',          // Jawaban dengan relasi ke question
            'answers.option'             // Jawaban dengan relasi ke option yang dipilih
        ]);

        // Return view dengan data submission lengkap untuk form penilaian
        return view('admin.final-exam-grading.edit', compact('submission'));
    }


    /**
     * Menyimpan nilai ujian yang telah dinilai oleh admin.
     *
     * Proses:
     * 1. Validasi input: total_score harus integer antara 0-100
     * 2. Update record submission dengan score & status 'graded'
     * 3. Redirect ke halaman index dengan pesan sukses
     *
     * Validasi:
     * - total_score: required, integer, min:0, max:100
     *
     * Update data:
     * - total_score: nilai dari form (0-100)
     * - status: diubah ke 'graded' (sudah dinilai)
     *
     * @param Request $request HTTP request dengan field 'total_score'
     * @param ExamSubmission $submission Submisi ujian (di-inject via route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function update(Request $request, ExamSubmission $submission)
    {
        // Validasi input dari form
        $request->validate([
            'total_score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // Update record submission dengan score & status
        $submission->update([
            'total_score' => $request->total_score,  // Simpan nilai dari input form
            'status' => 'graded',                     // Tandai sudah dinilai
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('admin.final-exam-grading.index')
                         ->with('success', 'Nilai untuk ' . $submission->mentee->name . ' berhasil disimpan.');
    }
}
