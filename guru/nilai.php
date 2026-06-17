<?php
session_start();

// 1. Validasi pastikan yang masuk adalah guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// 3. LOGIKA PROSES HAPUS (MENGGUNAKAN ID_RAPORT)
if (isset($_GET['hapus_id'])) {
    $id_raport = mysqli_real_escape_string($koneksi, $_GET['hapus_id']);
    
    // Perintah SQL menggunakan nama kolom database kamu yang asli: id_raport
    $query_hapus = mysqli_query($koneksi, "DELETE FROM raport_murid WHERE id_raport = '$id_raport'");
    
    if ($query_hapus) {
        echo "<script>
                alert('Data nilai berhasil dihapus!');
                window.location.href = 'nilai.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Gagal menghapus data!');
                window.location.href = 'nilai.php';
              </script>";
        exit;
    }
}

// 4. Query mengambil data nilai murid
$query_raport = mysqli_query($koneksi, "
    SELECT rm.*, u.nama AS nama_murid, m.nama_mapel 
    FROM raport_murid rm
    JOIN users u ON rm.user_id = u.id
    JOIN mapel m ON rm.id_mapel = m.id_mapel
    WHERE u.role = 'murid'
    ORDER BY u.nama ASC, m.nama_mapel ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Siswa - Panel Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Guru Panel</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-danger fw-bold" href="../auth/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Daftar Perolehan Nilai Murid (Per Mapel)</h2>
        <a href="dashboard.php" class="btn btn-secondary fw-bold">Kembali ke Dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold">Rekap Nilai Berdasarkan Kategori Ujian</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" width="5%" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle" width="20%">Nama Murid</th>
                            <th rowspan="2" class="align-middle" width="20%">Mata Pelajaran</th>
                            <th colspan="4" class="align-middle">Kategori Ujian</th>
                            <th rowspan="2" class="align-middle" width="10%">Nilai Akhir</th>
                            <th rowspan="2" class="align-middle" width="10%">Predikat</th>
                            <th rowspan="2" class="align-middle" width="10%">Aksi</th>
                        </tr>
                        <tr>
                            <th width="7%">Harian (10%)</th>
                            <th width="7%">Kuis (20%)</th>
                            <th width="7%">UTS (30%)</th>
                            <th width="7%">UAS (40%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($query_raport) > 0) {
                            while ($row = mysqli_fetch_assoc($query_raport)) { 
                                $harian = $row['nilai_harian'] ?? 0;
                                $kuis   = $row['nilai_kuis'] ?? 0;
                                $uts    = $row['nilai_uts'] ?? 0;
                                $uas    = $row['nilai_uas'] ?? 0;

                                // Mengambil data id_raport asli dari database
                                $id_data = $row['id_raport'];

                                // Hitung Nilai Akhir
                                $nilai_akhir = ($harian * 0.10) + ($kuis * 0.20) + ($uts * 0.30) + ($uas * 0.40);

                                // Tentukan Predikat
                                if ($nilai_akhir >= 85) { $predikat = 'A'; $bg_badge = 'success'; }
                                elseif ($nilai_akhir >= 75) { $predikat = 'B'; $bg_badge = 'primary'; }
                                elseif ($nilai_akhir >= 60) { $predikat = 'C'; $bg_badge = 'info text-dark'; }
                                else { $predikat = 'D'; $bg_badge = 'warning text-dark'; }
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td class="text-start text-capitalize fw-bold"><?php echo htmlspecialchars($row['nama_murid']); ?></td>
                                    <td class="text-start"><?php echo htmlspecialchars($row['nama_mapel']); ?></td>
                                    <td class="text-muted fw-bold"><?php echo $harian; ?></td>
                                    <td class="text-info fw-bold"><?php echo $kuis; ?></td>
                                    <td class="text-warning fw-bold"><?php echo $uts; ?></td>
                                    <td class="text-danger fw-bold"><?php echo $uas; ?></td>
                                    <td class="fw-bold text-dark fs-5"><?php echo number_format($nilai_akhir, 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $bg_badge; ?> p-2 fs-6 fw-bold">
                                            <?php echo $predikat; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="nilai.php?hapus_id=<?php echo $id_data; ?>" class="btn btn-sm btn-danger fw-bold" onclick="return confirm('Yakin ingin menghapus data nilai milik <?php echo htmlspecialchars($row['nama_murid']); ?>?');">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada data nilai.</td>
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