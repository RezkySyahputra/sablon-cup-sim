<?php
// Jalankan sekali lewat browser untuk membuat 3 akun contoh.
// Setelah berhasil, SEGERA HAPUS file ini dari server (jangan dibiarkan aktif).
require 'config/koneksi.php';

$akun = [
    ['nama' => 'Pemilik Usaha', 'username' => 'admin', 'password' => 'password123', 'role' => 'admin'],
    ['nama' => 'Kasir Toko', 'username' => 'kasir1', 'password' => 'password123', 'role' => 'kasir'],
    ['nama' => 'Petugas Produksi', 'username' => 'produksi1', 'password' => 'password123', 'role' => 'produksi'],
];

$dibuat = [];
foreach ($akun as $a) {
    $cek = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
    $cek->execute([$a['username']]);
    if (!$cek->fetch()) {
        $hash = password_hash($a['password'], PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)")
            ->execute([$a['nama'], $a['username'], $hash, $a['role']]);
        $dibuat[] = $a['username'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Setup Akun</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Setup Akun Contoh</h2>
    <?php if ($dibuat): ?>
        <p class="pesan">Akun berhasil dibuat: <?= implode(', ', $dibuat) ?></p>
    <?php else: ?>
        <p class="pesan">Semua akun contoh sudah ada, tidak ada yang dibuat ulang.</p>
    <?php endif; ?>
    <table>
        <tr><th>Username</th><th>Password</th><th>Role</th></tr>
        <tr><td>admin</td><td>password123</td><td>admin</td></tr>
        <tr><td>kasir1</td><td>password123</td><td>kasir</td></tr>
        <tr><td>produksi1</td><td>password123</td><td>produksi</td></tr>
    </table>
    <p style="color:#a32d2d">Setelah ini berhasil, hapus file setup_akun.php dari server untuk keamanan.</p>
    <a href="login.php">Ke halaman login &rarr;</a>
</div>
</body>
</html>
