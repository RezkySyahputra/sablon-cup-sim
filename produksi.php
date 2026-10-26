<?php
require 'config/auth.php';
wajib_role(['admin', 'produksi']);
require 'config/koneksi.php';

$urutan_status = ['antre', 'cetak', 'finishing', 'selesai'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int) $_POST['id_pesanan'];
    $status_baru = $_POST['status_baru'];

    if (!in_array($status_baru, $urutan_status, true)) {
        $pesan = "Status tidak valid.";
    } else {
        $pdo->prepare("UPDATE pesanan SET status_produksi = ? WHERE id_pesanan = ?")
            ->execute([$status_baru, $id_pesanan]);
        $pesan = "Status pesanan #$id_pesanan berhasil diperbarui menjadi \"$status_baru\".";
    }
}

$sql = "SELECT p.id_pesanan, pl.nama AS nama_pelanggan, p.tgl_pesan, p.status_produksi
        FROM pesanan p
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        WHERE p.status_produksi <> 'selesai'
        ORDER BY p.tgl_pesan";
$daftar = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Status Produksi</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Status Produksi</h2>
    <a href="index.php">&larr; Kembali ke dashboard</a>

    <?php if (!empty($pesan)): ?><p class="pesan"><?= htmlspecialchars($pesan) ?></p><?php endif; ?>

    <table>
        <tr><th>No. Pesanan</th><th>Pelanggan</th><th>Tgl Pesan</th><th>Status Saat Ini</th><th>Ubah Status</th></tr>
        <?php foreach ($daftar as $d): ?>
        <tr>
            <td>#<?= $d['id_pesanan'] ?></td>
            <td><?= htmlspecialchars($d['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($d['tgl_pesan']) ?></td>
            <td><?= htmlspecialchars($d['status_produksi']) ?></td>
            <td>
                <form method="post" style="display:flex; gap:6px;">
                    <input type="hidden" name="id_pesanan" value="<?= $d['id_pesanan'] ?>">
                    <select name="status_baru">
                        <?php foreach ($urutan_status as $s): ?>
                            <option value="<?= $s ?>" <?= $s === $d['status_produksi'] ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($daftar)): ?>
        <tr><td colspan="5">Tidak ada pesanan yang masih dalam proses.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
