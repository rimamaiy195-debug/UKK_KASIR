<?php
include "koneksi.php";
include "partials/cek_login.php";

$query = mysqli_query($koneksi, "SELECT aktivitas.aktivitas_id, aktivitas.aktivitas, aktivitas.waktu, user.username, user.level FROM aktivitas LEFT JOIN `user` ON aktivitas.user_id = user.user_id ORDER BY aktivitas.aktivitas_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Aktivitas - NovelKu</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
	<?php 
	include "partials/sidebar.php";
	?>

	<main class="main">
		<div class="topbar">
			<div>
				<h1>Aktivitas</h1>
				<span>
					Riwayat aktivitas pengguna sistem
				</span>
			</div>
		</div>

		<div class="panel">
			<h2>Riwayat Aktivitas</h2>

			<input type="text" id="search" placeholder="🔍 Cari aktivitas..." onkeyup="searchTable()">
			<br><br>

			<div style="overflow-x:auto;">
				<table>
					<thead>
						<tr>
							<th>No</th>
							<th>Waktu</th>
							<th>Username</th>
							<th>Level</th>
							<th>Aktivitas</th>
						</tr>
					</thead>

					<tbody>
						<?php
						$no = 1;
						if (mysqli_num_rows($query) > 0) {
							while ($data = mysqli_fetch_assoc($query)) {
						?>

							<tr>
								<td><?= $no++ ?></td>
								<td><?= date('d-m-Y H:i:s', strtotime($data['waktu']) )?></td>
								<td><?= htmlspecialchars($data['username'] ?? '-') ?></td>
								<td><?= htmlspecialchars($data['level'] ?? '-') ?></td>
								<td><?= htmlspecialchars($data['aktivitas']) ?></td>
							</tr>
						<?php
							}
						} else {
						?>

						<tr>
							<td colspan="5" style="text-align:center;">Belum ada aktivitas.</td>
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
				let text = row.innerText.toLowerCase();

				row.style.display = text.includes(input) ? "" : "none";
			})
		}
	</script>

</body>
</html>