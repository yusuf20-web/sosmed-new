<?php
session_start();
include 'db/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['email'])) {
    header("Location: login.php?silahkan_login");
    exit();
}

$email = $_SESSION['email'];

// Ambil data user yang sedang login
$queryUser = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
$dataUser = mysqli_fetch_assoc($queryUser);
$userId = $dataUser['id'];

// Proses posting status
if (isset($_POST['post'])) {
    $konten = trim($_POST['konten']);
    $gambar = null; // Default tidak ada gambar

    // Jika ada file gambar yang diunggah
    if (!empty($_FILES['gambar']['name'])) {
        $name = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $size = $_FILES['gambar']['size'];
        $error = $_FILES['gambar']['error'];
        
        // Ekstensi yang diperbolehkan
        $allwdExt = ['jpg', 'jpeg', 'png'];
        $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allwdExt) && $error === 0 && $size < 10000000) { // Maksimal 10MB
            $newFile = time() . "_" . uniqid() . "." . $fileExt; // Nama unik
            move_uploaded_file($tmp, "media/" . $newFile);
            $gambar = $newFile;
        } else {
            header("Location: index.php?error=gambar_invalid");
            exit();
        }
    }

    // Cek apakah postingan valid (tidak kosong)
    if (!empty($konten) || !empty($gambar)) {
        mysqli_query($conn, "INSERT INTO status (id_user, konten, gambar) VALUES ('$userId', '$konten', '$gambar')");
        header("Location: index.php?post=berhasil");
        exit();
    } else {
        header("Location: index.php?error=konten_kosong");
        exit();
    }
}

// Proses hapus status
if (isset($_POST['hapus'])) {
    $id_status = $_POST['id_status'];
    mysqli_query($conn, "DELETE FROM status WHERE id = '$id_status'");
    header("Location: index.php?hapus=berhasil");
    exit();
}

// Ambil semua status
$status = mysqli_query($conn, "SELECT status.*, user.nama FROM status LEFT JOIN user ON status.id_user = user.id WHERE status.id_user = '$userId' ORDER BY status.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - SosmedKu</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="light-theme">
    <!-- Navbar -->
    <?php include_once 'inc/navbar.php'; ?>
    <!-- /Navbar -->

    <!-- Konten Beranda -->
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Kiri -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Menu</h5>
                        <ul class="list-group">
                            <li class="list-group-item">Beranda</li>
                            <li class="list-group-item">Teman</li>
                            <li class="list-group-item">Grup</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Feed Utama -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <textarea class="form-control mb-2" name="konten" placeholder="Apa yang Anda pikirkan?" required></textarea>
                            <input type="file" name="gambar" class="form-control mb-2" accept="image/*">
                            <button type="submit" name="post" class="btn btn-primary">Posting</button>
                        </form>
                    </div>
                </div>

                <!-- Tampilkan Status -->
                <div class="card mb-3">
                    <div class="card-body">
                        <?php if (mysqli_num_rows($status) > 0) : ?> 
                            <?php while ($rowStatus = mysqli_fetch_assoc($status)) : ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($rowStatus['nama']); ?></h5>

                                    <!-- Jika status milik user yang login, tampilkan tombol Edit & Hapus -->
                                    <?php if ($rowStatus['id_user'] == $userId) : ?>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <strong>⋮</strong> <!-- ikon dropdown -->
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <!-- edit menggunakan anchor (<a>) -->
                                                <a href="editStatus.php?id_status=<?= $rowStatus['id']; ?>" class="dropdown-item">✏️ Edit</a>
                                            </li>
                                            <li>
                                                <!-- Form untuk Hapus -->
                                                <form action="index.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus status ini?');">
                                                    <input type="hidden" name="id_status" value="<?= $rowStatus['id']; ?>">
                                                    <button type="submit" name="hapus" class="dropdown-item text-danger">🗑️ Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Isi Konten Status -->
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($rowStatus['konten'])); ?></p> 
                                        <!-- Jika ada gambar, tampilkan -->
                                            <?php if (!empty($rowStatus['gambar'])) : ?>
                                                <img src="media/<?php echo htmlspecialchars($rowStatus['gambar']); ?>" class="img-fluid mb-3" alt="Gambar Status" style="width: 300px; height: 300px;">
                                            <?php endif; ?>
                                <!-- Waktu posting -->
                                <p class="card-text text-muted small">
                                    Diposting pada <?php echo date('d F Y H:i', strtotime($rowStatus['created_at'])); ?>
                                </p>
                                <hr>
                            <?php endwhile ?>
                        <?php else : ?>
                            <p class="text-muted">Belum ada status yang diposting.</p>
                        <?php endif ?>  
                    </div>
                </div>
                <!-- /Tampilkan Status -->
            </div>
            <!-- /Feed Utama -->

            <!-- Sidebar Kanan -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aktivitas Terbaru</h5>
                        <ul class="list-group">
                            <li class="list-group-item">Teman Anda mengirim pesan.</li>
                            <li class="list-group-item">Anda di-tag dalam foto.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Sidebar Kanan -->
        </div>
    </div>
    <!-- /Konten Beranda -->

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/theme.js"></script>
</body>
</html>
