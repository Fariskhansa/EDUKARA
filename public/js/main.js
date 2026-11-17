/* ============================================================
   Sistem Informasi Kesiswaan — SMA Negeri Situraja
   Main JavaScript — Landing Page Interactions
   ============================================================ */

(function () {
  'use strict';

  // --- DOM Elements ---
  const navbar = document.getElementById('navbar');
  const navbarToggle = document.getElementById('navbarToggle');
  const navbarNav = document.getElementById('navbarNav');
  const navLinks = document.querySelectorAll('.nav-link');

  // --- Mobile Nav Overlay ---
  let navOverlay = null;

  function createOverlay() {
    if (navOverlay) return;
    navOverlay = document.createElement('div');
    navOverlay.className = 'nav-overlay';
    navOverlay.setAttribute('aria-hidden', 'true');
    document.body.appendChild(navOverlay);

    navOverlay.addEventListener('click', closeNav);
  }

  // --- Mobile Navigation Toggle ---
  function openNav() {
    navbarNav.classList.add('open');
    navbarToggle.setAttribute('aria-expanded', 'true');
    navbarToggle.setAttribute('aria-label', 'Tutup menu navigasi');
    createOverlay();
    navOverlay.classList.add('visible');
    document.body.style.overflow = 'hidden';
  }

  function closeNav() {
    navbarNav.classList.remove('open');
    navbarToggle.setAttribute('aria-expanded', 'false');
    navbarToggle.setAttribute('aria-label', 'Buka menu navigasi');
    if (navOverlay) {
      navOverlay.classList.remove('visible');
    }
    document.body.style.overflow = '';
  }

  function toggleNav() {
    var isOpen = navbarToggle.getAttribute('aria-expanded') === 'true';
    if (isOpen) {
      closeNav();
    } else {
      openNav();
    }
  }

  if (navbarToggle) {
    navbarToggle.addEventListener('click', toggleNav);
  }

  // Close nav on link click (mobile)
  navLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      if (navbarNav.classList.contains('open')) {
        closeNav();
      }
    });
  });

  // Close nav on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && navbarNav.classList.contains('open')) {
      closeNav();
      navbarToggle.focus();
    }
  });

  // --- Navbar Scroll Effect ---
  function handleScroll() {
    if (!navbar) return;
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll(); // initial check

  // --- Active Nav Link on Scroll ---
  var sections = document.querySelectorAll('section[id]');

  function updateActiveNav() {
    var scrollPos = window.scrollY + 120;

    sections.forEach(function (section) {
      var top = section.offsetTop;
      var height = section.offsetHeight;
      var id = section.getAttribute('id');

      if (scrollPos >= top && scrollPos < top + height) {
        navLinks.forEach(function (link) {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + id) {
            link.classList.add('active');
          }
        });
      }
    });
  }

  window.addEventListener('scroll', updateActiveNav, { passive: true });
  updateActiveNav();

})();
