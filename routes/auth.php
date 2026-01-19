<?php

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Here is where you can register authentication routes for your application.
| These routes are typically used for user registration, login, logout,
| password reset, email verification, and other authentication-related
| functionalities. They are grouped appropriately based on middleware.
|
*/

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Rute untuk pengguna yang belum login (guest)
Route::middleware('guest')->group(function () {
    // Rute registrasi pengguna baru
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // Rute login pengguna
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Rute lupa password - permintaan reset password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Rute reset password - formulir untuk mengganti password
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Rute untuk pengguna yang sudah login (auth)
Route::middleware('auth')->group(function () {
    // Rute verifikasi email - prompt untuk verifikasi email
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Rute verifikasi email - verifikasi email dengan ID dan hash
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Rute verifikasi email - kirim ulang notifikasi verifikasi
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Rute konfirmasi password - tampilkan form konfirmasi password
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Rute konfirmasi password - proses konfirmasi password
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Rute ubah password - proses pembaruan password
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Rute logout - proses logout pengguna
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
