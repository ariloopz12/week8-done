<?php
session_start();
require '../../db_connection.php';

// Cek apakah pengguna sudah login dan memiliki akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== '1') {
    header("Location: ../../login.php");
    exit();
}

// Inisialisasi variabel form
$judul_berita = $isi_berita = $tanggal_publikasi = $foto_berita = $kategori = "";
$isEditMode = false;

// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'add') {
        // Tambah Berita
        $judul_berita = $_POST['judul_berita'];
        $isi_berita = $_POST['isi_berita'];
        $tanggal_publikasi = $_POST['tanggal_publikasi'];
        $kategori = $_POST['kategori'];

        // Proses upload foto
        if (isset($_FILES['foto_berita']) && $_FILES['foto_berita']['error'] == 0) {
            $foto_berita = 'uploads/' . basename($_FILES['foto_berita']['name']);
            move_uploaded_file($_FILES['foto_berita']['tmp_name'], "../../" . $foto_berita);
        }

        $stmt = $conn->prepare("INSERT INTO berita (judul_berita, isi_berita, tanggal_publikasi, foto_berita, kategori) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $judul_berita, $isi_berita, $tanggal_publikasi, $foto_berita, $kategori);
        $stmt->execute();
        $alert_message = "Berita berhasil ditambahkan!";
    } elseif ($action === 'edit') {
        // Update Berita
        $id_berita = $_POST['id_berita'];
        $judul_berita = $_POST['judul_berita'];
        $isi_berita = $_POST['isi_berita'];
        $tanggal_publikasi = $_POST['tanggal_publikasi'];
        $kategori = $_POST['kategori'];

        // Cek dan proses upload foto baru jika ada
        if (isset($_FILES['foto_berita']) && $_FILES['foto_berita']['error'] == 0) {
            $foto_berita = 'uploads/' . basename($_FILES['foto_berita']['name']);
            move_uploaded_file($_FILES['foto_berita']['tmp_name'], "../../" . $foto_berita);
            $stmt = $conn->prepare("UPDATE berita SET judul_berita=?, isi_berita=?, tanggal_publikasi=?, foto_berita=?, kategori=? WHERE id_berita=?");
            $stmt->bind_param("sssssi", $judul_berita, $isi_berita, $tanggal_publikasi, $foto_berita, $kategori, $id_berita);
        } else {
            $stmt = $conn->prepare("UPDATE berita SET judul_berita=?, isi_berita=?, tanggal_publikasi=?, kategori=? WHERE id_berita=?");
            $stmt->bind_param("ssssi", $judul_berita, $isi_berita, $tanggal_publikasi, $kategori, $id_berita);
        }
        $stmt->execute();
        $alert_message = "Berita berhasil diperbarui!";
    } elseif ($action === 'delete') {
        // Hapus Berita
        $id_berita = $_POST['id_berita'];
        $stmt = $conn->prepare("DELETE FROM berita WHERE id_berita=?");
        $stmt->bind_param("i", $id_berita);
        $stmt->execute();
        $alert_message = "Berita berhasil dihapus!";
    }
}

// Mendapatkan data berita untuk ditampilkan dalam tabel
$result = $conn->query("SELECT * FROM berita");

// Menyediakan data untuk form edit jika tombol Edit ditekan
if (isset($_POST['action']) && $_POST['action'] === 'edit-load') {
    $id_berita = $_POST['id_berita'];
    $isEditMode = true;

    // Ambil data berita berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM berita WHERE id_berita = ?");
    $stmt->bind_param("i", $id_berita);
    $stmt->execute();
    $beritaData = $stmt->get_result()->fetch_assoc();

    $judul_berita = $beritaData['judul_berita'];
    $isi_berita = $beritaData['isi_berita'];
    $tanggal_publikasi = $beritaData['tanggal_publikasi'];
    $foto_berita = $beritaData['foto_berita'];
    $kategori = $beritaData['kategori'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Berita</title>
    <link rel="stylesheet" href="../../css/manage.css">
</head>
<body>
<div class="container">
    <h2>Kelola Berita</h2>
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
    
    <!-- Tabel List Berita -->
    <table>
        <thead>
        <tr>
            <th>ID Berita</th>
            <th>Judul</th>
            <th>Isi</th>
            <th>Tanggal Publikasi</th>
            <th>Foto</th>
            <th>Kategori</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_berita']; ?></td>
                <td><?= $row['judul_berita']; ?></td>
                <td><?= substr($row['isi_berita'], 0, 50); ?>...</td>
                <td><?= $row['tanggal_publikasi']; ?></td>
                <td><img src="../../<?= $row['foto_berita']; ?>" width="50" alt="Foto Berita"></td>
                <td><?= $row['kategori']; ?></td>
                <td>
                    <!-- Form Aksi Edit dan Delete -->
                    <form action="manage_berita.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_berita" value="<?= $row['id_berita']; ?>">
                        <input type="hidden" name="action" value="edit-load">
                        <button type="submit">Edit</button>
                    </form>
                    <form action="manage_berita.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_berita" value="<?= $row['id_berita']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Form Tambah/Edit Berita -->
    <h3><?= $isEditMode ? "Edit" : "Tambah" ?> Berita</h3>
    <form action="manage_berita.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $isEditMode ? "edit" : "add" ?>">
        <?php if ($isEditMode): ?>
            <input type="hidden" name="id_berita" value="<?= $id_berita ?>">
        <?php endif; ?>

        <label for="judul_berita">Judul Berita:</label>
        <input type="text" id="judul_berita" name="judul_berita" value="<?= $judul_berita ?>" required>

        <label for="isi_berita">Isi Berita:</label>
        <textarea id="isi_berita" name="isi_berita" required><?= $isi_berita ?></textarea>

        <label for="tanggal_publikasi">Tanggal Publikasi:</label>
        <input type="date" id="tanggal_publikasi" name="tanggal_publikasi" value="<?= $tanggal_publikasi ?>" required>

        <label for="foto_berita">Foto Berita:</label>
        <input type="file" id="foto_berita" name="foto_berita" <?= $isEditMode ? "" : "required" ?>>

        <label for="kategori">Kategori:</label>
        <input type="text" id="kategori" name="kategori" value="<?= $kategori ?>" required>

        <button type="submit"><?= $isEditMode ? "Perbarui" : "Simpan" ?></button>
    </form>
</div>
</body>
</html>
