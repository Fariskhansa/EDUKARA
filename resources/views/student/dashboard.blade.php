@extends('layouts.app')

@section('title', 'Dashboard Siswa — EDUKARA | ' . $student->full_name)
@section('body-class', 'dashboard-body')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/dashboard-siswa.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper">

  <!-- ============ DASHBOARD NAVBAR ============ -->
  <header class="dashboard-header">
    <div class="dashboard-header-inner">
      <div class="header-brand">
        <a href="{{ route('home') }}" class="brand-link" title="Beranda EDUKARA SMA Negeri Situraja">
          <img src="{{ asset('assets/images/logo-sma-situraja.png') }}"
               alt="Logo SMAN Situraja"
               class="brand-logo"
               width="40"
               height="40">
          <div class="brand-info">
            <span class="brand-title">EDUKARA</span>
            <span class="brand-tag">SMAN Situraja</span>
          </div>
        </a>
      </div>

      <!-- User Profile & Logout -->
      <div class="header-user-menu">
        <div class="user-pill">
          <div class="user-avatar" aria-hidden="true">
            {{ strtoupper(substr($student->full_name, 0, 1)) }}
          </div>
          <div class="user-details">
            <span class="user-name">{{ $student->full_name }}</span>
            <span class="user-nis">NIS: {{ $student->nis }} • {{ $currentClass ? $currentClass->name : 'Siswa' }}</span>
          </div>
        </div>

        <!-- Tombol Logout dengan Proteksi CSRF -->
        <form action="{{ route('student.logout') }}" method="POST" class="logout-form" id="logoutForm">
          @csrf
          <button type="submit" class="btn btn-logout" id="btnLogout" title="Keluar dari akun">
            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
            <span class="logout-text">Keluar</span>
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- ============ DASHBOARD MAIN CONTENT (ACTION-FIRST) ============ -->
  <main class="dashboard-main">
    <div class="container">

      <!-- Notifikasi Flash Status -->
      @if(session('status'))
        <div class="alert alert-success" role="status">
          <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      <!-- ============================================================
           PRIORITAS 1: SAPAAN PERSONAL & STATUS ABSENSI HARI INI
           ============================================================ -->
      <section class="action-hero-section">
        
        <!-- Sapaan Hangat Personal -->
        <div class="greeting-bar">
          <div class="greeting-left">
            <span class="greeting-meta-badge">
              <i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
              {{ $currentDateFormatted }} • Tahun Ajaran {{ $currentClass?->academicYear?->name ?? '2026/2027 Ganjil' }}
            </span>
            <h1 class="greeting-title">Halo, {{ $firstName }}! 👋</h1>
            <p class="greeting-subtitle">Siap menjalani kegiatan belajar di SMA Negeri Situraja hari ini?</p>
          </div>

          <div class="greeting-right">
            <span class="badge-identity">
              <i class="fa-solid fa-chalkboard-user"></i>
              Kelas: <strong>{{ $currentClass ? $currentClass->name : 'X-1' }}</strong>
            </span>
          </div>
        </div>

        <!-- HERO ACTION CARD: KARTU STATUS & TINDAKAN ABSENSI HARI INI -->
        <div class="today-action-card">
          <div class="today-action-content">

            @if($todaySubmission)
              {{-- Kondisi 1: Sudah ada catatan absensi hari ini --}}
              @if($todaySubmission->status === 'accepted')
                <div class="status-indicator status-indicator--accepted">
                  <div class="status-icon-wrap status-icon-wrap--success">
                    <i class="fa-solid fa-circle-check"></i>
                  </div>
                  <div class="status-text-wrap">
                    <div class="status-header-line">
                      <span class="status-pill status-pill--success">
                        <i class="fa-solid fa-check"></i> Kehadiran Terverifikasi
                      </span>
                      <span class="status-time">Tercatat: {{ \Carbon\Carbon::parse($todaySubmission->submitted_at)->format('H:i') }} WIB</span>
                    </div>
                    <h2 class="status-heading">Anda Sudah Hadir Hari Ini</h2>
                    <p class="status-desc">
                      Kehadiran Anda telah disetujui oleh guru piket. 
                      Status: <strong>{{ $todaySubmission->is_late ? 'Terlambat' : 'Tepat Waktu' }}</strong> 
                      @if($todaySubmission->geofence_flag === 'inside')
                        • Berada di area radius sekolah.
                      @endif
                    </p>
                  </div>
                </div>
                <div class="today-action-cta">
                  <button type="button" class="btn btn-action-done" disabled>
                    <i class="fa-solid fa-check-double"></i> Absensi Selesai
                  </button>
                  <span class="cta-note">Terima kasih atas kedisiplinan Anda.</span>
                </div>

              @elseif($todaySubmission->status === 'pending')
                <div class="status-indicator status-indicator--pending">
                  <div class="status-icon-wrap status-icon-wrap--warning">
                    <i class="fa-solid fa-hourglass-half fa-spin"></i>
                  </div>
                  <div class="status-text-wrap">
                    <div class="status-header-line">
                      <span class="status-pill status-pill--warning">
                        <i class="fa-solid fa-clock"></i> Menunggu Validasi Guru
                      </span>
                      <span class="status-time">Diajukan: {{ \Carbon\Carbon::parse($todaySubmission->submitted_at)->format('H:i') }} WIB</span>
                    </div>
                    <h2 class="status-heading">Pengajuan Absensi Terkirim</h2>
                    <p class="status-desc">
                      Data absensi Anda sedang menunggu pemeriksaan oleh guru piket kesiswaan. Anda tidak perlu mengajukan absensi ulang.
                    </p>
                  </div>
                </div>
                <div class="today-action-cta">
                  <button type="button" class="btn btn-action-pending" disabled>
                    <i class="fa-solid fa-clock"></i> Menunggu Verifikasi
                  </button>
                  <span class="cta-note">Status akan otomatis diperbarui.</span>
                </div>

              @else
                <div class="status-indicator status-indicator--rejected">
                  <div class="status-icon-wrap status-icon-wrap--danger">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                  </div>
                  <div class="status-text-wrap">
                    <div class="status-header-line">
                      <span class="status-pill status-pill--danger">
                        <i class="fa-solid fa-circle-xmark"></i> Pengajuan Ditolak
                      </span>
                    </div>
                    <h2 class="status-heading">Absensi Hari Ini Ditolak</h2>
                    <p class="status-desc">
                      Pengajuan kehadiran Anda ditolak oleh validator. Silakan lakukan pemindaian ulang QR Code kelas atau hubungi guru piket.
                    </p>
                  </div>
                </div>
                <div class="today-action-cta">
                  <button type="button" class="btn btn-action-primary" onclick="bukaModalAbsensi()">
                    <i class="fa-solid fa-rotate-right"></i> Ajukan Ulang Absensi
                  </button>
                  <span class="cta-note">Laporkan ke guru piket jika ada kendala.</span>
                </div>
              @endif

            @else
              {{-- Kondisi 2: Belum mengajukan absensi hari ini (DEFAULT ACTION) --}}
              <div class="status-indicator status-indicator--notyet">
                <div class="status-icon-wrap status-icon-wrap--primary">
                  <i class="fa-solid fa-qrcode"></i>
                </div>
                <div class="status-text-wrap">
                  <div class="status-header-line">
                    <span class="status-pill status-pill--alert">
                      <i class="fa-solid fa-circle-dot"></i> Belum Melakukan Absensi
                    </span>
                    <span class="status-time">Sesi Masuk Aktif</span>
                  </div>
                  <h2 class="status-heading">Catat Kehadiran Anda Sekarang</h2>
                  <p class="status-desc">
                    Lakukan pemindaian QR Code kelas aktif yang tersedia di ruang kelas <strong>{{ $currentClass ? $currentClass->name : 'Anda' }}</strong> sebelum batas toleransi jam pelajaran berakhir.
                  </p>
                </div>
              </div>

              <div class="today-action-cta">
                <button type="button" class="btn btn-action-primary" onclick="bukaModalAbsensi()">
                  <i class="fa-solid fa-qrcode"></i> Mulai Absensi Siswa
                </button>
                <span class="cta-note"><i class="fa-solid fa-location-dot"></i> Pastikan GPS ponsel aktif di lingkungan sekolah</span>
              </div>
            @endif

          </div>
        </div>

      </section>

      <!-- ============================================================
           PRIORITAS 2: FITUR UTAMA SISWA (AKSES CEPAT)
           ============================================================ -->
      <section class="action-services-section">
        <div class="section-title-wrap">
          <div>
            <h2 class="section-heading-action">
              <i class="fa-solid fa-bolt-lightning text-primary" aria-hidden="true"></i> Akses Cepat Fitur Siswa
            </h2>
            <p class="section-desc-action">Fitur utama yang siap Anda gunakan untuk kebutuhan administrasi belajar.</p>
          </div>
        </div>

        <div class="action-cards-grid">

          <!-- 1. ABSENSI SISWA (FEATURED CARD — PALING MENONJOL) -->
          <article class="action-card action-card--featured" id="cardAbsensi">
            <div class="card-featured-badge">
              <i class="fa-solid fa-star"></i> Fitur Utama
            </div>
            <div class="card-body-content">
              <div class="action-card-icon action-card-icon--primary">
                <i class="fa-solid fa-qrcode" aria-hidden="true"></i>
              </div>
              <h3 class="action-card-title">Absensi Siswa</h3>
              <p class="action-card-desc">
                Pindai QR Code kelas secara langsung untuk mencatat absensi masuk harian Anda dengan validasi lokasi sekolah.
              </p>
            </div>
            <div class="action-card-footer">
              <button type="button" class="btn btn-primary btn-block-action" onclick="bukaModalAbsensi()">
                <i class="fa-solid fa-camera"></i> Buka Kamera Absensi
              </button>
            </div>
          </article>

          <!-- 2. RIWAYAT ABSENSI (TINDAKAN KEDUA) -->
          <article class="action-card" id="cardRiwayat">
            <div class="card-body-content">
              <div class="action-card-icon action-card-icon--success">
                <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
              </div>
              <h3 class="action-card-title">Riwayat Absensi</h3>
              <p class="action-card-desc">
                Lihat daftar kehadiran yang telah terekam, persentase tepat waktu, dan rekapitulasi per bulan.
              </p>
            </div>
            <div class="action-card-footer">
              <button type="button" class="btn btn-outline-action btn-block-action" onclick="bukaModalRiwayat()">
                <i class="fa-solid fa-calendar-days"></i> Lihat Riwayat ({{ $attendanceCount }})
              </button>
            </div>
          </article>

          <!-- 3. POIN PELANGGARAN (TINDAKAN KETIGA) -->
          <article class="action-card" id="cardPelanggaran">
            <div class="card-body-content">
              <div class="action-card-icon {{ $totalViolationPoints > 0 ? 'action-card-icon--danger' : 'action-card-icon--clean' }}">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
              </div>
              <h3 class="action-card-title">Poin Pelanggaran</h3>
              <p class="action-card-desc">
                Pantau catatan tata tertib siswa dan status akumulasi poin kedisiplinan Anda di sekolah.
              </p>
            </div>
            <div class="action-card-footer">
              <button type="button" class="btn btn-outline-action btn-block-action" onclick="document.getElementById('modalPelanggaran').showModal()">
                <i class="fa-solid fa-list-check"></i> Rincian Disiplin ({{ $totalViolationPoints }} Poin)
              </button>
            </div>
          </article>

        </div>
      </section>

      <!-- ============================================================
           PRIORITAS 3: RINGKASAN INFORMASI SISWA (COMPACT STRIP)
           ============================================================ -->
      <section class="compact-summary-section">
        <h2 class="compact-section-heading">Ringkasan Informasi Siswa</h2>
        
        <div class="compact-summary-strip">
          
          <div class="summary-item">
            <div class="summary-item-icon text-primary">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="summary-item-content">
              <span class="summary-item-label">Total Kehadiran</span>
              <strong class="summary-item-value">{{ $attendanceCount }} Sesi</strong>
              <span class="summary-item-sub">Terekam pada sistem</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-item-icon {{ $totalViolationPoints > 0 ? 'text-danger' : 'text-success' }}">
              <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="summary-item-content">
              <span class="summary-item-label">Poin Pelanggaran</span>
              <strong class="summary-item-value {{ $totalViolationPoints > 0 ? 'text-danger' : 'text-success' }}">
                {{ $totalViolationPoints }} Poin
              </strong>
              <span class="summary-item-sub">
                {{ $totalViolationPoints == 0 ? 'Disiplin Sangat Baik' : $violationsCount . ' Catatan' }}
              </span>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-item-icon text-warning">
              <i class="fa-solid fa-chalkboard"></i>
            </div>
            <div class="summary-item-content">
              <span class="summary-item-label">Kelas & Program</span>
              <strong class="summary-item-value">{{ $currentClass ? $currentClass->name : 'X-1' }}</strong>
              <span class="summary-item-sub">{{ $currentClass ? ($currentClass->grade_level . ' ' . ($currentClass->program ?? 'Reguler')) : 'Tingkat X' }}</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-item-icon text-info">
              <i class="fa-solid fa-id-card"></i>
            </div>
            <div class="summary-item-content">
              <span class="summary-item-label">Status Siswa</span>
              <strong class="summary-item-value text-success">{{ ucfirst($student->status) }}</strong>
              <span class="summary-item-sub">NIS: {{ $student->nis }}</span>
            </div>
          </div>

        </div>
      </section>

      <!-- ============================================================
           PRIORITAS 4: INFORMASI SEKOLAH & PANDUAN SISWA
           ============================================================ -->
      <section class="school-notice-section">
        <div class="notice-card">
          <div class="notice-icon">
            <i class="fa-solid fa-circle-info"></i>
          </div>
          <div class="notice-text">
            <h4>Panduan Disiplin Belajar SMA Negeri Situraja</h4>
            <p>
              Motto Almamater: <em>"Kawignyan Askaraning Tyas"</em> (Pengetahuan adalah Cahaya Jiwa). 
              Pastikan Anda melakukan pemindaian absensi sebelum jam 07.00 WIB setiap hari sekolah. Jika kamera ponsel atau sinyal GPS mengalami kendala, segera lapor ke guru piket di lobi sekolah.
            </p>
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- ============================================================
       MODAL DIALOG 1: MODUL PEMINDAI ABSENSI QR
       ============================================================ -->
  <dialog id="modalAbsensi" class="app-dialog">
    <div class="dialog-content">
      <div class="dialog-header">
        <h3><i class="fa-solid fa-qrcode text-primary"></i> Pemindai Absensi Siswa</h3>
        <button type="button" class="btn-close-dialog" onclick="document.getElementById('modalAbsensi').close()" aria-label="Tutup">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="dialog-body">
        <div class="qr-scanner-placeholder">
          <div class="scanner-frame">
            <i class="fa-solid fa-camera fa-2x"></i>
            <div class="scanner-line"></div>
          </div>
          <h4>Pemindai Kamera Siap Diintegrasikan</h4>
          <p>
            Modul pemindaian QR Code kelas sedang disiapkan untuk tahap implementasi modul absensi kamera & geolokasi GPS berikutnya.
          </p>
          <div class="scanner-rules">
            <small><i class="fa-solid fa-check text-success"></i> NIS Aktif: <strong>{{ $student->nis }}</strong> ({{ $student->full_name }})</small><br>
            <small><i class="fa-solid fa-check text-success"></i> Kelas Terdaftar: <strong>{{ $currentClass ? $currentClass->name : 'X-1' }}</strong></small>
          </div>
        </div>
      </div>
      <div class="dialog-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAbsensi').close()">Tutup</button>
      </div>
    </div>
  </dialog>

  <!-- ============================================================
       MODAL DIALOG 2: RIWAYAT ABSENSI (DATA RIIL DARI DATABASE)
       ============================================================ -->
  <dialog id="modalRiwayat" class="app-dialog">
    <div class="dialog-content">
      <div class="dialog-header">
        <h3><i class="fa-solid fa-clock-rotate-left text-success"></i> Riwayat Kehadiran Siswa</h3>
        <button type="button" class="btn-close-dialog" onclick="document.getElementById('modalRiwayat').close()" aria-label="Tutup">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="dialog-body">
        @if($recentAttendance->isEmpty())
          <div class="empty-state">
            <i class="fa-solid fa-calendar-xmark text-muted"></i>
            <h4>Belum Ada Riwayat Absensi</h4>
            <p>Data sesi absensi Anda akan tampil di sini setelah Anda melakukan absensi pertama.</p>
          </div>
        @else
          <ul class="attendance-list">
            @foreach($recentAttendance as $submission)
              <li class="attendance-item">
                <div class="att-info">
                  <strong class="att-date">{{ \Carbon\Carbon::parse($submission->submitted_at)->locale('id')->translatedFormat('l, d F Y') }}</strong>
                  <span class="att-time">
                    <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($submission->submitted_at)->format('H:i:s') }} WIB
                  </span>
                </div>
                <div class="att-badge-wrap">
                  @if($submission->status === 'accepted')
                    <span class="badge-status badge-status--accepted">
                      {{ $submission->is_late ? 'Terlambat' : 'Tepat Waktu' }}
                    </span>
                  @elseif($submission->status === 'pending')
                    <span class="badge-status badge-status--pending">Menunggu</span>
                  @else
                    <span class="badge-status badge-status--rejected">Ditolak</span>
                  @endif
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
      <div class="dialog-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalRiwayat').close()">Tutup</button>
      </div>
    </div>
  </dialog>

  <!-- ============================================================
       MODAL DIALOG 3: POIN KEDISIPLINAN (DATA RIIL DATABASE)
       ============================================================ -->
  <dialog id="modalPelanggaran" class="app-dialog">
    <div class="dialog-content">
      <div class="dialog-header">
        <h3><i class="fa-solid fa-shield-halved text-danger"></i> Catatan Kedisiplinan Siswa</h3>
        <button type="button" class="btn-close-dialog" onclick="document.getElementById('modalPelanggaran').close()" aria-label="Tutup">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="dialog-body">
        @if($recentViolations->isEmpty())
          <div class="empty-state">
            <i class="fa-solid fa-award text-success"></i>
            <h4>Rekor Kedisiplinan Bersih</h4>
            <p>Selamat! Anda memiliki rekor kedisiplinan yang bersih tanpa catatan pelanggaran tata tertib (0 Poin).</p>
          </div>
        @else
          <ul class="violations-list">
            @foreach($recentViolations as $violation)
              <li class="violation-item">
                <div class="violation-info">
                  <strong>{{ $violation->category?->name ?? 'Pelanggaran Tata Tertib' }}</strong>
                  <span class="violation-date">{{ $violation->occurred_at ? $violation->occurred_at->format('d M Y') : $violation->created_at->format('d M Y') }}</span>
                  @if($violation->description)
                    <p class="violation-desc">{{ $violation->description }}</p>
                  @endif
                </div>
                <span class="violation-points">+{{ $violation->points }} Poin</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
      <div class="dialog-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalPelanggaran').close()">Tutup</button>
      </div>
    </div>
  </dialog>

  <!-- ============ FOOTER ============ -->
  <footer class="dashboard-footer">
    <div class="container footer-content">
      <p>&copy; 2026 EDUKARA — SMA Negeri Situraja</p>
      <p class="footer-motto-small">"Kawignyan Askaraning Tyas"</p>
    </div>
  </footer>

</div>
@endsection

@push('scripts')
<script>
  function bukaModalAbsensi() {
    const modal = document.getElementById('modalAbsensi');
    if (modal) modal.showModal();
  }
  function bukaModalRiwayat() {
    const modal = document.getElementById('modalRiwayat');
    if (modal) modal.showModal();
  }
</script>
@endpush
