<?php

function catatAktivitas($koneksi, $aktivitas)
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    $user_id = $_SESSION['user_id'];

    $aktivitas = mysqli_real_escape_string($koneksi, $aktivitas);

    mysqli_query(
        $koneksi,
        "INSERT INTO aktivitas (user_id, aktivitas)
         VALUES ('$user_id', '$aktivitas')"
    );
}
?>