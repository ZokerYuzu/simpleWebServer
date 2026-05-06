# 🚀 Panduan Menjalankan Web Absensi (Debian/Ubuntu WSL)

File ini adalah panduan lengkap mulai dari tahap instalasi awal hingga langkah harian menjalankan tugas web server ini.

---

## Langkah 0: Instalasi Awal & Download (Hanya Dilakukan Sekali)
Jika Anda menggunakan komputer baru atau Ubuntu/Debian Anda baru saja di-reset, jalankan perintah-perintah ini untuk mendownload semua software yang dibutuhkan:

**1. Install Nginx, MariaDB, PHP, dan Wget**
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mariadb-server php-fpm php-mysql wget
```

**2. Setup Awal File & Database**
```bash
sudo mkdir -p /var/www/absensi
sudo cp /mnt/c/xampp/htdocs/webServer/absensi/*.php /var/www/absensi/
sudo chown -R www-data:www-data /var/www/absensi
sudo mysql -u root < /mnt/c/xampp/htdocs/webServer/absensi/database.sql
```

**3. Download & Pasang phpMyAdmin**
```bash
cd /var/www/absensi
sudo wget -qO phpmyadmin.tar.gz https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-all-languages.tar.gz
sudo tar xf phpmyadmin.tar.gz
sudo mv phpMyAdmin-5.2.1-all-languages phpmyadmin
sudo rm phpmyadmin.tar.gz
```

---

## Langkah 1: Nyalakan Service Server di Linux
Setiap kali laptop di-restart, server web biasanya dalam keadaan mati.
1. Buka aplikasi **Debian** (atau Ubuntu) dari Start Menu Windows.
2. Jalankan perintah berikut untuk menghidupkan Nginx, Database (MariaDB), dan PHP:
   ```bash
   sudo service nginx start
   sudo service mariadb start
   sudo service php8.2-fpm start
   ```
   *(Catatan: Sesuaikan `8.2` dengan versi PHP di sistem Anda).*

## Langkah 2: Cek IP Linux Anda
IP Address Linux WSL selalu berubah setiap kali laptop dinyalakan ulang.
Di terminal Linux, ketik:
```bash
hostname -I
```
*Anda akan melihat deretan angka, misalnya `172.25.10.5`. Ini adalah IP server Anda hari ini.*

## Langkah 3: Akses Secara Lokal (Hanya di Laptop Anda)
Jika Anda hanya ingin mengecek sendiri, Anda bisa langsung membuka browser di Windows dan mengetikkan angka IP tadi:
```text
http://172.25.10.5
```
*(Web Absensi akan langsung terbuka).*

---

## Langkah 4: Online-kan dengan Ngrok (Untuk Demo ke Dosen/Teman)
Jika Anda butuh link publik yang bisa diakses siapa saja:
1. Buka **File Explorer** Windows, cari folder tempat file `ngrok.exe` berada.
2. Klik bagian kosong di *Address Bar* (kolom alamat folder) di atas, ketik `cmd`, lalu tekan **Enter**.
3. Di layar hitam CMD yang muncul, jalankan perintah ngrok dengan IP yang Anda dapat di Langkah 2:
   ```cmd
   ngrok http 172.25.10.5:80
   ```
4. Ngrok akan memberikan Anda link yang berakhiran `ngrok-free.app`. Bagikan link tersebut!
*(Jika ada layar peringatan anti-phishing saat web dibuka, cukup klik "Visit Site").*

---

## ⚠️ PENTING: Jika Anda Mengedit Kode PHP/HTML
Ingat, Anda mengedit file di folder Windows (`C:\xampp\...`), sedangkan Nginx membaca file dari folder Linux (`/var/www/absensi`). 

Jika Anda melakukan perubahan kode di text editor Windows, **Anda wajib mencopy file tersebut ke Linux** agar perubahannya terlihat di website:
```bash
# Copy semua file PHP yang baru diedit ke server Nginx
sudo cp /mnt/c/xampp/htdocs/webServer/absensi/*.php /var/www/absensi/
```

## 🛠️ Cara Mengakses phpMyAdmin
Pastikan Nginx dan MariaDB sedang menyala (Langkah 1). Lalu tambahkan `/phpmyadmin` di akhir link Anda:
- Akses Lokal: `http://172.25.10.5/phpmyadmin`
- Akses Online: `https://link-ngrok-anda.ngrok-free.app/phpmyadmin`

**Login dengan kredensial yang kita buat:**
- **User:** `absensi`
- **Pass:** `rahasia123`

## 🧹 Cara Membersihkan Data Absen (Reset)
Jika Anda ingin mengosongkan tabel riwayat absen untuk keperluan presentasi ulang, jalankan perintah ini di Terminal WSL:
```bash
sudo mysql -u root -D db_absensi -e "DELETE FROM absensi; ALTER TABLE absensi AUTO_INCREMENT = 1;"
```
*(Atau Anda bisa membuka phpMyAdmin, klik tabel `absensi`, lalu klik menu **Kosongkan/Empty**).*
