<?php
require 'config/auth.php';
wajib_login();
require 'config/koneksi.php';

// Statistik ringkas untuk dashboard
$total_pelanggan = (int) $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$total_pesanan = (int) $pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$pesanan_proses = (int) $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_produksi <> 'selesai'")->fetchColumn();

$bulan_ini = date('Y-m');
$stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayaran WHERE DATE_FORMAT(tgl_bayar, '%Y-%m') = ?");
$stmt->execute([$bulan_ini]);
$pendapatan_bulan_ini = (int) $stmt->fetchColumn();

// 5 pesanan terbaru
$pesanan_terbaru = $pdo->query(
    "SELECT p.id_pesanan, pl.nama AS nama_pelanggan, p.tgl_pesan, p.status_produksi, p.total_harga
     FROM pesanan p
     JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
     ORDER BY p.id_pesanan DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$label_status = [
    'antre' => 'Antre',
    'cetak' => 'Cetak',
    'finishing' => 'Finishing',
    'selesai' => 'Selesai',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1>Sistem Informasi Manajemen Pesanan Sablon Plastik Cup Kertas</h1>
            <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
        </div>
        <a href="logout.php">Keluar</a>
    </div>

    <div class="stats">
        <div class="statcard">
            <div class="statnum"><?= $total_pesanan ?></div>
            <div class="statlbl">Total Pesanan</div>
        </div>
        <div class="statcard">
            <div class="statnum"><?= $pesanan_proses ?></div>
            <div class="statlbl">Pesanan Dalam Proses</div>
        </div>
        <div class="statcard">
            <div class="statnum"><?= $total_pelanggan ?></div>
            <div class="statlbl">Total Pelanggan</div>
        </div>
        <div class="statcard">
            <div class="statnum">Rp<?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></div>
            <div class="statlbl">Pendapatan Bulan Ini</div>
        </div>
    </div>

    <div class="menu">
        <?php if (in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
            <a href="pelanggan.php" class="card">Kelola Pelanggan</a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
            <a href="produk.php" class="card">Kelola Produk</a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
            <a href="pesanan.php" class="card">Pesanan Baru</a>
            <a href="pembayaran.php" class="card">Pembayaran</a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['role'], ['admin', 'produksi'])): ?>
            <a href="produksi.php" class="card">Status Produksi</a>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="laporan.php" class="card">Laporan Pendapatan</a>
        <?php endif; ?>
    </div>

    <h3>Pesanan Terbaru</h3>
    <table>
        <tr><th>No</th><th>Pelanggan</th><th>Tgl Pesan</th><th>Status</th><th>Total</th></tr>
        <?php if (empty($pesanan_terbaru)): ?>
        <tr><td colspan="5">Belum ada pesanan.</td></tr>
        <?php endif; ?>
        <?php foreach ($pesanan_terbaru as $ps): ?>
        <tr>
            <td>#<?= $ps['id_pesanan'] ?></td>
            <td><?= htmlspecialchars($ps['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($ps['tgl_pesan']) ?></td>
            <td><span class="status-badge status-<?= htmlspecialchars($ps['status_produksi']) ?>"><?= $label_status[$ps['status_produksi']] ?? htmlspecialchars($ps['status_produksi']) ?></span></td>
            <td>Rp<?= number_format($ps['total_harga'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
