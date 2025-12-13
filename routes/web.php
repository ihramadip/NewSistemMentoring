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
});

require __DIR__.'/auth.php';
