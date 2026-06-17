<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ujian_online";

// Membuat koneksi ke database MySQL
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>