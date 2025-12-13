# Roadmap & Progress Pembangunan Sistem Mentoring

Ini adalah catatan progres pengembangan untuk melacak fitur yang sudah dan akan dikerjakan.

### Fase 1: Fondasi & Manajemen Data Master
- [x] **Setup Awal & Autentikasi:** Laravel Breeze sudah terpasang.
- [ ] **Implementasi Role-Based Access Control (RBAC):**
    - [x] Konfigurasi relasi User & Role di Model.
    - [ ] Membuat Seeder untuk tabel `roles` (Admin, Mentor, Mentee).
    - [ ] Membuat Middleware untuk proteksi route berdasarkan role.
- [ ] **CRUD Data Master:** Membuat fitur Tambah, Baca, Ubah, Hapus untuk:
    - [ ] Fakultas (`faculties`)
    - [ ] Level Mentoring (`levels`)
- [ ] **Manajemen User oleh Admin:**
    - [ ] Halaman untuk melihat semua user dan rolenya.
    - [ ] Fitur untuk import data Mentee (sesuai `README.md`).

### Fase 2: Alur Pementor (Mentor's Journey)
- [ ] **Pendaftaran & Seleksi Mentor:**
    - [ ] Form pendaftaran mentor (`mentor_applications`).
    - [ ] Halaman Admin untuk me-review dan menyetujui pendaftaran.
- [ ] **Dashboard Mentor:**
    - [ ] Halaman utama untuk mentor setelah login.
    - [ ] Menampilkan kelompok mentoring yang dipegang.

### Fase 3: Alur Mentee & Sesi Mentoring
- [ ] **Placement Test:**
    - [ ] Halaman untuk Mentee mengerjakan placement test.
    - [ ] Sistem penilaian dan penentuan `level_id` otomatis.
- [ ] **Manajemen Kelompok:**
    - [ ] Logika untuk pembagian kelompok otomatis.
    - [ ] Halaman Admin untuk melihat dan mengelola kelompok.
- [ ] **Sesi Mentoring:**
    - [ ] Halaman untuk absensi online.
    - [ ] Fitur bagi mentor untuk mengisi laporan progres per pertemuan.

### Fase 4: Dashboard Admin & Laporan
- [ ] **Dashboard Statistik:**
    - [ ] Menampilkan statistik umum (jumlah mentor, mentee, dll).
- [ ] **Laporan & Rekap:**
    - [ ] Halaman untuk rekapitulasi nilai dan kehadiran.
