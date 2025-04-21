<?php 
session_start();
include 'db/db.php';
// Jika button register di tekan
    if(isset($_POST['register'])){
        $nama       = $_POST['nama'];
        $username   = $_POST['username'];
        $email      = $_POST['email'];
        $password   = $_POST['password'];
    
    // Memasukkan data ke database
    $insert = mysqli_query($conn, "INSERT INTO user (nama, username, email, password) VALUES ('$nama','$username', '$email', '$password')");
    // cek kondisi apakah register berhasil atau tidak
        if($insert){
            header('Location:login.php?register=berhasil');
            exit();
        } else {
            header('Location:register.php?register=gagal');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
    <link href="css/form.css" rel="stylesheet">
    <title>SosmedKu - Register</title>
</head>
<body>
    <div class="container h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-md-6">
        <div class="form-card">
          <h3>Please Register <span style="color: #cce0ff;">SosmedKu</span></h3>
          <form action="" method="post">
            <div class="mb-3">
              <label for="nama">Nama</label>
              <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" required>
            </div>
            <div class="mb-3">
              <label for="email">Username</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
              <label for="email">Email</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email atau username" required>
            </div>
            <div class="mb-3">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="mb-3 text-center">
              <a href="login.php" class="form-link">Sudah punya akun? Silahkan log in</a>
            </div>
            <div class="d-grid gap-2 col-8 mx-auto">
              <button type="submit" class="btn btn-submit" name="register">Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/theme.js"></script>
</body>
</html>