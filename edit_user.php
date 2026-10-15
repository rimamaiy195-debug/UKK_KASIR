<?php
include "koneksi.php";
include "partials/cek_login.php";
include "partials/cek_level.php";
include "partials/catat_aktivitas.php";

catatAktivitas(
    $koneksi,
    "Mengubah user $username"
);
// HANYA ADMIN
hanyaUntuk(['admin']);


// =========================
// CEK ID USER
// =========================

if (!isset($_GET['id'])) {
    header("Location: user.php");
    exit;
}

$id = $_GET['id'];


// =========================
// AMBIL DATA USER
// =========================

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM `user`
     WHERE user_id = '$id'"
);

$data = mysqli_fetch_assoc($query);


if (!$data) {
    header("Location: user.php");
    exit;
}


// =========================
// PROSES UPDATE
// =========================

if (isset($_POST['simpan'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $level = $_POST['level'];


    // =========================
    // PASSWORD KOSONG
    // PASSWORD LAMA TETAP
    // =========================

    if ($password == '') {

        $update = mysqli_query(
            $koneksi,
            "UPDATE `user`
             SET
                username = '$username',
                level = '$level'
             WHERE user_id = '$id'"
        );


    } else {

        // =========================
        // PASSWORD BARU → MD5
        // =========================

        $password = md5($password);


        $update = mysqli_query(
            $koneksi,
            "UPDATE `user`
             SET
                username = '$username',
                password = '$password',
                level = '$level'
             WHERE user_id = '$id'"
        );
    }


    // =========================
    // HASIL UPDATE
    // =========================

    if ($update) {

        header("Location: user.php");
        exit;

    } else {

        $error = "Data user gagal diubah!";

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

    <title>Edit User - FootWear Store</title>

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

            <h1>Edit User</h1>

            <span>
                Ubah data pengguna sistem
            </span>

        </div>

    </div>



    <div class="panel">

        <h2>Edit User</h2>


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
                <?= htmlspecialchars($error) ?>
            </div>

        <?php } ?>


        <form method="POST">


            <!-- USERNAME -->

            <div>

                <label>
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars($data['username']) ?>"
                    placeholder="Masukkan username"
                    required
                >

            </div>


            <br>


            <!-- PASSWORD -->

            <div>

                <label>
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                >

            </div>


            <br>


            <!-- LEVEL -->

            <div>

                <label>
                    Level
                </label>

                <select
                    name="level"
                    required
                >

                    <option
                        value="admin"
                        <?= $data['level'] == 'admin' ? 'selected' : '' ?>
                    >
                        Admin
                    </option>


                    <option
                        value="kasir"
                        <?= $data['level'] == 'kasir' ? 'selected' : '' ?>
                    >
                        Kasir
                    </option>


                    <option
                        value="owner"
                        <?= $data['level'] == 'owner' ? 'selected' : '' ?>
                    >
                        Owner
                    </option>

                </select>

            </div>


            <br><br>


            <!-- TOMBOL -->

            <button
                type="submit"
                name="simpan"
                class="btn"
            >
                Simpan Perubahan
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