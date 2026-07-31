<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    // ── Login ──────────────────────────────────────────────────────────────
    Route::get('login', fn() => view('auth.login'))->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // ── Password Reset ─────────────────────────────────────────────────────
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    // ── Logout ─────────────────────────────────────────────────────────────
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // ── Email Verification ─────────────────────────────────────────────────
    Route::get('verify-email', fn() => view('auth.verify-email'))
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});
