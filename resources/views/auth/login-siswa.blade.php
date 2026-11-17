@extends('layouts.app')

@section('title', 'Masuk Siswa — EDUKARA SMAN Situraja')
@section('body-class', 'login-body')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login-siswa.css') }}">
@endpush

@section('content')
<main class="login-wrapper">

  <!-- Tombol Kembali ke Beranda -->
  <a href="{{ route('home') }}" class="login-back-link" title="Kembali ke Beranda">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
    <span>Kembali ke Beranda</span>
  </a>

  <!-- Kartu Login Siswa -->
  <div class="login-card">

    <!-- Header Identitas -->
    <div class="login-header">
      <a href="{{ route('home') }}" class="login-logo-link" title="Beranda EDUKARA SMA Negeri Situraja">
        <img src="{{ asset('assets/images/logo-sma-situraja.png') }}"
             alt="Logo SMA Negeri Situraja"
             class="login-logo"
             width="64"
             height="64">
      </a>
      <h1 class="login-title">EDUKARA</h1>
      <p class="login-subtitle">Portal Siswa — SI Kesiswaan SMAN Situraja</p>
    </div>

    <!-- Alert Status Berhasil / Notifikasi Umum -->
    @if(session('status'))
      <div class="alert-status-success" role="status">
        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    <!-- Alert Error Validasi Server -->
    @if($errors->any())
      <div class="alert-status-danger" role="alert" id="serverAlert">
        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        <div class="alert-content">
          @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Form Login Khusus NIS -->
    <form action="{{ route('student.login.post') }}" method="POST" class="login-form" id="studentLoginForm" novalidate>
      @csrf

      <div class="form-group @error('nis') has-error @enderror" id="groupNis">
        <label for="nisInput" class="form-label">
          Nomor Induk Siswa (NIS)
          <span class="required-mark" aria-hidden="true">*</span>
        </label>
        <div class="input-container">
          <span class="input-icon" aria-hidden="true">
            <i class="fa-solid fa-id-card"></i>
          </span>
          <input
            type="text"
            id="nisInput"
            name="nis"
            class="form-input"
            value="{{ old('nis') }}"
            placeholder="Contoh: 9926001"
            autocomplete="username"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="20"
            required
            autofocus
            @error('nis') aria-invalid="true" aria-describedby="nisError" @enderror
          >
        </div>
        <span class="form-hint" id="nisHint">Masukkan 7 digit NIS yang terdaftar pada kartu pelajar atau buku rapor.</span>
        @error('nis')
          <span class="error-message" id="nisError" role="alert">{{ $message }}</span>
        @enderror
      </div>

      <!-- Tombol Masuk -->
      <button type="submit" class="btn-login-submit" id="btnSubmit">
        <span class="btn-text">Masuk</span>
        <span class="btn-spinner" hidden aria-hidden="true">
          <i class="fa-solid fa-spinner fa-spin"></i>
        </span>
      </button>
    </form>

    <!-- Informasi Catatan Keamanan Siswa -->
    <div class="login-info-box">
      <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
      <p>Login ini diperuntukkan khusus bagi siswa terdaftar aktif SMAN Situraja. Jika Anda mengalami kendala NIS tidak ditemukan, silakan hubungi wali kelas atau bagian Tata Usaha sekolah.</p>
    </div>

    <!-- Footer Kartu -->
    <div class="login-footer">
      <p class="motto-text">"Kawignyan Askaraning Tyas"</p>
      <p class="copyright-text">&copy; 2026 EDUKARA — SMA Negeri Situraja</p>
    </div>

  </div>
</main>
@endsection

@push('scripts')
  <script src="{{ asset('js/login-siswa.js') }}"></script>
@endpush
