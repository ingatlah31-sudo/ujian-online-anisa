<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'murid') {
    header("Location: ../auth/login.php");
    exit;
}

// Menangkap data nama dari session login
$nama_siswa = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Inara'; 

// Tangkap data nilai hasil kalkulasi dari URL ujian.php
$nilai_akhir  = isset($_GET['nilai']) ? intval($_GET['nilai']) : 0;
$jumlah_benar = isset($_GET['benar']) ? intval($_GET['benar']) : 0;
$jumlah_salah = isset($_GET['salah']) ? intval($_GET['salah']) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian Anda - Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white py-3 text-center">
            <h4 class="mb-0 fw-bold">Ringkasan Hasil Ujian</h4>
        </div>
        <div class="card-body p-4">
            
            <table class="table table-borderless fs-5">
                <tr>
                    <td width="40%">Nama Siswa</td>
                    <td width="5%">:</td>
                    <td class="fw-bold"><?= htmlspecialchars($nama_siswa); ?></td>
                </tr>
                <tr>
                    <td>Jumlah Benar</td>
                    <td>:</td>
                    <td class="fw-bold text-success"><?= $jumlah_benar; ?></td>
                </tr>
                <tr>
                    <td>Jumlah Salah</td>
                    <td>:</td>
                    <td class="fw-bold text-danger"><?= $jumlah_salah; ?></td>
                </tr>
            </table>

            <hr>

            <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center border">
                <span class="fs-4 fw-bold">Nilai Akhir</span>
                <span class="badge bg-primary fs-4 px-4 py-2"><?= $nilai_akhir; ?></span>
            </div>

            <div class="row mt-4">
                <div class="col-6">
                    <a href="dashboard.php" class="btn btn-outline-secondary w-100 py-2">Kembali ke Dashboard</a>
                </div>
                <div class="col-6">
                    <a href="../auth/logout.php" class="btn btn-danger w-100 py-2">Keluar Aplikasi</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>