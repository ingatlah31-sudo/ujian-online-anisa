<?php
session_start();

// Validasi pastikan yang masuk adalah guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

$pesan = "";

// PROSES SIMPAN NILAI
if (isset($_POST['simpan_nilai'])) {
    $user_id = $_POST['user_id'];
    $id_mapel = $_POST['id_mapel'];
    $uas = $_POST['nilai_uas'];
    $uts = $_POST['nilai_uts'];
    $kuis = $_POST['nilai_kuis'];
    $harian = $_POST['nilai_harian'];

    // LOGIKA HITUNG OTOMATIS SESUAI PERSENTASE CATATAN GURU
    $nilai_akhir = ($uas * 0.40) + ($uts * 0.30) + ($kuis * 0.20) + ($harian * 0.10);

    // LOGIKA MENENTUKAN PREDIKAT HURUF
    if ($nilai_akhir >= 90) {
        $predikat = "A";
    } elseif ($nilai_akhir >= 80) {
        $predikat = "B";
    } elseif ($nilai_akhir >= 70) {
        $predikat = "C";
    } else {
        $predikat = "D";
    }

    // Cek apakah murid ini sudah punya nilai di mapel tersebut
    $cek_nilai = mysqli_query($koneksi, "SELECT * FROM raport_murid WHERE user_id = '$user_id' AND id_mapel = '$id_mapel'");

    if (mysqli_num_rows($cek_nilai) > 0) {
        // Jika sudah ada, lakukan update
        $query = mysqli_query($koneksi, "UPDATE raport_murid SET nilai_uas='$uas', nilai_uts='$uts', nilai_kuis='$kuis', nilai_harian='$harian', nilai_akhir='$nilai_akhir', predikat='$predikat' WHERE user_id = '$user_id' AND id_mapel = '$id_mapel'");
    } else {
        // Jika belum ada, masukkan data baru (INSERT)
        $query = mysqli_query($koneksi, "INSERT INTO raport_murid (user_id, id_mapel, nilai_uas, nilai_uts, nilai_kuis, nilai_harian, nilai_akhir, predikat) VALUES ('$user_id', '$id_mapel', '$uas', '$uts', '$kuis', '$harian', '$nilai_akhir', '$predikat')");
    }

    if ($query) {
        $pesan = "<div class='alert alert-success'>Nilai Raport Berhasil Disimpan! Akhir: <b>$nilai_akhir</b> (Predikat: <b>$predikat</b>)</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal menyimpan data nilai.</div>";
    }
}

// Ambil data semua murid acak yang sudah daftar untuk pilihan dropdown form
$query_murid = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'murid' ORDER BY nama ASC");

// Ambil data mata pelajaran untuk pilihan dropdown form
$query_mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Nilai Raport Mapel - Panel Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Panel Guru - Input Nilai Mapel</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white me-3" href="soal.php">Kelola Soal</a>
            <a class="nav-link text-white me-3" href="nilai.php">Nilai Ujian Utama</a>
            <a class="nav-link text-danger fw-bold" href="../auth/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5" style="max-width: 700px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3 text-center">
            <h4 class="mb-0 fw-bold">Form Input Bobot Nilai Raport</h4>
            <p class="mb-0 small text-white-50">UAS (40%), UTS (30%), Kuis (20%), Harian (10%)</p>
        </div>
        <div class="card-body p-4">
            
            <?= $pesan; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Siswa / Murid</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih Murid --</option>
                        <?php while($m = mysqli_fetch_assoc($query_murid)) { ?>
                            <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['nama']); ?> (<?= htmlspecialchars($m['username']); ?>)</option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mata Pelajaran</label>
                    <select name="id_mapel" class="form-select" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php while($mp = mysqli_fetch_assoc($query_mapel)) { ?>
                            <option value="<?= $mp['id_mapel']; ?>"><?= htmlspecialchars($mp['nama_mapel']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row bg-light p-3 rounded mb-4 g-3 border">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-secondary">UAS (40%)</label>
                        <input type="number" name="nilai_uas" class="form-control" min="0" max="100" placeholder="0-100" required>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-secondary">UTS (30%)</label>
                        <input type="number" name="nilai_uts" class="form-control" min="0" max="100" placeholder="0-100" required>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-secondary">Kuis (20%)</label>
                        <input type="number" name="nilai_kuis" class="form-control" min="0" max="100" placeholder="0-100" required>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-secondary">Harian (10%)</label>
                        <input type="number" name="nilai_harian" class="form-control" min="0" max="100" placeholder="0-100" required>
                    </div>
                </div>

                <button type="submit" name="simpan_nilai" class="btn btn-primary w-100 py-2 fw-bold fs-5">Hitung & Simpan Nilai</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>