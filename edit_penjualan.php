
<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/cek_level.php";
include "partials/catat_aktivitas.php";

hanyaUntuk(['admin']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT * FROM penjualan WHERE penjualan_id = $id"
);

$penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$penjualan) {
    header("Location: penjualan.php");
    exit;
}

$pelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan ORDER BY nama_pelanggan"
);

$produk = mysqli_query(
    $koneksi,
    "SELECT * FROM produk ORDER BY nama_produk"
);

$query_detail = mysqli_query(
    $koneksi,
    "SELECT * FROM detail_penjualan
     WHERE penjualan_id = $id"
);

$detail_lama = [];

while ($row = mysqli_fetch_assoc($query_detail)) {
    $detail_lama[$row['produk_id']] = (int) $row['jumlah_produk'];
}

$keranjang = $detail_lama;
$pelanggan_id = (int) $penjualan['pelanggan_id'];
$diskon = (float) $penjualan['diskon'];
$error = '';

if (isset($_POST['simpan'])) {
    $pelanggan_id = (int) $_POST['pelanggan_id'];
    $diskon = (float) $_POST['diskon'];
    $jumlah_input = $_POST['jumlah'] ?? [];

    if ($diskon < 0 || $diskon > 100) {
        $error = "Diskon harus antara 0 sampai 100 persen.";
    } elseif ($pelanggan_id <= 0) {
        $error = "Pilih pelanggan terlebih dahulu.";
    } else {
        $keranjang_baru = [];

        foreach ($jumlah_input as $produk_id => $jumlah) {
            $produk_id = (int) $produk_id;
            $jumlah = (int) $jumlah;

            if ($jumlah > 0 && $produk_id > 0) {
                $keranjang_baru[$produk_id] = $jumlah;
            }
        }

        if (empty($keranjang_baru)) {
            $error = "Masukkan minimal satu barang.";
        } else {
            mysqli_begin_transaction($koneksi);

            try {
                // Kunci produk agar stok tidak berubah di tengah proses.
                $semua_id = array_unique(
                    array_merge(
                        array_keys($detail_lama),
                        array_keys($keranjang_baru)
                    )
                );

                sort($semua_id);

                $stok_produk = [];

                foreach ($semua_id as $produk_id) {
                    $q = mysqli_query(
                        $koneksi,
                        "SELECT produk_id, nama_produk, harga, stok
                         FROM produk
                         WHERE produk_id = $produk_id
                         FOR UPDATE"
                    );

                    $p = mysqli_fetch_assoc($q);

                    if (!$p) {
                        throw new Exception("Produk tidak ditemukan.");
                    }

                    $stok_produk[$produk_id] = $p;
                }

                // Kembalikan stok dari detail lama.
                foreach ($detail_lama as $produk_id => $jumlah_lama) {
                    $q = mysqli_query(
                        $koneksi,
                        "UPDATE produk
                         SET stok = stok + $jumlah_lama
                         WHERE produk_id = $produk_id"
                    );

                    if (!$q) {
                        throw new Exception("Gagal mengembalikan stok lama.");
                    }
                }

                // Periksa stok setelah stok lama dikembalikan.
                foreach ($keranjang_baru as $produk_id => $jumlah_baru) {
                    $stok_tersedia =
                        (int) $stok_produk[$produk_id]['stok']
                        + (int) ($detail_lama[$produk_id] ?? 0);

                    if ($jumlah_baru > $stok_tersedia) {
                        throw new Exception(
                            "Stok " .
                            $stok_produk[$produk_id]['nama_produk'] .
                            " tidak cukup. Tersedia: $stok_tersedia."
                        );
                    }
                }

                // Hapus detail lama. Trigger pengembalian stok
                // TIDAK BOLEH aktif bersamaan dengan pengembalian manual.
                $q = mysqli_query(
                    $koneksi,
                    "DELETE FROM detail_penjualan
                     WHERE penjualan_id = $id"
                );

                if (!$q) {
                    throw new Exception("Gagal memperbarui detail.");
                }

                // Masukkan detail baru dan kurangi stok.
                $total = 0;

                foreach ($keranjang_baru as $produk_id => $jumlah) {
                    $harga = (float) $stok_produk[$produk_id]['harga'];
                    $subtotal = $harga * $jumlah;
                    $total += $subtotal;

                    $q = mysqli_query(
                        $koneksi,
                        "INSERT INTO detail_penjualan
                        (penjualan_id, produk_id, jumlah_produk, subtotal)
                        VALUES ($id, $produk_id, $jumlah, $subtotal)"
                    );

                    if (!$q) {
                        throw new Exception("Gagal menyimpan barang transaksi.");
                    }

                    $q = mysqli_query(
                        $koneksi,
                        "UPDATE produk
                         SET stok = stok - $jumlah
                         WHERE produk_id = $produk_id"
                    );

                    if (!$q) {
                        throw new Exception("Gagal memperbarui stok.");
                    }
                }

                $total_akhir = $total - ($total * $diskon / 100);

                $q = mysqli_query(
                    $koneksi,
                    "UPDATE penjualan
                     SET pelanggan_id = $pelanggan_id,
                         diskon = $diskon,
                         total_harga = $total_akhir
                     WHERE penjualan_id = $id"
                );

                if (!$q) {
                    throw new Exception("Gagal memperbarui transaksi.");
                }

                mysqli_commit($koneksi);

                catatAktivitas(
                    $koneksi,
                    "Mengubah transaksi penjualan #$id"
                );

                header("Location: penjualan.php");
                exit;

            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                $error = $e->getMessage();
            }
        }
    }

    if ($error != '') {
        $keranjang = $keranjang_baru ?? $keranjang;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Penjualan - FootWear Store</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .form-edit {
            background: white;
            padding: 25px;
            border-radius: 15px;
        }

        .form-edit label {
            display: block;
            margin: 15px 0 6px;
        }

        .form-edit select,
        .form-edit input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .barang-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .barang {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
        }

        .error {
            color: #b42318;
            margin-bottom: 15px;
        }

        @media(max-width: 650px) {
            .barang-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include "partials/sidebar.php"; ?>

<main class="main">
    <div class="topbar">
        <div>
            <h1>Edit Penjualan #<?= $id ?></h1>
            <span>Ubah pelanggan dan barang yang dibeli</span>
        </div>
    </div>

    <div class="form-edit">

        <?php if ($error != '') { ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php } ?>

        <form method="POST">
            <label>Pelanggan</label>
            <select name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>

                <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
                    <option
                        value="<?= $p['pelanggan_id'] ?>"
                        <?= $pelanggan_id == $p['pelanggan_id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($p['nama_pelanggan']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Diskon (%)</label>
            <input
                type="number"
                name="diskon"
                min="0"
                max="100"
                step="0.01"
                value="<?= htmlspecialchars((string) $diskon) ?>"
                required
            >

            <h2 style="margin-top:25px;">Barang Transaksi</h2>
            <p>Isi jumlah barang. Masukkan 0 jika barang ingin dihapus dari transaksi.</p>

            <div class="barang-grid">
                <?php while ($p = mysqli_fetch_assoc($produk)) { ?>
                    <div class="barang">
                        <strong>
                            <?= htmlspecialchars($p['nama_produk']) ?>
                        </strong>

                        <p>
                            Harga: Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                        </p>

                        <p>
                            Stok sekarang: <?= (int) $p['stok'] ?>
                        </p>

                        <label>
                            Jumlah barang
                        </label>

                        <input
                            type="number"
                            name="jumlah[<?= $p['produk_id'] ?>]"
                            min="0"
                            value="<?= (int) ($keranjang[$p['produk_id']] ?? 0) ?>"
                        >
                    </div>
                <?php } ?>
            </div>

            <br>

            <button type="submit" name="simpan" class="btn">
                Simpan Perubahan
            </button>

            <a href="penjualan.php" class="btn secondary">
                Batal
            </a>
        </form>
    </div>
</main>

</body>
</html>
