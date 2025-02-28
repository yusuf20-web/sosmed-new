<?php 
session_start();
include_once 'db/db.php';

// Pastikan user sudah login
    if (!isset($_SESSION['email'])) {
        header("Location: login.php?silahkan_login");
        exit();
    }

// Ambil email dari session
$email = $_SESSION['email'];

// ambil id status dari get
if(isset($_GET['id_status'])){
    $idStatusProfil = $_GET['id_status'];

    // Ambil data status berdasarkan id
    $queryStatusProfil = mysqli_query($conn, "SELECT * FROM status WHERE id = '$idStatusProfil'");
    $dataStatusProfil = mysqli_fetch_assoc($queryStatusProfil);

    // JIka status not found, kembalikan ke index
    if(!$dataStatusProfil){
        header("Location: profile.php?error=status_tidak_ditemukan");
        exit();
    }

    // simpan gambar lama
    $gambarLama = $dataStatusProfil['gambar'];

} else {
    header("Location: profile.php?error=id_status_tidak_ada");
    exit();
}

// JIka tombol edit di tekan
    if(isset($_POST['edit'])){
        $kontenBaru = trim($_POST['kontenProfil']);
        $gambarBaru = $gambarLama; // Default tetap pakai gambar lama

        // Jika ada file gambar yang diunggah
        if(!empty($_FILES['gambarProfil']['name'])){
            $namaGambar = $_FILES['gambarProfil']['name'];
            $tmpProfil = $_FILES['gambarProfil']['tmp_name'];
            $errorProfil = $_FILES['gambarProfil']['error'];

            // Ekstensi yang diperbolehkan
            $extValid = ['jpg', 'jpeg', 'png'];
            $extProfil = strtolower(pathinfo($namaGambar, PATHINFO_EXTENSION));

            // VALIDASI ekstensi
            if (!in_array($extProfil, $extValid)) {
                header("Location: editStatusProfile.php?id_status=$idStatusProfil&error=ekstensi_tidak_valid");
                exit();
            } else {
                // Hapus gambar lama jika ada
                if (!empty($gambarLama) && file_exists("media/" . $gambarLama)) {
                    unlink("media/". $gambarLama);
                }

                // Simpan gambar baru dengan nama unik
                $namaBaru = time(). "_". uniqid(). ".". $extProfil;
                move_uploaded_file($tmpProfil, "media/". $namaBaru);
                $gambarBaru = $namaBaru; // Perbarui variabel gambar baru
            }
    }

    // Pastikan konten tidak kosong
    if(!empty($kontenBaru)){
        // Update status dengan konten dan gambar baru (jika ada)
        $updateStatus = mysqli_query($conn, "UPDATE status SET konten='$kontenBaru', gambar='$gambarBaru' WHERE id = '$idStatusProfil'");

        // Cek update
        if($updateStatus){
            header("Location: profile.php?edit=berhasil");
            exit();
        } else {
            header("Location: profile.php?edit=gagal");
        }
    } else {
        header("Location: editStatusProfile.php?id_status=$idStatusProfil&error=konten_kosong");
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
                <textarea name="kontenProfil" class="form-control" rows="3" required><?php echo htmlspecialchars($dataStatusProfil['konten']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="gambarProfil">Masukan gambar</label>
                <input type="file" class="form-control mt-2" id="uploadGambarProfil" name="gambarProfil">
            </div>
            <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
            <a href="profile.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
