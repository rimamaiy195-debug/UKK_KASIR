<?php

include "koneksi.php";
include "partials/cek_login.php";
include "partials/cek_level.php";
include "partials/catat_aktivitas.php";


// HANYA ADMIN
hanyaUntuk(['admin']);


// =========================
// TAMBAH USER
// =========================

if (isset($_POST['tambah'])) {

    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $level = $_POST['level'];

catatAktivitas(
    $koneksi,
    "Menambahkan user $username"
);
    // Cek username sudah ada atau belum

    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM `user`
         WHERE username = '$username'"
    );


    if (mysqli_num_rows($cek) > 0) {

        $error = "Username sudah digunakan!";

    } else {

        mysqli_query(
            $koneksi,
            "INSERT INTO `user`
            (username, password, level)
            VALUES
            ('$username', '$password', '$level')"
        );


        header("Location: user.php");
        exit;

    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah User - NovelKu</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>


<body>


<?php include "partials/sidebar.php"; ?>


<main class="main">


    <div class="topbar">

        <div>

            <h1>Tambah User</h1>

            <span>
                Tambahkan akun pengguna baru
            </span>

        </div>

    </div>



    <div class="panel">

        <h2>Form User</h2>


        <?php if (isset($error)) { ?>

            <div
                style="
                    background:#f8d7da;
                    color:#842029;
                    padding:10px;
                    border-radius:8px;
                    margin-bottom:15px;
                "
            >
                <?= $error ?>
            </div>

        <?php } ?>


        <form method="POST">


            <div>

                <label>
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >

            </div>


            <br>


            <div>

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>


            <br>


            <div>

                <label>
                    Level
                </label>

                <select
                    name="level"
                    required
                >

                    <option value="">
                        -- Pilih Level --
                    </option>

                    <option value="admin">
                        Admin
                    </option>

                    <option value="kasir">
                        Kasir
                    </option>

                    <option value="owner">
                        Owner
                    </option>

                </select>

            </div>


            <br>


            <button
                type="submit"
                name="tambah"
                class="btn"
            >
                Simpan User
            </button>


            <a
                href="user.php"
                class="btn secondary"
            >
                Batal
            </a>


        </form>

    </div>


</main>


</body>

</html>