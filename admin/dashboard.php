<?php
session_start();
require '../db_connection.php'; // Pastikan file ini terhubung ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek apakah pengguna adalah admin
$isAdmin = $_SESSION['role'] === '1';

// Query untuk mendapatkan data peserta dan program yang telah dipilih
$query = "
    SELECT peserta.id, peserta.nama, program.nama_program
    FROM peserta_program
    JOIN user AS peserta ON peserta_program.peserta_id = peserta.id
    JOIN program ON peserta_program.program_id = program.id_program
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/managedata.css" />
</head>
<body>
    <header>
        <h1>Kelola Data Admin</h1>
    </header>
    
    <main>
        <h1 style="padding: 7% 0%;">Selamat Datang di Dashboard Admin, <?= htmlspecialchars($_SESSION['nama']); ?>!</h1>
        
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <?php if ($isAdmin): ?>
                    <li><a href="manage_data.php">Kelola Data</a></li>
                <?php endif; ?>
                <li><a href="../login.php">Logout</a></li>
            </ul>
        </nav>
        
        <section>
            <h2 style="text-align: center; padding: 3%;">Data Peserta yang Memilih Program Pelatihan</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Nama Peserta</th>
                        <th>Nama Program</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['nama_program']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Tidak ada data peserta yang memilih program pelatihan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
