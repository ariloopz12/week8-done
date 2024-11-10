<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tempat_lahir = $_POST['tempat_lahir'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $hobi = $_POST['hobi'] ?? '';

    // Proses Upload Foto
    $uploadDir = 'uploads/';
    $foto = $_FILES['foto'];

    if ($foto['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($foto['name']);
        $fileTmpPath = $foto['tmp_name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileType;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            $fotoPath = $destination;
        } else {
            $error = "Gagal mengunggah foto.";
        }
    } else {
        $error = "Terjadi kesalahan saat mengunggah foto.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Registrasi</title>
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .card {
            background-color: <?= $jenis_kelamin === 'Laki-laki' ? '#3563e9' : '#e94560'; ?>;
            color: <?= $jenis_kelamin === 'Laki-laki' ? '#000' : '#fff'; ?>;
            width: 350px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 10px auto;
        }

        .card p {
            margin: 0 0 10px;
            font-size: 0.9rem;
        }

        .card span {
            font-weight: bold;
        }

        .back-button {
            display: inline-block;
            margin-top: 10px;
            background-color: #aaa;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }

        .back-button:hover {
            background-color: #888;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if (isset($fotoPath)) : ?>
            <img src="<?= htmlspecialchars($fotoPath); ?>" alt="Foto Diri">
        <?php endif; ?>
        <p><span>Nama:</span> <?= htmlspecialchars($nama) ?: 'Tidak ada data'; ?></p>
        <p><span>Jenis Kelamin:</span> <?= htmlspecialchars($jenis_kelamin) ?: 'Tidak ada data'; ?></p>
        <p><span>Tempat, Tanggal Lahir:</span> <?= htmlspecialchars($tempat_lahir) ?: 'Tidak ada data'; ?>, <?= htmlspecialchars($tanggal_lahir) ?: 'Tidak ada data'; ?></p>
        <p><span>Alamat:</span> <?= htmlspecialchars($alamat) ?: 'Tidak ada data'; ?></p>
        <p><span>Email:</span> <?= htmlspecialchars($email) ?: 'Tidak ada data'; ?></p>
        <p><span>No HP:</span> <?= htmlspecialchars($no_hp) ?: 'Tidak ada data'; ?></p>
        <p><span>Hobi:</span> <?= htmlspecialchars($hobi) ?: 'Tidak ada data'; ?></p>
        <a href="index.html" class="back-button">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>
