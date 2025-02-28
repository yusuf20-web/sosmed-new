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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>SosmedKu - Login</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-white bg-primary">
                        <h3>Silahkan Login</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <a href="register.php" class="text-decoration-none">Belum punya akun? Daftar disini</a>
                            </div>
                            <div class="mb-3 d-grid gap-2 col-6 mx-auto">
                                <button type="submit" class="btn btn-primary" name="login">Login</button>
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