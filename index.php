<?php
include "koneksi.php";
include "partials/cek_login.php";

// Jumlah produk
$query_produk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$produk = mysqli_fetch_assoc($query_produk)['total'];

// Jumlah pelanggan
$query_pelanggan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pelanggan");
$pelanggan = mysqli_fetch_assoc($query_pelanggan)['total'];

// Jumlah transaksi
$query_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM penjualan");
$transaksi = mysqli_fetch_assoc($query_transaksi)['total'];

// Total penjualan
$query_pendapatan = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(total_harga), 0) AS total FROM penjualan"
);
$pendapatan = mysqli_fetch_assoc($query_pendapatan)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - NovelKu</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<?php include "partials/sidebar.php"; ?>

<main class="main">

    <div class="topbar">
        <h1>Dashboard</h1>
        <span>Sistem Kasir Penjualan</span>
    </div>

    <div class="cards">

        <div class="card">
            <div class="label">Total Produk</div>

            <div class="number">
                <?= $produk ?>
            </div>

            <a href="produk.php">
                Kelola Produk →
            </a>
        </div>

        <div class="card">
            <div class="label">Total Pelanggan</div>

            <div class="number">
                <?= $pelanggan ?>
            </div>

            <a href="pelanggan.php">
                Kelola Pelanggan →
            </a>
        </div>

        <div class="card">
            <div class="label">Total Transaksi</div>

            <div class="number">
                <?= $transaksi ?>
            </div>

            <a href="penjualan.php">
                Lihat Penjualan →
            </a>
        </div>

        <div class="card">
            <div class="label">Total Penjualan</div>

            <div class="number">
                Rp <?= number_format($pendapatan, 0, ',', '.') ?>
            </div>
        </div>

    </div>

    <div class="panel">

        <h2>Menu Cepat</h2>

        <div class="quick">

            <?php if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'kasir') { ?>
                <a href="transaksi.php" class="btn">+ Tambah Transaksi</a>
            <?php } ?>

            <a class="btn secondary" href="produk.php">
                Kelola Produk
            </a>

            <a class="btn secondary" href="pelanggan.php">
                Kelola Pelanggan
            </a>

        </div>

    </div>

    <div class="panel">

        <h2>Informasi Sistem</h2>

        <p>
            NovelKu merupakan sistem kasir yang digunakan untuk mengelola data produk,
            pelanggan, transaksi penjualan, dan stok produk.
        </p>

        <p>
            Sistem memiliki hak akses berdasarkan level pengguna, yaitu admin, kasir, dan owner. 
            Setiap transaksi yang disimpan akan otomatis mengurangi stok produk.
        </p>

    </div>

</main>

</body>
</html>