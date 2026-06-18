<?php
session_start();
// Pastikan hanya guru yang bisa menghapus
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}

$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// Perintah mengosongkan tabel riwayat
$hapus_hasil  = mysqli_query($koneksi, "TRUNCATE TABLE hasil_ujian");
$hapus_raport = mysqli_query($koneksi, "TRUNCATE TABLE raport_murid");

if ($hapus_hasil && $hapus_raport) {
    echo "<script>
            alert('Semua riwayat hasil ujian berhasil dihapus bersih!');
            window.location='dashboard.php'; 
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus riwayat!');
            window.location='dashboard.php';
          </script>";
}
?>