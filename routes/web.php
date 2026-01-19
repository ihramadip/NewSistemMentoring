<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Rute Halaman Depan
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rute Program
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

// Rute Pendaftaran Pementor Publik
Route::get('daftar-pementor', [\App\Http\Controllers\MentorRegistrationController::class, 'create'])->name('mentor.register.create');
Route::post('daftar-pementor', [\App\Http\Controllers\MentorRegistrationController::class, 'store'])->name('mentor.register.store');

// Rute Dashboard Mentee
Route::get('/dashboard', [\App\Http\Controllers\MenteeDashboardController::class, 'index'])->middleware(['auth', 'mentee'])->name('dashboard');

// Rute Dashboard Mentor
Route::get('/mentor/dashboard', [\App\Http\Controllers\Mentor\DashboardController::class, 'index'])
    ->middleware(['auth', 'mentor'])->name('mentor.dashboard');

// Rute Mentor
Route::prefix('mentor')->name('mentor.')->middleware(['auth', 'mentor'])->group(function () {
    // Rute resource untuk kelompok mentoring
    Route::resource('groups', \App\Http\Controllers\Mentor\GroupController::class);

    // Rute kustom untuk membuat sesi untuk kelompok tertentu
    Route::get('group/{group}/new-session', [\App\Http\Controllers\Mentor\SessionController::class, 'create'])->name('sessions.create-for-group');
    Route::post('group/{group}/new-session', [\App\Http\Controllers\Mentor\SessionController::class, 'store'])->name('sessions.store-for-group');

    // Rute untuk memilih kelompok sebelum membuat sesi
    Route::get('sessions/create-step-1', [\App\Http\Controllers\Mentor\SessionController::class, 'selectGroupForSession'])->name('sessions.select-group');

    // Rute resource untuk sesi mentoring
    Route::resource('sessions', \App\Http\Controllers\Mentor\SessionController::class);

    // Rute resource untuk laporan progres
    Route::resource('reports', \App\Http\Controllers\Mentor\ProgressReportController::class);

    // Rute untuk pelatihan mentor
    Route::get('trainings', [\App\Http\Controllers\Admin\MentorTrainingController::class, 'index'])->name('trainings.index');
});

// Rute untuk mentee yang sudah login
Route::middleware(['auth', 'mentee'])->group(function () {
    // Rute Placement Test untuk Mentee
    Route::get('/placement-test/take', [\App\Http\Controllers\PlacementTestSubmissionController::class, 'create'])->name('placement-test.create');
    Route::post('/placement-test/take', [\App\Http\Controllers\PlacementTestSubmissionController::class, 'store'])->name('placement-test.store');

    // Rute Materi untuk Mentee
    Route::get('/materials', [\App\Http\Controllers\MenteeMaterialController::class, 'index'])->name('mentee.materials.index');

    // Rute Pengumuman untuk Mentee
    Route::get('/announcements', [\App\Http\Controllers\MenteeAnnouncementController::class, 'index'])->name('mentee.announcements.index');

    // Rute Kelompok untuk Mentee
    Route::get('/group', [\App\Http\Controllers\MenteeGroupController::class, 'index'])->name('mentee.group.index');

    // Rute Sesi untuk Mentee
    Route::get('/sessions', [\App\Http\Controllers\MenteeSessionController::class, 'index'])->name('mentee.sessions.index');

    // Rute Sesi Tambahan untuk Mentee
    Route::resource('additional-sessions', \App\Http\Controllers\AdditionalSessionController::class)->middleware(['auth', 'mentee']);

    // Rute Laporan untuk Mentee
    Route::get('/report', [\App\Http\Controllers\MenteeReportController::class, 'index'])->name('mentee.report.index');

    // Rute Ujian untuk Mentee
    Route::get('/exams', [\App\Http\Controllers\MenteeExamController::class, 'index'])->name('mentee.exams.index');
    Route::get('/exams/{exam}', [\App\Http\Controllers\MenteeExamController::class, 'show'])->name('mentee.exams.show');
    Route::get('/exams/completed', [\App\Http\Controllers\MenteeExamController::class, 'completed'])->name('mentee.exams.completed');
    Route::post('/exams/{exam}/submit', [\App\Http\Controllers\MenteeExamController::class, 'store'])->name('mentee.exams.store');
});

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    // Rute Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Admin
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        // Dashboard Admin
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Statistik Admin
        Route::get('statistics', [\App\Http\Controllers\Admin\StatisticController::class, 'index'])->name('statistics.index');

        // Rute Resource untuk Fakultas
        Route::resource('faculties', \App\Http\Controllers\Admin\FacultyController::class);

        // Rute Resource untuk Level
        Route::resource('levels', \App\Http\Controllers\Admin\LevelController::class);

        // Rute Resource untuk Pengumuman
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);

        // Rute Resource untuk Materi
        Route::resource('materials', \App\Http\Controllers\Admin\MaterialController::class);

        // Rute untuk streaming audio dan CV dari aplikasi mentor
        Route::get('mentor-applications/{application}/audio', [\App\Http\Controllers\Admin\MentorApplicationController::class, 'streamAudio'])->name('mentor-applications.audio');
        Route::get('mentor-applications/{application}/cv', [\App\Http\Controllers\Admin\MentorApplicationController::class, 'streamCv'])->name('mentor-applications.cv');

        // Rute Resource untuk aplikasi mentor
        Route::resource('mentor-applications', \App\Http\Controllers\Admin\MentorApplicationController::class);

        // Rute untuk menghapus banyak aplikasi mentor sekaligus
        Route::delete('mentor-applications/bulk-destroy', [\App\Http\Controllers\Admin\MentorApplicationController::class, 'bulkDestroy'])->name('mentor-applications.bulkDestroy');

        // Rute Resource untuk mentee (tanpa create, store, edit, update)
        Route::resource('mentees', \App\Http\Controllers\Admin\MenteeController::class)->except(['create', 'store', 'edit', 'update']);

        // Rute Impor Mentee
        Route::get('mentee-import', [\App\Http\Controllers\Admin\MenteeImportController::class, 'create'])->name('mentees.import.create');
        Route::post('mentee-import', [\App\Http\Controllers\Admin\MenteeImportController::class, 'store'])->name('mentees.import.store');

        // Rute untuk menghapus semua mentee
        Route::delete('mentees/destroy-all', [\App\Http\Controllers\Admin\MenteeController::class, 'destroyAll'])->name('mentees.destroyAll');

        // Rute untuk menghapus banyak mentee sekaligus
        Route::delete('mentees/bulk-destroy', [\App\Http\Controllers\Admin\MenteeController::class, 'bulkDestroy'])->name('mentees.bulkDestroy');

        // Rute untuk streaming audio dari placement test
        Route::get('placement-tests/{placementTest}/audio', [\App\Http\Controllers\Admin\PlacementTestController::class, 'streamAudio'])->name('placement-tests.audio');

        // Rute Resource untuk placement test
        Route::resource('placement-tests', \App\Http\Controllers\Admin\PlacementTestController::class);

        // Rute Pengelompokan Otomatis
        Route::get('mentoring-groups/auto-grouping', [\App\Http\Controllers\Admin\AutoGroupingController::class, 'create'])->name('mentoring-groups.auto-grouping.create');
        Route::post('mentoring-groups/auto-grouping', [\App\Http\Controllers\Admin\AutoGroupingController::class, 'store'])->name('mentoring-groups.auto-grouping.store');

        // Rute Resource untuk kelompok mentoring
        Route::resource('mentoring-groups', \App\Http\Controllers\Admin\MentoringGroupController::class);

        // Rute Resource untuk ujian
        Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);

        // Rute Resource untuk pertanyaan ujian
        Route::resource('exams.questions', \App\Http\Controllers\Admin\QuestionController::class);

        // Rute Resource untuk pelatihan mentor
        Route::resource('mentor-trainings', \App\Http\Controllers\Admin\MentorTrainingController::class);

        // Rute Penilaian Ujian Akhir
        Route::resource('final-exam-grading', \App\Http\Controllers\Admin\FinalExamGradingController::class)
            ->only(['index', 'edit', 'update']);
    });
});

// Include file auth.php
require __DIR__.'/auth.php';
