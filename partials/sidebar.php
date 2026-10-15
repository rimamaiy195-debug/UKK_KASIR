<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$level = $_SESSION['level'] ?? '';
$username = $_SESSION['username'] ?? '';

?>

<div class="sidebar">

    <div class="brand">
            Novel<span>Ku</span>
    </div>
    <a href="index.php">
        🏠 Dashboard
    </a>

    <a href="produk.php">
        📦 Produk
    </a>

    <a href="pelanggan.php">
        👥 Pelanggan
    </a>


    <?php if ($level == 'admin' || $level == 'kasir') { ?>

        <a href="transaksi.php">
            🛒 Transaksi
        </a>

    <?php } ?>


    <a href="penjualan.php">
        🧾 Data Penjualan
    </a>


    <a href="aktivitas.php">
    📋 Aktivitas
    </a>


    <a href="laporan.php">
    📊 Laporan
    </a>


    <?php if ($level == 'admin') { ?>

        <a href="user.php">
            Kelola User
        </a>

    <?php } ?>


    <hr>


    <div>
        Login sebagai:
        <strong>
            <?= htmlspecialchars($username) ?>
        </strong>
    </div>

    <div>
        Level:
        <strong>
            <?= htmlspecialchars($level) ?>
        </strong>
    </div>


    <a href="logout.php">
        ↪ Keluar
    </a>

</div>
