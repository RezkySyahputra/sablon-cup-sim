# Panduan Push ke GitHub (Per Anggota)

Supaya dosen bisa melihat siapa mengerjakan apa, **setiap anggota wajib commit dari akun GitHub milik sendiri** — bukan satu orang yang meng-upload semuanya sekaligus.

## 0. Sekali saja — yang bikin repo (1 orang)
1. Buat repository baru di GitHub, beri nama misalnya `sablon-cup-sim`. Jangan dicentang "Add README" (biar tidak bentrok dengan file yang sudah ada).
2. Di folder project ini, jalankan:
   ```
   git init
   git add .
   git commit -m "Setup awal: struktur project, schema database, README"
   git branch -M main
   git remote add origin https://github.com/USERNAME-KAMU/sablon-cup-sim.git
   git push -u origin main
   ```
3. Di GitHub, buka **Settings → Collaborators**, tambahkan 3 anggota lain sebagai collaborator.

## 1. Struktur branch per modul (wajib untuk Tugas 5)
Setiap anggota membuat branch sendiri sesuai modul yang menjadi tanggung jawabnya (lihat tabel kepemilikan modul di README.md), lalu membuka Pull Request ke `main`:

| Branch | Pemilik | Modul |
|---|---|---|
| `main` | — | Versi stabil/final gabungan seluruh modul |
| `modul-autentikasi` | Dominicus Luis F. Saceira | Login, manajemen peran, dashboard |
| `modul-data-master` | Muh Rezky Syahputra | Kelola pelanggan & produk |
| `modul-transaksi` | Imbran Doni Muar | Pesanan & pembayaran |
| `modul-laporan` | Iron Djerwi | Status produksi & laporan pendapatan |

```
git checkout -b modul-nama-kamu
# ... edit file bagianmu ...
git add .
git commit -m "Implementasi modul <nama modul>: <deskripsi singkat>"
git push -u origin modul-nama-kamu
```
Lalu buka **Pull Request** di GitHub dari branch tersebut ke `main` supaya ada histori review sebelum digabung.

## 2. Tiap anggota — clone & atur identitas sendiri
```
git clone https://github.com/USERNAME-PEMBUAT-REPO/sablon-cup-sim.git
cd sablon-cup-sim
git config user.name "Nama Lengkap Kamu"
git config user.email "email-akun-github-kamu@email.com"
```

## 3. Kalau terjadi conflict
```
git pull
```
Cari tanda `<<<<<<<`, `=======`, `>>>>>>>` di file yang bentrok, gabungkan versi yang benar, lalu:
```
git add nama_file.php
git commit -m "Resolve conflict di nama_file.php"
git push
```

## 4. Sebelum dikumpulkan
- Cek tab **Commits** — pastikan 4 nama kontributor muncul.
- Cek tab **Branches** — pastikan branch per modul ada (boleh sudah di-merge ke main).
- Cantumkan link repository di dokumen Tugas 5.
