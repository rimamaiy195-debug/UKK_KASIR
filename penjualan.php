
<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/cek_level.php";

$query = mysqli_query(
    $koneksi,
    "SELECT
        penjualan.penjualan_id,
        penjualan.tanggal_penjualan,
        penjualan.total_harga,
        penjualan.diskon,
        pelanggan.nama_pelanggan
     FROM penjualan
     LEFT JOIN pelanggan
        ON penjualan.pelanggan_id = pelanggan.pelanggan_id
     ORDER BY penjualan.penjualan_id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan - NovelKu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include "partials/sidebar.php"; ?>

<main class="main">
    <div class="topbar">
        <div>
            <h1>Data Penjualan</h1>
            <span>Riwayat transaksi penjualan</span>
        </div>
    </div>

    <?php if (in_array($_SESSION['level'], ['admin', 'kasir'])) { ?>
        <div style="margin-bottom:20px;">
            <a href="transaksi.php" class="btn">+ Transaksi Baru</a>
        </div>
    <?php } ?>

    <div class="panel">
        <h2>Riwayat Penjualan</h2>

        <input
            type="text"
            id="search"
            placeholder="Cari transaksi..."
            onkeyup="searchTable()"
        >

        <br><br>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Penjualan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Diskon</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $no = 1;

                if ($query && mysqli_num_rows($query) > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>#<?= $data['penjualan_id'] ?></td>
                        <td>
                            <?= date(
                                'd-m-Y H:i',
                                strtotime($data['tanggal_penjualan'])
                            ) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(
                                $data['nama_pelanggan'] ?? '-'
                            ) ?>
                        </td>
                        <td>
                            <?= number_format(
                                $data['diskon'], 2, ',', '.'
                            ) ?>%
                        </td>
                        <td>
                            Rp <?= number_format(
                                $data['total_harga'], 0, ',', '.'
                            ) ?>
                        </td>
                        <td>
                            <a
                                href="detail_penjualan.php?id=<?= $data['penjualan_id'] ?>"
                                class="btn secondary"
                            >Detail</a>

                            <?php if ($_SESSION['level'] == 'admin') { ?>
                                <a
                                    href="edit_penjualan.php?id=<?= $data['penjualan_id'] ?>"
                                    class="btn"
                                >Edit</a>

                                <a
                                    href="hapus_penjualan.php?id=<?= $data['penjualan_id'] ?>"
                                    class="btn secondary"
                                    onclick="return confirm('Yakin hapus transaksi ini? Stok barang akan dikembalikan.')"
                                >Hapus</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">
                            Belum ada data penjualan.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function searchTable() {
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(function(row) {
        row.style.display = row.innerText.toLowerCase().includes(input)
            ? ""
            : "none";
    });
}
</script>

</body>
</html>
