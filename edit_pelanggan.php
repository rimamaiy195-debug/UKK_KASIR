<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";

catatAktivitas(
    $koneksi,
    "Mengubah pelanggan $nama_pelanggan"
);

$id = $_GET['id'];

// Ambil data pelanggan
$query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE pelanggan_id = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Pelanggan tidak ditemukan.";
    exit;
}

// UPDATE PELANGGAN
if (isset($_POST['update'])) {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomor_telepon = $_POST['nomor_telepon'];

    mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan = '$nama_pelanggan', alamat = '$alamat', nomor_telepon = '$nomor_telepon'WHERE pelanggan_id = '$id'");

    header("Location: pelanggan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan - NovelKu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<?php include "partials/sidebar.php"; ?>

<main class="main">

    <div class="topbar">
        <h1>Edit Pelanggan</h1>
        <span>Ubah data pelanggan</span>
    </div>

    <div class="panel">

        <h2>Form Edit Pelanggan</h2>

        <form method="POST">

            <div>
                <label>Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" value="<?= htmlspecialchars($data['nama_pelanggan']) ?>"required>
            </div>

            <br>

            <div>
                <label>Alamat</label>
                <textarea name="alamat" required><?= htmlspecialchars($data['alamat']) ?></textarea>
            </div>

            <br>

            <div>
                <label>Nomor Telepon</label>
                <input type="text" name="nomor_telepon" value="<?= htmlspecialchars($data['nomor_telepon']) ?>" required>
            </div>

            <br>

            <button type="submit" name="update" class="btn">
                Simpan Perubahan
            </button>

            <a href="pelanggan.php" class="btn secondary">
                Kembali
            </a>

        </form>

    </div>

</main>

</body>
</html>