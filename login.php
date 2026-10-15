<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM `user` WHERE username = '$username' AND password = '$password'");

    $data = mysqli_fetch_assoc($query);

    if ($data) {

        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['level'] = $data['level'];

        mysqli_query(
    $koneksi,
    "INSERT INTO aktivitas (user_id, aktivitas)
     VALUES (
        '{$data['user_id']}',
        'Login ke sistem'
     )"
);

        header("Location: index.php");
        exit;

    } else {

        $error = "Username atau password salah!";

    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - NovelKu</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>

        .login-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 380px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .login-box h1 {
            margin-bottom: 5px;
        }

        .login-box p {
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .login-button {
            width: 100%;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <h1>NovelKu</h1>

        <p>Silakan login untuk melanjutkan</p>

        <?php if (isset($error)) { ?>

            <div class="error">
                <?= $error ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label>Password</label>

                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" name="login" class="btn login-button">
                Login
            </button>

        </form>

    </div>

</div>

</body>

</html>