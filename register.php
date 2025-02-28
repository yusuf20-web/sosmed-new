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
    <title>SosmedKu - Regoster</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-white bg-primary">
                        <h3>Silahkan Register</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group mb-3">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <a href="login.php" class="text-decoration-none">Sudah punya akun? Login disini</a>
                            </div>
                            <div class="mb-3 d-grid gap-2 col-6 mx-auto">
                                <button type="submit" class="btn btn-primary" name="register">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/theme.js"></script>
</body>
</html>