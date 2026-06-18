<?php
session_start();
session_unset();
session_destroy(); // Menghapus session sesuai ketentuan tugas
header("Location: login.php"); // Kembali ke halaman login
exit;
?>