<?php
session_start();
if($_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// Ambil ID soal yang dikirim dari halaman soal.php
$id = (int)$_GET['id'];

// Jalankan query hapus data
mysqli_query($koneksi, "DELETE FROM soal WHERE id=$id");

// Kembalikan ke halaman daftar soal setelah sukses menghapus
header("Location: soal.php");
exit;
?>