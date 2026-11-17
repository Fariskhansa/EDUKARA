/* ============================================================
   Sistem Informasi Kesiswaan — SMA Negeri Situraja
   Login Siswa — Vanilla JavaScript UX Enhancer
   ============================================================ */

(function () {
  'use strict';

  const form = document.getElementById('studentLoginForm');
  const nisInput = document.getElementById('nisInput');
  const groupNis = document.getElementById('groupNis');
  const submitBtn = document.getElementById('btnSubmit');
  const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
  const btnSpinner = submitBtn ? submitBtn.querySelector('.btn-spinner') : null;

  if (!form || !nisInput || !submitBtn) return;

  // 1. Filter input agar hanya menerima karakter angka (0-9)
  nisInput.addEventListener('input', function () {
    const rawValue = this.value;
    const numericOnly = rawValue.replace(/\D/g, '');

    if (rawValue !== numericOnly) {
      this.value = numericOnly;
    }

    // Bersihkan state error visual saat siswa mengetik ulang
    if (groupNis && groupNis.classList.contains('has-error')) {
      groupNis.classList.remove('has-error');
      const errSpan = groupNis.querySelector('.error-message');
      if (errSpan) errSpan.remove();
      this.removeAttribute('aria-invalid');
    }
  });

  // 2. Loading state saat form dikirimkan ke server Laravel
  form.addEventListener('submit', function (e) {
    const value = nisInput.value.trim();

    // Validasi awal client-side untuk mencegah request kosong
    if (!value) {
      e.preventDefault();
      nisInput.focus();
      if (groupNis) {
        groupNis.classList.add('has-error');
        let errSpan = groupNis.querySelector('.error-message');
        if (!errSpan) {
          errSpan = document.createElement('span');
          errSpan.className = 'error-message';
          errSpan.id = 'nisError';
          errSpan.setAttribute('role', 'alert');
          groupNis.appendChild(errSpan);
        }
        errSpan.textContent = 'Nomor Induk Siswa (NIS) wajib diisi.';
        nisInput.setAttribute('aria-invalid', 'true');
      }
      return;
    }

    // Ubah status tombol menjadi loading
    submitBtn.disabled = true;
    submitBtn.setAttribute('aria-busy', 'true');
    if (btnText) btnText.textContent = 'Memverifikasi...';
    if (btnSpinner) btnSpinner.removeAttribute('hidden');
  });
})();
