<?php

// Public Mentor Registration
Route::get('daftar-pementor', [\App\Http\Controllers\MentorRegistrationController::class, 'create'])->name('mentor.register.create');
Route::post('daftar-pementor', [\App\Http\Controllers\MentorRegistrationController::class, 'store'])->name('mentor.register.store');

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/program', function () {
    $programs = collect(config('programs', []));

    return view('program.index', [
        'programs' => $programs,
    ]);
})->name('program.index');

Route::get('/program/{slug}', function (string $slug) {
    $programs = collect(config('programs', []));
    $program = $programs->get($slug);

    abort_if(is_null($program), 404);

    $relatedPrograms = $programs->except($slug)->values();

    return view('program.show', [
        'program' => $program,
        'relatedPrograms' => $relatedPrograms,
    ]);
})->name('program.show');

    Route::get('/dashboard', [\App\Http\Controllers\MenteeDashboardController::class, 'index'])->middleware(['auth', 'mentee'])->name('dashboard');

    // Mentor Dashboard
    Route::get('/mentor/dashboard', [\App\Http\Controllers\Mentor\DashboardController::class, 'index'])
        ->middleware(['auth', 'mentor'])->name('mentor.dashboard');

    // Mentor Routes
    Route::prefix('mentor')->name('mentor.')->middleware(['auth', 'mentor'])->group(function () {
        Route::resource('groups', \App\Http\Controllers\Mentor\GroupController::class);
        // Custom routes for creating a session for a specific group
        Route::get('group/{group}/new-session', [\App\Http\Controllers\Mentor\SessionController::class, 'create'])->name('sessions.create-for-group');
        Route::post('group/{group}/new-session', [\App\Http\Controllers\Mentor\SessionController::class, 'store'])->name('sessions.store-for-group');

        Route::resource('sessions', \App\Http\Controllers\Mentor\SessionController::class);
        Route::resource('reports', \App\Http\Controllers\Mentor\ProgressReportController::class);
    });

Route::middleware(['auth', 'mentee'])->group(function () {
    // Mentee Placement Test Routes
    Route::get('/placement-test/take', [\App\Http\Controllers\PlacementTestSubmissionController::class, 'create'])->name('placement-test.create');
    Route::post('/placement-test/take', [\App\Http\Controllers\PlacementTestSubmissionController::class, 'store'])->name('placement-test.store');
    
    // Mentee Materials
    Route::get('/materials', [\App\Http\Controllers\MenteeMaterialController::class, 'index'])->name('mentee.materials.index');

    // Mentee Announcements
    Route::get('/announcements', [\App\Http\Controllers\MenteeAnnouncementController::class, 'index'])->name('mentee.announcements.index');

    // Mentee Group
    Route::get('/group', [\App\Http\Controllers\MenteeGroupController::class, 'index'])->name('mentee.group.index');

    // Mentee Sessions
    Route::get('/sessions', [\App\Http\Controllers\MenteeSessionController::class, 'index'])->name('mentee.sessions.index');

    // Mentee Report
    Route::get('/report', [\App\Http\Controllers\MenteeReportController::class, 'index'])->name('mentee.report.index');

    // Mentee Exams
    Route::get('/exams', [\App\Http\Controllers\MenteeExamController::class, 'index'])->name('mentee.exams.index');
    Route::get('/exams/{exam}', [\App\Http\Controllers\MenteeExamController::class, 'show'])->name('mentee.exams.show');
    Route::get('/exams/completed', [\App\Http\Controllers\MenteeExamController::class, 'completed'])->name('mentee.exams.completed');
    Route::post('/exams/{exam}/submit', [\App\Http\Controllers\MenteeExamController::class, 'store'])->name('mentee.exams.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('statistics', [\App\Http\Controllers\Admin\StatisticController::class, 'index'])->name('statistics.index');
        
        Route::resource('faculties', \App\Http\Controllers\Admin\FacultyController::class);
        Route::resource('levels', \App\Http\Controllers\Admin\LevelController::class);
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
        Route::resource('materials', \App\Http\Controllers\Admin\MaterialController::class);
                Route::get('mentor-applications/{application}/audio', [\App\Http\Controllers\Admin\MentorApplicationController::class, 'streamAudio'])->name('mentor-applications.audio');
        Route::get('mentor-applications/{application}/cv', [\App\Http\Controllers\Admin\MentorApplicationController::class, 'streamCv'])->name('mentor-applications.cv');
        Route::resource('mentor-applications', \App\Http\Controllers\Admin\MentorApplicationController::class);
        Route::delete('mentor-applications/bulk-destroy', [\App\Http\Controllers\Admin\MentorApplicationController::class, 'bulkDestroy'])->name('mentor-applications.bulkDestroy');
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
        Route::resource('mentees', \App\Http\Controllers\Admin\MenteeController::class)->except(['create', 'store', 'edit', 'update']);

        // Mentee Import Routes
        Route::get('mentee-import', [\App\Http\Controllers\Admin\MenteeImportController::class, 'create'])->name('mentees.import.create');
        Route::post('mentee-import', [\App\Http\Controllers\Admin\MenteeImportController::class, 'store'])->name('mentees.import.store');
        Route::delete('mentees/destroy-all', [\App\Http\Controllers\Admin\MenteeController::class, 'destroyAll'])->name('mentees.destroyAll');
        Route::delete('mentees/bulk-destroy', [\App\Http\Controllers\Admin\MenteeController::class, 'bulkDestroy'])->name('mentees.bulkDestroy');
        Route::get('placement-tests/{placementTest}/audio', [\App\Http\Controllers\Admin\PlacementTestController::class, 'streamAudio'])->name('placement-tests.audio');
        Route::resource('placement-tests', \App\Http\Controllers\Admin\PlacementTestController::class);
        
        // Auto Grouping
        Route::get('mentoring-groups/auto-grouping', [\App\Http\Controllers\Admin\AutoGroupingController::class, 'create'])->name('mentoring-groups.auto-grouping.create');
        Route::post('mentoring-groups/auto-grouping', [\App\Http\Controllers\Admin\AutoGroupingController::class, 'store'])->name('mentoring-groups.auto-grouping.store');

        Route::resource('mentoring-groups', \App\Http\Controllers\Admin\MentoringGroupController::class);
        Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
        Route::resource('exams.questions', \App\Http\Controllers\Admin\QuestionController::class);

        // Final Exam Grading
        Route::resource('final-exam-grading', \App\Http\Controllers\Admin\FinalExamGradingController::class)
            ->only(['index', 'edit', 'update']);
    });
});

require __DIR__.'/auth.php';
