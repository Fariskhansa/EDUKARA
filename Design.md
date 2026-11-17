# Dokumentasi Spesifikasi Desain (Design System & UI/UX)
## Landing Page SMA Negeri Situraja

---

## 1. Visi & Filosofi Desain

Landing page **SMA Negeri Situraja** dirancang untuk mencerminkan identitas sekolah menengah atas negeri modern di Kabupaten Sumedang, Jawa Barat. Desain memadukan kecanggihan teknologi antarmuka (UI/UX) berbasis *glassmorphism* modern dengan nilai-nilai lokalitas dan budaya yang terkandung dalam motto almamater: **"Kawignyan Askaraning Tyas"** *(Pengetahuan adalah Cahaya Jiwa)*.

### Pilar Utama Desain:
1. **Identitas Otentik (Brand Authenticity):** Mengambil warna langsung dari logo resmi sekolah dan arsitektur ikonik gedung sekolah (pilar kuning emas dan perisai biru).
2. **Keterbacaan & Navigasi Cepat (Usability):** Menggunakan tata letak terstruktur (*grid system*) yang memudahkan siswa, orang tua, dan calon pendaftar PPDB menemukan informasi penting.
3. **Interaktif & Responsif:** Menyediakan komponen interaktif seperti filter ekstrakurikuler, *Virtual Tour 360°*, serta simulasi form PPDB.

---

## 2. Palet Warna (Color Palette & Brand Identity)

Palet warna diekstraksi secara presisi dari dua elemen visual utama: **Logo Resmi SMAN Situraja** dan **Foto Fisik Gedung Utama Sekolah**.

```
+-----------------------------------------------------------------------+
|  Brand Blue       #1e73be  |  Warna Tameng Utama Logo                 |
|  Deep Blue Navy   #092540  |  Warna Background Hero & Header Top      |
|  Gold / Yellow    #f59e0b  |  Warna Padi Logo & Pilar Gedung Utama    |
|  Torch Red        #d32f2f  |  Warna Obor, Aksen & Bendera Merah-Putih|
|  Fresh Green      #16a34a  |  Warna Tangkai Kapas & Keasrian Kampus   |
|  Light Slate BG   #f8fafc  |  Warna Latar Belakang Konten Utama       |
+-----------------------------------------------------------------------+
```

### Rincian Penggunaan Warna:
* **Primary / Brand Blue (`#1e73be` & `#0f4c81`):** Digunakan untuk tombol *Call to Action* (CTA), header section, ikon aktif, dan garis tepi fokus.
* **Secondary Gold (`#f59e0b`):** Menandakan highlight prestasi, pilar arsitektur, badge *Info PPDB*, dan elemen pemenang/trofi.
* **Accent Red (`#d32f2f`):** Menarik perhatian pada informasi mendesak (*deadlines* PPDB, tag kategori, dan pilar kebangsaan).
* **Botanical Green (`#16a34a`):** Mewakili kategori keasrian lingkungan, olahraga, dan keseimbangan sosial.
* **Neutral Dark (`#0f172a`):** Warna teks utama (*heading*) untuk kontras tinggi (*WCAG AAA compliant*).

---

## 3. Tipografi (Typography System)

Menggunakan font **Plus Jakarta Sans** dari Google Fonts yang bersih, kontemporer, dan memiliki pembacaan sangat baik di berbagai ukuran layar.

| Peran | Font Weight | Size Range (Mobile - Desktop) | Penggunaan |
| :--- | :--- | :--- | :--- |
| **Hero Title (H1)** | 800 (ExtraBold) | `32px` - `60px` | Judul utama pada Hero Section |
| **Section Title (H2)** | 800 (ExtraBold) | `24px` - `36px` | Judul utama tiap seksi |
| **Card Title (H3/H4)** | 700 (Bold) | `16px` - `20px` | Judul kartu & fitur |
| **Body Text** | 400 (Regular) / 500 (Medium) | `12px` - `15px` | Deskripsi teks & narasi |
| **Badges & Microtext**| 700 (Bold) / 800 (ExtraBold) | `10px` - `12px` | Tag, label status, motto |

---

## 4. Arsitektur Komponen & Struktur Halaman

Halaman disusun secara sekuensial berdasarkan *user journey* calon siswa dan wali murid:

```
┌───────────────────────────────────────────────────────────┐
| 1. Top Announcement Bar (Info PPDB 2026/2027)            |
├───────────────────────────────────────────────────────────┤
| 2. Header & Sticky Glassmorphism Navbar                   |
├───────────────────────────────────────────────────────────┤
| 3. Hero Section (Gradient Overlay, CTA, Vector Building)  |
├───────────────────────────────────────────────────────────┤
| 4. Section Profil & Break-down Filosofi Lambang Logo     |
├───────────────────────────────────────────────────────────┤
| 5. Section Sambutan Kepala Sekolah                        |
├───────────────────────────────────────────────────────────┤
| 6. Section Keunggulan & Fasilitas Pendidikan             |
├───────────────────────────────────────────────────────────┤
| 7. Section Ekstrakurikuler (Interactive Filter Tabs)      |
├───────────────────────────────────────────────────────────┤
| 8. Section Galeri Kampus & Modals (Virtual Tour)          |
├───────────────────────────────────────────────────────────┤
| 9. Section PPDB Online & Form Konsultasi                  |
├───────────────────────────────────────────────────────────┤
| 10. Section Kontak, Peta Lokasi & Footer                  |
└───────────────────────────────────────────────────────────┘
```

### Detail Komponen Spesifik:

#### A. Hero Section & Ilustrasi Gedung
* **Visual Card:** Menyajikan ilustrasi vektor presisi yang merepresentasikan gambar asli gedung SMAN Situraja (pilar kuning emas, hiasan rumbai merah-putih, lemari piala di foyer, serta papan nama sekolah).
* **CTA Dual Button:** Pilihan utama untuk *"Daftar PPDB 2026"* (tombol emas bercahaya) dan *"Jelajahi Sekolah"* (tombol transparan *glassmorphism*).

#### B. Anatomi Filosofi Logo
* Memecah logo SMA Negeri Situraja menjadi 4 kartu analisis komponen:
  1. *Perisai Biru* (Kedamaian & Perlindungan)
  2. *Obor & Buku* (Penerang Ilmu Pengetahuan)
  3. *Padi & Kapas* (Kemakmuran & Keadilan)
  4. *Pita Motto* (*Kawignyan Askaraning Tyas*)

#### C. Grid Filter Ekstrakurikuler
* Memanfaatkan sistem filter berbasis JavaScript tanpa reload halaman.
* Kategori: *Semua*, *Akademik & Sains*, *Olahraga*, dan *Seni & Budaya*.

#### D. Virtual Tour & Image Preview Modals
* *Modal Overlay* berbasis backdrop blur (`backdrop-blur-md`) untuk menampilkan simulasi tur 360° dan galeri foto resolusi tinggi gedung sekolah.

---

## 5. Responsivitas & UX Micro-Interactions

### Adaptivitas Layar (Responsive Breakpoints):
* **Mobile (`< 640px`):** Hamburger menu drawer, susunan kartu 1 kolom, penyesuaian ukuran font hero.
* **Tablet (`640px - 1024px`):** Layout 2 kolom untuk ekstrakurikuler dan fasilitas.
* **Desktop (`> 1024px`):** Layout penuh 12-kolom grid, sticky header transparan, efek hover *card scaling*.

### Micro-Interactions & Animation:
* **Hover Scale Effects:** `hover:scale-105` pada kartu-kartu utama untuk memberikan respon umpan balik taktil.
* **Glow & Pulse Badges:** Animasi `badge-pulse` pada info penting PPDB untuk menarik perhatian tanpa mengganggu pembacaan.
* **Smooth Scrolling:** Transisi antar seksi berjalan halus saat menekan tautan navigasi.

---

## 6. Standar Aksesibilitas (Accessibility & Performance)

1. **Rasio Kontras Teks:** Kombinasi warna teks putih di atas latar navy (`#092540`) dan teks slate gelap (`#1e293b`) di atas latar putih memenuhi standar kriteria WCAG 2.1 AA.
2. **Icons & Fallback Text:** Seluruh ikon FontAwesome dilengkapi dengan elemen konteks teks visual pendukung.
3. **Optimasi Asset:** Logo disajikan menggunakan Format **SVG Vector** agar tajam di semua resolusi tanpa membebankan performa *load time*.