<?php

namespace App\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * AttendanceStatisticService
 *
 * Service untuk mengelola statistik kehadiran dalam sistem mentoring (MODULE D: Admin Dashboard)
 * Menyediakan data kehadiran mentee untuk berbagai jenis ujian dan sesi mentoring
 *
 * Fungsi utama:
 * - getAnalysis(): Menghitung statistik kehadiran berdasarkan fakultas
 *
 * Data yang dihasilkan:
 * - Jumlah total mentee per fakultas
 * - Jumlah kehadiran placement test per fakultas
 * - Persentase kehadiran placement test
 * - Jumlah kehadiran ujian akhir per fakultas
 * - Persentase kehadiran ujian akhir
 * - Interpretasi statistik
 *
 * @package App\Services\Statistic
 */
class AttendanceStatisticService
{
    /**
     * Menghitung dan mengembalikan statistik kehadiran berdasarkan fakultas
     *
     * Proses:
     * 1. Ambil jumlah total mentee per fakultas dari tabel users
     * 2. Ambil jumlah kehadiran placement test per fakultas
     * 3. Ambil jumlah kehadiran ujian akhir per fakultas
     * 4. Gabungkan data untuk setiap fakultas menjadi array statistik
     * 5. Buat interpretasi berdasarkan data statistik
     * 6. Kembalikan array berisi statistik dan interpretasi
     *
     * @param Collection $allFaculties Koleksi nama-nama fakultas untuk memastikan semua fakultas dimasukkan
     * @return array Array berisi statistik kehadiran dan interpretasi
     */
    public function getAnalysis(Collection $allFaculties): array
    {
        // Ambil jumlah total mentee per fakultas (role_id 3 diasumsikan sebagai Mentee)
        $totalMenteesByFaculty = DB::table('users')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->where('users.role_id', 3) // role_id 3 adalah Mentee
            ->select('faculties.name as faculty_name', DB::raw('COUNT(users.id) as total_mentees'))
            ->groupBy('faculties.name')
            ->pluck('total_mentees', 'faculty_name');

        // Ambil jumlah kehadiran placement test per fakultas
        $placementTestAttendance = DB::table('placement_tests')
            ->join('users', 'placement_tests.mentee_id', '=', 'users.id')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->select('faculties.name as faculty_name', DB::raw('COUNT(DISTINCT users.id) as attended_count'))
            ->groupBy('faculties.name')
            ->pluck('attended_count', 'faculty_name');

        // Ambil jumlah kehadiran ujian akhir per fakultas
        $finalExamAttendance = DB::table('exam_submissions')
            ->join('users', 'exam_submissions.mentee_id', '=', 'users.id')
            ->join('faculties', 'users.faculty_id', '=', 'faculties.id')
            ->select('faculties.name as faculty_name', DB::raw('COUNT(DISTINCT users.id) as attended_count'))
            ->groupBy('faculties.name')
            ->pluck('attended_count', 'faculty_name');

        // Gabungkan data kehadiran untuk setiap fakultas
        $attendanceStats = [];
        foreach ($allFaculties as $faculty) {
            // Iterasi semua fakultas untuk memastikan semua fakultas dimasukkan
            $total = $totalMenteesByFaculty[$faculty] ?? 0;
            $placementCount = $placementTestAttendance[$faculty] ?? 0;
            $finalExamCount = $finalExamAttendance[$faculty] ?? 0;

            // Hitung persentase kehadiran untuk setiap jenis ujian
            $attendanceStats[$faculty] = [
                'total_mentees' => $total,
                'placement_attended' => $placementCount,
                'placement_percentage' => $total > 0 ? ($placementCount / $total) * 100 : 0,
                'final_exam_attended' => $finalExamCount,
                'final_exam_percentage' => $total > 0 ? ($finalExamCount / $total) * 100 : 0,
            ];
        }

        // Buat interpretasi berdasarkan data statistik
        $interpretation = [];
        if(!empty($attendanceStats)) {
            // Siapkan data untuk analisis interpretasi
            $tempAttendanceStats = collect($attendanceStats)->map(function($stats, $facultyName) {
                $stats['faculty_name'] = $facultyName;
                return $stats;
            });

            // Temukan fakultas dengan persentase kehadiran tertinggi untuk masing-masing ujian
            $highestPlacement = $tempAttendanceStats->sortByDesc('placement_percentage')->first();
            $highestFinal = $tempAttendanceStats->sortByDesc('final_exam_percentage')->first();

            if($highestPlacement && $highestFinal) {
                // Tambahkan interpretasi tentang fakultas dengan kehadiran tertinggi
                $interpretation[] = "Partisipasi tertinggi pada Placement Test dicatatkan oleh **{$highestPlacement['faculty_name']}** (" . number_format($highestPlacement['placement_percentage'], 1) . "%), sedangkan untuk Ujian Akhir, partisipasi tertinggi dari **{$highestFinal['faculty_name']}** (" . number_format($highestFinal['final_exam_percentage'], 1) . "%).";
            }

            // Hitung rata-rata kehadiran placement test secara keseluruhan
            $totalMentees = $tempAttendanceStats->sum('total_mentees');
            $totalPlacementAttended = $tempAttendanceStats->sum('placement_attended');
            $avgPlacementPercentage = $totalMentees > 0 ? ($totalPlacementAttended / $totalMentees) * 100 : 0;
            $interpretation[] = "Rata-rata tingkat kehadiran mentee untuk Placement Test di semua fakultas adalah **" . number_format($avgPlacementPercentage, 1) . "%**.";
        }

        // Kembalikan data statistik dan interpretasi
        return [
            'attendanceStats' => $attendanceStats,
            'attendanceStatsInterpretation' => $interpretation,
        ];
    }
}
