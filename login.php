<?php
// Modul Autentikasi dan Manajemen Peran - PIC: Dominicus Luis Filipe Saceira (23083000033)
require 'config/koneksi.php';
session_start();

if (isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $pesan = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:400px; margin-top:80px;">
    <h2>Login</h2>
    <p>Sistem Informasi Manajemen Pesanan Sablon Plastik Cup Kertas</p>

    <?php if (!empty($pesan)): ?>
        <p class="pesan pesan-error"><?= htmlspecialchars($pesan) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required style="width:100%">
        <input type="password" name="password" placeholder="Password" required style="width:100%">
        <button type="submit" style="width:100%">Masuk</button>
    </form>

    <p style="margin-top:16px; font-size:12px; color:#888;">
        Belum ada akun? Jalankan <code>setup_akun.php</code> sekali untuk membuat akun contoh (admin, kasir, produksi).
    </p>
</div>
</body>
</html>
