@extends('layouts.app')

@section('title', 'EDUKARA — Sistem Informasi Kesiswaan SMA Negeri Situraja')

@section('content')
  <!-- ============ NAVBAR ============ -->
  <header class="navbar" id="navbar">
    <div class="navbar-inner">
      <a href="{{ route('home') }}" class="navbar-brand">
        <img src="{{ asset('assets/images/logo-sma-situraja.png') }}" alt="Logo SMA Negeri Situraja" class="navbar-logo" width="40" height="40">
        <div class="navbar-brand-text">
          <span class="navbar-title">EDUKARA</span>
          <span class="navbar-subtitle">SI Kesiswaan SMAN Situraja</span>
        </div>
      </a>

      <nav class="navbar-nav" id="navbarNav" aria-label="Navigasi utama">
        <ul class="navbar-menu">
          <li><a href="#beranda" class="nav-link active">Beranda</a></li>
          <li><a href="#tentang" class="nav-link">Tentang</a></li>
          <li><a href="#fitur" class="nav-link">Fitur</a></li>
          <li><a href="#kontak" class="nav-link">Kontak</a></li>
        </ul>
      </nav>

      @auth
        @if(Auth::user()->student)
          <a href="{{ route('student.dashboard') }}" class="btn btn-login-nav">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
          </a>
        @endif
      @else
        <a href="{{ route('student.login') }}" class="btn btn-login-nav">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
        </a>
      @endauth

      <button class="navbar-toggle" id="navbarToggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="navbarNav">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <!-- ============ HERO SECTION ============ -->
  <section class="hero" id="beranda">
    <div class="hero-bg" role="img" aria-label="Foto Gedung SMA Negeri Situraja"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <img src="{{ asset('assets/images/logo-sma-situraja.png') }}" alt="Logo SMAN Situraja" class="hero-logo" width="96" height="96">
      <h1 class="hero-title">EDUKARA <span class="hero-title-break">SMA Situraja</span></h1>
      <p class="hero-desc">Sistem Informasi Kesiswaan SMA Negeri Situraja. Kelola data absensi digital, catatan kedisiplinan, laporan, dan pemantauan siswa dalam satu platform terpadu.</p>
      <div class="hero-actions">
        @auth
          <a href="{{ route('student.dashboard') }}" class="btn btn-primary">Buka Dashboard Siswa</a>
        @else
          <a href="{{ route('student.login') }}" class="btn btn-primary">Login</a>
        @endauth
        <a href="#tentang" class="btn btn-outline">Pelajari Lebih Lanjut</a>
      </div>
    </div>
  </section>

  <!-- ============ TENTANG SECTION ============ -->
  <section class="section section-about" id="tentang">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Tentang Sistem</h2>
        <p class="section-lead">Platform digital yang mempermudah pengelolaan data kesiswaan secara efisien, transparan, dan dapat diakses oleh seluruh pihak yang berkepentingan.</p>
      </div>

      <div class="about-grid">
        <div class="about-card">
          <div class="about-icon">
            <i class="fa-solid fa-school" aria-hidden="true"></i>
          </div>
          <h3>Untuk Sekolah</h3>
          <p>Pencatatan dan pengelolaan data kesiswaan yang tersentralisasi, mengurangi administrasi manual dan risiko kehilangan data.</p>
        </div>
        <div class="about-card">
          <div class="about-icon">
            <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
          </div>
          <h3>Untuk Guru</h3>
          <p>Kemudahan dalam mencatat kehadiran, mendokumentasikan pelanggaran, dan memantau perkembangan siswa secara real-time.</p>
        </div>
        <div class="about-card">
          <div class="about-icon">
            <i class="fa-solid fa-users" aria-hidden="true"></i>
          </div>
          <h3>Untuk Orang Tua</h3>
          <p>Akses langsung untuk memantau kehadiran dan catatan perilaku anak, meningkatkan keterlibatan dalam proses pendidikan.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FITUR SECTION ============ -->
  <section class="section section-features" id="fitur">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Fitur Utama</h2>
        <p class="section-lead">Empat pilar layanan yang dirancang untuk menunjang pengelolaan kesiswaan secara menyeluruh.</p>
      </div>

      <div class="features-grid">
        <article class="feature-card">
          <div class="feature-icon feature-icon--blue">
            <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
          </div>
          <h3 class="feature-title">Absensi Digital</h3>
          <p class="feature-desc">Pencatatan kehadiran siswa secara digital dengan rekapitulasi otomatis per kelas, per semester, dan per tahun ajaran.</p>
        </article>

        <article class="feature-card">
          <div class="feature-icon feature-icon--red">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
          </div>
          <h3 class="feature-title">Catatan Pelanggaran</h3>
          <p class="feature-desc">Dokumentasi pelanggaran tata tertib beserta sistem poin yang terstruktur dan dapat ditelusuri riwayatnya.</p>
        </article>

        <article class="feature-card">
          <div class="feature-icon feature-icon--green">
            <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
          </div>
          <h3 class="feature-title">Laporan &amp; Statistik</h3>
          <p class="feature-desc">Laporan komprehensif mengenai kehadiran, pelanggaran, dan data kesiswaan yang dapat diekspor untuk kebutuhan evaluasi.</p>
        </article>

        <article class="feature-card">
          <div class="feature-icon feature-icon--gold">
            <i class="fa-solid fa-eye" aria-hidden="true"></i>
          </div>
          <h3 class="feature-title">Pemantauan Orang Tua</h3>
          <p class="feature-desc">Portal khusus bagi orang tua untuk memantau kehadiran dan catatan perilaku anak secara mandiri dan transparan.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ FOOTER ============ -->
  <footer class="footer" id="kontak">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <img src="{{ asset('assets/images/logo-sma-situraja.png') }}" alt="" class="footer-logo" width="48" height="48" aria-hidden="true">
          <div>
            <p class="footer-school-name">SMA Negeri Situraja</p>
            <p class="footer-motto">Kawignyan Askaraning Tyas</p>
          </div>
        </div>

        <div class="footer-contact">
          <h4>Kontak</h4>
          <address>
            <p><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Jl. Raya Situraja, Kec. Situraja, Kab. Sumedang, Jawa Barat</p>
            <p><i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:+62XXXXXXXXXX">(0261) XXX-XXXX</a></p>
            <p><i class="fa-solid fa-envelope" aria-hidden="true"></i> <a href="mailto:info@smansituraja.sch.id">info@smansituraja.sch.id</a></p>
          </address>
        </div>

        <div class="footer-links">
          <h4>Tautan</h4>
          <ul>
            <li><a href="#beranda">Beranda</a></li>
            <li><a href="#tentang">Tentang Sistem</a></li>
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="{{ route('student.login') }}">Login</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2026 EDUKARA — SMA Negeri Situraja. Sistem Informasi Kesiswaan.</p>
      </div>
    </div>
  </footer>
@endsection
