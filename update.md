# Update Progress - Sistem Mentoring

Berikut adalah ringkasan progres fitur yang sudah dan belum dikerjakan:

---

## Progres Terbaru (Updated: 10 Jan 2026 - Sesi 2)

*   **Implementasi Fungsionalitas Sisi Mentor (End-to-End):**
    *   **Dashboard Mentor Dinamis:** Merombak total dashboard mentor dari halaman statis menjadi hub dinamis dengan layout dua kolom. Menampilkan kartu statistik (jumlah kelompok, total mentee, laporan tertunda) dan widget untuk "Sesi Mendatang" serta "Kelompok Bimbingan".
    *   **Fitur Kelompok Bimbingan:** Mentor kini dapat melihat daftar kelompok yang dibimbingnya, lengkap dengan anggota mentee. Halaman detail grup juga sudah dibuat untuk menampilkan daftar sesi.
    *   **Fitur Sesi & Laporan:** Mentor dapat melihat daftar semua sesi, dan masuk ke halaman manajemen per sesi untuk mengisi absensi (hadir, absen, izin) dan laporan progres (skor & catatan) untuk setiap mentee.
    *   **Fitur Progres Mentee:** Mentor dapat melihat halaman rekapitulasi progres semua mentee yang dibimbingnya, menampilkan rangkuman tingkat kehadiran dan rata-rata skor.

*   **Standardisasi UI & Perbaikan Bug:**
    *   **Header Admin Konsisten:** Semua halaman di sisi Admin (Dashboard, Fakultas, Level, Pengumuman, Materi, Impor Mentee, Manajemen Mentee, Pendaftaran Mentor, Kelompok, Placement Test, dan Ujian) telah diperbarui untuk menggunakan komponen `<x-page-header>` yang seragam.
    *   **Perbaikan Bug Routing & Tampilan:**
        *   Memperbaiki `ParseError` di beberapa halaman admin (`exams`, `mentoring-groups`) yang disebabkan oleh `@endif` yang hilang.
        *   Memperbaiki `BadMethodCallException` dengan mengganti nama relasi di model `Session` dari `group()` menjadi `mentoringGroup()` agar konsisten.
        *   Memperbaiki link "Dashboard" di sidebar admin agar mengarah ke route yang benar.
        *   Memperbaiki alur pengalihan (redirect) setelah admin login agar langsung menuju ke dashboard admin.

---

## Fitur yang Sudah Selesai (Sesi 1):

*   **Desain Login & Signup:** Halaman login dan signup telah didesain ulang agar konsisten dengan tampilan landing page, termasuk background, form card, dan styling elemen.
*   **Layout Dashboard:** Implementasi layout dashboard dua kolom telah selesai, dengan sidebar navigasi di sisi kiri dan area konten utama di kanan. Ini juga mencakup styling untuk status aktif pada link sidebar.
*   **Manajemen Fakultas:** Fitur CRUD (Create, Read, Update, Delete) lengkap untuk mengelola data fakultas telah diimplementasikan, termasuk view, controller, dan integrasi sidebar.
*   **Manajemen Level:** Fitur CRUD lengkap untuk mengelola data level telah diimplementasikan, termasuk view, controller, dan integrasi sidebar.
*   **Manajemen Pengumuman:** Fitur CRUD lengkap untuk membuat, mengedit, dan menghapus pengumuman telah diimplementasikan, termasuk update model, controller, route, view, dan integrasi sidebar. Issue terkait missing column `published_at` juga telah diperbaiki dengan penambahan migrasi.
*   **Manajemen Materi:** Fitur CRUD lengkap untuk mengelola materi pembelajaran telah diimplementasikan, termasuk update model, controller (dengan file upload), route, view, dan integrasi sidebar. Symbolic link untuk storage juga telah dibuat.
*   **Peningkatan UI/UX:**
    *   **Navbar:** Mendesain ulang navbar atas menjadi header bar modern dengan tombol toggle sidebar, placeholder pencarian, dan ikon notifikasi. Mengubah warna latar belakang agar sesuai dengan tema.
    *   **Sidebar:** Menambahkan lengkungan elegan di sisi kanan sidebar untuk tampilan yang lebih halus.
    *   **Desain Tabel:** Merombak total desain semua tabel di halaman admin dengan gaya baru yang konsisten, mencakup *zebra-striping* dan efek *hover* yang sesuai dengan palet warna brand.

*   **Fitur Manajemen Mentee (End-to-End):**
    *   **Daftar Mentee:** Membuat halaman admin untuk menampilkan daftar semua user dengan peran 'Mentee'.
    *   **Impor Mentee:** Mengimplementasikan fitur impor mentee dari file CSV, termasuk logika *create-or-update* berbasis email untuk menangani duplikasi data. Proses ini juga melibatkan pembuatan `FacultySeeder` untuk memastikan data fakultas tersedia.
    *   **Hapus Semua Mentee:** Menambahkan tombol aksi massal untuk menghapus semua data mentee, lengkap dengan dialog konfirmasi dan perbaikan routing dari `DELETE` ke `POST` untuk mengatasi masalah browser.
    *   **Catatan Logika Penting (Alur Login & NPM):** Klarifikasi dibuat mengenai bagaimana sistem mengidentifikasi NPM mentee. Meskipun login menggunakan `email`, data `NPM` sudah tersimpan sebagai bagian dari profil `User` saat proses impor. Ketika mentee login, seluruh profilnya (termasuk NPM) dimuat, sehingga sistem secara otomatis mengetahui NPM dari user yang sedang aktif tanpa perlu input manual.

*   **Fitur Placement Test (End-to-End Sesuai README):**
    *   **Alur Mentee:** Membuat halaman (`/placement-test/take`) bagi mentee untuk mengerjakan tes, yang terdiri dari:
        *   Formulir unggah untuk rekaman audio tes bacaan.
        *   Soal pilihan ganda untuk tes teori tajwid (skor dihitung otomatis).
        *   Logika untuk mencegah pengerjaan ulang tes.
    *   **Alur Admin:**
        *   Membuat mekanisme *streaming* yang aman bagi admin untuk mendengarkan audio dari *private storage*.
        *   Memperbarui halaman "Grade / Edit" dengan pemutar audio tersemat, memungkinkan admin memberi skor tes bacaan dan menetapkan level akhir.
    *   **Database:** Menambahkan kolom `audio_recording_path` ke tabel `placement_tests` melalui migrasi baru untuk menyimpan lokasi file audio.

---

## Progres Awal (Sebelum Sesi 1)

*   **Sistem Kontrol Akses Berbasis Peran (RBAC) Komprehensif:**
    *   **Login & Pengalihan Dinamis**: Pengguna secara otomatis dialihkan ke dashboard yang sesuai (Admin, Mentor, Mentee) setelah login berhasil.
    *   **Middleware Peran**: Middleware (`AdminMiddleware`, `MentorMiddleware`, `MenteeMiddleware`) diimplementasikan untuk melindungi rute dan memastikan hanya peran yang diizinkan yang dapat mengakses fitur tertentu.
    *   **Akses Penuh Admin**: Admin dapat mengakses semua fitur yang terkait dengan Mentor dan Mentee.
    *   **Pencegahan Loop Pengalihan**: Memperbaiki masalah loop pengalihan Admin saat mencoba mengakses dashboard mentee.

*   **Peningkatan Antarmuka Pengguna (UI/UX) Global:**
    *   **Komponen Header Halaman Kustom (`x-page-header`)**: Dibuat untuk menyediakan judul halaman yang konsisten, menarik secara visual, dengan ikon dan subjudul di semua tampilan.
    *   **Desain Ulang Tampilan Mentee**: Menerapkan gaya visual berbasis kartu yang konsisten, penggunaan ikon yang lebih baik, tipografi yang ditingkatkan, dan penanganan status kosong yang menarik untuk semua tampilan mentee.

*   **Fitur Mentee (Sepenuhnya Diimplementasikan & Ditingkatkan Secara Visual):**
    *   **Dashboard Mentee**: Pusat informasi utama yang menarik secara visual, merangkum status mentee, kelompok mentoring, pengumuman terbaru, sesi mendatang, dan menyediakan tautan cepat ke fitur mentee lainnya.
    *   **Materi Belajar Saya**: Mentee dapat melihat materi pembelajaran yang relevan dengan level yang ditetapkan.
    *   **Pengumuman**: Mentee dapat melihat pengumuman penting yang dipublikasikan oleh admin.
    *   **Kelompok Mentoring Saya**: Mentee dapat melihat detail kelompok mentoring mereka, termasuk mentor dan anggota lainnya.
    *   **Sesi Mentoring Saya**: Mentee dapat melihat jadwal sesi, status absensi, dan laporan progres mereka untuk setiap sesi.
    *   **Laporan Hasil Mentoring**: Mentee dapat melihat laporan komprehensif perjalanan mentoring mereka, termasuk hasil tes penempatan dan ringkasan sesi.
    *   **Ujian Saya (Ujian Akhir)**:
        *   **Manajemen Ujian (Admin)**: UI CRUD untuk admin untuk membuat, melihat, mengedit, dan menghapus ujian, termasuk detail ujian dan level terkait.
        *   **Manajemen Pertanyaan (Admin)**: UI CRUD untuk admin untuk menambah, mengedit, dan menghapus pertanyaan (pilihan ganda, esai, respon audio) serta mengelola opsi untuk pilihan ganda.
        *   **Pengiriman Ujian (Mentee)**: Mentee dapat melihat daftar ujian yang tersedia, memulai ujian, mengirimkan jawaban (dengan penilaian awal untuk pilihan ganda), dan melihat halaman konfirmasi.

*   **Manajemen Kelompok Mentoring (Admin):**
    *   **UI CRUD Lengkap**: Fungsionalitas untuk admin membuat, melihat, mengedit, dan menghapus kelompok mentoring.
    *   **Penugasan Mentor/Mentee**: Admin dapat menugaskan mentor ke kelompok dan mengelola anggota mentee dalam kelompok melalui antarmuka edit.

*   **Perbaikan Infrastruktur & Bug:**
    *   **Migrasi & Model Baru**: Menambahkan model dan migrasi untuk `Exam`, `Question`, `Option`, `ExamSubmission`, `SubmissionAnswer` untuk mendukung fitur ujian.
    *   **Seeding Data Uji Komprehensif**: Mengembangkan dan menjalankan seeder untuk `levels`, `mentoring_groups`, `group_members`, `sessions`, `attendances`, dan `progress_reports` untuk menyediakan data awal.
    *   **Perbaikan Error Seeder**: Mengatasi `Data truncated` pada kolom `gender` dan `status` dalam seeder.
    *   **Konfigurasi Model Sesi**: Memperbaiki model `Session` untuk menunjuk ke tabel `mentoring_sessions` yang benar dan melakukan cast atribut `date` ke `datetime`.
    *   **Komponen Input Baru**: Membuat komponen `x-select-input` dan `x-textarea-input` untuk formulir yang konsisten.

---