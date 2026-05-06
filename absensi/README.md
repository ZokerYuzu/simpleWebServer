# 📝 Sistem Absensi Karyawan

Sistem Absensi Karyawan berbasis web yang dirancang khusus untuk memenuhi tugas mata kuliah **Web Server**. Aplikasi ini berjalan pada lingkungan **Linux (WSL Debian/Ubuntu)** menggunakan tumpukan teknologi server modern (LEMP Stack).

![Preview Aplikasi](https://img.shields.io/badge/Status-Completed-success)
![Environment](https://img.shields.io/badge/Environment-WSL_Linux-orange)
![Web Server](https://img.shields.io/badge/Web_Server-Nginx-green)

---

## 🛠️ Teknologi yang Digunakan (LEMP Stack)
Proyek ini dibangun menggunakan:
- **Sistem Operasi:** Debian / Ubuntu (Windows Subsystem for Linux - WSL)
- **Web Server:** Nginx
- **Pemrosesan Backend:** PHP-FPM (Versi 8.x) dengan pendekatan PDO
- **Database:** MariaDB (MySQL)
- **Frontend:** HTML5, CSS3, dan Bootstrap 5 (via CDN)
- **Database Manager:** phpMyAdmin

## ✨ Fitur Utama
1. **Statistik Real-Time:** Menampilkan jumlah total karyawan, yang sudah hadir, sudah pulang, dan belum absen.
2. **Jam Digital:** Jam berjalan secara *real-time* di layar.
3. **Pencatatan Cerdas:** Mencegah karyawan absen lebih dari satu kali dalam sehari, dan memastikan alur absen logis (Masuk -> Pulang).
4. **Riwayat Absensi:** Tabel riwayat absensi khusus hari ini dengan indikator warna status kehadiran.
5. **Keamanan:** Menerapkan `PDO Prepared Statements` untuk mencegah SQL Injection, dan `htmlspecialchars` untuk mencegah serangan XSS.

---

## 🚀 Panduan Instalasi & Penggunaan

Proyek ini telah dilengkapi dengan sebuah file SOP (*Standard Operating Procedure*) yang sangat detail bernama **`CARA_MENJALANKAN.md`**.

Di dalam file tersebut, Anda dapat menemukan:
1. Cara mengunduh dan memasang (*install*) Nginx, PHP-FPM, MariaDB, dan phpMyAdmin di lingkungan WSL yang baru.
2. Cara menyalakan ulang service/server setiap kali komputer di-restart.
3. Cara mengakses website secara lokal.
4. Cara membagikan *(hosting sementara)* web ke internet agar bisa diakses dosen menggunakan **Ngrok**.
5. Cara mereset ulang seluruh data database *(Clear Data)*.

Silakan buka file **`CARA_MENJALANKAN.md`** untuk membaca instruksi selengkapnya!

---

*Dibuat untuk Tugas Mata Kuliah Web Server.*
