-- =============================================================
--  SISTEM ABSENSI KARYAWAN
--  Database: db_absensi
-- =============================================================

CREATE DATABASE IF NOT EXISTS db_absensi
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_absensi;

-- -------------------------------------------------------------
-- Tabel: karyawan
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS karyawan (
    id        INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nama      VARCHAR(100)    NOT NULL,
    jabatan   VARCHAR(100)    NOT NULL,
    created_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Tabel: absensi
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS absensi (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    karyawan_id   INT UNSIGNED    NOT NULL,
    tanggal       DATE            NOT NULL,
    jam_masuk     TIME            NULL DEFAULT NULL,
    jam_pulang    TIME            NULL DEFAULT NULL,
    created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- Satu karyawan hanya boleh memiliki 1 record per hari
    UNIQUE KEY uq_karyawan_tanggal (karyawan_id, tanggal),
    CONSTRAINT fk_absensi_karyawan
        FOREIGN KEY (karyawan_id) REFERENCES karyawan (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Data Awal: Karyawan
-- -------------------------------------------------------------
INSERT INTO karyawan (nama, jabatan) VALUES
    ('Budi Santoso',    'Manager'),
    ('Siti Rahayu',     'Staff IT'),
    ('Ahmad Fauzi',     'Staff Keuangan'),
    ('Dewi Lestari',    'HRD'),
    ('Rizky Pratama',   'Staff Marketing');
