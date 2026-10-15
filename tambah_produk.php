<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";



// TAMBAH PRODUK
if (isset($_POST['tambah'])) {

    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    $folder = "uploads/";

    catatAktivitas(
    $koneksi,
    "Menambahkan produk $nama_produk"
);

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $nama_gambar = time() . "_" . $gambar;

    move_uploaded_file($tmp, $folder . $nama_gambar);

    mysqli_query(
        $koneksi,
        "INSERT INTO produk (nama_produk, harga, stok, gambar)
         VALUES ('$nama_produk', '$harga', '$stok', '$nama_gambar')"
    );

    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk - NovelKu</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>

        .form-produk {
            max-width: 700px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
        }

        .preview {
            width: 180px;
            height: 180px;
            border: 2px dashed #ccc;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .preview span {
            color: #999;
            text-align: center;
        }

    </style>

</head>

<body>

<?php include "partials/sidebar.php"; ?>

<main class="main">

    <div class="topbar">

        <div>
            <h1>Tambah Produk</h1>
            <span>Tambahkan produk baru</span>
        </div>

    </div>


    <div class="panel form-produk">

        <h2>Informasi Produk</h2>

        <form method="POST" enctype="multipart/form-data">

            <!-- GAMBAR -->
            <div class="form-group">

                <label>Gambar Produk</label>

                <div class="preview" id="preview">

                    <span>Preview<br>gambar produk</span>

                    <img id="preview-img">

                </div>

                <input type="file"name="gambar"accept="image/*"requiredonchange="previewGambar(event)">

            </div>


            <!-- NAMA -->
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" placeholder="Masukkan nama produk...." required>
            </div>


            <!-- HARGA -->
            <div class="form-group">

                <label>Harga</label>
                <input type="number" name="harga" min="0" placeholder="Masukkan harga produk...." required>
            </div>


            <!-- STOK -->
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" min="0" placeholder="Masukkan stok produk...." required>
            </div>

            <button type="submit" name="tambah"class="btn">
                + Simpan Produk
            </button>

            <a href="produk.php" class="btn secondary">
                Batal
            </a>

        </form>

    </div>

</main>


<script>

function previewGambar(event) {

    const input = event.target;
    const img = document.getElementById("preview-img");
    const text = document.querySelector(".preview span");

    if (input.files && input.files[0]) {

        const reader = new FileReader();

        reader.onload = function(e) {

            img.src = e.target.result;
            img.style.display = "block";
            text.style.display = "none";

        };

        reader.readAsDataURL(input.files[0]);
    }
}

</script>

</body>

</html>