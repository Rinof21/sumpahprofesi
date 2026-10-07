# Product Requirement Document (PRD)

**Nama Modul/Sistem:** Sistem Manajemen Pelaksanaan, Yudisium & Arsip Sumpah Profesi Kesehatan (*Multi-Prodi*)  
**Target Platform:** Laravel Web Application  
**Versi Dokumen:** 2.0.0 (Multi-Program Studi Architecture)  
**Status:** Approved for Implementation  

---

## 1. Ringkasan Eksekutif & Latar Belakang

Pelaksanaan angkat sumpah profesi di lingkungan Fakultas Kedokteran dan Ilmu Kesehatan diselenggarakan secara berkala (triwulanan / 4 periode setahun) oleh berbagai program studi:
* **Profesi Dokter** (Ikatan Dokter Indonesia - IDI)
* **Profesi Apoteker** (Ikatan Apoteker Indonesia - IAI)
* **Profesi Ners** (Persatuan Perawat Nasional Indonesia - PPNI)
* Serta program studi profesi/kesehatan lain yang akan bertambah di masa depan (Gizi, Bidan, dsb.).

Setiap prodi memiliki entitas organisasi profesi, format naskah lafal sumpah, dan kepanitiaan tersendiri. Namun, seluruh prodi menghadapi kendala operasional yang identik:
1. **Pemetaan Rohaniwan:** Pendataan agama peserta yang tercecer menghambat perhitungan kebutuhan serta penerbitan surat tugas resmi ke rohaniwan lintas agama (Islam, Protestan, Katolik, Hindu, Buddha, Konghucu).
2. **Pelanggaran Tata Tertib Acara:** Calon lulusan dan keluarga kerap mengabaikan tata tertib aula, terutama larangan membawa anak kecil/balita yang berisiko mengganggu kekhidmatan prosesi sakral.
3. **Koordinasi Teknis Acara:** Keterlambatan pengumpulan slide PPT profil, penetapan 1 perwakilan pemberi pesan & kesan, penyelarasan teks lafal sumpah spesifik prodi, dan pembatasan fotografer ruangan (maksimal 3 juru kamera resmi).
4. **Distribusi Kontak Tidak Tepat Sasaran:** Peserta sering salah menghubungi panitia (masalah teknis web ditanyakan ke staf prodi, urusan legalisir/toga ditanyakan ke tim IT).
5. **Absensi Repositori Historis:** Belum ada arsip digital berkala per periode per prodi yang menampilkan statistik lulusan, 10 foto kurasi terbaik, dan rekaman video prosesi.

Sistem ini dirancang dengan prinsip **modular multi-prodi** agar seluruh kebutuhan administratif dan operasional sumpah profesi dapat dioperasikan secara terpadu dalam satu instalasi aplikasi Laravel.

---

## 2. Tujuan & Sasaran Produk (Goals & Objectives)

* **Skalabilitas Multi-Prodi:** Mendukung penambahan program studi baru tanpa perlu merombak skema database dan alur kerja aplikasi.
* **Akurasi Rohaniwan 100%:** Rekapitulasi otomatis peserta berdasarkan agama untuk surat tugas rohaniwan dan formasi panggung per prodi.
* **Kepatuhan Tata Tertib (Zero Minor Disruption):** Mekanisme *Compliance Gate* (Pakta Integritas Digital) yang mengunci akses tiket undangan dan materi sebelum peserta menyetujui larangan membawa anak kecil.
* **Operasional Ruangan Terkendali:** Pembatasan kuota fotografer resmi ruangan tepat maksimal 3 orang per sesi/periode, modul upload slide PPT profil, penetapan 1 perwakilan pesan-kesan, dan distribusi naskah lafal sumpah sesuai agama dan prodi.
* **Pusat Bantuan Terarah:** Direktori narahubung terbagi tegas antara Meja IT dan Meja Administrasi masing-masing prodi.
* **Repositori & Galeri Periodik:** Portal arsip publik per prodi untuk menayangkan riwayat periode triwulanan, ringkasan lulusan, 10 foto terbaik, dan rekaman video YouTube.

---

## 3. Pengguna & Hak Akses (User Roles & Permissions)

| Role | Deskripsi | Hak Akses Utama |
| :--- | :--- | :--- |
| **Peserta (Calon Lulusan)** | Mahasiswa calon disumpah (Dokter, Apoteker, Ners, dll.) | Mendaftar pada periode aktif prodi terkait, mengisi biodata, menyetujui pakta tata tertib, mengunduh naskah lafal sumpah sesuai agama & prodi, mengunggah slide PPT, melihat kontak panitia, mengunduh e-ticket undangan. |
| **Admin Prodi / Panitia Sumpah** | Staf akademik / pengelola kegiatan prodi terkait | Mengelola periode sumpah prodi, validasi berkas peserta, melihat rekapitulasi rohaniwan, menunjuk 1 perwakilan pesan-kesan, mendaftarkan maksimal 3 fotografer resmi, mengelola kontak admin prodi, kurasi arsip periode. |
| **Superadmin / Tim IT Fakultas** | Pranata Komputer / Pengelola Sistem Fakultas | Mengelola master program studi, mengelola kontak person IT fakultas, manajemen akun pengguna, monitoring sistem secara keseluruhan. |
| **Publik / Tamu / Keluarga** | Masyarakat, alumni, keluarga peserta | Mengakses portal arsip publik untuk melihat rekaman YouTube, galeri 10 foto terbaik, dan statistik lulusan per prodi per periode. |

---

## 4. Kebutuhan Fungsional (Functional Requirements)

### Modul A: Master Data Program Studi (Multi-Tenancy Engine)
* **FR-A1:** Sistem mengelola master Program Studi dengan atribut: Kode Prodi, Nama Prodi (contoh: *Profesi Dokter*, *Profesi Apoteker*, *Profesi Ners*), Gelar Lulusan (dr., Apt., Ns.), Nama Organisasi Profesi (IDI, IAI, PPNI), dan Slug URL.
* **FR-A2:** Setiap periode kegiatan, peserta, template naskah sumpah, dan arsip galeri wajib berelasi dengan satu `study_program_id`.

### Modul B: Manajemen Periode & Siklus Triwulanan
* **FR-B1:** Setiap program studi dapat membuka periode sumpah independen per triwulan (Q1, Q2, Q3, Q4).
* **FR-B2:** Periode memiliki status: `Draft`, `Active` (Pendaftaran Terbuka), dan `Archived` (Selesai/Terarsip).
* **FR-B3:** Dalam satu prodi, hanya ada 1 periode yang berstatus `Active` dalam satu waktu.

### Modul C: Pendaftaran & Biodata Peserta (Master Candidates)
* **FR-C1:** Peserta mengisi atribut data wajib:
  * Program Studi (otomatis terkunci sesuai periode yang didaftar)
  * Nama Lengkap & Gelar Akademik Terdahulu (contoh: S.Ked, S.Farm, S.Kep)
  * NIM & NIK
  * Tempat & Tanggal Lahir
  * Nama Kandung Ayah & Nama Kandung Ibu
  * Jalur Masuk Kuliah (SNBP/SNMPTN, SNBT/SBMPTN, Mandiri, Kerjasama/Afirmasi)
  * Agama (Islam, Protestan, Katolik, Hindu, Buddha, Konghucu)
* **FR-C2:** Sistem menyajikan dasbor rekapitulasi otomatis jumlah peserta per agama per periode prodi untuk dasar persuratan rohaniwan ke instansi keagamaan.

### Modul D: Gerbang Tata Tertib (Compliance Gate)
* **FR-D1:** Fitur unduh e-ticket dan materi sumpah terkunci selama status `agreed_rules` bernilai `false`.
* **FR-D2:** Peserta wajib menyetujui lembar komitmen digital dengan penekanan visual tebal:
  * **Larangan mutlak membawa anak kecil/balita ke dalam ruang prosesi** demi menjaga kekhidmatan.
  * Kewajiban hadir gladi resik dan hari-H tepat waktu.
  * Standar tata busana resmi prosesi (toga/jas sipil lengkap).
* **FR-D3:** Lembar e-ticket undangan keluarga secara otomatis mencetak peringatan larangan anak kecil berukuran tebal.

### Modul E: Operasional Teknis Acara
* **FR-E1 (Naskah Lafal Sumpah Otomatis):** Peserta mengunduh naskah PDF lafal sumpah yang otomatis terfilter berdasarkan relasi **Prodi** dan **Agama** peserta (contoh: Dokumen Sumpah Dokter Islam, Sumpah Apoteker Katolik, Sumpah Ners Kristen, dsb.).
* **FR-E2 (Slide PPT Profil):** Formulir upload slide presentasi biodata peserta format `.ppt`, `.pptx`, atau `.pdf` dengan validasi ukuran file maksimal 20MB.
* **FR-E3 (Perwakilan Pesan-Kesan):** Admin prodi dapat menetapkan tepat 1 orang kandidat sebagai perwakilan pesan & kesan (`is_speech_rep = true`). Penetapan baru otomatis menganulir kandidat sebelumnya.
* **FR-E4 (Pembatasan Fotografer Ruangan - Maks. 3 Orang):**
  * Pendaftaran fotografer resmi ruangan dibatasi maksimal **3 orang per periode prodi**.
  * Input ke-4 ditolak otomatis oleh sistem.
  * Sistem menerbitkan Kartu Identitas Digital / E-Badge bertuliskan **"FOTOGRAFER RUANGAN RESMI (MAKS. 3)"** beserta QR Code verifikasi.

### Modul F: Meja Bantuan Terarah (Segmented Helpdesk)
* **FR-F1:** Menampilkan direktori kontak narahubung yang terpisah jelas pada portal publik dan dashboard peserta:
  * **Meja Bantuan IT Fakultas:** Untuk kendala reset akun, kegagalan upload PPT, atau galat sistem (Nama, Nomor WhatsApp dengan link `https://wa.me/...`).
  * **Meja Bantuan Administrasi Prodi:** Untuk konfirmasi berkas kelulusan, legalisir, ketentuan toga, dan koordinasi rohaniwan (Nama Personil Prodi, Nomor WhatsApp, Email).
* **FR-F2:** Admin dapat mengaktifkan atau menonaktifkan status penayangan kontak person.

### Modul G: Arsip & Galeri Periode Triwulanan
* **FR-G1:** Menampilkan statistik rekapitulasi lulusan per periode per prodi (total disumpah, rasio gender, sebaran jalur masuk).
* **FR-G2 (Kurasi 10 Foto Terbaik):** Modul galeri foto pasca-acara yang dibatasi tepat maksimal 10 foto beresolusi tinggi per periode, dilengkapi deskripsi/caption dan urutan tampil.
* **FR-G3 (Video Dokumentasi YouTube):** Input kolom URL video YouTube (live streaming prosesi atau video dokumentasi resmi) yang langsung ter-embed di halaman arsip periode terkait.

---

## 5. Kebutuhan Non-Fungsional (Non-Functional Requirements)

* **Modularity:** Penambahan prodi baru hanya memerlukan *seeding* atau penambahan baris pada tabel `study_programs` tanpa migrasi ulang.
* **High Concurrency & Integrity:** Kuota maksimal 3 fotografer diamankan dengan transaksi database bersyarat (*pessimistic locking* atau *transaction lock*) untuk mencegah celah *race condition*.
* **Mobile-First Experience:** Halaman pengisian form, pembacaan pakta tata tertib, dan navigasi kontak WhatsApp nyaman diakses melalui peramban ponsel.
* **Storage Authorization:** File naskah sumpah, slide PPT, dan pasfoto tersimpan pada direktori *private storage* Laravel dan hanya dapat diakses melalui *Signed URLs* atau Controller berautentikasi.

---

## 6. Arsitektur Data & Model Relasi

### Diagram Relasi Entitas (ERD)

```text
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
       │ code (e.g. KED, APT)   │
       │ name (e.g. Dokter)     │
       │ degree_title (dr.,Apt) │
       │ organization (IDI,IAI) │
       │ slug                   │
       └───────────┬────────────┘
                   │
                   │ 1:N
                   ▼
       ┌────────────────────────┐
       │      oath_periods      │
       ├────────────────────────┤
       │ id                     │
       │ study_program_id (FK)  │
       │ name (Periode I 2026)  │
       │ slug                   │
       │ event_date             │
       │ quarter_code (Q1-Q4)   │
       │ youtube_url            │
       │ status (draft/active/  │
       │         archived)      │
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
└───────────────────────┘