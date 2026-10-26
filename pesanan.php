<?php
require 'config/auth.php';
wajib_role(['admin', 'kasir']);
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelanggan = (int) $_POST['id_pelanggan'];
    $produk_ids = $_POST['produk_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $dp = (int) ($_POST['dp'] ?? 0);

    // Susun item yang valid (produk dipilih & qty > 0)
    $items = [];
    foreach ($produk_ids as $i => $pid) {
        $pid = (int) $pid;
        $qty = (int) ($qtys[$i] ?? 0);
        if ($pid > 0 && $qty > 0) {
            $items[] = ['id_produk' => $pid, 'qty' => $qty];
        }
    }

    if ($id_pelanggan <= 0 || empty($items)) {
        $pesan = "Pilih pelanggan dan minimal satu produk dengan jumlah lebih dari 0.";
    } else {
        $pdo->beginTransaction();
        try {
            // Hitung total dari harga produk saat ini
            $total = 0;
            $detail_final = [];
            foreach ($items as $it) {
                $stmt = $pdo->prepare("SELECT harga FROM produk WHERE id_produk = ?");
                $stmt->execute([$it['id_produk']]);
                $produk = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($produk) {
                    $subtotal = $produk['harga'] * $it['qty'];
                    $total += $subtotal;
                    $detail_final[] = ['id_produk' => $it['id_produk'], 'qty' => $it['qty'], 'subtotal' => $subtotal];
                }
            }

            $stmt = $pdo->prepare(
                "INSERT INTO pesanan (id_pelanggan, id_user, tgl_pesan, status_produksi, total_harga)
                 VALUES (?, ?, ?, 'antre', ?)"
            );
            $stmt->execute([$id_pelanggan, $_SESSION['id_user'], date('Y-m-d'), $total]);
            $id_pesanan = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, qty, subtotal) VALUES (?, ?, ?, ?)");
            foreach ($detail_final as $d) {
                $stmt->execute([$id_pesanan, $d['id_produk'], $d['qty'], $d['subtotal']]);
            }

            if ($dp > 0) {
                $pdo->prepare("INSERT INTO pembayaran (id_pesanan, jumlah_bayar, jenis, tgl_bayar) VALUES (?, ?, 'dp', ?)")
                    ->execute([$id_pesanan, $dp, date('Y-m-d')]);
            }

            $pdo->commit();
            $pesan = "Pesanan berhasil dibuat. Total: Rp" . number_format($total, 0, ',', '.') . ", DP diterima: Rp" . number_format($dp, 0, ',', '.');
        } catch (Exception $e) {
            $pdo->rollBack();
            $pesan = "Gagal membuat pesanan: " . $e->getMessage();
        }
    }
}

$daftar_pelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
$daftar_produk = $pdo->query("SELECT * FROM produk ORDER BY nama_produk")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT p.id_pesanan, pl.nama AS nama_pelanggan, p.tgl_pesan, p.status_produksi, p.total_harga,
        COALESCE(SUM(b.jumlah_bayar), 0) AS sudah_dibayar
        FROM pesanan p
        JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        LEFT JOIN pembayaran b ON b.id_pesanan = p.id_pesanan
        GROUP BY p.id_pesanan
        ORDER BY p.tgl_pesan DESC";
$daftar_pesanan = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Pesanan</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Pesanan</h2>
    <a href="index.php">&larr; Kembali ke dashboard</a>

    <?php if (!empty($pesan)): ?><p class="pesan"><?= htmlspecialchars($pesan) ?></p><?php endif; ?>

    <table>
        <tr><th>Pelanggan</th><th>Tgl Pesan</th><th>Status</th><th>Total</th><th>Dibayar</th></tr>
        <?php foreach ($daftar_pesanan as $ps): ?>
        <tr>
            <td><?= htmlspecialchars($ps['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($ps['tgl_pesan']) ?></td>
            <td><?= htmlspecialchars($ps['status_produksi']) ?></td>
            <td>Rp<?= number_format($ps['total_harga'], 0, ',', '.') ?></td>
            <td>Rp<?= number_format($ps['sudah_dibayar'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Buat Pesanan Baru</h3>
    <form method="post">
        <label>Pelanggan</label>
        <select name="id_pelanggan" required>
            <option value="">-- Pilih pelanggan --</option>
            <?php foreach ($daftar_pelanggan as $pl): ?>
                <option value="<?= $pl['id_pelanggan'] ?>"><?= htmlspecialchars($pl['nama']) ?></option>
            <?php endforeach; ?>
        </select>

        <p style="width:100%; margin-bottom:0;">Item pesanan (isi minimal 1 baris):</p>
        <?php for ($i = 0; $i < 3; $i++): ?>
        <div style="display:flex; gap:8px; width:100%;">
            <select name="produk_id[]" style="flex:2">
                <option value="">-- Pilih produk --</option>
                <?php foreach ($daftar_produk as $pr): ?>
                    <option value="<?= $pr['id_produk'] ?>"><?= htmlspecialchars($pr['nama_produk']) ?> (Rp<?= number_format($pr['harga'], 0, ',', '.') ?>)</option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="qty[]" placeholder="Jumlah" min="0" style="flex:1">
        </div>
        <?php endfor; ?>

        <label>Pembayaran DP (opsional)</label>
        <input type="number" name="dp" placeholder="Jumlah DP diterima" min="0">

        <button type="submit">Simpan Pesanan</button>
    </form>
</div>
</body>
</html>
