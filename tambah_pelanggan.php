<?php

include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";


// =========================
// TAMBAH PELANGGAN
// =========================

if (isset($_POST['tambah'])) {

    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomor_telepon = $_POST['nomor_telepon'];
    
catatAktivitas(
    $koneksi,
    "Menambahkan pelanggan $nama_pelanggan"
);

    mysqli_query(
        $koneksi,
        "INSERT INTO pelanggan
        (nama_pelanggan, alamat, nomor_telepon)
        VALUES
        ('$nama_pelanggan', '$alamat', '$nomor_telepon')"
    );


    header("Location: pelanggan.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Pelanggan - NovelKu</title>

    <link rel="stylesheet" href="assets/style.css">

</head>


<body>


<?php include "partials/sidebar.php"; ?>


<main class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>Tambah Pelanggan</h1>

            <span>
                Tambahkan data pelanggan baru
            </span>

        </div>

    </div>



    <!-- FORM -->

    <div class="panel">

        <h2>Form Pelanggan</h2>


        <form method="POST">


            <div>

                <label>
                    Nama Pelanggan
                </label>

                <input
                    type="text"
                    name="nama_pelanggan"
                    placeholder="Masukkan nama pelanggan"
                    required
                >

            </div>


            <br>


            <div>

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    placeholder="Masukkan alamat"
                    required
                ></textarea>

            </div>


            <br>


            <div>

                <label>
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="nomor_telepon"
                    placeholder="Masukkan nomor telepon"
                    required
                >

            </div>


            <br>


            <button
                type="submit"
                name="tambah"
                class="btn"
            >
                Simpan Pelanggan
            </button>


            <a
                href="pelanggan.php"
                class="btn secondary"
            >
                Batal
            </a>


        </form>

    </div>


</main>


</body>

</html>