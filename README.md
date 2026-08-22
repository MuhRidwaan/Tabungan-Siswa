# 🎒 Sistem Informasi Manajemen Tabungan Siswa (SIP-Tabungan)

![CodeIgniter 4](https://img.shields.io/badge/Framework-CodeIgniter%204.6-orange?style=for-the-badge&logo=codeigniter)
![PHP 8.2](https://img.shields.io/badge/Language-PHP%208.2-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/Database-MySQL-blue?style=for-the-badge&logo=mysql)
![AdminLTE 3](https://img.shields.io/badge/UI-AdminLTE%203-green?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-brightgreen?style=for-the-badge)

**SIP-Tabungan** adalah aplikasi manajemen tabungan siswa berbasis web yang dirancang khusus untuk sekolah/madrasah guna mengelola setoran, penarikan, pembagian tabungan akhir tahun ajaran, alokasi komisi/bagi hasil admin kas (Sekolah & Guru), serta laporan siap cetak secara transparan, akuntabel, dan modern.

---

## 🌟 Fitur Utama & Modul Sistem

### 1. 📊 Dashboard Analitis & Statistik Real-Time
* **Ringkasan Kas Sekolah**: Total pemasokan setor, total penarikan, saldo kas bersih, dan rincian alokasi biaya admin.
* **Informasi Rombel & Siswa**: Menampilkan jumlah siswa aktif, total kelas/rombel per Tahun Ajaran aktif, dan grafik transaksi bulanan.

### 2. 👨‍🎓 Manajemen Data Siswa
* **Pengelolaan Lengkap**: Pencatatan NIS, Nama Lengkap, Jenis Kelamin, Tanggal Lahir, Alamat, dan Status Siswa (`Aktif`, `Lulus`, `Pindah`, `Non-Aktif`).
* **Filtering Cepat**: Menyaring data siswa berdasarkan Kelas, Tahun Ajaran, dan Status Siswa.
* **Import & Export Excel (.xls)**:
  * Fitur unggah massal via file Excel.
  * Fitur unduh template *Multi-Sheet* yang dilengkapi Sheet Referensi Nama Kelas Valid.

### 3. 🏫 Manajemen Data Kelas & Tahun Ajaran
* **Pengikatan Kelas ke Tahun Ajaran (`tahun_ajaran_id`)**: Setiap kelas terikat secara spesifik pada Tahun Ajaran pembuatannya.
* **Penempatan & Kenaikan Kelas Massal (`/manajemen-kelas`)**: Alat interaktif untuk menempatkan siswa baru ke kelas atau memproses kenaikan/kelulusan kelas massal dari tahun ke tahun.

### 4. 💵 Modul Transaksi Instan & Kolektif
* **Transaksi Instan (Setor / Tarik)**: Pencatatan cepat dilengkapi pembuatan nomor transaksi unik otomatis (`TRX-YYYYMMDD-XXXX`) dan cetak struk kwitansi thermal.
* **Setor / Tarik Kolektif**: Memudahkan guru/wali kelas memasukkan nominal tabungan satu kelas sekaligus dalam satu halaman.
* **Setor Multi-Tanggal**: Memfasilitasi rekapitulasi setoran dari beberapa tanggal yang terlewat.

### 5. 🎓 Modul Penarikan Tabungan Akhir Tahun Ajaran (`/transaksi/akhir-tahun`)
* **Pembagian Tabungan Lunas Per-Siswa**: Penarikan seluruh sisa saldo tabungan siswa pada akhir tahun ajaran (Saldo menjadi Rp 0).
* **Otomatisasi Status Kelulusan**: Siswa yang telah ditarik lunas tabungannya secara otomatis diperbarui statusnya menjadi `Lulus`, sehingga tidak lagi mengotori form transaksi aktif sehari-hari.
* **Bagi Hasil Alokasi Biaya Admin Kas**: Otomatis menghitung dan mencatat potongan alokasi komisi untuk **Sekolah (%)** dan **Guru / Wali Kelas (%)**.
* **Eksekusi Kelulusan Massal**: Tombol 1-klik untuk menetapkan seluruh siswa di suatu kelas menjadi status `Lulus`.

### 6. 🖨️ Modul Laporan & Siap Cetak (`/laporan`)
* **Penyaringan Berkelanjutan**: Filter laporan berdasarkan Periode Tanggal, Kelas, dan Tahun Ajaran.
* **Visual Siap Cetak (Print Stylesheet `@media print`)**: Secara otomatis menyembunyikan sidebar, navbar, dan tombol web UI saat dicetak atau disimpan sebagai PDF.
* **Kop Surat Resmi & Blok Tanda Tangan**: Dilengkapi Kop Surat Sekolah resmi dan kolom pengesahan Tanda Tangan Kepala Sekolah & Bendahara/Pengelola Tabungan.
* **Export Excel**: Fitur unduh laporan ke format Excel lengkap dengan rincian alokasi komisi sekolah dan guru.

### 7. ⚙️ Pengaturan Komisi Admin & Profil
* **Persentase Bagi Hasil**: Pengaturan fleksibel untuk persentase alokasi komisi guru (contoh: 1.0%) dan sekolah (contoh: 1.5%).
* **Manajemen Profil Pengguna**: Update username, email, password, dan foto profil pengguna.

### 8. 🎨 Halaman Login Ultra Modern & Animated Greeting
* **Desain Glassmorphism**: Tampilan modern berlatar *animated gradient mesh* dengan font *Plus Jakarta Sans*.
* **Fullscreen Welcome Overlay**: Animasi sapaan ramah berbasis waktu (*Selamat Pagi / Siang / Sore / Malam*) dan **Kartu Kata-Kata Semangat Motivasi Inspiratif** (4 detik jeda sebelum diarahkan masuk ke Dashboard).

---

## 🗄️ Struktur Tabel Utama Database

| Nama Tabel | Deskripsi |
| :--- | :--- |
| `users` & `auth_identities` | Tabel autentikasi pengguna bawaan CodeIgniter Shield |
| `pengguna` | Data profil tambahan pengguna (Guru, Wali Kelas, Admin) |
| `tahun_ajaran` | Master data Tahun Ajaran (contoh: 2024/2025, 2025/2026) |
| `kelas` | Master data Kelas yang terikat pada `tahun_ajaran_id` |
| `siswa` | Data siswa, saldo akhir, dan `status_siswa` (`aktif`, `lulus`, `pindah`, `nonaktif`) |
| `riwayat_kelas_siswa` | Tabel pivot penempatan siswa pada kelas & tahun ajaran tertentu |
| `transaksi_tabungan` | Log transaksi setor dan tarik tabungan siswa |
| `alokasi_biaya_admin` | Rincian pembagian nominal komisi bagi hasil untuk Sekolah dan Guru |
| `pengaturan` | Konfigurasi sistem (Nama Sekolah, Alamat, % Komisi) |

---

## 🚀 Panduan Instalasi & Run Local

### 1. Persyaratan Sistem
* PHP versi `>= 8.1` (Disarankan PHP 8.2)
* Extension PHP yang wajib aktif: `intl`, `mbstring`, `mysqli`, `curl`, `json`
* Database Engine: MySQL / MariaDB (Laragon / XAMPP)
* Composer

### 2. Langkah-Langkah Instalasi
1. **Clone Repository**:
   ```bash
   git clone https://github.com/MuhRidwaan/Tabungan-Siswa.git
   cd tabungan_siswa
   ```

2. **Instalasi Dependency Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin file `env` menjadi `.env`, lalu atur konfigurasi database Anda:
   ```ini
   database.default.hostname = 127.0.0.1
   database.default.database = db_tabungan_siswa
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   ```

4. **Jalankan Migrasi Database**:
   ```bash
   php spark migrate
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php spark serve
   ```
   Atau buka via web server Laragon: `http://127.0.0.1/tabungan_siswa/public/`

---

## 📄 Lisensi

Dikembangkan oleh **Google DeepMind Advanced Agentic Coding Team** bersama **MuhRidwaan**. Hak Cipta &copy; <?= date('Y') ?>. Bebas digunakan dan dikembangkan untuk kepentingan pendidikan.
