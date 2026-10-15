<?php

include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";


// =========================
// HAPUS PRODUK
// =========================

if (isset($_GET['hapus'])) {

    // Hanya admin yang boleh menghapus
    if ($_SESSION['level'] != 'admin') {
        header("Location: produk.php");
        exit;
    }

    $id = $_GET['hapus'];
    catatAktivitas(
    $koneksi,
    "Menghapus produk $nama_produk"
);

    mysqli_query(
        $koneksi,
        "DELETE FROM produk WHERE produk_id = '$id'"
    );

    header("Location: produk.php");
    exit;
}


// =========================
// AMBIL DATA PRODUK
// =========================

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk ORDER BY produk_id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produk - NovelKu</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>

        .produk-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .produk-header h2 {
            margin: 0;
        }


        /* GRID PRODUK */

        .produk-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }


        /* CARD PRODUK */

        .produk-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: 0.2s;
        }

        .produk-card:hover {
            transform: translateY(-3px);
        }


        /* GAMBAR */

        .produk-gambar {
            width: 100%;
            height: 220px;
            background: #f5f5f5;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;
        }

        .produk-gambar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gambar-kosong {
            color: #999;
            font-size: 14px;
        }


        /* INFORMASI PRODUK */

        .produk-info {
            padding: 18px;
        }

        .produk-info h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .produk-harga {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .produk-stok {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }


        /* TOMBOL AKSI */

        .produk-aksi {
            display: flex;
            gap: 8px;
        }

        .produk-aksi .btn {
            text-decoration: none;
        }


        /* RESPONSIVE */

        @media (max-width: 900px) {

            .produk-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 600px) {

            .produk-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<?php include "partials/sidebar.php"; ?>


<main class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>Data Produk</h1>

            <span>
                Lihat produk dan stok
            </span>

        </div>


        <!-- TAMBAH PRODUK HANYA ADMIN -->

        <?php if ($_SESSION['level'] == 'admin') { ?>

            <a
                href="tambah_produk.php"
                class="btn"
            >
                + Tambah Produk
            </a>

        <?php } ?>

    </div>

            <input type="text" id ="searchProduk" placeholder="🔍 Cari produk..." onkeyup="searchProduk()">




    <!-- PANEL -->

    <div class="panel">


        <div class="produk-header">

            <div>

                <h2>
                    Daftar Produk
                </h2>

            </div>

        </div>



        <!-- GRID -->

        <div class="produk-grid">


            <?php while ($data = mysqli_fetch_assoc($query)) { ?>


                <div class="produk-card">


                    <!-- GAMBAR PRODUK -->

                    <div class="produk-gambar">


                        <?php if (!empty($data['gambar'])) { ?>


                            <img
                                src="uploads/<?= htmlspecialchars($data['gambar']) ?>"
                                alt="<?= htmlspecialchars($data['nama_produk']) ?>"
                            >


                        <?php } else { ?>


                            <div class="gambar-kosong">

                                Tidak ada gambar

                            </div>


                        <?php } ?>


                    </div>



                    <!-- INFORMASI -->

                    <div class="produk-info">


                        <h3>

                            <?= htmlspecialchars(
                                $data['nama_produk']
                            ) ?>

                        </h3>


                        <div class="produk-harga">

                            Rp
                            <?= number_format(
                                $data['harga'],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </div>


                        <div class="produk-stok">

                            Stok:

                            <strong>
                                <?= $data['stok'] ?>
                            </strong>

                        </div>



                        <!-- AKSI ADMIN -->

                        <?php if ($_SESSION['level'] == 'admin') { ?>


                            <div class="produk-aksi">


                                <a
                                    href="edit_produk.php?id=<?= $data['produk_id'] ?>"
                                    class="btn secondary"
                                >
                                    Edit
                                </a>


                                <a
                                    href="produk.php?hapus=<?= $data['produk_id'] ?>"
                                    class="btn"
                                    onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                >
                                    Hapus
                                </a>


                            </div>


                        <?php } ?>


                    </div>


                </div>


            <?php } ?>


        </div>


    </div>


</main>

<script>
function searchProduk() {

    let input = document
        .getElementById("searchProduk")
        .value
        .toLowerCase();

    let cards = document.querySelectorAll(".produk-card");

    cards.forEach(function(card) {

        let text = card.innerText.toLowerCase();

        if (text.includes(input)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }

    });

}
</script>


</body>

</html>