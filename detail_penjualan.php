<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";

$id = (int) $_GET['id'];

// Data transaksi
$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT penjualan.penjualan_id, penjualan.tanggal_penjualan, penjualan.total_harga, pelanggan.nama_pelanggan, pelanggan.alamat, pelanggan.nomor_telepon FROM penjualan LEFT JOIN pelanggan ON penjualan.pelanggan_id = pelanggan.pelanggan_id WHERE penjualan.penjualan_id = '$id'");

$penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$penjualan) {
    echo "Transaksi tidak ditemukan.";
    exit;
}

// Detail produk
$query_detail = mysqli_query(
    $koneksi,
    "SELECT detail_penjualan.jumlah_produk, detail_penjualan.subtotal, produk.nama_produk, produk.harga FROM detail_penjualan INNER JOIN produk ON detail_penjualan.produk_id = produk.produk_id WHERE detail_penjualan.penjualan_id = '$id'");

$total_item = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Struk #<?= $penjualan['penjualan_id'] ?> - NovelKu</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>
        /* Kartu panel dipersempit khusus halaman ini agar tidak lebar kosong di kiri-kanan */
        .panel {
            max-width: 460px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Alas abu-abu supaya kertas putih struk kelihatan batasnya */
        .nk-wrap {
            display: block !important;
            width: 100% !important;
            background: #ebe6dd;
            border-radius: 10px;
            padding: 24px 14px 40px !important;
            margin-bottom: 18px;
        }

        /* Kertas struk */
        .nk-struk {
            display: block !important;
            box-sizing: border-box !important;
            width: 340px !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            background: #fff;
            color: #111;
            padding: 22px 20px 34px !important;
            font-family: "Courier New", Courier, monospace !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            text-align: left !important;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .25);
            -webkit-mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 12px 100%;
                    mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 12px 100%;
        }

        .nk-struk *,
        .nk-struk *::before,
        .nk-struk *::after {
            box-sizing: border-box !important;
            font-family: inherit !important;
            font-size: inherit;
            color: inherit;
        }

        .nk-struk p {
            margin: 0 !important;
            padding: 0 !important;
            text-align: inherit !important;
        }

        .nk-struk .nk-center {
            text-align: center !important;
        }

        .nk-struk .nk-toko {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .nk-struk .nk-garis {
            display: block !important;
            width: 100% !important;
            height: 0 !important;
            border: 0 !important;
            border-top: 1px dashed #111 !important;
            margin: 10px 0 !important;
            padding: 0 !important;
            background: none !important;
        }

        .nk-struk .nk-garis.nk-ganda {
            border-top: 3px double #111 !important;
        }

        .nk-struk .nk-baris {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 10px;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            background: none !important;
            text-align: left !important;
        }

        .nk-struk .nk-baris span {
            display: block;
            padding: 0 !important;
            margin: 0 !important;
        }

        .nk-struk .nk-baris span:last-child {
            text-align: right !important;
        }

        .nk-struk .nk-item {
            margin: 0 0 6px 0 !important;
            padding: 0 !important;
            border: 0 !important;
            background: none !important;
        }

        .nk-struk .nk-item-nama {
            word-break: break-word;
        }

        .nk-struk .nk-total {
            font-size: 15px;
            font-weight: bold;
        }

        .nk-aksi {
            text-align: center;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            .sidebar,
            .topbar,
            .no-print {
                display: none !important;
            }

            .main {
                margin: 0 !important;
                padding: 0 !important;
            }

            .panel {
                background: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: none !important;
                margin: 0 !important;
            }

            body {
                background: white;
            }

            .nk-wrap {
                background: none;
                padding: 0 !important;
                margin: 0;
            }

            .nk-struk {
                width: 80mm !important;
                box-shadow: none;
                -webkit-mask: none;
                        mask: none;
                padding: 4mm !important;
            }
        }
    </style>

</head>

<body>

<?php include "partials/sidebar.php"; ?>

<main class="main">

    <div class="topbar no-print">
        <h1>Struk</h1>
        <span>Detail transaksi penjualan</span>
    </div>

    <div class="panel">

        <div class="nk-wrap">

            <div class="nk-struk">

                <!-- HEADER TOKO -->
                <div class="nk-center">
                    <p class="nk-toko">NovelKu</p>
                    <p>Sistem Kasir Penjualan</p>
                </div>

                <hr class="nk-garis">

                <!-- INFO TRANSAKSI -->
                <div class="nk-baris">
                    <span>No</span>
                    <span>#<?= $penjualan['penjualan_id'] ?></span>
                </div>
                <div class="nk-baris">
                    <span>Tanggal</span>
                    <span><?= date('d-m-Y H:i', strtotime($penjualan['tanggal_penjualan'])) ?></span>
                </div>
                <div class="nk-baris">
                    <span>Pelanggan</span>
                    <span><?= htmlspecialchars($penjualan['nama_pelanggan']) ?></span>
                </div>
                <?php if (!empty($penjualan['alamat'])) { ?>
                <div class="nk-baris">
                    <span>Alamat</span>
                    <span><?= htmlspecialchars($penjualan['alamat']) ?></span>
                </div>
                <?php } ?>
                <?php if (!empty($penjualan['nomor_telepon'])) { ?>
                <div class="nk-baris">
                    <span>Telp</span>
                    <span><?= htmlspecialchars($penjualan['nomor_telepon']) ?></span>
                </div>
                <?php } ?>

                <hr class="nk-garis">

                <!-- DAFTAR PRODUK -->
                <?php while ($data = mysqli_fetch_assoc($query_detail)) {
                    $total_item += $data['jumlah_produk'];
                ?>
                    <div class="nk-item">
                        <p class="nk-item-nama"><?= htmlspecialchars($data['nama_produk']) ?></p>
                        <div class="nk-baris">
                            <span><?= $data['jumlah_produk'] ?> x <?= number_format($data['harga'], 0, ',', '.') ?></span>
                            <span><?= number_format($data['subtotal'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                <?php } ?>

                <hr class="nk-garis">

                <!-- TOTAL -->
                <div class="nk-baris">
                    <span>Total Item</span>
                    <span><?= $total_item ?></span>
                </div>
                <div class="nk-baris nk-total">
                    <span>TOTAL</span>
                    <span>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?></span>
                </div>

                <hr class="nk-garis nk-ganda">

                <!-- FOOTER -->
                <div class="nk-center">
                    <p>Terima kasih telah berbelanja!</p>
                    <p>Barang yang sudah dibeli</p>
                    <p>tidak dapat dikembalikan</p>
                    <p style="margin-top:8px;">*** NovelKu ***</p>
                </div>

            </div>

        </div>

        <div class="no-print nk-aksi">

            <button onclick="window.print()" class="btn">
                🖨 Cetak Struk
            </button>

            <a href="penjualan.php" class="btn secondary">
                Kembali
            </a>

        </div>

    </div>

</main>

</body>
</html>