<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'EDUKARA — Sistem Informasi Kesiswaan SMA Negeri Situraja')</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Core Base Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

  @stack('styles')
</head>
<body class="@yield('body-class', '')">

  @yield('content')

  <!-- Core Script -->
  <script src="{{ asset('js/main.js') }}"></script>

  @stack('scripts')
</body>
</html>
