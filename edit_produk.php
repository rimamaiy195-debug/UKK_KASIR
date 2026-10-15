<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/catat_aktivitas.php";

catatAktivitas(
    $koneksi,
    "Mengubah produk $nama_produk"
);

$id = $_GET['id'];

$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE produk_id = '$id'");

$data = mysqli_fetch_assoc($query);
if (!$data) {
	echo "Produk tidak ditemukan.";
	exit;
}

if (isset($_POST['update'])) {
	$nama_produk = $_POST['nama_produk'];
	$harga = $_POST['harga'];
	$stok = $_POST['stok'];

	mysqli_query($koneksi, "UPDATE produk SET nama_produk = '$nama_produk', harga ='$harga', stok = '$stok' WHERE produk_id = '$id'");
	header("Location: produk.php");
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Produk - NovelKu</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
	<?php include "partials/sidebar.php"; ?>

	<main class="main">
		
		<div class="topbar">
			<h1>Edit Produk</h1>
			<span>Ubah data produk</span>
		</div>

		<div class="panel">
			<h2>Form Edit Produk</h2>
			<form method="POST">
				<div>
					<label>Nama Produk</label>
					<input type="text" name="nama_produk" value="<?= htmlspecialchars($data['nama_produk']) ?>" required>
				</div>

				<br>

				<div>
					<label>Harga</label>
					<input type="number" name="harga" value="<?= $data['harga'] ?>" min="0" required>
				</div>

				<br>

				<div>
					<label>Stok</label>
					<input type="number" name="stok" value="<?= $data['stok'] ?>" min="0" required>
				</div>

				<br>

				<button type="submit" name="update" class="btn">
					Simpan Perubahan
				</button>

				<a href="produk.php" class="btn secondary">Kembali</a>
			</form>
		</div>
	</main>

</body>
</html>