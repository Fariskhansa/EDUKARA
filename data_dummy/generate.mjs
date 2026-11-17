/**
 * Generator DATA DUMMY — SI Absensi Siswa SMA Situraja
 * ----------------------------------------------------
 * SELURUH DATA DI SINI FIKTIF. Nama, NIS, NISN, NIP, nomor telepon, email,
 * koordinat sekolah, jam sesi, dan bobot poin hanyalah contoh untuk pengembangan
 * dan pengujian. JANGAN dipakai di produksi.
 *
 * Penanda data dummy:
 *   - NIS & NISN diawali "99"
 *   - Nomor telepon diawali "0800" (bukan prefiks seluler)
 *   - Email memakai domain "contoh.test" (TLD .test dicadangkan, tidak dapat menerima email)
 *
 * Jalankan:  node data_dummy/generate.mjs
 * Hasil:     data_dummy/json/*.json, data_dummy/csv/*.csv, data_dummy/ringkasan_akun.md
 * Hasil selalu sama setiap dijalankan (PRNG dengan seed tetap).
 */
import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = dirname(fileURLToPath(import.meta.url));
const OUT_JSON = join(ROOT, 'json');
const OUT_CSV = join(ROOT, 'csv');
mkdirSync(OUT_JSON, { recursive: true });
mkdirSync(OUT_CSV, { recursive: true });

// ---------------------------------------------------------------------------
// Utilitas
// ---------------------------------------------------------------------------
function mulberry32(seed) {
  return function () {
    let t = (seed += 0x6d2b79f5);
    t = Math.imul(t ^ (t >>> 15), t | 1);
    t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
}
const rand = mulberry32(20261014);
const randInt = (min, max) => Math.floor(rand() * (max - min + 1)) + min;
const pick = (arr) => arr[Math.floor(rand() * arr.length)];
const chance = (p) => rand() < p;
const hex = (len) => Array.from({ length: len }, () => Math.floor(rand() * 16).toString(16)).join('');
const uuid = () => {
  const h = hex(32);
  const variant = (8 + Math.floor(rand() * 4)).toString(16);
  return `${h.slice(0, 8)}-${h.slice(8, 12)}-4${h.slice(13, 16)}-${variant}${h.slice(17, 20)}-${h.slice(20, 32)}`;
};
const pad = (n, l = 2) => String(n).padStart(l, '0');

// Semua waktu adalah "jam dinding" WIB (Asia/Jakarta). Objek Date dipakai dengan
// metode UTC agar hasil tidak bergantung zona waktu komputer yang menjalankan skrip.
const wib = (dateStr, time = '00:00:00') => new Date(`${dateStr}T${time}Z`);
const fmt = (d) =>
  `${d.getUTCFullYear()}-${pad(d.getUTCMonth() + 1)}-${pad(d.getUTCDate())} ` +
  `${pad(d.getUTCHours())}:${pad(d.getUTCMinutes())}:${pad(d.getUTCSeconds())}`;
const fmtDate = (d) => fmt(d).slice(0, 10);
const addSec = (d, s) => new Date(d.getTime() + s * 1000);

const ids = {};
const nextId = (table) => (ids[table] = (ids[table] || 0) + 1);

// ---------------------------------------------------------------------------
// Konstanta dataset (DUMMY)
// ---------------------------------------------------------------------------
const TODAY = '2026-10-14';
const NOW = wib(TODAY, '10:00:00'); // "waktu sekarang" dataset
const SEED_CREATED = '2026-07-01 08:00:00';
const DUMMY_PASSWORD = 'Dummy#2026'; // hanya untuk lingkungan pengembangan
const SCHOOL_CENTER = { lat: -6.864, lng: 108.0325 }; // DUMMY, bukan koordinat resmi sekolah
const GEOFENCE_RADIUS_M = 150; // DUMMY
const PERIOD = { opens: '06:00:00', lateAfter: '07:00:00', closes: '12:00:00' }; // DUMMY
const HOLIDAYS = [{ date: '2026-10-09', description: 'Libur kegiatan sekolah (DUMMY)' }];
const SESSION_RANGE = { from: '2026-09-21', to: '2026-10-15' };

const USER_AGENTS = [
  'Mozilla/5.0 (Linux; Android 13; SM-A145F) AppleWebKit/537.36 Chrome/128.0 Mobile Safari/537.36',
  'Mozilla/5.0 (Linux; Android 12; Redmi Note 11) AppleWebKit/537.36 Chrome/127.0 Mobile Safari/537.36',
  'Mozilla/5.0 (Linux; Android 14; CPH2591) AppleWebKit/537.36 Chrome/129.0 Mobile Safari/537.36',
  'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 Version/17.5 Mobile/15E148 Safari/604.1',
];
const randomIp = () => `10.10.${randInt(1, 20)}.${randInt(2, 250)}`;

const M_FIRST = ['Rizki', 'Fajar', 'Dimas', 'Raka', 'Ilham', 'Gilang', 'Rendi', 'Bayu', 'Asep', 'Dede', 'Yoga', 'Agung', 'Rafi', 'Hafiz', 'Iqbal', 'Fikri', 'Ridwan', 'Cecep', 'Ujang', 'Aditya'];
const F_FIRST = ['Siti', 'Nurul', 'Aulia', 'Putri', 'Rina', 'Dewi', 'Intan', 'Salsa', 'Neng', 'Euis', 'Anisa', 'Rahma', 'Fitri', 'Lestari', 'Nabila', 'Zahra', 'Citra', 'Ayu', 'Wulan', 'Tiara'];
const LAST = ['Pratama', 'Ramadhan', 'Hidayat', 'Nugraha', 'Permana', 'Saputra', 'Kurniawan', 'Firmansyah', 'Maulana', 'Gunawan', 'Setiawan', 'Rahayu', 'Suryani', 'Kartika', 'Purnama', 'Sukmawati', 'Hermawan', 'Wijaya', 'Fauzi', 'Hamdani'];
const ADULT_F = ['Euis', 'Yayah', 'Tati', 'Imas', 'Neneng', 'Ani', 'Lilis', 'Iis', 'Enok', 'Popon', 'Rohayati', 'Nining', 'Elis', 'Cucu', 'Yeni'];
const ADULT_M = ['Dadang', 'Ujang', 'Asep', 'Endang', 'Wawan', 'Iwan', 'Dedi', 'Yayan', 'Agus', 'Cecep', 'Tatang', 'Aep', 'Hendra', 'Rudi', 'Dodi'];

// ---------------------------------------------------------------------------
// Roles & Permissions (default usulan Tahap 1 §2.3, nilai ⚙️ diisi versi dummy)
// ---------------------------------------------------------------------------
const PERMISSIONS = [
  ['attendance.submit', 'absensi', 'Mengajukan absensi'],
  ['attendance.view_own', 'absensi', 'Melihat riwayat absensi sendiri'],
  ['attendance.view_all', 'validasi', 'Melihat seluruh pengajuan'],
  ['attendance.photo.view', 'validasi', 'Melihat foto bukti'],
  ['attendance.validate', 'validasi', 'Menerima/menolak pengajuan'],
  ['attendance.revise_decision', 'validasi', 'Merevisi keputusan validasi'],
  ['report.view', 'laporan', 'Melihat rekap & laporan'],
  ['report.export', 'laporan', 'Ekspor laporan Excel'],
  ['dashboard.school', 'laporan', 'Dashboard sekolah'],
  ['teacher_data.view', 'laporan', 'Dashboard data guru'],
  ['child.view', 'orang_tua', 'Melihat data anak terhubung'],
  ['violation.view_own', 'pelanggaran', 'Melihat poin pelanggaran sendiri'],
  ['violation.view_child', 'pelanggaran', 'Melihat poin pelanggaran anak'],
  ['violation.create', 'pelanggaran', 'Mencatat pelanggaran'],
  ['eagenda.access', 'e_agenda', 'Akses E-Agenda'],
  ['master.academic', 'master', 'Kelola tahun ajaran, kelas, penempatan'],
  ['master.students', 'master', 'Kelola data siswa'],
  ['master.teachers', 'master', 'Kelola data guru'],
  ['master.guardians', 'master', 'Kelola data orang tua'],
  ['guardian.verify', 'master', 'Verifikasi relasi orang tua–siswa'],
  ['user.manage', 'akses', 'Kelola akun pengguna'],
  ['role.manage', 'akses', 'Kelola role & permission'],
  ['qr.manage', 'qr', 'Kelola QR kelas'],
  ['session.manage', 'absensi', 'Kelola periode, sesi, kalender'],
  ['settings.manage', 'pengaturan', 'Kelola pengaturan sekolah'],
  ['audit.view', 'audit', 'Melihat log audit'],
];
const ROLES = [
  ['siswa', 'Siswa', ['attendance.submit', 'attendance.view_own', 'violation.view_own']],
  // PJ selalu juga memegang role "siswa"; hak E-Agenda menunggu keputusan lingkup (Q-12).
  ['pengurus_kelas', 'Pengurus Kelas (PJ)', []],
  ['guru', 'Guru', ['attendance.view_all', 'attendance.photo.view', 'attendance.validate', 'report.view', 'violation.create']],
  ['wakasek_kurikulum', 'Wakasek Kurikulum', ['attendance.view_all', 'attendance.photo.view', 'attendance.validate', 'attendance.revise_decision', 'report.view', 'report.export', 'dashboard.school', 'teacher_data.view', 'audit.view']],
  // Dummy: Kepala Sekolah TIDAK diberi hak validasi & foto -> untuk menguji pemeriksaan izin.
  ['kepala_sekolah', 'Kepala Sekolah', ['attendance.view_all', 'report.view', 'report.export', 'dashboard.school', 'teacher_data.view', 'audit.view']],
  ['orang_tua', 'Orang Tua', ['child.view', 'violation.view_child']],
  ['admin_sistem', 'Administrator Sistem', ['master.academic', 'master.students', 'master.teachers', 'master.guardians', 'guardian.verify', 'user.manage', 'role.manage', 'qr.manage', 'session.manage', 'settings.manage', 'audit.view', 'report.view']],
];

const permissions = PERMISSIONS.map(([name, module, display_name], i) => ({ id: i + 1, name, module, display_name }));
const permId = Object.fromEntries(permissions.map((p) => [p.name, p.id]));
const roles = ROLES.map(([name, display_name], i) => ({ id: i + 1, name, display_name, is_system: true }));
const roleId = Object.fromEntries(roles.map((r) => [r.name, r.id]));
const permission_role = ROLES.flatMap(([name, , perms]) => perms.map((p) => ({ permission_id: permId[p], role_id: roleId[name] })));

// ---------------------------------------------------------------------------
// Users
// ---------------------------------------------------------------------------
const users = [];
const role_user = [];
function addUser({ name, username = null, email = null, password = DUMMY_PASSWORD, roleNames = [], isActive = true, lastLogin = null, assignedBy = 1 }) {
  const id = nextId('users');
  users.push({
    id, name, username, email,
    password_dev: password, // seeder WAJIB meng-hash; null = tanpa kata sandi (siswa, auth.student_method = nis)
    must_change_password: false,
    is_active: isActive,
    last_login_at: lastLogin,
    created_at: SEED_CREATED, updated_at: SEED_CREATED, deleted_at: null,
  });
  for (const r of roleNames) role_user.push({ user_id: id, role_id: roleId[r], assigned_by: id === 1 ? null : assignedBy, assigned_at: SEED_CREATED });
  return id;
}
const ADMIN_ID = addUser({ name: 'Admin Sistem (Dummy)', username: 'admin', email: 'admin@contoh.test', roleNames: ['admin_sistem'], lastLogin: '2026-10-13 15:12:00' });

// ---------------------------------------------------------------------------
// Teachers
// ---------------------------------------------------------------------------
const TEACHERS = [
  { name: 'Dra. Hj. Euis Kurniasih, M.Pd.', username: 'kepsek', roleNames: ['kepala_sekolah'] },
  { name: 'Drs. Dadang Supriatna, M.Pd.', username: 'wakasek', roleNames: ['guru', 'wakasek_kurikulum'] },
  { name: 'Asep Saepul Rohman, S.Pd.', username: 'guru01', roleNames: ['guru'] },
  { name: 'Nenden Nurhayati, S.Pd.', username: 'guru02', roleNames: ['guru'] },
  { name: 'Rudi Hermawan, S.Pd.', username: 'guru03', roleNames: ['guru'] },
  { name: 'Lia Amalia, S.Pd.', username: 'guru04', roleNames: ['guru'] },
  { name: 'Hendra Gunawan, S.Kom.', username: 'guru05', roleNames: ['guru'] },
  { name: 'Siti Maryam, S.Pd.', username: 'guru06', roleNames: ['guru'] },
  { name: 'Yudi Permana, S.Pd.', username: 'guru07', roleNames: ['guru'] },
  { name: 'Rina Marlina, S.Pd., Gr.', username: 'guru08', roleNames: ['guru'], isActive: false }, // akun nonaktif
];
const teachers = [];
const teacherByUsername = {};
TEACHERS.forEach((t, i) => {
  const isActive = t.isActive !== false;
  const userId = addUser({
    name: t.name, username: t.username, email: `${t.username}@contoh.test`, roleNames: t.roleNames, isActive,
    lastLogin: isActive ? `2026-10-${pad(randInt(10, 13))} ${pad(randInt(7, 15))}:${pad(randInt(0, 59))}:00` : null,
  });
  const row = { id: i + 1, user_id: userId, nip: `999${pad(i + 1, 15)}`, full_name: t.name, phone: `0800${pad(randInt(0, 99999999), 8)}`, is_active: isActive };
  teachers.push(row);
  teacherByUsername[t.username] = row;
});
const validatorUserIds = TEACHERS.filter((t) => t.isActive !== false && t.roleNames.includes('guru')).map((t) => teacherByUsername[t.username].user_id);
const WAKASEK_USER_ID = teacherByUsername.wakasek.user_id;

// ---------------------------------------------------------------------------
// Academic years & classes
// ---------------------------------------------------------------------------
const academic_years = [
  { id: 1, name: '2025/2026', starts_on: '2025-07-14', ends_on: '2026-06-30', is_active: false },
  { id: 2, name: '2026/2027', starts_on: '2026-07-13', ends_on: '2027-06-30', is_active: true },
];
const classes = [];
function addClass(ayId, grade, name, homeroomUsername) {
  const row = { id: nextId('classes'), academic_year_id: ayId, grade_level: grade, name, program: null, homeroom_teacher_id: homeroomUsername ? teacherByUsername[homeroomUsername].id : null, is_active: ayId === 2 };
  classes.push(row);
  return row;
}
const prevClass = {
  'X-1': addClass(1, 'X', 'X-1', 'guru01'), 'X-2': addClass(1, 'X', 'X-2', 'guru02'),
  'XI-1': addClass(1, 'XI', 'XI-1', 'guru03'), 'XI-2': addClass(1, 'XI', 'XI-2', 'guru04'),
};
const CUR = [
  { key: 'X-1', grade: 'X', angkatan: 26, homeroom: 'guru01', prev: null },
  { key: 'X-2', grade: 'X', angkatan: 26, homeroom: 'guru02', prev: null },
  { key: 'XI-1', grade: 'XI', angkatan: 25, homeroom: 'guru03', prev: 'X-1' },
  { key: 'XI-2', grade: 'XI', angkatan: 25, homeroom: 'guru04', prev: 'X-2' },
  { key: 'XII-1', grade: 'XII', angkatan: 24, homeroom: 'guru05', prev: 'XI-1' },
  { key: 'XII-2', grade: 'XII', angkatan: 24, homeroom: 'guru06', prev: 'XI-2' },
];
const curClass = {};
for (const c of CUR) curClass[c.key] = addClass(2, c.grade, c.key, c.homeroom);

// ---------------------------------------------------------------------------
// Students, enrollments, class officers
// ---------------------------------------------------------------------------
const students = [];
const class_enrollments = [];
const class_officers = [];
const usedNames = new Set();
const usedNisn = new Set();
const studentMeta = {}; // id -> { classKey, idx, classId, leftAt, userId }
const studentByKey = {}; // "X-1#0" -> student row

function genName(gender) {
  for (;;) {
    const first = pick(gender === 'L' ? M_FIRST : F_FIRST);
    const last = pick(LAST);
    const middle = chance(0.3) ? ' ' + pick(gender === 'L' ? M_FIRST : F_FIRST) : '';
    const name = `${first}${middle} ${last}`;
    if (!usedNames.has(name) && !middle.includes(first)) { usedNames.add(name); return name; }
  }
}
function genNisn() {
  for (;;) { const n = `99${pad(randInt(0, 99999999), 8)}`; if (!usedNisn.has(n)) { usedNisn.add(n); return n; } }
}

const seqByAngkatan = {};
for (const c of CUR) {
  for (let i = 0; i < 10; i++) {
    const gender = chance(0.5) ? 'L' : 'P';
    const name = genName(gender);
    const seq = (seqByAngkatan[c.angkatan] = (seqByAngkatan[c.angkatan] || 0) + 1);
    const nis = `99${c.angkatan}${pad(seq, 3)}`;
    const isMoved = c.key === 'XI-2' && i === 9; // skenario: siswa pindah sekolah
    const roleNames = ['siswa', ...(i < 2 ? ['pengurus_kelas'] : [])];
    const userId = addUser({ name, password: null, roleNames, isActive: !isMoved });
    const id = nextId('students');
    const row = { id, user_id: userId, nis, nisn: genNisn(), full_name: name, gender, status: isMoved ? 'pindah' : 'aktif', created_at: SEED_CREATED, updated_at: isMoved ? '2026-09-30 10:00:00' : SEED_CREATED, deleted_at: null };
    students.push(row);
    studentByKey[`${c.key}#${i}`] = row;
    const leftAt = isMoved ? '2026-09-30' : null;
    studentMeta[id] = { classKey: c.key, idx: i, classId: curClass[c.key].id, leftAt, userId };

    if (c.prev) {
      class_enrollments.push({ id: nextId('class_enrollments'), student_id: id, class_id: prevClass[c.prev].id, academic_year_id: 1, enrolled_at: '2025-07-14', left_at: '2026-06-30' });
    }
    class_enrollments.push({ id: nextId('class_enrollments'), student_id: id, class_id: curClass[c.key].id, academic_year_id: 2, enrolled_at: '2026-07-13', left_at: leftAt });
    if (i < 2) class_officers.push({ id: nextId('class_officers'), class_id: curClass[c.key].id, student_id: id, position: i === 0 ? 'ketua' : 'sekretaris' });
  }
}

// ---------------------------------------------------------------------------
// Guardians & guardian_student
// ---------------------------------------------------------------------------
const guardians = [];
const guardian_student = [];
const familyOfStudent = {}; // studentId -> [guardianRow + relationship]
const SIBLINGS = [ // [kakak, adik] -> adik memakai orang tua yang sama
  ['XII-1#3', 'X-1#3'],
  ['XI-1#5', 'X-2#5'],
  ['XII-2#2', 'XI-2#2'],
];
const youngerSet = new Set(SIBLINGS.map(([, y]) => y));
const NO_GUARDIAN = new Set(['XII-2#9']);
let familyIdx = 0;
let guardianUserSeq = 0;

function addGuardian(fullName, hasAccount) {
  let userId = null;
  let username = null;
  if (hasAccount) {
    guardianUserSeq += 1;
    username = `ortu${pad(guardianUserSeq, 3)}`;
    userId = addUser({ name: fullName, username, email: `${username}@contoh.test`, roleNames: ['orang_tua'] });
  }
  const row = { id: nextId('guardians'), user_id: userId, full_name: fullName, phone: `0800${pad(randInt(0, 99999999), 8)}`, email: username ? `${username}@contoh.test` : null, is_active: true };
  guardians.push(row);
  return row;
}
function link(guardian, studentId, relationship) {
  guardian_student.push({
    id: nextId('guardian_student'), guardian_id: guardian.id, student_id: studentId, relationship,
    is_primary_contact: relationship === 'ibu', verification_status: 'verified', verified_by: ADMIN_ID,
    verified_at: '2026-07-20 09:00:00', revoked_at: null, notes: null,
  });
}
for (const [key, s] of Object.entries(studentByKey)) {
  if (youngerSet.has(key) || NO_GUARDIAN.has(key)) continue;
  const hasAccount = familyIdx % 7 !== 3; // sebagian orang tua belum punya akun
  const familyLast = s.full_name.split(' ').slice(-1)[0];
  const fam = [];
  const ibu = addGuardian(`${pick(ADULT_F)} ${pick(LAST)}`, hasAccount);
  link(ibu, s.id, 'ibu'); fam.push([ibu, 'ibu']);
  if (familyIdx % 2 === 0) {
    const ayah = addGuardian(`${pick(ADULT_M)} ${familyLast}`, hasAccount);
    link(ayah, s.id, 'ayah'); fam.push([ayah, 'ayah']);
  }
  familyOfStudent[s.id] = fam;
  familyIdx += 1;
}
for (const [olderKey, youngerKey] of SIBLINGS) {
  const fam = familyOfStudent[studentByKey[olderKey].id];
  for (const [g, rel] of fam) link(g, studentByKey[youngerKey].id, rel);
  familyOfStudent[studentByKey[youngerKey].id] = fam;
}
// Skenario: relasi menunggu verifikasi
for (const gs of guardian_student.filter((x) => x.student_id === studentByKey['X-2#7'].id)) {
  Object.assign(gs, { verification_status: 'pending', verified_by: null, verified_at: null, notes: 'Menunggu dokumen KK (DUMMY)' });
}
// Skenario: relasi dicabut (ayah pertama di XI-1 indeks >= 7)
const revokedLink = guardian_student.find((gs) => gs.relationship === 'ayah' && ['XI-1#7', 'XI-1#8', 'XI-1#9', 'XII-1#7', 'XII-1#8'].some((k) => studentByKey[k].id === gs.student_id));
if (revokedLink) Object.assign(revokedLink, { verification_status: 'revoked', revoked_at: '2026-09-15 10:30:00', notes: 'Hak asuh berubah, dicabut atas permintaan sekolah (DUMMY)' });

// ---------------------------------------------------------------------------
// QR codes
// ---------------------------------------------------------------------------
const class_qr_codes = [];
const qrOld = { id: nextId('class_qr_codes'), class_id: curClass['X-1'].id, token: hex(40), is_active: false, generated_by: ADMIN_ID, generated_at: '2026-07-10 09:00:00', revoked_at: '2026-09-30 15:00:00', revoked_by: ADMIN_ID, revoke_reason: 'Stiker QR rusak terkena air (DUMMY)' };
class_qr_codes.push(qrOld);
const activeQr = {};
for (const c of CUR) {
  const isX1 = c.key === 'X-1';
  const row = { id: nextId('class_qr_codes'), class_id: curClass[c.key].id, token: hex(40), is_active: true, generated_by: ADMIN_ID, generated_at: isX1 ? '2026-09-30 15:00:00' : '2026-07-10 09:00:00', revoked_at: null, revoked_by: null, revoke_reason: null };
  class_qr_codes.push(row);
  activeQr[c.key] = row;
}
const qrFor = (classKey, dateStr) => (classKey === 'X-1' && dateStr <= '2026-09-30' ? qrOld : activeQr[classKey]);

// ---------------------------------------------------------------------------
// Periods, calendar, sessions
// ---------------------------------------------------------------------------
const attendance_periods = [{ id: 1, name: 'Masuk', opens_at: PERIOD.opens, closes_at: PERIOD.closes, late_after: PERIOD.lateAfter, active_days: [1, 2, 3, 4, 5], is_active: true }];
const academic_calendar = HOLIDAYS.map((h, i) => ({ id: i + 1, date: h.date, type: 'libur', description: h.description, is_school_day: false }));
const holidaySet = new Set(HOLIDAYS.map((h) => h.date));
const attendance_sessions = [];
for (let d = wib(SESSION_RANGE.from); d <= wib(SESSION_RANGE.to); d = addSec(d, 86400)) {
  const date = fmtDate(d);
  const dow = d.getUTCDay();
  if (dow === 0 || dow === 6 || holidaySet.has(date)) continue;
  attendance_sessions.push({
    id: nextId('attendance_sessions'), attendance_period_id: 1, session_date: date,
    opens_at: `${date} ${PERIOD.opens}`, closes_at: `${date} ${PERIOD.closes}`, late_after: `${date} ${PERIOD.lateAfter}`,
    status: date < TODAY ? 'closed' : date === TODAY ? 'open' : 'scheduled',
  });
}

// ---------------------------------------------------------------------------
// Submissions, photos, validations, activity logs
// ---------------------------------------------------------------------------
const attendance_submissions = [];
const attendance_photos = [];
const attendance_validations = [];
const activity_logs = [];
const shaByKey = {};

const REJECT_GENERAL = [
  'Foto tidak menunjukkan suasana kelas.',
  'Foto terlalu gelap/buram sehingga tidak dapat diverifikasi.',
  'Foto tampak diambil di luar lingkungan sekolah.',
];
const REJECT_LOCATION = 'Lokasi pengajuan jauh di luar area sekolah.';

function log(action, userId, subjectType, subjectId, properties, createdAt, ip = null, ua = null) {
  activity_logs.push({ id: nextId('activity_logs'), user_id: userId, action, subject_type: subjectType, subject_id: subjectId, properties, ip_address: ip, user_agent: ua, created_at: createdAt });
}
function gpsSample(kind) {
  let distance; let accuracy;
  if (kind === 'outside') { distance = randInt(200, 2500); accuracy = randInt(8, 40); }
  else if (kind === 'low_accuracy') { distance = randInt(20, 200); accuracy = randInt(55, 150); }
  else { distance = randInt(5, 140); accuracy = randInt(5, 40); }
  const bearing = rand() * 2 * Math.PI;
  const lat = SCHOOL_CENTER.lat + (distance * Math.cos(bearing)) / 111320;
  const lng = SCHOOL_CENTER.lng + (distance * Math.sin(bearing)) / (111320 * Math.cos((SCHOOL_CENTER.lat * Math.PI) / 180));
  return {
    latitude: Number(lat.toFixed(7)), longitude: Number(lng.toFixed(7)),
    gps_accuracy_m: Number((accuracy + rand()).toFixed(1)), distance_from_school_m: distance,
    geofence_flag: distance <= GEOFENCE_RADIUS_M ? 'inside' : 'outside',
  };
}
function randomGpsKind() {
  const r = rand();
  return r < 0.03 ? 'outside' : r < 0.08 ? 'low_accuracy' : 'inside';
}

function addDecision(sub, action, toStatus, reason, validatorId, decidedAt) {
  const fromStatus = sub.status;
  attendance_validations.push({ id: nextId('attendance_validations'), attendance_submission_id: sub.id, validator_id: validatorId, action, from_status: fromStatus, to_status: toStatus, reason, decided_at: fmt(decidedAt), ip_address: randomIp() });
  sub.status = toStatus;
  sub.lock_version += 1;
  sub.updated_at = fmt(decidedAt);
  const logAction = action === 'revise' ? 'validation.revised' : toStatus === 'accepted' ? 'validation.accepted' : 'validation.rejected';
  log(logAction, validatorId, 'attendance_submission', sub.id, { from: fromStatus, to: toStatus, reason }, fmt(decidedAt));
}

/**
 * opts: time ('HH:MM:SS'), gpsKind, gps (objek tetap), ip, ua, decision ('auto' | 'accept' | 'reject' | 'pending'),
 *       reason, shaFrom (kunci skenario), shaKey, revise { to, reason }
 */
function makeSubmission(session, student, opts = {}) {
  const meta = studentMeta[student.id];
  const date = session.session_date;
  const isToday = date === TODAY;
  let t;
  if (opts.time) t = wib(date, opts.time);
  else {
    const late = !opts.onTime && chance(0.15);
    const minutes = late ? randInt(61, 150) : randInt(10, 59);
    t = addSec(wib(date, PERIOD.opens), minutes * 60 + randInt(0, 59));
  }
  if (isToday && t > NOW) return null;

  const gpsKind = opts.gpsKind || randomGpsKind();
  const gps = opts.gps || gpsSample(gpsKind);
  const ip = opts.ip || randomIp();
  const ua = opts.ua || pick(USER_AGENTS);
  const id = nextId('attendance_submissions');
  const sub = {
    id, uuid: uuid(), student_id: student.id, class_id: meta.classId, attendance_session_id: session.id,
    class_qr_code_id: qrFor(meta.classKey, date).id, submitted_at: fmt(t), ...gps,
    gps_captured_at_client: fmt(addSec(t, -randInt(5, 40))),
    is_late: t > wib(date, PERIOD.lateAfter), status: 'pending', lock_version: 0, idempotency_key: uuid(),
    superseded_at: null, ip_address: ip, user_agent: ua, created_at: fmt(t), updated_at: fmt(t),
  };
  attendance_submissions.push(sub);

  const sha = opts.shaFrom ? shaByKey[opts.shaFrom] : hex(64);
  if (opts.shaKey) shaByKey[opts.shaKey] = sha;
  attendance_photos.push({
    id: nextId('attendance_photos'), attendance_submission_id: id, disk: 'private',
    path: `attendance/${date.slice(0, 4)}/${date.slice(5, 7)}/${sub.uuid}.jpg`, mime_type: 'image/jpeg',
    size_bytes: randInt(180000, 600000), width: 1280, height: 960, sha256: sha,
    retention_until: null, purged_at: null, created_at: fmt(t),
  });
  log('attendance.submitted', meta.userId, 'attendance_submission', id, { session_date: date, class: meta.classKey, is_late: sub.is_late }, fmt(t), ip, ua);

  // --- Keputusan validasi ---
  let decision = opts.decision || 'auto';
  if (decision === 'auto') {
    const daysAgo = (wib(TODAY) - wib(date)) / 86400000;
    const outside = gps.geofence_flag === 'outside';
    if (isToday) decision = t < wib(date, '07:30:00') && chance(0.55) ? (outside ? 'reject' : 'accept') : 'pending';
    else if (daysAgo <= 2 && chance(0.03)) decision = 'pending'; // pengajuan "basi" belum divalidasi
    else if (outside) decision = chance(0.7) ? 'reject' : 'accept';
    else if (gpsKind === 'low_accuracy') decision = chance(0.15) ? 'reject' : 'accept';
    else decision = chance(0.02) ? 'reject' : 'accept';
  }
  if (decision === 'pending') return sub;

  const decidedAt = addSec(t, randInt(5, 120) * 60);
  if (decidedAt > NOW) return sub; // belum sempat divalidasi
  const validator = pick(validatorUserIds);
  const reason = decision === 'reject' ? opts.reason || (gps.geofence_flag === 'outside' ? REJECT_LOCATION : pick(REJECT_GENERAL)) : null;
  addDecision(sub, decision, decision === 'accept' ? 'accepted' : 'rejected', reason, validator, decidedAt);

  if (opts.revise) {
    const revisedAt = addSec(decidedAt, 26 * 3600);
    addDecision(sub, 'revise', opts.revise.to, opts.revise.reason, WAKASEK_USER_ID, revisedAt);
  }
  return sub;
}

// Skenario khusus (kunci: "tanggal|kelas#indeks")
const TITIP = { ip: '10.10.5.77', ua: USER_AGENTS[0], gps: { latitude: -6.8641023, longitude: 108.0326811, gps_accuracy_m: 12.4, distance_from_school_m: 18, geofence_flag: 'inside' } };
const SPECIAL = {
  [`2026-10-13|X-2#1`]: { time: '06:31:10', gpsKind: 'inside', decision: 'accept', shaKey: 'dup' },
  [`2026-10-13|X-2#2`]: { time: '06:31:55', gpsKind: 'inside', decision: 'reject', shaFrom: 'dup', reason: 'Foto identik dengan pengajuan siswa lain pada sesi yang sama.' },
  [`2026-10-12|XII-1#4`]: { time: '06:40:12', gpsKind: 'inside', decision: 'reject', reason: 'Foto terlalu gelap/buram sehingga tidak dapat diverifikasi.', revise: { to: 'accepted', reason: 'Dikonfirmasi wali kelas: siswa hadir di kelas pada hari tersebut.' } },
  [`${TODAY}|XI-1#3`]: { time: '06:42:05', decision: 'pending', ...TITIP },
  [`${TODAY}|XI-1#4`]: { time: '06:42:31', decision: 'pending', ...TITIP },
  [`${TODAY}|XI-1#6`]: { time: '06:42:58', decision: 'pending', ...TITIP },
  [`${TODAY}|X-1#4`]: { time: '07:24:13', gpsKind: 'inside', decision: 'pending' },
  [`${TODAY}|X-2#6`]: { time: '06:55:40', gpsKind: 'outside', decision: 'pending' },
  [`${TODAY}|XII-2#5`]: { time: '06:58:02', gpsKind: 'low_accuracy', decision: 'pending' },
};
const NEVER_SUBMIT = new Set(['X-1#9']);
const ALWAYS_ON_TIME = new Set(['X-1#0']);

for (const session of attendance_sessions.filter((s) => s.status !== 'scheduled')) {
  const date = session.session_date;
  for (const [key, student] of Object.entries(studentByKey)) {
    const meta = studentMeta[student.id];
    if (meta.leftAt && date > meta.leftAt) continue;
    if (NEVER_SUBMIT.has(key)) continue;
    const special = SPECIAL[`${date}|${key}`];
    if (special) { makeSubmission(session, student, special); continue; }
    if (ALWAYS_ON_TIME.has(key)) { makeSubmission(session, student, { onTime: true, gpsKind: 'inside', decision: date === TODAY ? 'auto' : 'accept' }); continue; }
    const p = date === TODAY ? 0.8 : 0.9;
    if (!chance(p)) continue;
    makeSubmission(session, student);
  }
}

// Log tambahan untuk skenario uji
log('qr.regenerated', ADMIN_ID, 'class_qr_code', qrOld.id, { class: 'X-1', old_qr_id: qrOld.id, new_qr_id: activeQr['X-1'].id, reason: qrOld.revoke_reason }, '2026-09-30 15:00:00', '10.10.1.10');
log('student.status_changed', ADMIN_ID, 'student', studentByKey['XI-2#9'].id, { from: 'aktif', to: 'pindah' }, '2026-09-30 10:00:00', '10.10.1.10');
if (revokedLink) log('guardian.link.revoked', ADMIN_ID, 'guardian_student', revokedLink.id, { reason: revokedLink.notes }, revokedLink.revoked_at, '10.10.1.10');
log('auth.login.failed', null, 'user', null, { username: 'guru03', reason: 'kata sandi salah' }, '2026-10-13 07:05:11', '10.10.2.31');
log('auth.login.failed', null, 'user', null, { username: 'guru03', reason: 'kata sandi salah' }, '2026-10-13 07:05:40', '10.10.2.31');
log('attendance.identify.failed', null, 'student', null, { nis: '9925999', reason: 'NIS tidak terdaftar' }, `${TODAY} 06:47:20`, '10.10.7.12');
log('attendance.identify.class_mismatch', studentMeta[studentByKey['XII-2#1'].id].userId, 'student', studentByKey['XII-2#1'].id, { qr_class: 'XI-1', student_class: 'XII-2' }, `${TODAY} 06:50:03`, '10.10.9.44');
activity_logs.sort((a, b) => (a.created_at < b.created_at ? -1 : a.created_at > b.created_at ? 1 : a.id - b.id));
activity_logs.forEach((l, i) => { l.id = i + 1; });

// ---------------------------------------------------------------------------
// Pelanggaran (TAHAP LANJUT — kategori & poin DUMMY, menunggu Q-21)
// ---------------------------------------------------------------------------
const violation_categories = [
  { id: 1, code: 'SRG', name: 'Tidak memakai seragam sesuai ketentuan', default_points: 5, severity: 'ringan', is_active: true },
  { id: 2, code: 'HP', name: 'Menggunakan HP saat pembelajaran tanpa izin', default_points: 10, severity: 'sedang', is_active: true },
  { id: 3, code: 'BLS', name: 'Meninggalkan kelas tanpa izin', default_points: 20, severity: 'sedang', is_active: true },
  { id: 4, code: 'KLH', name: 'Berkelahi', default_points: 50, severity: 'berat', is_active: true },
];
const student_violations = [];
const VIOLATIONS = [
  ['X-1#2', 1, '2026-09-22 07:15:00', 'guru01'], ['X-1#2', 2, '2026-10-05 10:20:00', 'guru03'],
  ['X-2#4', 1, '2026-09-29 07:10:00', 'guru02'], ['XI-1#7', 2, '2026-10-01 09:45:00', 'guru05'],
  ['XI-2#3', 3, '2026-10-06 11:00:00', 'guru04'], ['XII-1#6', 1, '2026-10-07 07:12:00', 'guru05'],
  ['XII-2#8', 4, '2026-10-08 12:30:00', 'wakasek'], ['X-1#5', 2, '2026-10-12 08:40:00', 'guru07'],
];
VIOLATIONS.forEach(([key, catId, at, reporter], i) => {
  const s = studentByKey[key];
  const cat = violation_categories.find((c) => c.id === catId);
  const voided = i === 5;
  student_violations.push({
    id: i + 1, student_id: s.id, class_id: studentMeta[s.id].classId, violation_category_id: catId, points: cat.default_points,
    occurred_at: at, description: `${cat.name} (DUMMY)`, reported_by: teacherByUsername[reporter].user_id,
    status: voided ? 'voided' : 'recorded', voided_by: voided ? WAKASEK_USER_ID : null, void_reason: voided ? 'Salah input siswa (DUMMY)' : null,
  });
});

// ---------------------------------------------------------------------------
// School settings (nilai null = BELUM DITETAPKAN sekolah)
// ---------------------------------------------------------------------------
const SETTINGS = [
  ['app.timezone', 'Asia/Jakarta', 'Zona waktu sekolah', false],
  ['attendance.sessions_per_day', 1, 'Absensi sekali sehari (keputusan sekolah 14-10-2026)', false],
  ['attendance.late_policy', 'tandai', 'Pengajuan setelah batas terlambat tetap diterima dan ditandai Terlambat (keputusan sekolah)', false],
  ['attendance.periods', [{ name: 'Masuk', opens_at: '06:00', late_after: '07:00', closes_at: '12:00', active_days: [1, 2, 3, 4, 5] }], 'Jam sesi — NILAI DUMMY, menunggu Q-02', true],
  ['attendance.absence_types_enabled', false, 'Izin/Sakit/Alpa ditunda (keputusan sekolah)', false],
  ['attendance.resubmit_after_reject', null, 'Belum ditetapkan (Q-09)', false],
  ['attendance.revision_window_hours', null, 'Belum ditetapkan (Q-09)', false],
  ['attendance.validation_deadline', null, 'Belum ditetapkan (Q-07)', false],
  ['auth.student_method', 'nis', 'Siswa login dengan NIS saja (keputusan sekolah)', false],
  ['auth.max_attempts', 5, 'Batas percobaan login — NILAI DUMMY', true],
  ['auth.lockout_minutes', 15, 'Durasi blokir sementara — NILAI DUMMY', true],
  ['camera.preferred_facing', 'environment', 'Kamera belakang, memotret kelas (keputusan sekolah)', false],
  ['gps.school_center', SCHOOL_CENTER, 'Titik pusat sekolah — NILAI DUMMY, menunggu Q-18', true],
  ['gps.radius_m', GEOFENCE_RADIUS_M, 'Radius toleransi — NILAI DUMMY, menunggu Q-18', true],
  ['gps.max_accuracy_m', 50, 'Akurasi memadai — NILAI DUMMY, menunggu Q-18', true],
  ['gps.outside_policy', 'tandai', 'Di luar radius hanya ditandai — NILAI DUMMY, menunggu Q-18', true],
  ['photo.max_size_kb', 2048, 'Batas ukuran foto — NILAI DUMMY', true],
  ['photo.retention_days', null, 'Belum ditetapkan (Q-15)', false],
];
const school_settings = SETTINGS.map(([key, value, description, is_dummy_value], i) => ({ id: i + 1, key, value, description, is_dummy_value, updated_by: ADMIN_ID, updated_at: SEED_CREATED }));

// ---------------------------------------------------------------------------
// Tulis JSON
// ---------------------------------------------------------------------------
const TABLES = {
  roles, permissions, permission_role, users, role_user,
  academic_years, classes, teachers, students, class_enrollments, class_officers,
  guardians, guardian_student, class_qr_codes, attendance_periods, academic_calendar,
  attendance_sessions, attendance_submissions, attendance_photos, attendance_validations,
  school_settings, activity_logs, violation_categories, student_violations,
};
const manifest = { dataset: 'DUMMY — SI Absensi Siswa SMA Situraja', fiktif: true, waktu_acuan: fmt(NOW) + ' WIB', zona_waktu: 'Asia/Jakarta', urutan_seed: Object.keys(TABLES), jumlah: {} };
for (const [name, rows] of Object.entries(TABLES)) {
  writeFileSync(join(OUT_JSON, `${name}.json`), JSON.stringify(rows, null, 2) + '\n', 'utf8');
  manifest.jumlah[name] = rows.length;
}
const statusCount = attendance_submissions.reduce((acc, s) => ((acc[s.status] = (acc[s.status] || 0) + 1), acc), {});
manifest.ringkasan_pengajuan = { ...statusCount, terlambat: attendance_submissions.filter((s) => s.is_late).length, di_luar_radius: attendance_submissions.filter((s) => s.geofence_flag === 'outside').length };
writeFileSync(join(OUT_JSON, '_manifest.json'), JSON.stringify(manifest, null, 2) + '\n', 'utf8');

// ---------------------------------------------------------------------------
// CSV contoh impor siswa
// ---------------------------------------------------------------------------
const CSV_HEADER = 'nis,nisn,nama_lengkap,jenis_kelamin,kelas,tahun_ajaran';
const validRows = [];
for (let i = 0; i < 10; i++) {
  const gender = chance(0.5) ? 'L' : 'P';
  validRows.push([`99260${21 + i}`, genNisn(), genName(gender), gender, i < 5 ? 'X-1' : 'X-2', '2026/2027'].join(','));
}
writeFileSync(join(OUT_CSV, 'contoh_import_siswa_valid.csv'), [CSV_HEADER, ...validRows].join('\n') + '\n', 'utf8');
const errorRows = [
  ['9926031', genNisn(), 'Baris Valid Contoh', 'L', 'X-1', '2026/2027'],      // 2: valid
  [students[0].nis, genNisn(), 'NIS Sudah Terdaftar', 'P', 'X-1', '2026/2027'], // 3: NIS duplikat dengan database
  ['9926032', genNisn(), '', 'L', 'X-2', '2026/2027'],                         // 4: nama kosong
  ['9926033', genNisn(), 'Kelas Tidak Ada', 'P', 'X-9', '2026/2027'],          // 5: kelas tidak ditemukan
  ['9926034', genNisn(), 'Jenis Kelamin Salah', 'X', 'X-2', '2026/2027'],      // 6: jenis kelamin tidak valid
  ['ABC123', genNisn(), 'NIS Bukan Angka', 'L', 'X-2', '2026/2027'],           // 7: format NIS salah
  ['9926035', genNisn(), 'Duplikat Dalam File A', 'P', 'X-1', '2026/2027'],    // 8: valid (pertama)
  ['9926035', genNisn(), 'Duplikat Dalam File B', 'L', 'X-1', '2026/2027'],    // 9: NIS duplikat di file
  ['9926036', genNisn(), 'Tahun Ajaran Salah', 'L', 'X-1', '2030/2031'],       // 10: tahun ajaran tidak ada
];
writeFileSync(join(OUT_CSV, 'contoh_import_siswa_dengan_error.csv'), [CSV_HEADER, ...errorRows.map((r) => r.join(','))].join('\n') + '\n', 'utf8');

// ---------------------------------------------------------------------------
// Ringkasan akun (Markdown)
// ---------------------------------------------------------------------------
const userById = Object.fromEntries(users.map((u) => [u.id, u]));
const roleNamesOf = (uid) => role_user.filter((r) => r.user_id === uid).map((r) => roles.find((x) => x.id === r.role_id).name).join(', ');
let md = `# Ringkasan Akun Data Dummy\n\n> Dihasilkan otomatis oleh \`generate.mjs\` — jangan diedit manual. **Semua data fiktif.**\n> Kata sandi semua akun staf & orang tua: \`${DUMMY_PASSWORD}\` (khusus pengembangan). Siswa masuk dengan **NIS saja**.\n\n`;
md += `## Staf\n\n| Username | Nama | Role | Status | Catatan |\n|---|---|---|---|---|\n`;
md += `| admin | ${userById[ADMIN_ID].name} | admin_sistem | aktif | |\n`;
for (const t of TEACHERS) {
  const tr = teacherByUsername[t.username];
  const homeroom = classes.filter((c) => c.academic_year_id === 2 && c.homeroom_teacher_id === tr.id).map((c) => c.name).join(', ');
  md += `| ${t.username} | ${t.name} | ${roleNamesOf(tr.user_id)} | ${tr.is_active ? 'aktif' : '**nonaktif**'} | ${homeroom ? 'Wali kelas ' + homeroom : ''} |\n`;
}
md += `\n## Orang Tua\n\n| Username | Nama | Anak (kelas) · status relasi |\n|---|---|---|\n`;
for (const g of guardians) {
  const links = guardian_student.filter((gs) => gs.guardian_id === g.id).map((gs) => {
    const s = students.find((x) => x.id === gs.student_id);
    return `${s.full_name} (${studentMeta[s.id].classKey}, ${gs.relationship}) · ${gs.verification_status}`;
  }).join('<br>');
  md += `| ${g.user_id ? userById[g.user_id].username : '_belum punya akun_'} | ${g.full_name} | ${links} |\n`;
}
md += `\n## Siswa\n\n| NIS | Nama | Kelas | Role | Catatan |\n|---|---|---|---|---|\n`;
const NOTES = {
  'X-1#0': 'Selalu hadir tepat waktu', 'X-1#9': 'Tidak pernah mengajukan', 'X-1#4': 'Hari ini terlambat, menunggu',
  'X-2#1': 'Foto asli (13/10)', 'X-2#2': 'Foto identik ditolak (13/10)', 'X-2#6': 'Hari ini di luar radius, menunggu',
  'X-2#7': 'Relasi ortu pending', 'XI-1#3': 'Pola titip absen hari ini', 'XI-1#4': 'Pola titip absen hari ini', 'XI-1#6': 'Pola titip absen hari ini',
  'XI-2#9': 'Status pindah (akun nonaktif)', 'XII-1#4': 'Ditolak lalu direvisi diterima (12/10)', 'XII-2#1': 'Mencoba QR kelas lain hari ini',
  'XII-2#5': 'Hari ini akurasi GPS rendah', 'XII-2#9': 'Tanpa orang tua terhubung',
};
for (const s of SIBLINGS) { NOTES[s[0]] = (NOTES[s[0]] ? NOTES[s[0]] + '; ' : '') + `Kakak dari ${studentByKey[s[1]].full_name}`; NOTES[s[1]] = (NOTES[s[1]] ? NOTES[s[1]] + '; ' : '') + `Adik dari ${studentByKey[s[0]].full_name}`; }
for (const [key, s] of Object.entries(studentByKey)) {
  md += `| ${s.nis} | ${s.full_name} | ${studentMeta[s.id].classKey} | ${roleNamesOf(s.user_id)} | ${NOTES[key] || ''} |\n`;
}
writeFileSync(join(ROOT, 'ringkasan_akun.md'), md, 'utf8');

console.log('Data dummy berhasil dibuat:');
console.table(manifest.jumlah);
console.log('Ringkasan pengajuan:', manifest.ringkasan_pengajuan);
