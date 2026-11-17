<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStudent
{
    /**
     * Pastikan pengguna yang sedang mengakses rute terautentikasi dan merupakan siswa aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek otentikasi
        if (!Auth::check()) {
            return redirect()->route('student.login')
                ->withErrors(['nis' => 'Silakan masuk menggunakan NIS Anda terlebih dahulu.']);
        }

        $user = Auth::user();
        $student = $user->student;

        // 2. Cek apakah memiliki profil siswa terikat
        if (!$student) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.login')
                ->withErrors(['nis' => 'Akun Anda bukan akun siswa yang terdaftar.']);
        }

        // 3. Cek status keaktifan siswa
        if ($student->status !== 'aktif' || !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.login')
                ->withErrors(['nis' => 'Status siswa atau akun Anda tidak aktif. Silakan hubungi pihak sekolah.']);
        }

        return $next($request);
    }
}
