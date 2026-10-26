<?php
require 'config/auth.php';
wajib_role(['admin', 'kasir']);
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int) $_POST['id_pesanan'];
    $jumlah = (int) $_POST['jumlah'];

    if ($jumlah <= 0) {
        $pesan = "Jumlah pembayaran harus lebih dari 0.";
    } else {
        $pdo->prepare("INSERT INTO pembayaran (id_pesanan, jumlah_bayar, jenis, tgl_bayar) VALUES (?, ?, 'pelunasan', ?)")
            ->execute([$id_pesanan, $jumlah, date('Y-m-d')]);
        $pesan = "Pembayaran pelunasan sebesar Rp" . number_format($jumlah, 0, ',', '.') . " berhasil dicatat.";
    }
}

// Daftar pesanan beserta sisa tagihan
$sql = "SELECT p.id_pesanan, pl.nama AS nama_pelanggan, p.total_harga,
        COALESCE(SUM(b.jumlah_bayar), 0) AS sudah_dibayar,
        (p.total_harga - COALESCE(SUM(b.jumlah_bayar), 0)) AS sisa_tagihan
        FROM pesanan p
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        LEFT JOIN pembayaran b ON b.id_pesanan = p.id_pesanan
        GROUP BY p.id_pesanan
        HAVING sisa_tagihan > 0
        ORDER BY p.tgl_pesan";
$daftar = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Pembayaran</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Pembayaran</h2>
    <a href="index.php">&larr; Kembali ke dashboard</a>

    <?php if (!empty($pesan)): ?><p class="pesan"><?= htmlspecialchars($pesan) ?></p><?php endif; ?>

    <table>
        <tr><th>No. Pesanan</th><th>Pelanggan</th><th>Total</th><th>Sudah Dibayar</th><th>Sisa Tagihan</th><th>Bayar</th></tr>
        <?php foreach ($daftar as $d): ?>
        <tr>
            <td>#<?= $d['id_pesanan'] ?></td>
            <td><?= htmlspecialchars($d['nama_pelanggan']) ?></td>
            <td>Rp<?= number_format($d['total_harga'], 0, ',', '.') ?></td>
            <td>Rp<?= number_format($d['sudah_dibayar'], 0, ',', '.') ?></td>
            <td>Rp<?= number_format($d['sisa_tagihan'], 0, ',', '.') ?></td>
            <td>
                <form method="post" style="display:flex; gap:6px;">
                    <input type="hidden" name="id_pesanan" value="<?= $d['id_pesanan'] ?>">
                    <input type="number" name="jumlah" placeholder="Jumlah" min="1" max="<?= $d['sisa_tagihan'] ?>" style="width:100px">
                    <button type="submit">Simpan</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($daftar)): ?>
        <tr><td colspan="6">Tidak ada tagihan yang belum lunas.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
