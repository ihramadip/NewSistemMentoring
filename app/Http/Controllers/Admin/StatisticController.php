<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\Statistic\DemographicStatisticService;
use App\Services\Statistic\LevelStatisticService;
use App\Services\Statistic\ComparisonStatisticService;
use App\Services\Statistic\ProgressionStatisticService;
use App\Services\Statistic\AttendanceStatisticService;
use App\Services\Statistic\PerformanceStatisticService;
use App\Services\Statistic\IndividualStatisticService;

/**
 * StatisticController
 *
 * Controller untuk manage laporan statistik sistem (MODULE D: Admin Dashboard)
 * Menyediakan berbagai jenis statistik untuk monitoring & evaluasi program mentoring
 *
 * Fitur:
 * - Index: halaman utama statistik dengan berbagai jenis analisis
 * - Demographic Stats: statistik berdasarkan fakultas & program studi
 * - Level Stats: distribusi mentee berdasarkan level mentoring
 * - Comparison Stats: perbandingan skor placement test vs final exam
 * - Progression Stats: analisis progres mentee berdasarkan fakultas & level
 * - Attendance Stats: statistik kehadiran mentee & mentor
 * - Performance Stats: analisis kinerja mentoring
 * - Individual Analysis: analisis individu mentee (dengan pagination & search)
 *
 * Data structure:
 * - Menggunakan berbagai service untuk menghitung statistik
 * - Data disimpan dalam array untuk dikirim ke view
 * - Sebagian besar data di-cache untuk performa
 * - Individual analysis tidak di-cache karena perlu search & pagination
 *
 * Services:
 * - DemographicStatisticService: statistik demografi mentee
 * - LevelStatisticService: statistik distribusi level
 * - ComparisonStatisticService: perbandingan skor
 * - ProgressionStatisticService: analisis progres
 * - AttendanceStatisticService: statistik kehadiran
 * - PerformanceStatisticService: analisis kinerja
 * - IndividualStatisticService: analisis individu mentee
 *
 * Caching:
 * - General statistics di-cache selama 6 jam
 * - Cache key: 'statistics.general'
 * - Individual analysis tidak di-cache karena dinamis (search & pagination)
 *
 * Flow:
 * 1. Admin akses halaman statistik
 * 2. Controller mengumpulkan data dari berbagai service
 * 3. Data di-cache untuk performa
 * 4. Individual analysis diambil terpisah (tidak di-cache)
 * 5. Data dikirim ke view untuk ditampilkan
 *
 * @package App\Http\Controllers\Admin
 */
class StatisticController extends Controller
{
    /**
     * Menampilkan halaman utama statistik
     *
     * Proses:
     * 1. Setup cache key & duration untuk general statistics
     * 2. Gunakan Cache::remember untuk meng-cache data statistik
     * 3. Instantiate semua statistic services
     * 4. Ambil data dari masing-masing service:
     *    - Demographic stats (fakultas & program)
     *    - Level stats (distribusi & effectiveness)
     *    - Comparison stats (placement vs final exam)
     *    - Progression stats (progres berdasarkan fakultas & level)
     *    - Attendance stats (kehadiran)
     *    - Performance stats (kinerja mentoring)
     * 5. Ambil individual analysis (tidak di-cache karena pagination & search)
     * 6. Return view dengan semua data statistik
     *
     * Caching:
     * - General statistics di-cache selama 6 jam
     * - Cache key: 'statistics.general'
     * - Tujuan: menghindari komputasi berat berulang-ulang
     * - Individual analysis tidak di-cache karena dinamis
     *
     * Services integration:
     * - DemographicStatisticService: getFacultyStats(), getProgramStats()
     * - LevelStatisticService: getDistribution()
     * - ComparisonStatisticService: getScoreComparison()
     * - ProgressionStatisticService: getAnalysis()
     * - AttendanceStatisticService: getAnalysis()
     * - PerformanceStatisticService: getAnalysis()
     * - IndividualStatisticService: getPaginatedAnalysis()
     *
     * @param Request $request Request object untuk pagination & search individual analysis
     * @return \Illuminate\View\View View halaman statistik
     */
    public function index(Request $request)
    {
        // Setup cache untuk general statistics (komputasi berat, tidak perlu sering dihitung)
        $cacheKey = 'statistics.general';
        $cacheDuration = now()->addHours(6); // Cache selama 6 jam

        // Ambil data dari cache atau hitung jika belum ada
        $data = Cache::remember($cacheKey, $cacheDuration, function () {
            $result = [];

            // Instantiate semua statistic services
            $demographicService = new DemographicStatisticService();
            $levelService = new LevelStatisticService();
            $comparisonService = new ComparisonStatisticService();
            $progressionService = new ProgressionStatisticService();
            $attendanceService = new AttendanceStatisticService();
            $performanceService = new PerformanceStatisticService();

            // 1. & 2. Demographic Statistics
            // Ambil data statistik berdasarkan fakultas & program studi
            $facultyData = $demographicService->getFacultyStats();
            $result['facultyStats'] = $facultyData['stats'];
            $result['facultyStatsInterpretation'] = $facultyData['interpretation'];
            $result['programStats'] = $demographicService->getProgramStats();

            // 3. Level Statistics
            // Ambil data distribusi mentee berdasarkan level mentoring
            $levelData = $levelService->getDistribution();
            $result['levels'] = $levelData['levels'];
            $result['levelNames'] = $levelData['levelNames'];
            $result['levelDistribution'] = $levelData['levelDistribution'];
            $result['levelDistributionInterpretation'] = $levelData['interpretation'];
            $result['levelEffectivenessMatrix'] = $levelData['levelEffectivenessMatrix'];
            $result['levelEffectivenessInterpretation'] = $levelData['levelEffectivenessInterpretation'];

            // 4. Comparison Statistics
            // Ambil data perbandingan skor placement test vs final exam
            $comparisonData = $comparisonService->getScoreComparison();
            $result['scoreComparisonData'] = $comparisonData['data'];
            $result['scoreComparisonInterpretation'] = $comparisonData['interpretation'];

            // Shared data yang dibutuhkan oleh beberapa services
            $allFaculties = DB::table('faculties')->orderBy('name')->pluck('name');

            // 5. & 6. Progression Statistics
            // Analisis progres mentee berdasarkan fakultas & level
            $progressionData = $progressionService->getAnalysis($result['levels'], $allFaculties);
            $result = array_merge($result, $progressionData);

            // 7. Attendance Statistics
            // Statistik kehadiran mentee & mentor
            $attendanceData = $attendanceService->getAnalysis($allFaculties);
            $result = array_merge($result, $attendanceData);

            // 9. & 10. Performance Statistics
            // Analisis kinerja mentoring
            $performanceData = $performanceService->getAnalysis();
            $result = array_merge($result, $performanceData);

            return $result;
        });

        // 8. Individual Analysis (Tidak di-cache karena perlu search & pagination)
        // Ambil data analisis individu mentee dengan pagination & search
        $individualService = new IndividualStatisticService();
        $individualAnalyses = $individualService->getPaginatedAnalysis($request);
        $data['individualAnalyses'] = $individualAnalyses;

        // Return view dengan semua data statistik
        return view('admin.statistics.index', $data);
    }
}