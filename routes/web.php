<?php

use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Informasi Kesiswaan SMA Negeri Situraja
|--------------------------------------------------------------------------
*/

// --- 1. Halaman Publik (Landing Page) ---
Route::get('/', function () {
    return view('landing');
})->name('home');

// --- 2. Autentikasi Siswa (Khusus NIS) ---
Route::middleware('guest')->group(function () {
    Route::get('/login-siswa', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login-siswa', [StudentAuthController::class, 'login'])->name('student.login.post');
});

// --- 3. Area Terproteksi Siswa ---
Route::middleware(['auth', 'student.auth'])->group(function () {
    Route::get('/dashboard-siswa', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
});
