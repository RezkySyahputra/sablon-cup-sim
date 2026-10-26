-- Skema Basis Data
-- Sistem Informasi Manajemen Pesanan Sablon Cup

CREATE DATABASE IF NOT EXISTS sablon_db;
USE sablon_db;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir', 'produksi') NOT NULL
);

CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(30)
);

CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    jenis VARCHAR(50),
    harga INT NOT NULL DEFAULT 0
);

CREATE TABLE pesanan (
    id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT NOT NULL,
    id_user INT NOT NULL COMMENT 'kasir yang mencatat pesanan',
    tgl_pesan DATE NOT NULL,
    status_produksi ENUM('antre', 'cetak', 'finishing', 'selesai') NOT NULL DEFAULT 'antre',
    total_harga INT NOT NULL DEFAULT 0,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

CREATE TABLE detail_pesanan (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT NOT NULL,
    id_produk INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    subtotal INT NOT NULL DEFAULT 0,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);

CREATE TABLE pembayaran (
    id_bayar INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT NOT NULL,
    jumlah_bayar INT NOT NULL,
    jenis ENUM('dp', 'pelunasan') NOT NULL,
    tgl_bayar DATE NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan)
);

-- Data awal produk
-- Catatan: akun pengguna (admin/kasir/produksi) TIDAK di-insert lewat SQL ini,
-- karena password harus di-hash oleh PHP (password_hash). Jalankan setup_akun.php
-- sekali lewat browser untuk membuat 3 akun contoh secara otomatis.
INSERT INTO produk (nama_produk, jenis, harga) VALUES
('Cup Plastik 16oz Sablon Logo', 'Cup Plastik', 450),
('Cup Plastik 22oz Sablon Logo', 'Cup Plastik', 550),
('Cup Kertas 16oz Sablon Logo', 'Cup Kertas', 600),
('Tutup Cup (Lid) Custom Logo', 'Tutup Cup', 150);
