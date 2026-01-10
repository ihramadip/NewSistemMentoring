<?php

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mentee Placement Test Routes
    Route::get('/placement-test/take', [\App\Http\Controllers\PlacementTestSubmissionController::class, 'create'])->name('placement-test.create');
    Route::post('/placement-test/take', [\App\Http\Controllers\PlacementTestSubmissionController::class, 'store'])->name('placement-test.store');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('faculties', \App\Http\Controllers\Admin\FacultyController::class);
        Route::resource('levels', \App\Http\Controllers\Admin\LevelController::class);
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
        Route::resource('materials', \App\Http\Controllers\Admin\MaterialController::class);
        Route::resource('mentor-applications', \App\Http\Controllers\Admin\MentorApplicationController::class);
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
        Route::resource('mentees', \App\Http\Controllers\Admin\MenteeController::class)->except(['create', 'store', 'edit', 'update']);

        // Mentee Import Routes
        Route::get('mentee-import', [\App\Http\Controllers\Admin\MenteeImportController::class, 'create'])->name('mentees.import.create');
        Route::post('mentee-import', [\App\Http\Controllers\Admin\MenteeImportController::class, 'store'])->name('mentees.import.store');
        Route::delete('mentees/destroy-all', [\App\Http\Controllers\Admin\MenteeController::class, 'destroyAll'])->name('mentees.destroyAll');
        Route::get('placement-tests/{placementTest}/audio', [\App\Http\Controllers\Admin\PlacementTestController::class, 'streamAudio'])->name('placement-tests.audio');
        Route::resource('placement-tests', \App\Http\Controllers\Admin\PlacementTestController::class);
    });
});

require __DIR__.'/auth.php';
