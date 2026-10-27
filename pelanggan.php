<?php
// Modul Data Master Pelanggan dan Produk - PIC: Muh Rezky Syahputra (23083000023)
require 'config/auth.php';
wajib_role(['admin', 'kasir']);
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);
    if ($nama === '') {
        $pesan = "Nama pelanggan wajib diisi.";
    } else {
        $pdo->prepare("INSERT INTO pelanggan (nama, no_hp) VALUES (?, ?)")->execute([$nama, $no_hp]);
        $pesan = "Pelanggan berhasil ditambahkan.";
    }
}

// Hapus pelanggan
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    try {
        $pdo->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?")->execute([$id_hapus]);
        $pesan = "Pelanggan berhasil dihapus.";
    } catch (PDOException $e) {
        $pesan = "Pelanggan tidak bisa dihapus karena sudah pernah dipakai di pesanan. Hapus/ubah pesanan terkait terlebih dahulu jika tetap ingin menghapusnya.";
    }
}

$daftar = $pdo->query("SELECT * FROM pelanggan ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kelola Pelanggan</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Kelola Pelanggan</h2>
    <a href="index.php">&larr; Kembali ke dashboard</a>

    <?php if (!empty($pesan)): ?><p class="pesan"><?= htmlspecialchars($pesan) ?></p><?php endif; ?>

    <table>
        <tr><th>Nama</th><th>No. HP</th><th>Aksi</th></tr>
        <?php foreach ($daftar as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['nama']) ?></td>
            <td><?= htmlspecialchars($d['no_hp']) ?></td>
            <td><a class="btn-hapus" href="?hapus=<?= $d['id_pelanggan'] ?>" onclick="return confirm('Yakin hapus pelanggan ini?');">Hapus</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Tambah Pelanggan</h3>
    <form method="post">
        <input type="text" name="nama" placeholder="Nama pelanggan" required>
        <input type="text" name="no_hp" placeholder="No. HP">
        <button type="submit">Simpan</button>
    </form>
</div>
</body>
</html>
