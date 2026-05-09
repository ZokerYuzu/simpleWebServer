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

## 🚀 Cara Menjalankan Proyek

Untuk menjalankan proyek ini secara rutin (setelah instalasi awal selesai), Anda perlu menghidupkan beberapa *service* di terminal Linux (WSL):

### 1. Jalankan Service Server
Buka terminal Debian/Ubuntu (WSL) Anda dan jalankan perintah berikut untuk menghidupkan web server (Nginx), database (MariaDB), dan pemroses PHP (PHP-FPM):
```bash
sudo service nginx start
sudo service mariadb start
sudo service php8.2-fpm start
```
*(Catatan: Sesuaikan `8.2` dengan versi PHP di sistem Anda).*

### 2. Cek IP Address Lokal
Ketik perintah ini untuk mengetahui alamat IP Linux Anda:
```bash
hostname -I
```
*Contoh output: `172.25.10.5`*

### 3. Akses Website
Buka browser di Windows Anda, lalu ketikkan alamat IP yang didapat pada langkah ke-2:
```text
http://172.25.10.5
```
*(Tambahkan `/phpmyadmin` di akhir URL jika ingin mengakses database manager).*

### ⚠️ Penting: Sinkronisasi Kode
Karena Anda mengedit file di folder Windows, sedangkan Nginx membaca file dari folder `/var/www/absensi` di Linux, maka setiap ada perubahan kode PHP/HTML, Anda **wajib** menyalinnya ulang dengan perintah ini di WSL:
```bash
sudo cp /mnt/c/xampp/htdocs/webServer/absensi/*.php /var/www/absensi/
```

> **Panduan Lengkap:** Untuk instalasi dari awal (install Nginx, MariaDB, dll), setup database pertama kali, atau cara membagikan web ke internet untuk presentasi menggunakan **Ngrok**, silakan baca selengkapnya pada file **[`CARA_MENJALANKAN.md`](CARA_MENJALANKAN.md)**.

---

*Dibuat untuk Tugas Mata Kuliah Web Server.*
