<?php
// Informasi database
$host = 'localhost';      // Server database (biasanya localhost)
$dbname = 'db_lembaga_pelatihan';  // Nama database yang digunakan
$username = 'root';        // Username database MySQL
$password = '';            // Password database MySQL

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Memeriksa apakah koneksi berhasil atau tidak
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
