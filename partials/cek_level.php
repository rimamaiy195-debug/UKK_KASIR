<?php

if (!isset($_SESSION['level'])) {
    header("Location: login.php");
    exit;
}

function hanyaUntuk($level_yang_diizinkan)
{
    if (!in_array($_SESSION['level'], $level_yang_diizinkan)) {
        header("Location: index.php");
        exit;
    }
}

?>