-- =====================================================================
-- TUGAS 6 - IMPLEMENTASI BASIS DATA FINAL
-- Proyek : Sistem Informasi Manajemen Pesanan Sablon Plastik Cup Kertas
-- Klien  : Sablon Plastik Cup Kertas (Bapak Yaafi Ramadan, A.Md.)
-- Kelompok 3 - Kelas 7A1 - MK54 Proyek Sistem Informasi Terapan
-- DBMS   : MySQL / MariaDB (XAMPP / Laragon) - dijalankan lewat phpMyAdmin
-- =====================================================================
-- Kepemilikan tabel:
--   users                    -> Dominicus Luis Filipe Saceira (23083000033)
--   pelanggan, produk        -> Muh Rezky Syahputra          (23083000023)
--   pesanan, detail_pesanan  -> Imbran Doni Muar             (23083000034)
--   pembayaran, view laporan -> Iron Djerwi                  (23083000011)
-- =====================================================================
-- [AI] prompt: "Oke lanjut tugas 6" - berdasarkan instruksi Classroom Tugas 6
-- dan schema.sql dari Tugas 4. Seluruh skrip ini disusun dengan bantuan AI,
-- lalu direviu dan dijalankan oleh anggota pemilik tiap blok tabel.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS sablon_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE sablon_db;

-- Hapus tabel dari anak ke induk agar skrip bisa dijalankan ulang.
SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW  IF EXISTS v_pendapatan_harian;
DROP VIEW  IF EXISTS v_omzet_produk;
DROP TRIGGER IF EXISTS trg_detail_pesanan_bi;
DROP TABLE IF EXISTS pembayaran;
DROP TABLE IF EXISTS detail_pesanan;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;


-- =====================================================================
-- TABEL: users
-- PIC   : Dominicus Luis Filipe Saceira (23083000033)
-- Modul : Autentikasi dan Manajemen Peran
-- =====================================================================
-- [AI] prompt: "Buat CREATE TABLE users dengan PK, UNIQUE username, dan ENUM
-- role admin/kasir/produksi sesuai ERD Tugas 5"
-- Penjelasan PIC (Luis):
-- 1. username dibuat UNIQUE supaya tidak ada dua akun dengan username sama, jadi login selalu menunjuk ke satu pengguna.
-- 2. password bertipe VARCHAR(255) karena yang disimpan adalah hash dari password_hash() PHP (bcrypt, 60 karakter), bukan password asli. Panjang 255 agar tetap muat bila algoritma bawaan PHP berubah.
-- 3. role memakai ENUM('admin','kasir','produksi') agar hanya tiga peran itu yang bisa tersimpan. Hak akses halaman dicek dari kolom ini.
-- 4. users adalah induk bagi pesanan lewat kolom id_user (kasir yang mencatat pesanan). FK-nya RESTRICT, jadi user yang sudah punya pesanan tidak bisa dihapus.
CREATE TABLE users (
    id_user   INT AUTO_INCREMENT,
    nama      VARCHAR(100) NOT NULL,
    username  VARCHAR(50)  NOT NULL,
    password  VARCHAR(255) NOT NULL COMMENT 'hash bcrypt dari password_hash() PHP',
    role      ENUM('admin', 'kasir', 'produksi') NOT NULL,
    CONSTRAINT pk_users          PRIMARY KEY (id_user),
    CONSTRAINT uq_users_username UNIQUE (username)
) ENGINE=InnoDB;

-- Data awal users (password semua akun contoh: password123, sudah di-hash)
INSERT INTO users (nama, username, password, role) VALUES
('Pemilik Usaha',      'admin',     '$2y$10$w0N/xQdnWod.zHqeSP9kluE.Ofr2slZflCpg4dI1F5LOq90S4BjHy', 'admin'),
('Kasir Toko Pagi',    'kasir1',    '$2y$10$w0N/xQdnWod.zHqeSP9kluE.Ofr2slZflCpg4dI1F5LOq90S4BjHy', 'kasir'),
('Kasir Toko Sore',    'kasir2',    '$2y$10$w0N/xQdnWod.zHqeSP9kluE.Ofr2slZflCpg4dI1F5LOq90S4BjHy', 'kasir'),
('Petugas Cetak',      'produksi1', '$2y$10$w0N/xQdnWod.zHqeSP9kluE.Ofr2slZflCpg4dI1F5LOq90S4BjHy', 'produksi'),
('Petugas Finishing',  'produksi2', '$2y$10$w0N/xQdnWod.zHqeSP9kluE.Ofr2slZflCpg4dI1F5LOq90S4BjHy', 'produksi');


-- =====================================================================
-- TABEL: pelanggan
-- PIC   : Muh Rezky Syahputra (23083000023)
-- Modul : Data Master Pelanggan dan Produk
-- =====================================================================
-- [AI] prompt: "Buat CREATE TABLE pelanggan dengan PK dan nama NOT NULL"
CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT,
    nama         VARCHAR(100) NOT NULL,
    no_hp        VARCHAR(30),
    CONSTRAINT pk_pelanggan PRIMARY KEY (id_pelanggan)
) ENGINE=InnoDB;

INSERT INTO pelanggan (nama, no_hp) VALUES
('Kedai Kopi Senja',      '081234560001'),
('Es Teh Pak Jono',       '081234560002'),
('Warung Boba Kita',      '081234560003'),
('Dapur Bu Siti',         '081234560004'),
('Kafe Ruang Temu',       '081234560005'),
('Toko Minuman Segar',    '081234560006');


-- =====================================================================
-- TABEL: produk
-- PIC   : Muh Rezky Syahputra (23083000023)
-- Modul : Data Master Pelanggan dan Produk
-- =====================================================================
-- [AI] prompt: "Buat CREATE TABLE produk dengan PK, harga NOT NULL, dan
-- CHECK harga tidak boleh negatif"
CREATE TABLE produk (
    id_produk   INT AUTO_INCREMENT,
    nama_produk VARCHAR(100) NOT NULL,
    jenis       VARCHAR(50),
    harga       INT NOT NULL DEFAULT 0 COMMENT 'harga satuan (Rp) per pcs',
    CONSTRAINT pk_produk       PRIMARY KEY (id_produk),
    CONSTRAINT ck_produk_harga CHECK (harga >= 0)
) ENGINE=InnoDB;

INSERT INTO produk (nama_produk, jenis, harga) VALUES
('Cup Plastik 16oz Sablon Logo', 'Cup Plastik', 450),
('Cup Plastik 22oz Sablon Logo', 'Cup Plastik', 550),
('Cup Kertas 16oz Sablon Logo',  'Cup Kertas',  600),
('Tutup Cup (Lid) Custom Logo',  'Tutup Cup',   150),
('Cup Kertas 8oz Sablon Logo',   'Cup Kertas',  400),
('Cup Plastik 12oz Sablon Logo', 'Cup Plastik', 400);


-- =====================================================================
-- TABEL: pesanan
-- PIC   : Imbran Doni Muar (23083000034)
-- Modul : Transaksi Pesanan dan Pembayaran
-- Relasi: FK ke pelanggan (RESTRICT) dan users/kasir (RESTRICT)
-- =====================================================================
-- [AI] prompt: "Buat CREATE TABLE pesanan dengan FK ke pelanggan dan users,
-- status_produksi ENUM bertahap, dan total_harga tidak boleh negatif"
CREATE TABLE pesanan (
    id_pesanan      INT AUTO_INCREMENT,
    id_pelanggan    INT  NOT NULL,
    id_user         INT  NOT NULL COMMENT 'kasir yang mencatat pesanan',
    tgl_pesan       DATE NOT NULL,
    status_produksi ENUM('antre', 'cetak', 'finishing', 'selesai') NOT NULL DEFAULT 'antre',
    total_harga     INT  NOT NULL DEFAULT 0,
    CONSTRAINT pk_pesanan           PRIMARY KEY (id_pesanan),
    CONSTRAINT ck_pesanan_total     CHECK (total_harga >= 0),
    CONSTRAINT fk_pesanan_pelanggan FOREIGN KEY (id_pelanggan)
        REFERENCES pelanggan (id_pelanggan) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_pesanan_user      FOREIGN KEY (id_user)
        REFERENCES users (id_user) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO pesanan (id_pelanggan, id_user, tgl_pesan, status_produksi, total_harga) VALUES
(1, 2, '2026-10-05', 'selesai',   600000),
(2, 2, '2026-10-08', 'selesai',   300000),
(3, 3, '2026-10-12', 'finishing', 560000),
(4, 3, '2026-10-15', 'cetak',     240000),
(5, 2, '2026-10-20', 'antre',     625000),
(1, 3, '2026-10-24', 'antre',     600000);


-- =====================================================================
-- TABEL: detail_pesanan
-- PIC   : Imbran Doni Muar (23083000034)
-- Modul : Transaksi Pesanan dan Pembayaran
-- Relasi: FK ke pesanan (CASCADE) dan produk (RESTRICT)
-- =====================================================================
-- [AI] prompt: "Buat CREATE TABLE detail_pesanan; baris detail ikut terhapus
-- bila pesanan dihapus (ON DELETE CASCADE), produk yang sudah dipesan tidak
-- boleh dihapus (RESTRICT), qty harus lebih dari 0"
CREATE TABLE detail_pesanan (
    id_detail  INT AUTO_INCREMENT,
    id_pesanan INT NOT NULL,
    id_produk  INT NOT NULL,
    qty        INT NOT NULL DEFAULT 1,
    subtotal   INT NOT NULL DEFAULT 0,
    CONSTRAINT pk_detail_pesanan  PRIMARY KEY (id_detail),
    CONSTRAINT ck_detail_qty      CHECK (qty > 0),
    CONSTRAINT ck_detail_subtotal CHECK (subtotal >= 0),
    CONSTRAINT fk_detail_pesanan  FOREIGN KEY (id_pesanan)
        REFERENCES pesanan (id_pesanan) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detail_produk   FOREIGN KEY (id_produk)
        REFERENCES produk (id_produk) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO detail_pesanan (id_pesanan, id_produk, qty, subtotal) VALUES
(1, 1, 1000, 450000),
(1, 4, 1000, 150000),
(2, 3,  500, 300000),
(3, 2,  800, 440000),
(3, 4,  800, 120000),
(4, 5,  600, 240000),
(5, 6, 1000, 400000),
(5, 1,  500, 225000),
(6, 3, 1000, 600000);

-- Trigger: subtotal selalu dihitung otomatis = qty x harga produk saat ini,
-- sehingga tidak bisa diisi nilai yang tidak konsisten dari luar.
-- (Di phpMyAdmin: blok DELIMITER ini bisa dijalankan langsung lewat tab Import.)
-- [AI] prompt: "Buat trigger BEFORE INSERT pada detail_pesanan yang mengisi
-- subtotal dari qty dikali harga di tabel produk"
DELIMITER $$
CREATE TRIGGER trg_detail_pesanan_bi
BEFORE INSERT ON detail_pesanan
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.qty * (SELECT harga FROM produk WHERE id_produk = NEW.id_produk);
END$$
DELIMITER ;


-- =====================================================================
-- TABEL: pembayaran
-- PIC   : Iron Djerwi (23083000011)
-- Modul : Status Produksi dan Laporan (sumber data laporan pendapatan)
-- Relasi: FK ke pesanan (RESTRICT: riwayat pembayaran tidak boleh hilang)
-- =====================================================================
-- [AI] prompt: "Buat CREATE TABLE pembayaran dengan FK ke pesanan, jenis ENUM
-- dp/pelunasan, jumlah_bayar harus lebih dari 0"
CREATE TABLE pembayaran (
    id_bayar     INT AUTO_INCREMENT,
    id_pesanan   INT  NOT NULL,
    jumlah_bayar INT  NOT NULL,
    jenis        ENUM('dp', 'pelunasan') NOT NULL,
    tgl_bayar    DATE NOT NULL,
    CONSTRAINT pk_pembayaran       PRIMARY KEY (id_bayar),
    CONSTRAINT ck_pembayaran_jumlah CHECK (jumlah_bayar > 0),
    CONSTRAINT fk_pembayaran_pesanan FOREIGN KEY (id_pesanan)
        REFERENCES pesanan (id_pesanan) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO pembayaran (id_pesanan, jumlah_bayar, jenis, tgl_bayar) VALUES
(1, 300000, 'dp',        '2026-10-05'),
(1, 300000, 'pelunasan', '2026-10-10'),
(2, 150000, 'dp',        '2026-10-08'),
(2, 150000, 'pelunasan', '2026-10-13'),
(3, 280000, 'dp',        '2026-10-12'),
(4, 120000, 'dp',        '2026-10-15'),
(5, 300000, 'dp',        '2026-10-20'),
(6, 300000, 'dp',        '2026-10-24');


-- =====================================================================
-- VIEW LAPORAN
-- PIC   : Iron Djerwi (23083000011)
-- Modul : Status Produksi dan Laporan
-- =====================================================================
-- [AI] prompt: "Buat view laporan pendapatan harian dari tabel pembayaran dan
-- view omzet per produk dari detail_pesanan"
CREATE VIEW v_pendapatan_harian AS
SELECT tgl_bayar, COUNT(*) AS jumlah_transaksi, SUM(jumlah_bayar) AS total_pendapatan
FROM pembayaran
GROUP BY tgl_bayar;

CREATE VIEW v_omzet_produk AS
SELECT pr.id_produk, pr.nama_produk, pr.jenis,
       SUM(dp.qty) AS total_qty, SUM(dp.subtotal) AS total_omzet
FROM detail_pesanan dp
JOIN produk pr ON pr.id_produk = dp.id_produk
GROUP BY pr.id_produk, pr.nama_produk, pr.jenis;

-- Pemeriksaan cepat setelah skrip dijalankan (jumlah baris per tabel)
SELECT 'users' AS tabel, COUNT(*) AS jumlah_baris FROM users
UNION ALL SELECT 'pelanggan',      COUNT(*) FROM pelanggan
UNION ALL SELECT 'produk',         COUNT(*) FROM produk
UNION ALL SELECT 'pesanan',        COUNT(*) FROM pesanan
UNION ALL SELECT 'detail_pesanan', COUNT(*) FROM detail_pesanan
UNION ALL SELECT 'pembayaran',     COUNT(*) FROM pembayaran;
