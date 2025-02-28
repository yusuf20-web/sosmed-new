<?php
session_start();
include_once 'db/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php?silahkan_login");
    exit();
}

$email = $_SESSION['email'];

// Ambil ID status dari GET
if (isset($_GET['id_status'])) {
    $idStatus = $_GET['id_status'];

    // Ambil data status berdasarkan ID dan milik user yang login
    $query = mysqli_query($conn, "SELECT * FROM status WHERE id = '$idStatus'");
    $dataUser = mysqli_fetch_assoc($query);

    // Jika status tidak ditemukan, kembali ke index
    if (!$dataUser) {
        header("Location: index.php?error=status_tidak_ditemukan");
        exit();
    }

    $oldImage = $dataUser['gambar']; // Simpan gambar lama
} else {
    header("Location: index.php?error=no_id_provided");
    exit();
}

// Jika tombol edit ditekan
if (isset($_POST['edit'])) {
    $newContent = trim($_POST['konten']);
    $newImage = $oldImage; // Default tetap pakai gambar lama

    // Jika ada file gambar yang diunggah
    if (!empty($_FILES['gambar']['name'])) {
        $nama = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $error = $_FILES['gambar']['error'];

        // Tentukan ekstensi yang diizinkan
        $allwdExt = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($nama, PATHINFO_EXTENSION));

        // Validasi ekstensi
        if (!in_array($ext, $allwdExt)) {
            header("Location: editStatus.php?id_status=$idStatus&error=ekstensi_tidak_valid");
            exit();
        } else {
            // Hapus gambar lama jika ada
            if (!empty($oldImage) && file_exists("media/" . $oldImage)) {
                unlink("media/" . $oldImage);
            }

            // Simpan gambar baru dengan nama unik
            $newFileName = time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($tmp, "media/" . $newFileName);
            $newImage = $newFileName; // Perbarui variabel gambar baru
        }
    }

    // Pastikan konten tidak kosong
    if (!empty($newContent)) {
        // Update status dengan konten dan gambar baru (jika ada)
        $update = mysqli_query($conn, "UPDATE status SET konten='$newContent', gambar='$newImage' WHERE id = '$idStatus'");

        // Cek update berhasil atau tidak
        if ($update) {
            header("Location: index.php?edit=berhasil");
            exit();
        } else {
            header("Location: index.php?edit=gagal");
            exit();
        }
    } else {
        header("Location: editStatus.php?id_status=$idStatus&error=konten_kosong");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Status</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Status</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <textarea name="konten" class="form-control" rows="3" required><?php echo htmlspecialchars($dataUser['konten']); ?></textarea>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control mt-2" id="uploadFoto" name="gambar">
            </div>
            <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
