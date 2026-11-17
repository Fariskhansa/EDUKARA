<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentAuthController extends Controller
{
    /**
     * Menampilkan form login siswa (NIS saja).
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Jika sudah login sebagai siswa aktif, langsung arahkan ke dashboard
        if (Auth::check() && Auth::user()->student) {
            return redirect()->route('student.dashboard');
        }

        return view('auth.login-siswa');
    }

    /**
     * Memproses login siswa menggunakan Nomor Induk Siswa (NIS).
     *
     * Catatan Arsitektur Keamanan:
     * Karena login hanya menggunakan NIS (tanpa kata sandi/PIN), proteksi diperketat
     * menggunakan Rate Limiting (pembatasan percobaan per IP), validasi status siswa aktif,
     * serta regenerasi ID sesi untuk mencegah session fixation.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Terapkan Rate Limiting (Maksimal 5 percobaan per menit per alamat IP)
        $throttleKey = 'student-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('nis'))
                ->withErrors([
                    'nis' => "Terlalu banyak percobaan masuk. Mohon tunggu {$seconds} detik sebelum mencoba kembali.",
                ]);
        }

        // 2. Validasi input format NIS
        $request->validate([
            'nis' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
        ], [
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi.',
            'nis.regex' => 'Format NIS tidak valid. NIS hanya terdiri dari angka.',
            'nis.max' => 'NIS maksimal terdiri dari 20 digit angka.',
        ]);

        $nis = trim($request->input('nis'));

        // 3. Verifikasi apakah NIS terdaftar di database
        $student = Student::with('user')->where('nis', $nis)->first();

        if (!$student) {
            RateLimiter::hit($throttleKey, 60);

            // Log aktivitas gagal jika tabel activity_logs tersedia
            if (class_exists(ActivityLog::class)) {
                ActivityLog::record('auth.student_login.failed', null, null, [
                    'nis' => $nis,
                    'reason' => 'NIS tidak ditemukan',
                    'ip' => $request->ip(),
                ]);
            }

            return back()
                ->withInput($request->only('nis'))
                ->withErrors([
                    'nis' => 'Nomor Induk Siswa (NIS) tidak terdaftar dalam sistem.',
                ]);
        }

        // 4. Verifikasi status keaktifan siswa (harus 'aktif')
        if ($student->status !== 'aktif') {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('nis'))
                ->withErrors([
                    'nis' => 'Status kesiswaan Anda tidak aktif (' . ucfirst($student->status) . '). Silakan hubungi bagian Kesiswaan/Tata Usaha SMAN Situraja.',
                ]);
        }

        // 5. Verifikasi akun pengguna (User) terikat
        $user = $student->user;
        if (!$user || !$user->is_active) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('nis'))
                ->withErrors([
                    'nis' => 'Akun pengguna siswa tidak aktif atau belum diinisialisasi. Hubungi administrator sekolah.',
                ]);
        }

        // 6. Login berhasil: Bersihkan limiter percobaan
        RateLimiter::clear($throttleKey);

        // Update riwayat login terakhir
        $user->update(['last_login_at' => now()]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record('auth.student_login.success', $user->id, $student, [
                'ip' => $request->ip(),
            ]);
        }

        // 7. Otentikasi pengguna melalui session Laravel
        Auth::login($user);

        // 8. Regenerasi Session ID untuk mencegah serangan Session Fixation
        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'))->with('status', 'Selamat datang, ' . $student->full_name . '!');
    }

    /**
     * Mengakhiri sesi login siswa secara aman.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
