<?php
// Mulai session
session_start();

// Hapus semua session yang ada
session_unset();

// Hancurkan session
session_destroy();

// Arahkan pengguna kembali ke halaman login
header("location:login.php");
exit()
?>