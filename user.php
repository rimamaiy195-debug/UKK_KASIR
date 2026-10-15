<?php

include "koneksi.php";
include "partials/cek_login.php";
include "partials/cek_level.php";
include "partials/catat_aktivitas.php";

// HANYA ADMIN
hanyaUntuk(['admin']);


// =========================
// HAPUS USER
// =========================

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];
    catatAktivitas(
    $koneksi,
    "Menghapus user $username"
);

    // Mencegah admin menghapus akun dirinya sendiri
    if ($id == $_SESSION['user_id']) {

        header("Location: user.php");
        exit;

    }

    mysqli_query(
        $koneksi,
        "DELETE FROM `user` WHERE user_id = '$id'"
    );

    header("Location: user.php");
    exit;
}


// =========================
// AMBIL DATA USER
// =========================

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM `user` ORDER BY user_id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola User - NovelKu</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>


<body>


<?php include "partials/sidebar.php"; ?>


<main class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>Kelola User</h1>

            <span>
                Kelola akun pengguna sistem
            </span>

        </div>


        <a
            href="tambah_user.php"
            class="btn"
        >
            + Tambah User
        </a>

    </div>



    <!-- TABEL USER -->

    <div class="panel">

        <h2>Daftar User</h2>
        <input type="text" id ="search" placeholder="🔍 Cari user..." onkeyup="searchTable()">
        <br><br>


        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Username</th>
                    <th>Level</th>
                    <th>Aksi</th>

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
                                $data['username']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data['level']
                            ) ?>
                        </td>


                        <td>

                            <a
                                href="edit_user.php?id=<?= $data['user_id'] ?>"
                                class="btn secondary"
                            >
                                Edit
                            </a>


                            <?php if ($data['user_id'] != $_SESSION['user_id']) { ?>

                                <a
                                    href="user.php?hapus=<?= $data['user_id'] ?>"
                                    class="btn"
                                    onclick="return confirm('Yakin ingin menghapus user ini?')"
                                >
                                    Hapus
                                </a>

                            <?php } ?>


                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>


</main>

<script>
        function searchTable() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(function(row) {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>


</body>

</html>