<?php 
$host = "localhost";
$username = "root";
$password = "";
$db = "sosmed_new";

$conn = mysqli_connect($host,$username,$password,$db);

if (!$conn) {
    die("Koneksi Gagal: ". mysqli_connect_error());
}
?>