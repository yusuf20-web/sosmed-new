<?php 
session_start();
include 'db/db.php';

// membuat kondisi jika button login di tekan
if (isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $id = $_POST['id'];
    
    // query untuk mencari email yang sesuai di database
    $login = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email' AND password = '$password'");

    // Jika email di temukan
if(mysqli_num_rows($login) > 0){
    $rowLogin = mysqli_fetch_assoc($login);

    // jika password yang diinput sesuai dengan yang ada di database
    if($password == $rowLogin['password']){
        // membuat session
        $_SESSION['id'] = $rowLogin['id'];
        $_SESSION['nama'] = $rowLogin['nama'];
        $_SESSION['username'] = $rowLogin['username'];
        $_SESSION['email'] = $rowLogin['email'];
        header('Location:index.php?login=berhasil');
        exit();
    } else {
        // jika password yang diinput tidak sesuai
        header('Location:login.php?login=gagal&reason=password');
        exit();
    }
} else {
    // Jika email tidak terdaftar
    header('Location:login.php?email_tidak_ada');
    exit();
}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SosmedKu - Login</title>
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/form.css" rel="stylesheet">
</head>
<body>
  <div class="container h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-md-6">
        <div class="form-card">
          <h3>Welcome Back to <span style="color: #cce0ff;">SosmedKu</span></h3>
          <form action="" method="post">
            <div class="mb-3">
              <label for="email">Email / Username</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email atau username" required>
            </div>
            <div class="mb-3">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="mb-3 text-center">
              <a href="register.php" class="form-link">Belum punya akun? Daftar disini</a>
            </div>
            <div class="d-grid gap-2 col-8 mx-auto">
              <button type="submit" class="btn btn-submit" name="login">Masuk</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
