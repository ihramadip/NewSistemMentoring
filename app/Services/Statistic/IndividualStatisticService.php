<?php

namespace App\Services\Statistic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * IndividualStatisticService
 *
 * Service untuk mengelola analisis statistik individu mentee (MODULE D: Admin Dashboard)
 * Menyediakan data analisis individu dengan kemampuan pencarian dan pagination
 *
 * Fungsi utama:
 * - getPaginatedAnalysis(): Mengembalikan data analisis individu mentee dengan pagination dan pencarian
 *
 * Data yang dihasilkan:
 * - NPM mentee
 * - Nama mentee
 * - Skor placement test
 * - Skor ujian akhir
 *
 * @package App\Services\Statistic
 */
class IndividualStatisticService
{
    /**
     * Mengembalikan data analisis individu mentee dengan pagination dan kemampuan pencarian
     *
     * Proses:
     * 1. Bangun query untuk menggabungkan data dari tabel users, placement_tests, dan exam_submissions
     * 2. Filter data hanya untuk mentee (role_id = 3) dengan ujian akhir yang sudah dinilai
     * 3. Tambahkan kondisi pencarian jika parameter search disediakan
     * 4. Lakukan pagination dengan 10 item per halaman
     * 5. Kembalikan hasil dengan query string untuk menjaga parameter pencarian
     *
     * @param Request $request Request object yang berisi parameter pencarian dan pagination
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Data analisis individu dengan pagination
     */
    public function getPaginatedAnalysis(Request $request)
    {
        // Bangun query dasar untuk menggabungkan data dari tabel users, placement_tests, dan exam_submissions
        $query = DB::table('users')
            ->join('placement_tests', 'users.id', '=', 'placement_tests.mentee_id')
            ->join('exam_submissions', 'users.id', '=', 'exam_submissions.mentee_id')
            ->where('users.role_id', 3) // Filter hanya untuk mentee (role_id = 3)
            ->where('exam_submissions.status', 'graded') // Hanya ambil ujian akhir yang sudah dinilai
            ->whereNotNull('placement_tests.audio_reading_score') // Pastikan skor placement test tersedia
            ->whereNotNull('placement_tests.theory_score') // Pastikan skor theory test tersedia
            ->select(
                'users.npm', // Nomor Pokok Mahasiswa
                'users.name', // Nama mentee
                DB::raw('ROUND((placement_tests.audio_reading_score + placement_tests.theory_score) / 2, 2) as placement_score'), // Skor rata-rata placement test
                'exam_submissions.total_score as final_exam_score' // Skor ujian akhir
            );

        // Tambahkan kondisi pencarian jika parameter search diisi
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%'; // Format untuk pencarian LIKE
            $query->where(function($q) use ($searchTerm) {
                // Cari berdasarkan nama atau NPM mentee
                $q->where('users.name', 'like', $searchTerm)
                  ->orWhere('users.npm', 'like', $searchTerm);
            });
        }

        // Lakukan pagination dan kembalikan hasil dengan query string untuk menjaga parameter pencarian
        return $query->orderBy('users.npm')->paginate(10)->withQueryString();
    }
}
