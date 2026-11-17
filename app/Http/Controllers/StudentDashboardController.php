<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    /**
     * Menampilkan Action-First Dashboard Siswa.
     *
     * Memprioritaskan status absensi hari ini dan tindakan utama siswa,
     * dengan statistik serta profil sebagai informasi pendukung sekunder.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $student = $user->student;

        // Ambil kelas aktif siswa pada tahun ajaran berjalan
        $currentClass = $student->currentClass();

        // Ambil nama depan untuk sapaan akrab & ramah
        $nameParts = explode(' ', trim($student->full_name));
        $firstName = $nameParts[0] ?? $student->full_name;

        // Cek status kehadiran hari ini (zona waktu WIB)
        $now = now()->timezone('Asia/Jakarta');
        $today = $now->toDateString();
        $todaySubmission = $student->submissions()
            ->whereDate('submitted_at', $today)
            ->latest('submitted_at')
            ->first();

        // Format tanggal hari ini dalam Bahasa Indonesia
        $currentDateFormatted = $now->locale('id')->translatedFormat('l, d F Y');

        // Data riil kedisiplinan & absensi dari database
        $violationsCount = $student->violations()->where('status', 'recorded')->count();
        $totalViolationPoints = (int) $student->violations()->where('status', 'recorded')->sum('points');
        $recentViolations = $student->violations()
            ->with('category')
            ->where('status', 'recorded')
            ->latest('occurred_at')
            ->take(5)
            ->get();

        $attendanceCount = $student->submissions()->count();
        $recentAttendance = $student->submissions()
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Sapaan waktu
        $hour = (int) $now->format('H');
        if ($hour >= 5 && $hour < 11) {
            $greetingTime = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greetingTime = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greetingTime = 'Selamat Sore';
        } else {
            $greetingTime = 'Selamat Malam';
        }

        return view('student.dashboard', [
            'student' => $student,
            'user' => $user,
            'firstName' => $firstName,
            'currentClass' => $currentClass,
            'todaySubmission' => $todaySubmission,
            'currentDateFormatted' => $currentDateFormatted,
            'violationsCount' => $violationsCount,
            'totalViolationPoints' => $totalViolationPoints,
            'recentViolations' => $recentViolations,
            'attendanceCount' => $attendanceCount,
            'recentAttendance' => $recentAttendance,
            'greetingTime' => $greetingTime,
        ]);
    }
}
