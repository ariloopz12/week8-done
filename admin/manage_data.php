<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== '1') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data</title>
    <link rel="stylesheet" href="../css/managedata.css">
</head>
<body>
    <header>
        <h1>Kelola Data Admin</h1>
    </header>
    <h1 style="padding: 5%;">Pilih Data yang akan di Ubah!</h1>
    <nav>
        <ul>
            <li><a href="./user/manage_user.php">Data Peserta</a></li>
            <li><a href="./user/manage_program.php">Data Program Pelatihan</a></li>
            <li><a href="./user/manage_berita.php">Data Berita</a></li>
            <li><a href="../admin/dashboard.php">Kembali</a></li>
        </ul>
    </nav>
</body>
</html>
