<?php
session_start();
require '../../db_connection.php';

// Cek apakah pengguna sudah login dan memiliki akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== '1') {
    header("Location: ../../login.php");
    exit();
}

// Inisialisasi variabel form
$nama = $alamat = $no_telp = $email = $role = "";
$isEditMode = false;

// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'add') {
        // Tambah User
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $no_telp = $_POST['no_telp'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO user (nama, alamat, no_telp, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nama, $alamat, $no_telp, $email, $password, $role);
        $stmt->execute();
        $alert_message = "User berhasil ditambahkan!";
    } elseif ($action === 'edit') {
        // Update User
        $id = $_POST['user_id'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $no_telp = $_POST['no_telp'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE user SET nama=?, alamat=?, no_telp=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("ssssii", $nama, $alamat, $no_telp, $email, $role, $id);
        $stmt->execute();
        $alert_message = "User berhasil diperbarui!";
    } elseif ($action === 'delete') {
        // Hapus User
        $id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $alert_message = "User berhasil dihapus!";
    }
}

// Mendapatkan data user untuk ditampilkan dalam tabel
$result = $conn->query("SELECT * FROM user");

// Menyediakan data untuk form edit jika tombol Edit ditekan
if (isset($_POST['action']) && $_POST['action'] === 'edit-load') {
    $id = $_POST['user_id'];
    $isEditMode = true;

    // Ambil data user berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    $nama = $userData['nama'];
    $alamat = $userData['alamat'];
    $no_telp = $userData['no_telp'];
    $email = $userData['email'];
    $role = $userData['role'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna</title>
    <link rel="stylesheet" href="../../css/manage.css">
</head>
<body>
<div class="container">
    <h2>Kelola Pengguna</h2>
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
    
    <!-- Tabel List User -->
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= $row['alamat']; ?></td>
                <td><?= $row['no_telp']; ?></td>
                <td><?= $row['email']; ?></td>
                <td>
                    <?php 
                        if ($row['role'] == 1) echo 'Admin';
                        elseif ($row['role'] == 2) echo 'Peserta';
                        elseif ($row['role'] == 3) echo 'Tenaga Pelatih';
                    ?>
                </td>
                <td>
                    <!-- Form Aksi Edit dan Delete -->
                    <form action="manage_user.php" method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                        <input type="hidden" name="action" value="edit-load">
                        <button type="submit">Edit</button>
                    </form>
                    <form action="manage_user.php" method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Form Tambah/Edit User -->
    <h3><?= $isEditMode ? "Edit" : "Tambah" ?> Pengguna</h3>
    <form action="manage_user.php" method="post">
        <input type="hidden" name="action" value="<?= $isEditMode ? "edit" : "add" ?>">
        <?php if ($isEditMode): ?>
            <input type="hidden" name="user_id" value="<?= $id ?>">
        <?php endif; ?>

        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" value="<?= $nama ?>" required>

        <label for="alamat">Alamat:</label>
        <input type="text" id="alamat" name="alamat" value="<?= $alamat ?>" required>

        <label for="no_telp">No Telepon:</label>
        <input type="text" id="no_telp" name="no_telp" value="<?= $no_telp ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $email ?>" required>

        <?php if (!$isEditMode): ?>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        <?php endif; ?>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="1" <?= $role == 1 ? 'selected' : '' ?>>Admin</option>
            <option value="2" <?= $role == 2 ? 'selected' : '' ?>>Peserta</option>
            <option value="3" <?= $role == 3 ? 'selected' : '' ?>>Tenaga Pelatih</option>
        </select>

        <button type="submit"><?= $isEditMode ? "Perbarui" : "Simpan" ?></button>
    </form>
</div>
</body>
</html>
