
<?php
include "koneksi.php";
include "partials/cek_login.php";

$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

if ($tahun < 2000 || $tahun > 2100) {
    $tahun = (int) date('Y');
}

$bulan_ini = date('m');
$tahun_ini = date('Y');

// Pendapatan bulan ini
$query_bulan_ini = mysqli_query(
    $koneksi,
    "SELECT
        COUNT(*) AS transaksi,
        COALESCE(SUM(total_harga), 0) AS pendapatan
     FROM penjualan
     WHERE MONTH(tanggal_penjualan) = '$bulan_ini'
       AND YEAR(tanggal_penjualan) = '$tahun_ini'"
);

$data_bulan_ini = mysqli_fetch_assoc($query_bulan_ini);

// Pendapatan bulan sebelumnya
$query_sebelumnya = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(total_harga), 0) AS pendapatan
     FROM penjualan
     WHERE tanggal_penjualan >= DATE_FORMAT(
        DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'
     )
     AND tanggal_penjualan < DATE_FORMAT(
        CURDATE(), '%Y-%m-01'
     )"
);

$data_sebelumnya = mysqli_fetch_assoc($query_sebelumnya);

// Total barang terjual bulan ini
$query_barang = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(d.jumlah_produk), 0) AS total
     FROM detail_penjualan d
     INNER JOIN penjualan p
        ON d.penjualan_id = p.penjualan_id
     WHERE MONTH(p.tanggal_penjualan) = '$bulan_ini'
       AND YEAR(p.tanggal_penjualan) = '$tahun_ini'"
);

$data_barang = mysqli_fetch_assoc($query_barang);

// Rekap pendapatan per bulan untuk tahun pilihan
$query_rekap = mysqli_query(
    $koneksi,
    "SELECT
        MONTH(tanggal_penjualan) AS bulan,
        COUNT(*) AS transaksi,
        COALESCE(SUM(total_harga), 0) AS pendapatan
     FROM penjualan
     WHERE YEAR(tanggal_penjualan) = '$tahun'
     GROUP BY MONTH(tanggal_penjualan)
     ORDER BY MONTH(tanggal_penjualan)"
);

$rekap = [];

while ($row = mysqli_fetch_assoc($query_rekap)) {
    $rekap[(int) $row['bulan']] = $row;
}

$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - NovelKu Store</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .laporan-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .laporan-card {
            background: white;
            padding: 22px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .laporan-card h3 {
            margin: 0 0 12px;
            color: #777;
            font-size: 15px;
        }

        .laporan-card .angka {
            font-size: 23px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .filter-box form {
            display: flex;
            align-items: end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-box input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        @media (max-width: 800px) {
            .laporan-grid {
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
            <h1>Laporan Penjualan</h1>
            <span>Ringkasan pendapatan dan penjualan NovelKu</span>
        </div>
    </div>

    <div class="laporan-grid">
        <div class="laporan-card">
            <h3>Pendapatan Bulan Ini</h3>
            <div class="angka">
                Rp <?= number_format(
                    $data_bulan_ini['pendapatan'], 0, ',', '.'
                ) ?>
            </div>
        </div>

        <div class="laporan-card">
            <h3>Transaksi Bulan Ini</h3>
            <div class="angka">
                <?= number_format($data_bulan_ini['transaksi'], 0, ',', '.') ?>
            </div>
        </div>

        <div class="laporan-card">
            <h3>Barang Terjual Bulan Ini</h3>
            <div class="angka">
                <?= number_format($data_barang['total'], 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:25px;">
        <h2>Perbandingan Pendapatan</h2>

        <p>
            Bulan sebelumnya:
            <strong>
                Rp <?= number_format(
                    $data_sebelumnya['pendapatan'], 0, ',', '.'
                ) ?>
            </strong>
        </p>
    </div>

    <div class="filter-box">
        <h2>Rekap Pendapatan Tahunan</h2>

        <form method="GET">
            <div>
                <label for="tahun">Pilih Tahun</label><br>
                <input
                    type="number"
                    id="tahun"
                    name="tahun"
                    min="2000"
                    max="2100"
                    value="<?= $tahun ?>"
                    required
                >
            </div>

            <button type="submit" class="btn">Tampilkan</button>
        </form>
    </div>

    <div class="panel">
        <h2>Rekap Bulanan Tahun <?= $tahun ?></h2>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Jumlah Transaksi</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $no = 1;
                $total_transaksi = 0;
                $total_pendapatan = 0;

                for ($i = 1; $i <= 12; $i++) {
                    $transaksi = isset($rekap[$i])
                        ? (int) $rekap[$i]['transaksi'] : 0;

                    $pendapatan = isset($rekap[$i])
                        ? (float) $rekap[$i]['pendapatan'] : 0;

                    $total_transaksi += $transaksi;
                    $total_pendapatan += $pendapatan;
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $nama_bulan[$i] ?></td>
                        <td><?= number_format($transaksi, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($pendapatan, 0, ',', '.') ?></td>
                    </tr>
                <?php } ?>

                    <tr>
                        <th colspan="2">Total Tahun <?= $tahun ?></th>
                        <th><?= number_format($total_transaksi, 0, ',', '.') ?></th>
                        <th>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
