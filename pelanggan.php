<?php

include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";


// =========================
// HAPUS PELANGGAN
// =========================

if (isset($_GET['hapus'])) {

    // Hanya admin dan kasir yang boleh menghapus
    if ($_SESSION['level'] != 'admin' && $_SESSION['level'] != 'kasir') {
        header("Location: pelanggan.php");
        exit;
    }

    $id = $_GET['hapus'];
    catatAktivitas(
    $koneksi,
    "Menghapus pelanggan $nama_pelanggan"
);

    mysqli_query(
        $koneksi,
        "DELETE FROM pelanggan WHERE pelanggan_id = '$id'"
    );

    header("Location: pelanggan.php");
    exit;
}


// =========================
// AMBIL DATA PELANGGAN
// =========================

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan ORDER BY pelanggan_id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pelanggan - FootWear Store</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<?php include "partials/sidebar.php"; ?>


<main class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>Data Pelanggan</h1>

            <span>
                Kelola data pelanggan
            </span>

        </div>


        <!-- TAMBAH PELANGGAN -->
        <!-- Hanya Admin dan Kasir -->

        <?php if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'kasir') { ?>

            <a
                href="tambah_pelanggan.php"
                class="btn"
            >
                + Tambah Pelanggan
            </a>

        <?php } ?>

    </div>



    <!-- PANEL -->

    <div class="panel">

        <h2>Daftar Pelanggan</h2>


        <!-- SEARCH -->

        <input
            type="text"
            id="search"
            placeholder="🔍 Cari pelanggan..."
            onkeyup="searchTable()"
        >

        <br><br>


        <table>

            <thead>

                <tr>

                    <th>No</th>

                    <th>Nama Pelanggan</th>

                    <th>Alamat</th>

                    <th>Nomor Telepon</th>


                    <!-- AKSI HANYA ADMIN DAN KASIR -->

                    <?php if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'kasir') { ?>

                        <th>Aksi</th>

                    <?php } ?>

                </tr>

            </thead>


            <tbody>

                <?php

                $no = 1;

                while ($data = mysqli_fetch_assoc($query)) {

                ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data['nama_pelanggan']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data['alamat']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data['nomor_telepon']
                            ) ?>
                        </td>


                        <!-- AKSI -->

                        <?php if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'kasir') { ?>

                            <td>

                                <a
                                    href="edit_pelanggan.php?id=<?= $data['pelanggan_id'] ?>"
                                    class="btn secondary"
                                >
                                    Edit
                                </a>


                                <a
                                    href="pelanggan.php?hapus=<?= $data['pelanggan_id'] ?>"
                                    class="btn"
                                    onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                                >
                                    Hapus
                                </a>

                            </td>

                        <?php } ?>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>


</main>


<!-- =========================
     LIVE SEARCH
========================= -->

<script>

function searchTable() {

    let input = document
        .getElementById("search")
        .value
        .toLowerCase();

    let rows = document.querySelectorAll("tbody tr");


    rows.forEach(function(row) {

        let text = row.innerText.toLowerCase();

        if (text.includes(input)) {

            row.style.display = "";

        } else {

            row.style.display = "none";

        }

    });

}

</script>


</body>

</html>