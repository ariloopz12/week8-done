<?php
session_start();
require '../db_connection.php';

// Cek apakah pengguna sudah login sebagai peserta (role = 2)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== '2') {
    header("Location: ../../login.php");
    exit();
}

// Ambil nama pengguna dari sesi
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : "Pengguna";

// Ambil data program dari database
$programs = $conn->query("SELECT * FROM program");

// Proses form pengajuan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_programs = $_POST['programs'] ?? [];
    $peserta_id = $_SESSION['user_id'];

    // Hapus pilihan sebelumnya
    $conn->query("DELETE FROM peserta_program WHERE peserta_id = $peserta_id");

    // Masukkan pilihan baru
    foreach ($selected_programs as $program_id) {
        $stmt = $conn->prepare("INSERT INTO peserta_program (peserta_id, program_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $peserta_id, $program_id);
        $stmt->execute();
    }

    $success_message = "Program pelatihan berhasil dipilih!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Program Pelatihan</title>
    <link rel="stylesheet" href="../css/pilihprogram.css">
</head>
<body>
    <div class="container">
        <!-- Sambutan dengan Nama Peserta -->
        <h1>Selamat datang, <?= htmlspecialchars($nama); ?>! Silakan pilih program di bawah</h1>
            <a href="../login.php" class="button" style="
            background-color: #12876f;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            "> << Kembali </a>

        <!-- Tampilkan pesan sukses jika ada -->
        <?php if (!empty($success_message)) : ?>
            <div class="alert success"><?= $success_message; ?></div>
        <?php endif; ?>

        <!-- Form untuk memilih program pelatihan -->
        <form action="pilih_program.php" method="POST">
            <?php while ($program = $programs->fetch_assoc()) : ?>
                <div>
                    <input type="checkbox" name="programs[]" value="<?= $program['id_program']; ?>" id="program_<?= $program['id_program']; ?>">
                    <label for="program_<?= $program['id_program']; ?>"><?= htmlspecialchars($program['nama_program']); ?></label>
                </div>
            <?php endwhile; ?>
            <button type="submit">Simpan Pilihan</button>
        </form>
    </div>
</body>
</html>
