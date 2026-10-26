<?php
require 'config/auth.php';
wajib_role(['admin', 'kasir']);
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $jenis = trim($_POST['jenis']);
    $harga = (int) $_POST['harga'];
    if ($nama_produk === '' || $harga <= 0) {
        $pesan = "Nama produk wajib diisi dan harga harus lebih dari 0.";
    } else {
        $pdo->prepare("INSERT INTO produk (nama_produk, jenis, harga) VALUES (?, ?, ?)")
            ->execute([$nama_produk, $jenis, $harga]);
        $pesan = "Produk berhasil ditambahkan.";
    }
}

// Hapus produk
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    try {
        $pdo->prepare("DELETE FROM produk WHERE id_produk = ?")->execute([$id_hapus]);
        $pesan = "Produk berhasil dihapus.";
    } catch (PDOException $e) {
        $pesan = "Produk tidak bisa dihapus karena sudah pernah dipakai di pesanan. Hapus/ubah pesanan terkait terlebih dahulu jika tetap ingin menghapusnya.";
    }
}

$daftar = $pdo->query("SELECT * FROM produk ORDER BY nama_produk")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kelola Produk</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Kelola Produk</h2>
    <a href="index.php">&larr; Kembali ke dashboard</a>

    <?php if (!empty($pesan)): ?><p class="pesan"><?= htmlspecialchars($pesan) ?></p><?php endif; ?>

    <table>
        <tr><th>Nama Produk</th><th>Jenis</th><th>Harga</th><th>Aksi</th></tr>
        <?php foreach ($daftar as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['nama_produk']) ?></td>
            <td><?= htmlspecialchars($d['jenis']) ?></td>
            <td>Rp<?= number_format($d['harga'], 0, ',', '.') ?></td>
            <td><a class="btn-hapus" href="?hapus=<?= $d['id_produk'] ?>" onclick="return confirm('Yakin hapus produk ini?');">Hapus</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Tambah Produk</h3>
    <form method="post">
        <input type="text" name="nama_produk" placeholder="Nama produk (mis. Cup Plastik 16oz Sablon)" required>
        <input type="text" name="jenis" placeholder="Jenis (mis. Cup Plastik, Cup Kertas, Tutup Cup)">
        <input type="number" name="harga" placeholder="Harga per unit" min="1" required>
        <button type="submit">Simpan</button>
    </form>
</div>
</body>
</html>
