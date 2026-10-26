# Sistem Informasi Manajemen Pesanan Sablon Plastik Cup Kertas
Tugas Mata Kuliah Proyek Sistem Informasi Terapan (MK54) — Universitas Merdeka Malang

## Cara Menjalankan
1. Install XAMPP atau Laragon (PHP + MySQL).
2. Buat database dengan mengimpor `schema.sql` lewat phpMyAdmin.
3. Sesuaikan kredensial di `config/koneksi.php` jika perlu (default: user `root`, password kosong).
4. Salin folder ini ke `htdocs` (XAMPP) atau `www` (Laragon), lalu buka `http://localhost/sablon-app/setup_akun.php` SATU KALI untuk membuat 3 akun contoh.
5. **Setelah akun berhasil dibuat, hapus file `setup_akun.php` dari server** (jangan dibiarkan aktif karena bisa disalahgunakan).
6. Akses aplikasi: `http://localhost/sablon-app/login.php`

## Akun Contoh (dibuat lewat setup_akun.php)
| Username | Password | Peran |
|---|---|---|
| admin | password123 | Admin/Owner — kelola produk, lihat laporan, akses semua modul |
| kasir1 | password123 | Kasir — kelola pelanggan, buat pesanan, catat pembayaran |
| produksi1 | password123 | Petugas Produksi — update status produksi pesanan |

**Segera ganti password default ini** setelah sistem diserahkan ke klien.

## Struktur Fitur (sesuai Ruang Lingkup di Project Charter)
- `login.php` / `logout.php` — otentikasi dengan 3 peran pengguna
- `pelanggan.php` — kelola data pelanggan (admin, kasir)
- `produk.php` — kelola data produk & harga (admin)
- `pesanan.php` — buat pesanan baru (multi-item) + catat DP (admin, kasir)
- `produksi.php` — update status produksi: antre → cetak → finishing → selesai (admin, produksi)
- `pembayaran.php` — catat pelunasan sisa tagihan (admin, kasir)
- `laporan.php` — laporan pendapatan & omzet per jenis produk per periode (admin)

## Catatan
Kode ini adalah implementasi dasar sesuai ruang lingkup pada project charter. Belum diuji langsung di server PHP+MySQL sungguhan (dites di komputer/laptop kalian sendiri) — kalau ada error saat dicoba, kirim pesan errornya untuk dibantu perbaiki.
