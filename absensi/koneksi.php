<?php
/**
 * koneksi.php
 * Manajemen koneksi database menggunakan PDO (aman dari SQL Injection).
 */

// Set Timezone ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'db_absensi');
define('DB_USER', 'absensi');       // User MariaDB
define('DB_PASS', 'rahasia123');    // Password MariaDB
define('DB_CHARSET', 'utf8mb4');

/**
 * Mengembalikan instance PDO (singleton sederhana).
 *
 * @return PDO
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,  // Prepared statement asli
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Tampilkan pesan ramah; sembunyikan detail di produksi
            http_response_code(500);
            die('<div style="font-family:sans-serif;padding:2rem;color:#c0392b;">
                    <h2>⚠️ Koneksi Database Gagal</h2>
                    <p>Pastikan MariaDB/MySQL sudah berjalan dan konfigurasi di <code>koneksi.php</code> sudah benar.</p>
                    <pre style="background:#f8d7da;padding:1rem;border-radius:6px;">' .
                    htmlspecialchars($e->getMessage()) . '</pre>
                 </div>');
        }
    }

    return $pdo;
}
