<?php
session_start();
require 'db_connection.php'; // Koneksi ke database

// Proses login setelah form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Persiapkan dan jalankan query untuk mencari pengguna berdasarkan email
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama']; // Menyimpan nama pengguna dalam sesi

        // Arahkan berdasarkan role
        if ($_SESSION['role'] === '1') {
            header("Location: ./admin/dashboard.php");
        } elseif ($_SESSION['role'] === '2') {
            header("Location: ./user/pilih_program.php");
        }
        exit();
    } else {
        $error_message = "Email atau Password salah. Coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        <?php if (!empty($error_message)) : ?>
            <div class="error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
    </form>
    <div class="register-link">
        <p>Don't have an account? <a href="./register.php">Register here</a></p>
    </div>
</div>
</body>
</html>
