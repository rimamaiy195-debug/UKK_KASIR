<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/cek_level.php";
include "partials/catat_aktivitas.php";

hanyaUntuk(['admin', 'kasir']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// SIMPAN PELANGGAN
// =====================================================

if (isset($_POST['pilih_pelanggan'])) {

    $_SESSION['pelanggan_id'] = $_POST['pelanggan_id'];

    header("Location: transaksi.php");
    exit;
}

// =====================================================
// TAMBAH PRODUK KE KERANJANG
// =====================================================

if (isset($_POST['tambah'])) {
    $produk_id = $_POST['produk_id'];
    $jumlah = (int) $_POST['jumlah_produk'];

    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE produk_id = '$produk_id'");
    $produk_data = mysqli_fetch_assoc($query);

    if (!$produk_data) {
        die("Produk tidak ditemukan.");
    }

    if ($jumlah < 1) {
        die("Jumlah produk minimal 1.");
    }

    // Jumlah produk yang sudah ada di keranjang
    $jumlah_lama = $_SESSION['keranjang'][$produk_id]['jumlah'] ?? 0;
    $jumlah_total = $jumlah_lama + $jumlah;

    // Cek stok
    if ($jumlah_total > $produk_data['stok']) {
        die("Jumlah melebihi stok produk.");
    }

    // Buat keranjang jika belum ada
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Masukkan produk
    $_SESSION['keranjang'][$produk_id] = [
        'produk_id' => $produk_data['produk_id'],
        'nama_produk' => $produk_data['nama_produk'],
        'harga' => $produk_data['harga'],
        'jumlah' => $jumlah_total
    ];

    header("Location: transaksi.php");
    exit;
}

// =====================================================
// HAPUS PRODUK DARI KERANJANG
// =====================================================

if (isset($_GET['hapus'])) {
    $produk_id = $_GET['hapus'];

    if (isset($_SESSION['keranjang'][$produk_id])) {
        unset($_SESSION['keranjang'][$produk_id]);
    }

    header("Location: transaksi.php");
    exit;
}

// =====================================================
// BATAL TRANSAKSI
// =====================================================

if (isset($_GET['batal'])) {
    unset($_SESSION['pelanggan_id']);
    unset($_SESSION['keranjang']);
    header("Location: transaksi.php");
    exit;
}

// =====================================================
// SIMPAN TRANSAKSI
// =====================================================

if (isset($_POST['simpan'])) {
    // Cek pelanggan
    if (!isset($_SESSION['pelanggan_id'])) {
        die("Pelanggan belum dipilih.");
    }

    // Cek keranjang
    if (empty($_SESSION['keranjang'])) {
        die("Keranjang masih kosong.");
    }

    $pelanggan_id = $_SESSION['pelanggan_id'];
    $total_harga = 0;

    // =================================================
    // HITUNG TOTAL
    // =================================================

    foreach ($_SESSION['keranjang'] as $item) {
        $subtotal = $item['harga'] * $item['jumlah'];
        $total_harga += $subtotal;
    }

    // =================================================
    // DISKON
    // =================================================

    $diskon = isset($_POST['diskon']) ? (float) $_POST['diskon'] : 0;

    // Batasi diskon 0 - 100%
    if ($diskon < 0) {
        $diskon = 0;
    }

    if ($diskon > 100) {
        $diskon = 100;
    }

    // Hitung nominal potongan
    $potongan = $total_harga * ($diskon / 100);

    // Total setelah diskon
    $total_akhir = $total_harga - $potongan;

    // =================================================
    // SIMPAN DATA PENJUALAN
    // =================================================

    $simpan_penjualan = mysqli_query($koneksi, "INSERT INTO penjualan(tanggal_penjualan, total_harga, diskon, pelanggan_id)VALUES(NOW(), '$total_akhir', '$diskon', '$pelanggan_id')");

    if (!$simpan_penjualan) {
        die("Gagal menyimpan transaksi: " . mysqli_error($koneksi));
    }
    $penjualan_id = mysqli_insert_id($koneksi);

    // =================================================
    // SIMPAN DETAIL PENJUALAN
    // =================================================

    foreach ($_SESSION['keranjang'] as $item) {
        $produk_id = $item['produk_id'];
        $jumlah = $item['jumlah'];
        $subtotal = $item['harga'] * $jumlah;

        mysqli_query($koneksi, "INSERT INTO detail_penjualan(penjualan_id, produk_id, jumlah_produk, subtotal)VALUES('$penjualan_id', '$produk_id', '$jumlah', '$subtotal')");
    }

    // =================================================
    // KOSONGKAN TRANSAKSI
    // =================================================

    unset($_SESSION['keranjang']);
    unset($_SESSION['pelanggan_id']);

    // Kembali ke data penjualan
    header("Location: penjualan.php");
    exit;
}

// =====================================================
// DATA PELANGGAN
// =====================================================

$pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");

// =====================================================
// DATA PRODUK
// =====================================================

$produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama_produk ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - NovelKu</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

    <style>
        .total-box {
            margin-top: 20px;
            padding: 15px;
            background: #f8f8f8;
            border-radius: 10px;
        }

        .total-box p {
            margin: 8px 0;
        }

        .total-akhir {
            font-size: 22px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include "partials/sidebar.php"; ?>

    <main class="main">
        <div class="topbar">
            <div>
                <h1>Transaksi Baru</h1>
                <span>Input transaksi penjualan</span>
            </div>
        </div>

        <?php if (!isset($_SESSION['pelanggan_id'])) { ?>

            <div class="panel">
                <h2>Pilih Pelanggan</h2>
                <form method="POST">
                    <label>Pelanggan</label>
                    <select name="pelanggan_id" id="pelanggan" required>
                        <option value="">
                            -- Pilih Pelanggan --
                        </option>
                        <?php while (
                            $data = mysqli_fetch_assoc($pelanggan)
                        ) { ?>
                            <option value="<?= $data['pelanggan_id'] ?>">
                                <?= htmlspecialchars($data['nama_pelanggan']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <br>
                    <button type="submit" name="pilih_pelanggan" class="btn">Lanjut</button>
                </form>
            </div>

            <?php } else { ?>

                <?php
                $id_pelanggan = $_SESSION['pelanggan_id'];
                $query_nama = mysqli_query( $koneksi, "SELECT nama_pelanggan FROM pelanggan WHERE pelanggan_id = '$id_pelanggan'");
                $nama_pelanggan = mysqli_fetch_assoc($query_nama);
                ?>

                <div class="panel">
                    <h2>Pelanggan</h2>
                    <p>
                        <strong><?= htmlspecialchars($nama_pelanggan['nama_pelanggan']) ?></strong>
                    </p>
                </div>

                <div class="panel">
                    <h2>Tambah Produk</h2>
                    <form method="POST">
                        <label>Produk</label>
                        <select name="produk_id" id="produk" required>
                            <option value="">
                                -- Pilih Produk --
                            </option>
                            <?php while (
                                $data = mysqli_fetch_assoc($produk)
                            ) { ?>
                                <option value="<?= $data['produk_id'] ?>">
                                    <?= htmlspecialchars($data['nama_produk']) ?>
                                    - Rp <?= number_format($data['harga'], 0, ',', '.') ?>
                                    (Stok: <?= $data['stok'] ?>)
                                </option>
                            <?php } ?>
                        </select>
                        
                        <br>

                        <label>Jumlah</label>
                        <input type="number" name="jumlah_produk" min="1" value="1" required>
                        
                        <br>

                        <button type="submit" name="tambah" class="btn">+ Tambah Produk</button>
                    </form>
                </div>

                <div class="panel">
                    <h2>Keranjang</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                            $no = 1;
                            $total = 0;
                            if (!empty($_SESSION['keranjang'])) {
                                foreach ($_SESSION['keranjang'] as $item) {
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                    $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                    <td><?= $item['jumlah'] ?></td>
                                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                    <td><a href="transaksi.php?hapus=<?= $item['produk_id'] ?>" class="btn" onclick="return confirm('Hapus produk ini?')">Hapus</a></td>
                                </tr>

                            <?php }
                            } else {
                            ?>

                                <tr>
                                    <td colspan="6">Keranjang masih kosong.</td>
                                </tr>

                            <?php } ?>

                        </tbody>
                    </table>

                    <?php if (!empty($_SESSION['keranjang'])) { ?>

                        <div class="total-box">
                            <p>Total Belanja: <strong>Rp <?= number_format($total, 0, ',', '.' ) ?></strong></p>
                            <form method="POST">
                                <label>Diskon (%)</label>
                                <input type="number" name="diskon" min="0" max="100" step="0.01" value="0" placeholder="Contoh: 10">

                                <br>

                                <p>Masukkan diskon sebelum menyimpan transaksi.</p>
                                <button type="submit" name="simpan" class="btn">Simpan Transaksi</button>
                                <a href="transaksi.php?batal=1" class="btn secondary" onclick="return confirm('Batalkan transaksi ini? Semua isi keranjang akan dihapus.')">Batal</a>
                            </form>
                        </div>

                        <?php } else { ?>

                        <br>

                        <h2>Total: Rp <?= number_format($total, 0, ',', '.') ?></h2>

                        <?php } ?>
                </div>

            <?php } ?>

        </main>

<script>
    document.addEventListener(
        "DOMContentLoaded", function () {
            if (document.querySelector("#pelanggan")) {
                new TomSelect("#pelanggan", {
                            placeholder: "🔍 Cari pelanggan...",
                            allowEmptyOption: true,
                            create: false,
                            maxOptions: 1000
                        }
                    );
                }

                if (document.querySelector("#produk")) {
                    new TomSelect("#produk", {
                            placeholder: "🔍 Cari produk...",
                            allowEmptyOption: true,
                            create: false,
                            maxOptions: 1000
                        }
                    );
                }
            }
        );
</script>

</body>
</html>