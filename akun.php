<?php 
session_start();
include 'db/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['email'])) {
    header('Location: login.php?silahkan_login');
    exit();
}

// Ambil email user yang sedang login
$email = $_SESSION['email'];

// Ambil data user berdasarkan email
$queryUser = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
if ($queryUser && mysqli_num_rows($queryUser) > 0) {
    $userData = mysqli_fetch_assoc($queryUser);
} else {
    header('Location: akun.php?email=tidak_ditemukan');
    exit();
}

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    // Ambil data dari form
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $emailBaru = $_POST['email'];
    $bio      = $_POST['bio'];

    // Gunakan password lama jika tidak diisi
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = $userData['password'];
    }

    // Inisialisasi variabel untuk foto_profil
    $fotoBaru = $userData['foto_profil']; // Gunakan foto_profil lama jika tidak ada yang diupload
    $fotoHeader = $userData['foto_header']; // Pakai foto header lama kalau tidak ada yang diupload

    // Jika user mengunggah foto_profil baru
    if (!empty($_FILES['foto_profil']['name'])){
      $fotoHeader = $_FILES['foto_profil']['name'];
      $tmpFoto = $_FILES['foto_profil']['tmp_name']; // Ambil path sementara
      $sizeFoto = $_FILES['foto_profil']['size'];
  
      // sortir ekstensi
      $ext = array('png', 'jpg', 'jpeg'); // ekstensi hanya png, jpg, jpeg
      $extFoto = pathinfo($fotoHeader, PATHINFO_EXTENSION);
  
      // validasi ekstensi file
      if (!in_array(strtolower($extFoto), $ext)) {
          header('Location: akun.php?foto_profil=ekstensi_tidak_valid');
          exit();
      } else {
          // Pastikan folder upload ada, jika tidak buat foldernya
          if (!is_dir('upload')) {
              mkdir('upload', 0777, true);
              exit();
          }
  
          // Proses upload file ke folder "upload"
          if (move_uploaded_file($tmpFoto, 'upload/' . $fotoHeader)) {
              // Update data user termasuk foto_profil baru
              $update = mysqli_query($conn, "UPDATE user SET nama='$nama', username='$username', email='$email', bio='$bio', foto_profil='$fotoHeader' WHERE email='$email'");
              
              // Cek apakah update berhasil
              if($update){
                  header('Location: akun.php?update=berhasil');
                  exit();
              } else {
                  header('Location: akun.php?update=gagal');
                  exit();
              }
          } else {
              header('Location: akun.php?foto_profil=gagal_diupload');
              exit();
          }
      }
  }
    // Jika user mengunggah foto_header baru
    if (!empty($_FILES['foto_header']['name'])){
      $fotoHeader = $_FILES['foto_header']['name'];
      $tmpFoto = $_FILES['foto_header']['tmp_name']; // Ambil path sementara
      $sizeFoto = $_FILES['foto_header']['size'];
  
      // sortir ekstensi
      $extHeader = array('png', 'jpg', 'jpeg'); // ekstensi hanya png, jpg, jpeg
      $extFotoHeader = pathinfo($fotoHeader, PATHINFO_EXTENSION);
  
      // validasi ekstensi file
      if (!in_array(strtolower($extFotoHeader), $extHeader)) {
          header('Location: akun.php?foto_header=ekstensi_tidak_valid');
          exit();
      } else {
          // Pastikan folder upload ada, jika tidak buat foldernya
          if (!is_dir('upload')) {
              mkdir('upload', 0777, true);
              exit();
          }
  
          // Proses upload file ke folder "upload"
          if (move_uploaded_file($tmpFoto, 'upload/' . $fotoHeader)) {
              // Update data user termasuk foto_header baru
              $update = mysqli_query($conn, "UPDATE user SET nama='$nama', username='$username', email='$email', bio='$bio', foto_header='$fotoHeader' WHERE email='$email'");
              
              // Cek apakah update berhasil
              if($update){
                  header('Location: akun.php?update=berhasil');
                  exit();
              } else {
                  header('Location: akun.php?update=gagal');
                  exit();
              }
          } else {
              header('Location: akun.php?foto_header=gagal_diupload');
              exit();
          }
      }
  }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Akun - SosmedKu</title>
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/theme.css">
 
</head>
<body>
  <!-- Navbar -->
  <?php include 'inc/navbar.php'; ?>
  <!-- /Navbar -->

  <!-- Form Edit Akun -->
  <div class="container mt-4">
    <div class="row">
      <div class="col-lg-6 mx-auto">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title text-center">Edit Akun</h3>
            <form action="" method="post" enctype="multipart/form-data">
              <!-- Foto Header -->
              <div class="card mb-5">
                  <div class="card-body p-0 position-relative" style="height: 200px; background: #f0f0f0;">
                      <img src="<?php echo !empty($userData['foto_header']) ? 'upload/' . htmlspecialchars ($userData['foto_header']) : 'https://placehold.co/1200x200'; ?>  " class="img-fluid w-100 h-100">
                      <!-- Foto Profil -->
                      <img src="<?php echo !empty($userData['foto_profil']) ? 'upload/' . htmlspecialchars($userData['foto_profil']) : 'https://placehold.co/150x150'; ?>" 
                          alt="Foto Profil" 
                          class="rounded-circle border border-white border-3 position-absolute mb-3" 
                          style="width: 150px; height: 150px; bottom: -75px; left: 50%; transform: translateX(-50%);">
                  </div>
              </div>
              <div class="form-group mb-3"> 
                <label for="foto_header">Upload Foto Header</label>  
                <input type="file" class="form-control mt-2" id="uploadFotoHeader" name="foto_header">
              </div>
              <div class="form-group mb-3"> 
                <label for="foto_profil">Upload Foto Profil</label>  
                <input type="file" class="form-control mt-2" id="uploadFoto" name="foto_profil">
              </div>
              <div class="form-group mb-3">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($userData['nama']); ?>" required>
              </div>
              <div class="form-group mb-3">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
              </div>
              <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
              </div>
              <div class="form-group mb-3">
                <label for="bio">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tulis sesuatu tentang dirimu..."><?php echo htmlspecialchars($userData['bio']); ?></textarea>
              </div>
              <div class="form-group mb-3">
                <label for="password">Password Baru</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($userData['password']); ?>">
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>