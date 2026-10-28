<?php
// Modul Status Produksi dan Laporan - PIC: Iron Djerwi (23083000011)
require 'config/auth.php';
wajib_role(['admin']);
require 'config/koneksi.php';

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Total pendapatan (dari seluruh pembayaran) pada periode
$stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah_bayar), 0) AS total FROM pembayaran WHERE tgl_bayar BETWEEN ? AND ?");
$stmt->execute([$dari, $sampai]);
$total_pendapatan = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Omzet per jenis produk (dihitung dari detail_pesanan pada pesanan periode terkait)
$sql = "SELECT pr.nama_produk, pr.jenis, SUM(dp.qty) AS total_qty, SUM(dp.subtotal) AS total_omzet
        FROM detail_pesanan dp
        JOIN produk pr ON dp.id_produk = pr.id_produk
        JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
        WHERE p.tgl_pesan BETWEEN ? AND ?
        GROUP BY pr.id_produk
        ORDER BY total_omzet DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$dari, $sampai]);
$omzet_produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Laporan Pendapatan</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Laporan Pendapatan &amp; Omzet per Produk</h2>
    <a href="index.php">&larr; Kembali ke dashboard</a>

    <form method="get" class="inline-form" style="display:flex; gap:8px; align-items:center;">
        <label>Dari</label>
        <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>">
        <label>Sampai</label>
        <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>">
        <button type="submit">Tampilkan</button>
    </form>

    <h3>Total Pendapatan (DP + Pelunasan) Periode Ini</h3>
    <p style="font-size:22px; font-weight:bold; color:#e10600;">Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></p>

    <h3>Omzet per Jenis Produk</h3>
    <table>
        <tr><th>Produk</th><th>Jenis</th><th>Total Qty Terjual</th><th>Total Omzet</th></tr>
        <?php foreach ($omzet_produk as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['nama_produk']) ?></td>
            <td><?= htmlspecialchars($o['jenis']) ?></td>
            <td><?= (int) $o['total_qty'] ?></td>
            <td>Rp<?= number_format($o['total_omzet'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($omzet_produk)): ?>
        <tr><td colspan="4">Belum ada transaksi pada periode ini.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
