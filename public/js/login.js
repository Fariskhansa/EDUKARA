/* ============================================================
   Sistem Informasi Kesiswaan — SMA Negeri Situraja
   Login Script — Validation, UI States, and API Service
   ============================================================ */

(function () {
  'use strict';

  // --- Configuration ---
  const API_CONFIG = {
    // Alamat endpoint REST API Laravel (bisa disesuaikan dengan konfigurasi server aktual)
    baseUrl: 'http://localhost:8000/api',
    loginEndpoint: '/login',
    timeoutMs: 8000,
  };

  // --- DOM Elements ---
  const form = document.getElementById('loginForm');
  const usernameInput = document.getElementById('usernameInput');
  const passwordInput = document.getElementById('passwordInput');
  const groupUsername = document.getElementById('groupUsername');
  const groupPassword = document.getElementById('groupPassword');
  const usernameError = document.getElementById('usernameError');
  const passwordError = document.getElementById('passwordError');
  const togglePasswordBtn = document.getElementById('togglePasswordBtn');
  const togglePasswordIcon = document.getElementById('togglePasswordIcon');
  const submitBtn = document.getElementById('loginSubmitBtn');
  const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
  const btnSpinner = submitBtn ? submitBtn.querySelector('.btn-spinner') : null;
  const alertBox = document.getElementById('loginAlert');
  const alertMessage = document.getElementById('loginAlertMessage');

  // --- Password Toggle Feature ---
  if (togglePasswordBtn && passwordInput && togglePasswordIcon) {
    togglePasswordBtn.addEventListener('click', function () {
      const isPassword = passwordInput.getAttribute('type') === 'password';

      if (isPassword) {
        passwordInput.setAttribute('type', 'text');
        togglePasswordIcon.classList.remove('fa-eye');
        togglePasswordIcon.classList.add('fa-eye-slash');
        togglePasswordBtn.setAttribute('aria-label', 'Sembunyikan kata sandi');
        togglePasswordBtn.setAttribute('title', 'Sembunyikan kata sandi');
      } else {
        passwordInput.setAttribute('type', 'password');
        togglePasswordIcon.classList.remove('fa-eye-slash');
        togglePasswordIcon.classList.add('fa-eye');
        togglePasswordBtn.setAttribute('aria-label', 'Tampilkan kata sandi');
        togglePasswordBtn.setAttribute('title', 'Tampilkan kata sandi');
      }
    });
  }

  // --- Alert Helpers ---
  function showAlert(message) {
    if (!alertBox || !alertMessage) return;
    alertMessage.textContent = message;
    alertBox.removeAttribute('hidden');
    alertBox.focus();
  }

  function hideAlert() {
    if (!alertBox || !alertMessage) return;
    alertBox.setAttribute('hidden', '');
    alertMessage.textContent = '';
  }

  // --- Validation Helpers ---
  function setFieldError(group, errorElement, input, message) {
    if (group) group.classList.add('has-error');
    if (errorElement) errorElement.textContent = message;
    if (input) input.setAttribute('aria-invalid', 'true');
  }

  function clearFieldError(group, errorElement, input) {
    if (group) group.classList.remove('has-error');
    if (errorElement) errorElement.textContent = '';
    if (input) input.removeAttribute('aria-invalid');
  }

  // Clear errors on typing
  if (usernameInput) {
    usernameInput.addEventListener('input', function () {
      clearFieldError(groupUsername, usernameError, usernameInput);
      hideAlert();
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', function () {
      clearFieldError(groupPassword, passwordError, passwordInput);
      hideAlert();
    });
  }

  // Validate inputs before submission
  function validateForm() {
    let isValid = true;
    const username = usernameInput ? usernameInput.value.trim() : '';
    const password = passwordInput ? passwordInput.value : '';

    // Validate username / identity
    if (!username) {
      setFieldError(
        groupUsername,
        usernameError,
        usernameInput,
        'Identitas pengguna wajib diisi.'
      );
      isValid = false;
    } else if (username.length < 3) {
      setFieldError(
        groupUsername,
        usernameError,
        usernameInput,
        'Identitas pengguna minimal 3 karakter.'
      );
      isValid = false;
    } else {
      clearFieldError(groupUsername, usernameError, usernameInput);
    }

    // Validate password
    if (!password) {
      setFieldError(
        groupPassword,
        passwordError,
        passwordInput,
        'Kata sandi wajib diisi.'
      );
      isValid = false;
    } else if (password.length < 6) {
      setFieldError(
        groupPassword,
        passwordError,
        passwordInput,
        'Kata sandi minimal 6 karakter.'
      );
      isValid = false;
    } else {
      clearFieldError(groupPassword, passwordError, passwordInput);
    }

    return isValid;
  }

  // --- Loading State ---
  function setLoadingState(isLoading) {
    if (!submitBtn) return;

    if (isLoading) {
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-busy', 'true');
      if (btnText) btnText.textContent = 'Memproses...';
      if (btnSpinner) btnSpinner.removeAttribute('hidden');
    } else {
      submitBtn.disabled = false;
      submitBtn.removeAttribute('aria-busy');
      if (btnText) btnText.textContent = 'Masuk';
      if (btnSpinner) btnSpinner.setAttribute('hidden', '');
    }
  }

  // --- Backend API Communication ---
  /**
   * Mengirim permintaan autentikasi ke backend Laravel REST API.
   * Fungsi ini terisolasi dan siap diintegrasikan ketika backend tersedia.
   *
   * @param {Object} credentials - { username, password }
   * @returns {Promise<Object>} Respon dari server
   */
  async function requestLoginApi(credentials) {
    const fullUrl = API_CONFIG.baseUrl + API_CONFIG.loginEndpoint;

    const controller = new AbortController();
    const timeoutId = setTimeout(function () {
      controller.abort();
    }, API_CONFIG.timeoutMs);

    try {
      const response = await fetch(fullUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          identity: credentials.username,
          password: credentials.password,
        }),
        signal: controller.signal,
      });

      clearTimeout(timeoutId);

      const data = await response.json().catch(function () {
        return {};
      });

      return {
        ok: response.ok,
        status: response.status,
        data: data,
      };
    } catch (error) {
      clearTimeout(timeoutId);

      // Handle specific network / abort error
      if (error.name === 'AbortError') {
        throw new Error('Koneksi waktu habis (timeout). Server tidak merespons.');
      }
      throw new Error('NETWORK_UNAVAILABLE');
    }
  }

  // --- Form Submission Handler ---
  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      hideAlert();

      // Client-side validation check
      if (!validateForm()) {
        const firstErrorInput = form.querySelector('[aria-invalid="true"]');
        if (firstErrorInput) firstErrorInput.focus();
        return;
      }

      const credentials = {
        username: usernameInput.value.trim(),
        password: passwordInput.value,
      };

      setLoadingState(true);

      try {
        const result = await requestLoginApi(credentials);

        if (result.ok) {
          // Jika backend merespons sukses (HTTP 200)
          // Catatan: Token dan redirect hanya diproses jika diverifikasi oleh backend
          showAlert('Autentikasi berhasil. Memuat sistem...');
          if (result.data && result.data.redirect_url) {
            window.location.href = result.data.redirect_url;
          }
        } else {
          // Handle response error dari backend (401, 422, 500)
          if (result.status === 401 || result.status === 403) {
            showAlert('Identitas pengguna atau kata sandi tidak sesuai.');
          } else if (result.status === 422 && result.data && result.data.message) {
            showAlert(result.data.message);
          } else if (result.status >= 500) {
            showAlert('Terjadi gangguan pada server. Silakan hubungi administrator.');
          } else {
            showAlert(
              (result.data && result.data.message) ||
              'Gagal masuk. Silakan periksa kembali data Anda.'
            );
          }
        }
      } catch (err) {
        // Handle ketika backend API belum siap / tidak dapat dihubungi
        if (err.message === 'NETWORK_UNAVAILABLE') {
          showAlert(
            'Tidak dapat terhubung ke server (Laravel REST API belum aktif pada ' +
            API_CONFIG.baseUrl +
            '). Selesaikan persiapan backend untuk mengaktifkan login.'
          );
        } else {
          showAlert(err.message);
        }
      } finally {
        setLoadingState(false);
      }
    });
  }

})();
