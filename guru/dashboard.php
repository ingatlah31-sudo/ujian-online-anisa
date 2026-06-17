<?php
session_start();
// Memastikan hanya user dengan role guru yang bisa mengakses halaman ini
if($_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru - Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Guru Panel</a>
    <div class="navbar-nav">
      <a class="nav-link active" href="dashboard.php">Dashboard</a>
      <a class="nav-link" href="soal.php">Kelola Soal</a>
      <a class="nav-link" href="nilai.php">Lihat Nilai</a>
      <a class="nav-link text-danger" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
      <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Selamat Datang, <?= $_SESSION['nama']; ?>! 🧑‍🏫</h1>
        <p class="col-md-8 fs-4 text-muted">Melalui halaman panel ini, Anda dapat membuat soal ujian pilihan ganda baru, mengubah data soal, hingga memantau hasil nilai ujian para murid secara *real-time*.</p>
        <a href="soal.php" class="btn btn-primary btn-lg">Mulai Kelola Soal</a>
      </div>
    </div>
</div>
</body>
</html>