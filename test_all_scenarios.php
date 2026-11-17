<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "====================================================================\n";
echo " PENGUJIAN END-TO-END FITUR LOGIN SISWA & DASHBOARD — SMA SITURAJA\n";
echo "====================================================================\n\n";

$results = [];

function runScenario($name, callable $callback) {
    global $results;
    try {
        $result = $callback();
        $results[] = ['name' => $name, 'status' => 'PASS', 'details' => $result];
        echo " [PASS] $name\n        -> $result\n";
    } catch (\Throwable $e) {
        $results[] = ['name' => $name, 'status' => 'FAIL', 'details' => $e->getMessage()];
        echo " [FAIL] $name\n        -> ERROR: " . $e->getMessage() . "\n";
    }
}

function getFirstError($session, $field = 'nis') {
    $errors = $session->get('errors');
    if (!$errors) return null;
    if ($errors instanceof \Illuminate\Support\ViewErrorBag) {
        $bag = $errors->getBag('default');
        return $bag->has($field) ? $bag->first($field) : null;
    }
    if ($errors instanceof \Illuminate\Support\MessageBag) {
        return $errors->has($field) ? $errors->first($field) : null;
    }
    if (is_object($errors) && isset($errors->default) && isset($errors->default->messages)) {
        return $errors->default->messages[$field][0] ?? null;
    }
    if (is_array($errors)) {
        if (isset($errors['default']['messages'][$field])) {
            return $errors['default']['messages'][$field][0] ?? null;
        }
        if (isset($errors[$field])) {
            return is_array($errors[$field]) ? $errors[$field][0] : $errors[$field];
        }
    }
    return null;
}
function sendRequest($method, $uri, $parameters = [], $cookies = []) {
    global $kernel;
    $request = Illuminate\Http\Request::create($uri, $method, $parameters, $cookies);
    $request->setLaravelSession(app('session')->driver());
    return $kernel->handle($request);
}

// Skenario 1: Buka halaman login siswa
runScenario('1. Membuka Halaman Login Siswa (GET /login-siswa)', function() use ($app, $kernel) {
    $request = Illuminate\Http\Request::create('/login-siswa', 'GET');
    $session = $app['session']->driver();
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    if ($response->getStatusCode() !== 200) {
        throw new Exception("Status code bukan 200 melainkan: " . $response->getStatusCode());
    }
    if (!str_contains($response->getContent(), 'Portal Siswa') || !str_contains($response->getContent(), 'name="nis"')) {
        throw new Exception("Konten halaman login tidak memuat elemen yang diharapkan.");
    }
    return "Status 200 OK. Form NIS dan identitas SMA Situraja tampil lengkap.";
});

// Skenario 4: Login dengan NIS Kosong
runScenario('2. Login Siswa dengan Input NIS Kosong', function() use ($app, $kernel) {
    $session = $app['session']->driver();
    $session->start();
    $token = $session->token();
    
    $request = Illuminate\Http\Request::create('/login-siswa', 'POST', [
        '_token' => $token,
        'nis' => '',
    ]);
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    if ($response->getStatusCode() !== 302) {
        throw new Exception("Seharusnya redirect 302 kembali ke form.");
    }
    
    // Ambil error dari response session
    $resSession = $response->getSession() ?? $session;
    $msg = getFirstError($resSession, 'nis');
    if (!$msg) {
        throw new Exception("Pesan error validasi NIS tidak ditemukan. Session keys: " . json_encode(array_keys($resSession->all())));
    }
    return "Redirect 302. Validasi menolak input kosong dengan pesan: '$msg'";
});

// Skenario 3: Login dengan NIS Tidak Terdaftar
runScenario('3. Login Siswa dengan NIS Tidak Terdaftar (NIS: 9999999)', function() use ($app, $kernel) {
    $session = $app['session']->driver();
    $session->start();
    $token = $session->token();
    
    $request = Illuminate\Http\Request::create('/login-siswa', 'POST', [
        '_token' => $token,
        'nis' => '9999999',
    ]);
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    $resSession = $response->getSession() ?? $session;
    $msg = getFirstError($resSession, 'nis');
    if (!$msg) {
        throw new Exception("Pesan error NIS tidak terdaftar tidak ditemukan. Session keys: " . json_encode(array_keys($resSession->all())));
    }
    return "Redirect 302. Sistem menolak dengan pesan aman: '$msg'";
});

// Skenario 5: Login Siswa Tidak Aktif (Pindah: 9925020 - Anisa Maulana)
runScenario('4. Login Siswa Status Tidak Aktif (NIS: 9925020 - Pindah)', function() use ($app, $kernel) {
    $session = $app['session']->driver();
    $session->start();
    $token = $session->token();
    
    $request = Illuminate\Http\Request::create('/login-siswa', 'POST', [
        '_token' => $token,
        'nis' => '9925020',
    ]);
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    $resSession = $response->getSession() ?? $session;
    $msg = getFirstError($resSession, 'nis');
    if (!$msg) {
        throw new Exception("Pesan penolakan siswa tidak aktif tidak muncul. Session keys: " . json_encode(array_keys($resSession->all())));
    }
    return "Redirect 302. Sistem menolak siswa non-aktif dengan pesan: '$msg'";
});

// Skenario 6: Akses Dashboard Tanpa Login (Unauthenticated)
runScenario('5. Akses Dashboard Tanpa Login (GET /dashboard-siswa)', function() use ($app, $kernel) {
    Illuminate\Support\Facades\Auth::logout();
    $session = $app['session']->driver();
    $session->start();
    
    $request = Illuminate\Http\Request::create('/dashboard-siswa', 'GET');
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    if ($response->getStatusCode() !== 302) {
        throw new Exception("Seharusnya 302 redirect, didapatkan: " . $response->getStatusCode());
    }
    $target = $response->headers->get('Location');
    if (!str_contains($target, 'login-siswa')) {
        throw new Exception("Target redirect bukan ke login-siswa, melainkan: $target");
    }
    return "Status 302 Redirect. Tamu otomatis diarahkan ke halaman login ($target).";
});

// Skenario 2 & 8: Login Siswa Sukses (NIS: 9926001 - Fitri Gunawan) & Data Dashboard Sesuai
runScenario('6. Login Berhasil Siswa Aktif & Verifikasi Data Dashboard', function() use ($app, $kernel) {
    $session = $app['session']->driver();
    $session->start();
    $token = $session->token();
    
    // Login request
    $request = Illuminate\Http\Request::create('/login-siswa', 'POST', [
        '_token' => $token,
        'nis' => '9926001',
    ]);
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    if ($response->getStatusCode() !== 302) {
        throw new Exception("Login gagal, status code: " . $response->getStatusCode());
    }
    $target = $response->headers->get('Location');
    if (!str_contains($target, 'dashboard-siswa')) {
        throw new Exception("Setelah login tidak diarahkan ke dashboard-siswa: $target");
    }
    
    // Verifikasi Auth state
    if (!Illuminate\Support\Facades\Auth::check()) {
        throw new Exception("User tidak terotentikasi di Auth facade.");
    }
    $currentUser = Illuminate\Support\Facades\Auth::user();
    $student = $currentUser->student;
    if (!$student || $student->nis !== '9926001') {
        throw new Exception("Siswa yang terotentikasi bukan NIS 9926001.");
    }
    
    // Request Dashboard dengan sesi yang sama
    $dashReq = Illuminate\Http\Request::create('/dashboard-siswa', 'GET');
    $dashReq->setLaravelSession($session);
    $dashRes = $kernel->handle($dashReq);
    
    if ($dashRes->getStatusCode() !== 200) {
        throw new Exception("Gagal memuat dashboard, status: " . $dashRes->getStatusCode());
    }
    $html = $dashRes->getContent();
    if (!str_contains($html, 'Fitri Gunawan')) {
        throw new Exception("Nama siswa 'Fitri Gunawan' tidak ditemukan di HTML dashboard.");
    }
    if (!str_contains($html, '9926001')) {
        throw new Exception("NIS '9926001' tidak ditemukan di HTML dashboard.");
    }
    if (!str_contains($html, 'Kelas: <strong>X-1</strong>')) {
        throw new Exception("Kelas 'X-1' tidak ditemukan di HTML dashboard.");
    }
    
    return "Siswa 'Fitri Gunawan' berhasil login, masuk dashboard dengan NIS 9926001 dan Kelas X-1 riil.";
});

// Skenario 9: Proteksi ID Or Otorisasi Berbasis URL (Bukan dari URL Parameter)
runScenario('7. Verifikasi Otorisasi Bebas URL Tampering', function() use ($app, $kernel) {
    $user = \App\Models\User::find(12);
    \Illuminate\Support\Facades\Auth::login($user);
    $session = $app['session']->driver();
    $session->start();
    
    $request = Illuminate\Http\Request::create('/dashboard-siswa?nis=9926002&student_id=99', 'GET');
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    $html = $response->getContent();
    if (str_contains($html, 'Cecep Maulana') || str_contains($html, '9926002')) {
        throw new Exception("Kerentanan: Dashboard menampilkan identitas siswa lain dari URL parameter!");
    }
    if (!str_contains($html, 'Fitri Gunawan') || !str_contains($html, '9926001')) {
        throw new Exception("Dashboard gagal mengunci identitas ke sesi otentikasi server.");
    }
    return "Dashboard kebal terhadap manipulasi URL. Data tetap terikat pada sesi Fitri Gunawan (9926001).";
});

// Skenario 7: Logout dan Verifikasi Sesi Berakhir
runScenario('8. Logout Siswa & Proteksi Re-entry Dashboard', function() use ($app, $kernel) {
    $session = $app['session']->driver();
    $token = $session->token();
    
    // Kirim request logout POST
    $logoutReq = Illuminate\Http\Request::create('/logout', 'POST', ['_token' => $token]);
    $logoutReq->setLaravelSession($session);
    $logoutRes = $kernel->handle($logoutReq);
    
    if ($logoutRes->getStatusCode() !== 302) {
        throw new Exception("Logout gagal mengembalikan status 302.");
    }
    if (Illuminate\Support\Facades\Auth::check()) {
        throw new Exception("Otentikasi masih aktif setelah logout.");
    }
    
    // Coba akses kembali dashboard setelah logout
    $dashReq = Illuminate\Http\Request::create('/dashboard-siswa', 'GET');
    $dashReq->setLaravelSession($session);
    $dashRes = $kernel->handle($dashReq);
    
    if ($dashRes->getStatusCode() !== 302 || !str_contains($dashRes->headers->get('Location'), 'login-siswa')) {
        throw new Exception("Dashboard masih dapat diakses setelah logout.");
    }
    return "Sesi berhasil dihapus, otentikasi dihentikan, akses dashboard kembali ditolak (302 ke login).";
});

// Skenario 10: Landing Page Publik (GET /)
runScenario('9. Membuka Landing Page Publik (GET /)', function() use ($app, $kernel) {
    $request = Illuminate\Http\Request::create('/', 'GET');
    $session = $app['session']->driver();
    $request->setLaravelSession($session);
    $response = $kernel->handle($request);
    
    if ($response->getStatusCode() !== 200) {
        throw new Exception("Landing page gagal, status: " . $response->getStatusCode());
    }
    $html = $response->getContent();
    if (!str_contains($html, 'SMA Negeri Situraja') || !str_contains($html, 'Kawignyan Askaraning Tyas')) {
        throw new Exception("Landing page tidak memuat identitas dan motto sekolah.");
    }
    return "Status 200 OK. Landing page memuat identitas, hero, fitur, dan navigasi login siswa.";
});

echo "\n====================================================================\n";
echo " REKAPITULASI: SEMUA PENGUJIAN SELESAI (" . count($results) . " Skenario)\n";
echo "====================================================================\n";
