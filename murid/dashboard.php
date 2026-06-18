<?php
session_start();

// 1. Validasi pastikan yang masuk adalah murid
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'murid') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// 3. Ambil ID murid dari session untuk mencari nilainya + JOIN ke tabel mapel
$user_id = $_SESSION['id'];
$query_nilai = mysqli_query($koneksi, "SELECT hasil_ujian.*, mapel.nama_mapel 
                                       FROM hasil_ujian 
                                       LEFT JOIN mapel ON hasil_ujian.id_mapel = mapel.id_mapel 
                                       WHERE hasil_ujian.user_id = '$user_id' 
                                       ORDER BY hasil_ujian.tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Murid - Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Panel Murid</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-danger fw-bold" href="../auth/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow-sm border-0 mb-4 text-white bg-success">
        <div class="card-body p-4">
            <h2 class="fw-bold">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']); ?>! 👋</h2>
            <p class="mb-0">Silakan klik tombol di bawah untuk mulai mengerjakan ujian pilihan ganda yang tersedia.</p>
            <a href="ujian.php" class="btn btn-light text-success fw-bold mt-3 px-4">Mulai Ujian Sekarang</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold">Riwayat Nilai Ujian Kamu</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kategori Ujian</th> <th>Jumlah Benar</th>
                            <th>Jumlah Salah</th>
                            <th>Nilai Akhir</th>
                            <th>Tanggal Ujian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($query_nilai) > 0) {
                            while ($row = mysqli_fetch_assoc($query_nilai)) { 
                                // Jika id_mapel kosong, tampilkan tanda strip (-)
                                $mapel = !empty($row['nama_mapel']) ? htmlspecialchars($row['nama_mapel']) : '-';
                                
                                // Ambil data kategori ujian (mengubah teks menjadi huruf kapital semua agar rapi, misal: uts -> UTS)
                                $kategori = !empty($row['kategori_ujian']) ? strtoupper($row['kategori_ujian']) : '-';
                        ?>
                                <tr class="text-center">
                                    <td><?= $no++; ?></td>
                                    <td class="text-start fw-bold text-secondary"><?= $mapel; ?></td>
                                    <td>
                                        <?php if($kategori == 'UTS' || $kategori == 'UAS'): ?>
                                            <span class="badge bg-warning text-dark fw-bold px-3 py-2"><?= $kategori; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark fw-bold px-3 py-2"><?= $kategori; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success fw-bold"><?= $row['jumlah_benar']; ?></td>
                                    <td class="text-danger fw-bold"><?= $row['jumlah_salah']; ?></td>
                                    <td class="fw-bold text-primary fs-5"><?= number_format($row['nilai'], 2); ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Kamu belum pernah mengikuti ujian.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>