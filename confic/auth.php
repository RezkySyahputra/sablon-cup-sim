<?php
// Helper otentikasi & pembatasan akses per peran.
// Panggil di baris paling atas tiap halaman yang butuh login.
session_start();

function wajib_login() {
    if (!isset($_SESSION['id_user'])) {
        header('Location: login.php');
        exit;
    }
}

// Contoh: wajib_role(['admin', 'kasir']) — hanya admin & kasir yang boleh akses
function wajib_role(array $role_diizinkan) {
    wajib_login();
    if (!in_array($_SESSION['role'], $role_diizinkan, true)) {
        die("Akses ditolak. Halaman ini hanya untuk peran: " . implode(', ', $role_diizinkan));
    }
}
