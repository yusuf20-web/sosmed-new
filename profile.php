<?php
session_start();
include 'db/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php?silahkan_login");
    exit();
}

// ambil session email
    $email = $_SESSION['email'];

// ambil data user yang sedang login
    $queryProfile = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
    $userProfile = mysqli_fetch_assoc($queryProfile);
    $userIdProfile = $userProfile['id'];

// proses posting status
    if(isset($_POST['post'])){
        $konten = trim($_POST['konten']);
        $gambar = null; // Default tidak ada gambar

        // Jika ada file gambar yang diunggah
        if(!empty($_FILES['gambar']['name'])){
            $name = $_FILES['gambar']['name'];
            $tmp = $_FILES['gambar']['tmp_name'];
            $size = $_FILES['gambar']['size'];
            $error = $_FILES['gambar']['error'];
        
            // Ekstensi yang diperbolehkan
            $allowExt = ['jpg', 'png', 'jpeg'];
            $ext      = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
            // VALIDASI UKURAN FILE
            if (in_array($ext, $allowExt) && $error === 0 && $size < 10000000){
                $newFile = time(). "_". uniqid(). ".". $ext; // Nama unik
                move_uploaded_file($tmp, 'media/'. $newFile);
                $gambar = $newFile;
            } else {
                header('Location: profile.php?error=gambar_invalid');
                exit();
            }
        
        }

        // Cek apakah postingan ada (tidak kosong)
        if(!empty($konten) ||!empty($gambar)){
            mysqli_query($conn, "INSERT INTO status (id_user, konten, gambar) VALUES ('$userIdProfile', '$konten', '$gambar')");
            header('Location: profile.php?post=berhasil');
            exit();
        } else {
            header('Location: profile.php?error=konten_kosong');
            exit();
        }
    }
    
// Proses hapus status
if(isset($_POST['hapus'])) {
    $idStatus = $_POST['id_status'];
    mysqli_query($conn, "DELETE FROM status WHERE id = '$idStatus'");
    header('Location: profile.php?hapus=berhasil');
    exit();
}

// Ambil semua status
    $allStatus = mysqli_query($conn, "SELECT status.*, user.nama FROM status LEFT JOIN user ON status.id_user = user.id WHERE status.id_user = '$userIdProfile' ORDER BY status.id DESC");

// Untuk kolom gambar yg udah pernah di upload
    $allImages = mysqli_query($conn, "SELECT gambar FROM status WHERE id_user = '$userIdProfile' AND gambar IS NOT NULL");
    $rowAllImages = mysqli_fetch_all($allImages);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - SosmedKu</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="light-theme">
    <!-- Navbar -->
    <?php include_once 'inc/navbar.php'; ?>
    <!-- /Navbar -->

    <!-- Konten Profil -->
    <div class="container mt-3">
        <!-- Header Profile -->
        <div class="card mb-5">
                  <div class="card-body p-0 position-relative" style="height: 300px; background: #f0f0f0;">
                      <img src="<?php echo !empty($userProfile['foto_header']) ? 'upload/' . htmlspecialchars ($userProfile['foto_header']) : 'https://placehold.co/1200x200'; ?>  " class="img-fluid w-100 h-100">
                      <!-- Foto Profil -->
                      <img src="<?php echo !empty($userProfile['foto_profil']) ? 'upload/' . htmlspecialchars($userProfile['foto_profil']) : 'https://placehold.co/150x150'; ?>" 
                          alt="Foto Profil" 
                          class="rounded-circle border border-white border-3 position-absolute mb-5" 
                          style="width: 150px; height: 150px; bottom: -75px; left: 10%; transform: translateX(-70%);">
                  </div>
              </div>

        <!-- Layout Konten -->
        <div class="row justify-content-center">
            <!-- Galeri Foto (Kiri) -->
            <div class="col-lg-4">
                <div class="card m-auto">
                    <div class="card-body m-2">
                        <h5 class="card-title"><a href="#" class="text-decoration-none text-black">Galeri Foto</a></h5>
                        <div class="row row-cols-2 g-2 text-center">
                            <?php foreach ($allImages as $image) : ?>
                                <?php if (!empty($image['gambar']) && file_exists("media/" . $image['gambar'])) : ?>
                                    <div class="col">
                                        <img src="media/<?php echo htmlspecialchars($image['gambar']); ?>" 
                                            class="img-fluid rounded" 
                                            alt="galeri" 
                                            style="width: 100%; height: 150px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feed Status (Tengah) -->
            <div class="col-lg-8">
                <!-- Form Posting Status -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <textarea class="form-control mb-2" name="konten" placeholder="Apa yang Anda pikirkan?" required></textarea>
                            <div class="d-flex justify-content-between align-items-center">
                                <input type="file" name="gambar" class="form-control me-2" accept="image/*">
                                <button type="submit" name="post" class="btn btn-primary btn-sm px-3">Posting</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List Status yang Sudah Diposting -->
                <div class="card">
                    <div class="card-body">
                        <?php if (mysqli_num_rows($allStatus) > 0) : ?> 
                            <?php while ($rowStatus = mysqli_fetch_assoc($allStatus)) : ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($rowStatus['nama']); ?></h5>
                                    <?php if ($rowStatus['id_user'] == $userIdProfile) : ?>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">⋮</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="editStatusProfile.php?id_status=<?= $rowStatus['id']; ?>" class="dropdown-item">✏️ Edit</a></li>
                                                <li>
                                                    <form action="index.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus status ini?');">
                                                        <input type="hidden" name="id_status" value="<?= $rowStatus['id']; ?>">
                                                        <button type="submit" name="hapus" class="dropdown-item text-danger">🗑️ Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($rowStatus['konten'])); ?></p> 
                                <?php if (!empty($rowStatus['gambar'])) : ?>
                                    <img src="media/<?php echo htmlspecialchars($rowStatus['gambar']); ?>" class="img-fluid rounded mb-3" alt="Gambar Status" style="width: 300px; height: 300px;">
                                <?php endif; ?>
                                <p class="card-text text-muted small">Diposting pada <?php echo date('d F Y H:i', strtotime($rowStatus['created_at'])); ?></p>
                                <hr>
                            <?php endwhile ?>
                        <?php else : ?>
                            <p class="text-muted">Belum ada status yang diposting.</p>
                        <?php endif ?>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Konten Profil -->

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/theme.js"></script>
</body>
</html>

