<?php
require 'db_connection.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashing password
    $role = 2; // Role default sebagai peserta

    // Proses upload foto
    $foto = $_FILES['foto'];
    $foto_nama = time() . '_' . basename($foto['name']);
    $target_dir = 'uploads/';
    $target_file = $target_dir . $foto_nama;

    if (move_uploaded_file($foto['tmp_name'], $target_file)) {
        // Menyimpan data pengguna ke database
        $stmt = $conn->prepare("INSERT INTO user (nama, alamat, no_telp, email, password, foto, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $nama, $alamat, $no_telp, $email, $password, $foto_nama, $role);

        if ($stmt->execute()) {
            header("Location: login.php"); // Redirect ke halaman login setelah registrasi berhasil
            exit();
        } else {
            $error_message = "Terjadi kesalahan saat menyimpan data.";
        }
    } else {
        $error_message = "Gagal mengupload foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formulir Registrasi</title>
    <link rel="stylesheet" href="./css/register.css" />
</head>
<body>
    <div class="container">
        <h2>Formulir Registrasi</h2>
        <?php if (!empty($error_message)) : ?>
            <div class="error"><?= $error_message; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required />

            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" rows="3" required></textarea>

            <label for="no_telp">No Telepon:</label>
            <input type="text" id="no_telp" name="no_telp" required />

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required />

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />

            <label for="foto">Upload Foto Diri:</label>
            <input type="file" id="foto" name="foto" accept="image/*" required />

            <button type="submit" name="submit">Simpan</button>
            <div class="login-link">
                <p>Don't have an account? <a href="./login.php">Register here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
