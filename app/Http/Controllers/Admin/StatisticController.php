<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Services\Statistic\DemographicStatisticService;
use App\Services\Statistic\LevelStatisticService;
use App\Services\Statistic\ComparisonStatisticService;
use App\Services\Statistic\ProgressionStatisticService;
use App\Services\Statistic\AttendanceStatisticService;
use App\Services\Statistic\PerformanceStatisticService;
use App\Services\Statistic\IndividualStatisticService;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cache general statistics that are heavy to compute and don't depend on search/pagination.
        $cacheKey = 'statistics.general';
        $cacheDuration = now()->addHours(6);

        $data = Cache::remember($cacheKey, $cacheDuration, function () {
            $result = [];

            // Instantiate all services
            $demographicService = new DemographicStatisticService();
            $levelService = new LevelStatisticService();
            $comparisonService = new ComparisonStatisticService();
            $progressionService = new ProgressionStatisticService();
            $attendanceService = new AttendanceStatisticService();
            $performanceService = new PerformanceStatisticService();

            // 1. & 2. Demographic Stats
            $facultyData = $demographicService->getFacultyStats();
            $result['facultyStats'] = $facultyData['stats'];
            $result['facultyStatsInterpretation'] = $facultyData['interpretation'];
            $result['programStats'] = $demographicService->getProgramStats();

            // 3. Level Stats
            $levelData = $levelService->getDistribution();
            $result['levels'] = $levelData['levels'];
            $result['levelNames'] = $levelData['levelNames'];
            $result['levelDistribution'] = $levelData['levelDistribution'];
            $result['levelDistributionInterpretation'] = $levelData['interpretation']; // Correctly keyed
            $result['levelEffectivenessMatrix'] = $levelData['levelEffectivenessMatrix'];
            $result['levelEffectivenessInterpretation'] = $levelData['levelEffectivenessInterpretation'];

            // 4. Comparison Stats
            $comparisonData = $comparisonService->getScoreComparison();
            $result['scoreComparisonData'] = $comparisonData['data'];
            $result['scoreComparisonInterpretation'] = $comparisonData['interpretation'];

            // Shared data needed for subsequent services
            $allFaculties = DB::table('faculties')->orderBy('name')->pluck('name');

            // 5. & 6. Progression Stats
            $progressionData = $progressionService->getAnalysis($result['levels'], $allFaculties);
            $result = array_merge($result, $progressionData);

            // 7. Attendance Stats
            $attendanceData = $attendanceService->getAnalysis($allFaculties);
            $result = array_merge($result, $attendanceData);

            // 9. & 10. Performance Stats
            $performanceData = $performanceService->getAnalysis();
            $result = array_merge($result, $performanceData);

            return $result;
        });

        // 8. Individual Analysis (Paginated & Searched - NOT CACHED)
        $individualService = new IndividualStatisticService();
        $individualAnalyses = $individualService->getPaginatedAnalysis($request);
        $data['individualAnalyses'] = $individualAnalyses;

        return view('admin.statistics.index', $data);
    }
}