<?php
session_start();
require '../../db_connection.php';

// Cek apakah pengguna sudah login dan memiliki akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== '1') {
    header("Location: ../../login.php");
    exit();
}

// Inisialisasi variabel form
$nama_program = $deskripsi = $jadwal = $biaya = $materi = "";
$isEditMode = false;

// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'add') {
        // Tambah Program
        $nama_program = $_POST['nama_program'];
        $deskripsi = $_POST['deskripsi'];
        $jadwal = $_POST['jadwal'];
        $biaya = $_POST['biaya'];
        $materi = $_POST['materi'];

        $stmt = $conn->prepare("INSERT INTO program (nama_program, deskripsi, jadwal, biaya, materi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $nama_program, $deskripsi, $jadwal, $biaya, $materi);
        $stmt->execute();
        $alert_message = "Program berhasil ditambahkan!";
    } elseif ($action === 'edit') {
        // Update Program
        $id_program = $_POST['id_program'];
        $nama_program = $_POST['nama_program'];
        $deskripsi = $_POST['deskripsi'];
        $jadwal = $_POST['jadwal'];
        $biaya = $_POST['biaya'];
        $materi = $_POST['materi'];

        $stmt = $conn->prepare("UPDATE program SET nama_program=?, deskripsi=?, jadwal=?, biaya=?, materi=? WHERE id_program=?");
        $stmt->bind_param("sssisi", $nama_program, $deskripsi, $jadwal, $biaya, $materi, $id_program);
        $stmt->execute();
        $alert_message = "Program berhasil diperbarui!";
    } elseif ($action === 'delete') {
        // Hapus Program
        $id_program = $_POST['id_program'];
        $stmt = $conn->prepare("DELETE FROM program WHERE id_program=?");
        $stmt->bind_param("i", $id_program);
        $stmt->execute();
        $alert_message = "Program berhasil dihapus!";
    }
}

// Mendapatkan data program untuk ditampilkan dalam tabel
$result = $conn->query("SELECT * FROM program");

// Menyediakan data untuk form edit jika tombol Edit ditekan
if (isset($_POST['action']) && $_POST['action'] === 'edit-load') {
    $id_program = $_POST['id_program'];
    $isEditMode = true;

    // Ambil data program berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM program WHERE id_program = ?");
    $stmt->bind_param("i", $id_program);
    $stmt->execute();
    $programData = $stmt->get_result()->fetch_assoc();

    $nama_program = $programData['nama_program'];
    $deskripsi = $programData['deskripsi'];
    $jadwal = $programData['jadwal'];
    $biaya = $programData['biaya'];
    $materi = $programData['materi'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Program</title>
    <link rel="stylesheet" href="../../css/manage.css">
</head>
<body>
<div class="container">
    <h2>Kelola Program</h2>

    <a href="../manage_data.php" class="button" style="
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
    
    <!-- Menampilkan pesan alert jika ada -->
    <?php if (isset($alert_message)): ?>
        <div class="alert success"><?= $alert_message; ?></div>
    <?php endif; ?>
    
    <!-- Tabel List Program -->
    <table>
        <thead>
        <tr>
            <th>ID Program</th>
            <th>Nama Program</th>
            <th>Deskripsi</th>
            <th>Jadwal</th>
            <th>Biaya</th>
            <th>Materi</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_program']; ?></td>
                <td><?= $row['nama_program']; ?></td>
                <td><?= $row['deskripsi']; ?></td>
                <td><?= $row['jadwal']; ?></td>
                <td><?= $row['biaya']; ?></td>
                <td><?= $row['materi']; ?></td>
                <td>
                    <!-- Form Aksi Edit dan Delete -->
                    <form action="manage_program.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_program" value="<?= $row['id_program']; ?>">
                        <input type="hidden" name="action" value="edit-load">
                        <button type="submit">Edit</button>
                    </form>
                    <form action="manage_program.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_program" value="<?= $row['id_program']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus program ini?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Form Tambah/Edit Program -->
    <h3><?= $isEditMode ? "Edit" : "Tambah" ?> Program</h3>
    <form action="manage_program.php" method="post">
        <input type="hidden" name="action" value="<?= $isEditMode ? "edit" : "add" ?>">
        <?php if ($isEditMode): ?>
            <input type="hidden" name="id_program" value="<?= $id_program ?>">
        <?php endif; ?>

        <label for="nama_program">Nama Program:</label>
        <input type="text" id="nama_program" name="nama_program" value="<?= $nama_program ?>" required>

        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" required><?= $deskripsi ?></textarea>

        <label for="jadwal">Jadwal:</label>
        <input type="text" id="jadwal" name="jadwal" value="<?= $jadwal ?>" required>

        <label for="biaya">Biaya:</label>
        <input type="number" id="biaya" name="biaya" value="<?= $biaya ?>" required>

        <label for="materi">Materi:</label>
        <textarea id="materi" name="materi" required><?= $materi ?></textarea>

        <button type="submit"><?= $isEditMode ? "Perbarui" : "Simpan" ?></button>
    </form>
</div>
</body>
</html>
