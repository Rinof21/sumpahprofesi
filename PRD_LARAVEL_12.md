# PRODUCT REQUIREMENT DOCUMENT (PRD)
## Sistem Informasi Yudisium, Pelaksanaan & Repositori Sumpah Profesi Kesehatan Multi-Prodi

- **Platform Target:** Laravel 12 (PHP ^8.2 / ^8.3 / ^8.4)
- **Desain UI/UX:** SB Admin 2 Modern Style (Tailwind CSS, Font Awesome 6 Free, Google Fonts Nunito)
- **Arsitektur:** Modular Multi-Program Studi dengan Role-Based Access Control (Spatie Laravel Permission) & Fast Token Access Mahasiswa
- **Versi Dokumen:** 2.5.0 (Production-Ready Architecture)
- **Status:** Approved for Implementation in Laravel 12

---

## 1. Ringkasan Eksekutif & Latar Belakang

Pelaksanaan angkat sumpah profesi di lingkungan Fakultas Kedokteran dan Ilmu Kesehatan (FKIK) diselenggarakan secara berkala (triwulanan / 4 periode per tahun) oleh berbagai program studi:
1. **Profesi Dokter** (Ikatan Dokter Indonesia - IDI)
2. **Profesi Apoteker** (Ikatan Apoteker Indonesia - IAI)
3. **Profesi Ners** (Persatuan Perawat Nasional Indonesia - PPNI)
4. Serta dirancang siap menampung prodi profesi baru di masa depan (Gizi, Bidan, dsb.) secara dinamis (*multi-tenancy engine*).

Setiap prodi memiliki entitas organisasi profesi, format naskah lafal sumpah, dan kepanitiaan tersendiri. Namun, seluruh prodi menghadapi tantangan operasional yang identik:
- **Kalkulasi & Pemetaan Rohaniwan:** Pendataan agama calon lulusan yang tercecer menghambat perhitungan kebutuhan serta penerbitan surat tugas resmi ke rohaniwan lintas agama (Islam, Protestan, Katolik, Hindu, Buddha, Konghucu).
- **Pelanggaran Tata Tertib Aula (Zero Minor Disruption):** Masuknya anak kecil/balita ke dalam ruang prosesi aula berisiko mengganggu kekhidmatan sakral angkat sumpah. Sistem memblokir tiket undangan keluarga sampai calon lulusan menyetujui lembar komitmen digital (*Compliance Gate*).
- **Operasional Ruangan Terkendali:**
  - Pembatasan kuota fotografer resmi ruangan tepat maksimal 3 orang per periode prodi (*concurrency-safe / pessimistic locking*) dengan penerbitan E-Badge kartu akreditasi aula.
  - Penunjukan tepat 1 perwakilan resmi pesan & kesan wisudawan dan 1 koordinator sumpah.
  - Pengumpulan slide PPT profil calon lulusan (.ppt, .pptx, .pdf max 20MB) dan integrasi folder Google Drive panitia.
  - Distribusi naskah lafal sumpah resmi dalam bentuk penampil PDF maupun teks khusus per agama & prodi.
- **Dual Path Access (Fleksibilitas Pendaftaran):**
  - Mahasiswa dapat mendaftar dan mengelola biodata **tanpa login akun** menggunakan **Kode Token Akses Periode** (sesi aktif 60 menit via Session & Cookie).
  - Mahasiswa juga dapat mendaftar melalui akun pengguna berautentikasi (Role `Peserta`).
- **Fitur Penguncian Data Peserta (*Freeze/Lock*):** Admin Prodi dapat mengunci pendaftaran & pengeditan biodata sebelum gladi resik atau hari-H.
- **Pusat Bantuan Terarah (*Segmented Helpdesk*):** Pemisahan jelas antara kontak Meja IT Fakultas (gangguan teknis web) dan Meja Administrasi Prodi (legalisir, busana toga, rohaniwan) dengan integrasi pesan WhatsApp langsung.
- **Repositori Arsip Historis:** Portal arsip publik per prodi menampilkan statistik sebaran jalur masuk & agama, embed siaran langsung YouTube, serta kurasi maksimal 10 foto terbaik pasca-acara.

---

## 2. Aktor & Matriks Hak Akses (User Roles & Permissions)

Sistem menggunakan paket **Spatie Laravel Permission** dengan pembagian peran berikut:

| Role | Target Pengguna | Ruang Lingkup & Hak Akses |
| :--- | :--- | :--- |
| **Superadmin** | Tim IT Fakultas / Administrator Utama | Akses global seluruh prodi, CRUD Master Program Studi, CRUD Akun Pengguna, CRUD Role & Permission Spatie, pengalokasian izin khusus per user, manajemen Kontak IT Fakultas. |
| **Admin Prodi** | Sekretariat / Panitia Sumpah Prodi | Dibatasi hanya pada prodi yang terikat (`study_program_id`). Manajemen periode sumpah (termasuk soft delete/restore), buka/kunci edit data peserta (`is_locked`), validasi peserta, penunjukan perwakilan pesan-kesan & koordinator sumpah, kuota 3 fotografer & cetak E-Badge, upload naskah PDF sumpah prodi, kurasi galeri 10 foto, ekspor data peserta ke Excel, dan kelola kontak admin prodi. |
| **Peserta** | Mahasiswa terdaftar (Login Akun) | Mendaftar pada periode aktif prodi, edit/hapus pendaftaran sebelum dikunci, persetujuan Pakta Integritas (Compliance Gate), unduh/cetak e-Ticket QR Code, buka Naskah Sumpah, dan upload slide PPT profil. |
| **Token Mahasiswa** | Mahasiswa (Tanpa Login via Token) | Akses portal pendaftaran mandiri menggunakan Kode Token Akses Periode (TK-XXXX). Sesi disimpan selama 60 menit (Session + Cookie fallback). Mengisi biodata, mengedit biodata, menghapus data, dan mengunggah PPT selama periode belum berstatus *Locked*. |
| **Publik / Tamu** | Keluarga, Alumni, Umum | Melihat beranda prodi, mengakses detail arsip periode, melihat statistik kelulusan, menonton dokumentasi YouTube, melihat kurasi galeri foto, dan mengakses direktori Helpdesk WhatsApp. |

---

## 3. Arsitektur Basis Data (Database Schema & Relations)

```
       ┌────────────────────────┐
       │     contact_people     │
       ├────────────────────────┤
       │ id                     │
       │ study_program_id (FK*) │ ──► (*Nullable: jika null = IT Fakultas)
       │ category (it/admin)    │
       │ name, phone, email     │
       │ is_active              │
       └────────────────────────┘

       ┌────────────────────────┐
       │     study_programs     │
       ├────────────────────────┤
       │ id                     │
       │ code (e.g. Dr, Apt)    │
       │ name (e.g. Dokter)     │
       │ degree_title (dr.,Apt) │
       │ organization (IDI,IAI) │
       │ slug                   │
       │ oath_pdf_path          │
       └───────────┬────────────┘
                   │
                   │ 1:N
                   ▼
       ┌────────────────────────┐
       │      oath_periods      │
       ├────────────────────────┤
       │ id                     │
       │ study_program_id (FK)  │
       │ name (Periode II 2026) │
       │ slug                   │
       │ event_date             │
       │ quarter_code (Q1-Q4)   │
       │ access_token           │
       │ drive_url              │
       │ oath_pdf_path          │
       │ youtube_url            │
       │ status (draft/active/  │
       │         archived)      │
       │ is_locked (boolean)    │
       │ deleted_at (soft-del)  │
       └───────────┬────────────┘
                   │
    ┌──────────────┼──────────────────────────┐
    │ 1:N          │ 1:N                      │ 1:N (Max 10)
    ▼              ▼                          ▼
┌───────────────────────┐  ┌───────────────────────┐  ┌───────────────────────┐
│     oath_candidates   │  │     photographers     │  │      event_photos     │
├───────────────────────┤  ├───────────────────────┤  ├───────────────────────┤
│ id                    │  │ id                    │  │ id                    │
│ period_id (FK)        │  │ period_id (FK)        │  │ period_id (FK)        │
│ user_id (FK, nullable)│  │ name                  │  │ photo_path            │
│ nim (unique)          │  │ agency_name           │  │ caption               │
│ nik                   │  │ phone_number          │  │ sort_order (1 - 10)   │
│ full_name             │  │ badge_code (unique)   │  └───────────────────────┘
│ birth_place           │  └───────────────────────┘
│ birth_date            │
│ father_name           │
│ mother_name           │
│ admission_path        │
│ religion              │
│ agreed_rules (bool)   │
│ agreed_at (timestamp) │
│ ppt_file_path         │
│ is_speech_rep (bool)  │
│ speech_notes          │
│ is_oath_coordinator   │
│ coordinator_notes     │
└───────────────────────┘
```

### 3.1. Tabel `users`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `name` (VARCHAR 255)
* `email` (VARCHAR 255, UNIQUE)
* `email_verified_at` (TIMESTAMP, NULLABLE)
* `password` (VARCHAR 255)
* `study_program_id` (BIGINT, NULLABLE, FK -> `study_programs.id`)
* `remember_token` (VARCHAR 100, NULLABLE)
* `created_at`, `updated_at`

### 3.2. Tabel `study_programs`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `code` (VARCHAR 10, UNIQUE) — Kode singkat (contoh: `Dr`, `Apt`, `Ns`)
* `name` (VARCHAR 255) — Nama program studi (contoh: `Profesi Dokter`)
* `degree_title` (VARCHAR 50) — Gelar lulusan (contoh: `dr.`, `Apt.`, `Ns.`)
* `organization` (VARCHAR 255) — Nama organisasi profesi (contoh: `Ikatan Dokter Indonesia (IDI)`)
* `slug` (VARCHAR 255, UNIQUE)
* `oath_pdf_path` (VARCHAR 255, NULLABLE) — Path file PDF naskah lafal sumpah resmi prodi
* `created_at`, `updated_at`

### 3.3. Tabel `oath_periods`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `study_program_id` (BIGINT, FK -> `study_programs.id`, ON DELETE CASCADE)
* `name` (VARCHAR 255) — Nama periode (contoh: `Sumpah Dokter Periode II 2026`)
* `slug` (VARCHAR 255, UNIQUE)
* `event_date` (DATE) — Tanggal pelaksanaan prosesi
* `quarter_code` (VARCHAR 10) — Siklus triwulan (`Q1`, `Q2`, `Q3`, `Q4`)
* `access_token` (VARCHAR 50, NULLABLE) — Kode rahasia token akses mahasiswa (contoh: `TK-DOKTER26`)
* `drive_url` (TEXT, NULLABLE) — Tautan folder Google Drive panitia
* `oath_pdf_path` (VARCHAR 255, NULLABLE) — Dokumen PDF spesifik periode (opsional)
* `youtube_url` (VARCHAR 255, NULLABLE) — Tautan video dokumentasi YouTube
* `status` (ENUM: `draft`, `active`, `archived`, DEFAULT: `draft`)
* `is_locked` (BOOLEAN, DEFAULT: `false`) — Penguncian pendaftaran & pengubahan data kandidat
* `deleted_at` (TIMESTAMP, NULLABLE) — Mendukung fitur *Soft Deletes*
* `created_at`, `updated_at`

### 3.4. Tabel `oath_candidates`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `period_id` (BIGINT, FK -> `oath_periods.id`, ON DELETE CASCADE)
* `user_id` (BIGINT, NULLABLE, FK -> `users.id`, ON DELETE SET NULL)
* `nim` (VARCHAR 50, UNIQUE) — Nomor Induk Mahasiswa
* `nik` (VARCHAR 30) — Nomor Induk Kependudukan KTP
* `full_name` (VARCHAR 255) — Nama Lengkap beserta gelar terdahulu
* `birth_place` (VARCHAR 255)
* `birth_date` (DATE)
* `father_name` (VARCHAR 255)
* `mother_name` (VARCHAR 255)
* `admission_path` (VARCHAR 100) — `SNBP/SNMPTN`, `SNBT/SBMPTN`, `Mandiri`, `Kerjasama/Afirmasi`
* `religion` (VARCHAR 50) — `Islam`, `Protestan`, `Katolik`, `Hindu`, `Buddha`, `Konghucu`
* `agreed_rules` (BOOLEAN, DEFAULT: `false`) — Status persetujuan pakta integritas
* `agreed_at` (TIMESTAMP, NULLABLE)
* `ppt_file_path` (VARCHAR 255, NULLABLE) — Path slide PPT profil (.ppt, .pptx, .pdf max 20MB)
* `is_speech_rep` (BOOLEAN, DEFAULT: `false`) — Perwakilan pesan & kesan (Max 1 orang per periode)
* `speech_notes` (TEXT, NULLABLE)
* `is_oath_coordinator` (BOOLEAN, DEFAULT: `false`) — Koordinator sumpah (Max 1 orang per periode)
* `coordinator_notes` (TEXT, NULLABLE)
* `created_at`, `updated_at`

### 3.5. Tabel `photographers`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `period_id` (BIGINT, FK -> `oath_periods.id`, ON DELETE CASCADE)
* `name` (VARCHAR 255)
* `agency_name` (VARCHAR 255)
* `phone_number` (VARCHAR 50)
* `badge_code` (VARCHAR 100, UNIQUE) — Format: `PHOTO-{KODE_PRODI}-{TAHUN}-{SEQ}`
* `created_at`, `updated_at`

### 3.6. Tabel `event_photos`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `period_id` (BIGINT, FK -> `oath_periods.id`, ON DELETE CASCADE)
* `photo_path` (VARCHAR 255)
* `caption` (VARCHAR 255, NULLABLE)
* `sort_order` (INTEGER, DEFAULT: 1) — Diurutkan 1 sampai maksimal 10
* `created_at`, `updated_at`

### 3.7. Tabel `contact_people`
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `study_program_id` (BIGINT, NULLABLE, FK -> `study_programs.id`, ON DELETE SET NULL)
* `category` (ENUM: `it`, `admin`, DEFAULT: `admin`)
* `name` (VARCHAR 255)
* `phone` (VARCHAR 50)
* `email` (VARCHAR 255, NULLABLE)
* `is_active` (BOOLEAN, DEFAULT: `true`)
* `created_at`, `updated_at`

---

## 4. Kebutuhan Fungsional & Aturan Bisnis (Business Rules)

### 4.1. Modul Periode & Siklus Triwulanan
- **Rule Single Active Period:** Dalam satu program studi, hanya boleh ada **maksimal 1 periode berstatus `active`**. Saat periode baru disimpan atau diubah ke `active`, periode aktif sebelumnya pada prodi yang sama otomatis diubah menjadi `archived`.
- **Generate Token Akses:** Jika kolom `access_token` dikosongkan oleh admin, sistem secara otomatis menghasilkan token acak berformat `TK-{6_KARAKTER_ALFANUMERIK}`.
- **Locking Enforcement (`is_locked`):** Admin Prodi dapat mengaktifkan sakelar kunci. Saat terkunci:
  - Form pendaftaran peserta ditutup.
  - Tombol edit biodata dan hapus pendaftaran dinonaktifkan (baik di portal token maupun dashboard peserta).
  - Unggah slide PPT ditolak.
- **Soft Deletes & Sampah:** Periode yang dihapus tidak langsung hilang dari database. Tersedia tab khusus *Trash* dengan fungsi *Restore* dan *Force Delete*.

### 4.2. Modul Akses Token Mahasiswa (Tanpa Login Akun)
- **Verifikasi Token:** Mahasiswa memilih periode aktif lalu memasukkan kode token.
- **Persistensi Sesi 60 Menit:** Informasi verifikasi disimpan dalam `session(['verified_period_id' => $id, 'verified_period_expires_at' => $timestamp])` dan di-backup ke cookie berenkripsi `verified_period_access` berdurasi 60 menit.
- **Portal Token Mahasiswa:**
  - Melihat sisa waktu aktif token (menit).
  - Menambah peserta baru (otomatis set `agreed_rules = true`).
  - Mengedit dan menghapus biodata masing-masing mahasiswa dalam periode tersebut sebelum periode dikunci.
  - Mengunggah slide presentasi profil (.ppt, .pptx, .pdf max 20MB).
  - Membuka tautan Google Drive panitia dan membaca dokumen naskah sumpah resmi.
  - Tombol Reset Token untuk berpindah ke periode lain.

### 4.3. Modul Compliance Gate (Pakta Integritas Digital)
- Bagi mahasiswa yang login dengan akun (`Peserta`), fitur e-Ticket dan unduh naskah sumpah diblokir sebelum menyetujui komitmen digital:
  1. **Larangan Mutlak Membawa Anak Kecil / Balita ke Ruang Prosesi Aula.**
  2. Kewajiban Hadir Gladi Resik dan Hari-H Tepat Waktu.
  3. Standar Busana Resmi Prosesi (Toga / Jas Sipil Lengkap).
- Setelah disetujui, mencatat timestamp `agreed_at` dan status `agreed_rules = true`.

### 4.4. Modul e-Ticket Undangan Keluarga
- Hanya dapat diakses setelah lulus Compliance Gate.
- Menampilkan identitas peserta, NIM, agama, tanggal pelaksanaan, lokasi (Aula Utama FKIK), kuota pendamping keluarga (maksimal 2 orang dewasa tanpa balita).
- Menampilkan **banner merah tebal peringatan larangan balita**.
- Memuat kode QR digital verifikasi dengan hash `md5(nim + id)`.
- Menyediakan tombol cetak langsung (*print-friendly*).

### 4.5. Modul Naskah Lafal Sumpah Official
- Jika Admin Prodi sudah mengunggah file PDF naskah sumpah prodi, dokumen ditampilkan melalui viewer `<iframe>` responsif disertai tombol unduh PDF.
- Jika belum diunggah, sistem menyediakan teks lafal sumpah otomatis terpersonalisasi:
  - Kalimat pembuka sumpah disesuaikan dengan agama (`Islam: Demi Allah Saya Bersumpah...`, `Kristen/Katolik: Saya Berjanji Di Hadapan Tuhan...`, `Hindu: Om Atmahnam...`, `Buddha: Namo Buddhaya...`, `Konghucu: Kehadapan Tian...`).
  - Butir sumpah disesuaikan dengan organisasi profesi (IDI untuk Dokter, IAI untuk Apoteker, PPNI untuk Ners).
  - Blok tanda tangan peserta dan Dekan/Ketua Senat FKIK.

### 4.6. Modul Fotografer Resmi Ruangan (Maks. 3 Orang)
- **Pessimistic Concurrency Lock:** Pendaftaran fotografer dibungkus dalam `DB::transaction` dengan query `Photographer::where('period_id', $id)->lockForUpdate()->count()`.
- Jika jumlah sudah mencapai 3 orang, pendaftaran ke-4 digagalkan dan memunculkan notifikasi error.
- Menerbitkan ID Badge cetak berukuran standar kartu gantung leher dengan label: **"FOTOGRAFER RUANGAN RESMI (MAKS. 3)"**, nama juru kamera, nama agensi, kode unik badge, dan QR code.

### 4.7. Modul Perwakilan Pesan & Kesan dan Koordinator Sumpah
- Admin Prodi dapat menunjuk tepat 1 kandidat sebagai **Perwakilan Pesan & Kesan Wisudawan**. Menunjuk kandidat baru otomatis membatalkan kandidat sebelumnya via transaksi database.
- Admin Prodi dapat menunjuk tepat 1 kandidat sebagai **Koordinator Sumpah Wisudawan** untuk memimpin lafal sumpah di panggung.

### 4.8. Modul Ekspor Data Peserta ke Excel
- Menggunakan `Maatwebsite\Excel` (`CandidatesExport`).
- File `.xlsx` terformat rapi:
  - Baris 1: Judul "DAFTAR PESERTA SUMPAH PROFESI" (Merge A1:G1, Bold, Size 14, Center).
  - Baris 2: Nama Periode.
  - Baris 3: Tanggal Kegiatan.
  - Baris 5: Header kolom dengan latar belakang biru SB Admin (`#4E73DF`), font putih tebal, dan border tipis abu-abu.
  - Kolom: No, Nama Lengkap, NIM, Tempat Tanggal Lahir, Jalur Masuk, Nama Ayah, Nama Ibu.

### 4.9. Modul Repositori Arsip Publik & Galeri
- Halaman publik per periode menampilkan:
  - Rekapitulasi jumlah wisudawan.
  - Grafik persentase agama peserta (keperluan rohaniwan).
  - Grafik sebaran jalur masuk kuliah lulusan.
  - Spotlight perwakilan pesan & kesan.
  - Embed video YouTube live streaming atau dokumentasi acara.
  - Kurasi tepat maksimal 10 foto terbaik pasca-acara dengan caption dan urutan nomor foto.

### 4.10. Modul Segmented Helpdesk (Meja Bantuan)
- Segmentasi tegas:
  - **Meja Bantuan IT Fakultas:** Untuk reset password, gagal upload slide PPT, kendala token, dan bug aplikasi.
  - **Meja Bantuan Administrasi Prodi:** Untuk konfirmasi kelulusan, legalisir, tata busana toga, dan persuratan rohaniwan.
- Tombol tautan langsung ke WhatsApp Web / Mobile (`wa.me`).

### 4.11. Modul Manajemen User, Role & Permission (Superadmin)
- Antarmuka visual lengkap (*GUI*) untuk:
  - Tambah, ubah, dan hapus Spatie Role.
  - Tambah, ubah, dan hapus Spatie Permission.
  - Sinkronisasi daftar izin ke masing-masing peran.
  - Memberikan hak izin khusus (*direct permission*) per akun pengguna.
  - Mengubah peran pengguna dan program studi yang dikelola.
- Proteksi sistem: Role bawaan (`Superadmin`, `Admin Prodi`, `Peserta`) tidak dapat dihapus.

---

## 5. Matriks Rute Aplikasi (Route Map)

```php
// ==========================================
// 1. PUBLIC ROUTES (Tanpa Login)
// ==========================================
GET    /                                PublicController@index                 [name: public.index]
GET    /archive/{slug}                  PublicController@archiveDetail         [name: public.archive-detail]
GET    /helpdesk                        PublicController@helpdesk              [name: public.helpdesk]

// ==========================================
// 2. PORTAL TOKEN MAHASISWA (Tanpa Login)
// ==========================================
GET    /pendaftaran-token               TokenAccessController@showTokenForm    [name: token-access.index]
POST   /pendaftaran-token/verify        TokenAccessController@verifyToken      [name: token-access.verify]
POST   /pendaftaran-token/reset         TokenAccessController@resetTokenSession [name: token-access.reset]
GET    /pendaftaran-token/portal        TokenAccessController@portal           [name: token-access.portal]
POST   /pendaftaran-token/peserta       TokenAccessController@storeCandidate   [name: token-access.store]
PUT    /pendaftaran-token/peserta/{id}  TokenAccessController@updateCandidate  [name: token-access.update]
DELETE /pendaftaran-token/peserta/{id}  TokenAccessController@deleteCandidate  [name: token-access.destroy]
POST   /pendaftaran-token/peserta/{id}/ppt TokenAccessController@uploadPpt     [name: token-access.upload-ppt]

// ==========================================
// 3. AUTENTIKASI & QUICK DEMO SWITCHER
// ==========================================
GET    /login                           AuthController@showLogin               [name: login]
POST   /login                           AuthController@login                   [name: login.post]
POST   /logout                          AuthController@logout                  [name: logout]
GET    /demo-login/{user}               AuthController@loginAs                 [name: demo.login-as]

// ==========================================
// 4. PESERTA (Middleware: auth, role:Peserta)
// ==========================================
GET    /candidate/dashboard             CandidateController@dashboard          [name: candidate.dashboard]
POST   /candidate/register              CandidateController@registerStore      [name: candidate.register.store]
PUT    /candidate/biodata               CandidateController@updateBiodata      [name: candidate.biodata.update]
DELETE /candidate/registration          CandidateController@deleteRegistration [name: candidate.registration.delete]
POST   /candidate/agree-rules           CandidateController@agreeRules         [name: candidate.agree-rules]
GET    /candidate/oath-script           CandidateController@oathScript         [name: candidate.oath-script]
POST   /candidate/upload-ppt            CandidateController@uploadPpt          [name: candidate.upload-ppt]
GET    /candidate/e-ticket              CandidateController@eTicket            [name: candidate.e-ticket]

// ==========================================
// 5. ADMIN PRODI (Middleware: auth, role:Admin Prodi)
// ==========================================
GET    /admin/dashboard                 AdminProdiController@dashboard         [name: admin.dashboard]
POST   /admin/dashboard/oath-pdf        AdminProdiController@updateOathPdf     [name: admin.dashboard.update-oath-pdf]

// Periode Sumpah
GET    /admin/periods                   AdminProdiController@periods           [name: admin.periods.index]
POST   /admin/periods                   AdminProdiController@storePeriod        [name: admin.periods.store]
PUT    /admin/periods/{period}          AdminProdiController@updatePeriod      [name: admin.periods.update]
PATCH  /admin/periods/{period}/status   AdminProdiController@updatePeriodStatus [name: admin.periods.update-status]
PATCH  /admin/periods/{period}/toggle-lock AdminProdiController@toggleLock     [name: admin.periods.toggle-lock]
DELETE /admin/periods/{period}          AdminProdiController@destroyPeriod     [name: admin.periods.destroy]
POST   /admin/periods/{id}/restore      AdminProdiController@restorePeriod     [name: admin.periods.restore]
DELETE /admin/periods/{id}/force-delete AdminProdiController@forceDeletePeriod [name: admin.periods.force-delete]

// Kandidat & Ekspor Excel
GET    /admin/candidates/export         AdminProdiController@exportCandidates  [name: admin.candidates.export]
GET    /admin/candidates                AdminProdiController@candidates        [name: admin.candidates.index]
POST   /admin/candidates/{id}/speech-rep AdminProdiController@assignSpeechRep   [name: admin.candidates.speech-rep]
POST   /admin/candidates/{id}/oath-coordinator AdminProdiController@assignOathCoordinator [name: admin.candidates.oath-coordinator]

// Fotografer Kuota Max 3
GET    /admin/photographers             AdminProdiController@photographers     [name: admin.photographers.index]
POST   /admin/photographers             AdminProdiController@storePhotographer [name: admin.photographers.store]
DELETE /admin/photographers/{id}        AdminProdiController@destroyPhotographer [name: admin.photographers.destroy]
GET    /admin/photographers/{id}/badge  AdminProdiController@printBadge         [name: admin.photographers.badge]

// Kontak & Kurasi Galeri
GET    /admin/contacts                  AdminProdiController@contacts          [name: admin.contacts.index]
POST   /admin/contacts                  AdminProdiController@storeContact      [name: admin.contacts.store]
PATCH  /admin/contacts/{id}/toggle      AdminProdiController@toggleContact     [name: admin.contacts.toggle]
GET    /admin/gallery                   AdminProdiController@gallery           [name: admin.gallery.index]
POST   /admin/gallery/photo             AdminProdiController@storePhoto        [name: admin.gallery.store-photo]
PATCH  /admin/gallery/youtube/{period}  AdminProdiController@updateYoutube    [name: admin.gallery.update-youtube]
DELETE /admin/gallery/photo/{photo}     AdminProdiController@destroyPhoto      [name: admin.gallery.destroy-photo]

// ==========================================
// 6. SUPERADMIN (Middleware: auth, role:Superadmin)
// ==========================================
GET    /superadmin/dashboard            SuperadminController@dashboard         [name: superadmin.dashboard]
GET    /superadmin/study-programs       SuperadminController@studyPrograms     [name: superadmin.study-programs.index]
POST   /superadmin/study-programs       SuperadminController@storeStudyProgram [name: superadmin.study-programs.store]
GET    /superadmin/it-contacts          SuperadminController@itContacts        [name: superadmin.it-contacts.index]
POST   /superadmin/it-contacts          SuperadminController@storeITContact    [name: superadmin.it-contacts.store]
PATCH  /superadmin/it-contacts/{id}/toggle SuperadminController@toggleITContact [name: superadmin.it-contacts.toggle]
GET    /superadmin/users                SuperadminController@users             [name: superadmin.users.index]
POST   /superadmin/users                SuperadminController@storeUser         [name: superadmin.users.store]
PATCH  /superadmin/users/{id}/role      SuperadminController@updateUserRole    [name: superadmin.users.update-role]

// Spatie Roles & Permissions GUI
GET    /superadmin/roles-permissions    RolePermissionController@index         [name: superadmin.roles-permissions.index]
POST   /superadmin/roles                RolePermissionController@storeRole     [name: superadmin.roles.store]
PUT    /superadmin/roles/{role}         RolePermissionController@updateRole    [name: superadmin.roles.update]
DELETE /superadmin/roles/{role}         RolePermissionController@destroyRole   [name: superadmin.roles.destroy]
POST   /superadmin/permissions          RolePermissionController@storePermission [name: superadmin.permissions.store]
PUT    /superadmin/permissions/{id}     RolePermissionController@updatePermission [name: superadmin.permissions.update]
DELETE /superadmin/permissions/{id}     RolePermissionController@destroyPermission [name: superadmin.permissions.destroy]
PATCH  /superadmin/users/{id}/permissions RolePermissionController@updateUserPermissions [name: superadmin.users.update-permissions]
```

---

## 6. Standar Desain UI/UX & Tema

- **Desain Tema:** SB Admin 2 Modern Edition dibangun menggunakan Tailwind CSS.
- **Palet Warna Utama:**
  - SB Blue Primary: `#4e73df`
  - SB Blue Dark: `#2e59d9`
  - SB Navy Sidebar: `#224abe`
  - Background Halaman: `#f8f9fc`
  - Card & Container: `#ffffff`
  - State Colors: Emerald Success (`#10b981`), Red Danger (`#ef4444`), Amber Warning (`#f59e0b`), Purple/Indigo Info (`#6366f1`)
- **Tipografi:** Google Fonts **Nunito** (`300, 400, 600, 700, 800, 900`).
- **Ikonografi:** Font Awesome 6 Free CDN.
- **Komponen Khusus:**
  - **Quick User Switcher Dropdown:** Berada di bilah atas header untuk berpindah akun demo instan tanpa perlu logout-login berulang.
  - **Mobile Responsive Drawer:** Sidebar geser dengan tombol hamburger dan backdrop blur gelap untuk akses ponsel.
  - **Print CSS (`@media print`):** Format cetak bersih otomatis pada e-Ticket, ID Badge Fotografer, dan Naskah Lafal Sumpah.

---

## 7. Langkah Instalasi & Rekonstruksi di Laravel 12

### 7.1. Instalasi Paket Composer
```bash
composer require spatie/laravel-permission
composer require maatwebsite/excel
```

### 7.2. Konfigurasi `bootstrap/app.php`
Daftarkan alias middleware Spatie di dalam closure `withMiddleware`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

### 7.3. Konfigurasi Storage & Migrasi
```bash
php artisan storage:link
php artisan migrate:fresh --seed
```

### 7.4. Data Seeder Bawaan
- **Role:** `Superadmin`, `Admin Prodi`, `Peserta`.
- **Master Prodi:**
  - `Dr` - Profesi Dokter (Ikatan Dokter Indonesia - IDI)
  - `Apt` - Profesi Apoteker (Ikatan Apoteker Indonesia - IAI)
  - `Ns` - Profesi Ners (Persatuan Perawat Nasional Indonesia - PPNI)
- **Akun Bawaan:**
  - Superadmin IT: `rino.f@untan.ac.id` / `password`
  - Admin Prodi Dokter: `evi.risdianty@untan.ac.id` / `password`
  - Admin Prodi Apoteker: `admin.apoteker@fkik.ac.id` / `password`
  - Admin Prodi Ners: `admin.ners@fkik.ac.id` / `password`
- **Kontak Meja Bantuan:** Helpdesk IT Fakultas dan kontak admin masing-masing prodi dengan nomor WhatsApp aktif.
