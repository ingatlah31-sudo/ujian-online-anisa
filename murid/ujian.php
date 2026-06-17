<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'murid') {
    header("Location: ../auth/login.php");
    exit;
}

// Hubungkan ke database kamu
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// Menangkap data ID dan Nama murid dari session login
$user_id = $_SESSION['id'] ?? 1; 
$nama_siswa = $_SESSION['nama'] ?? 'Siswa';

$show_soal = false;
$soal_list = [];
$selected_mapel = "";
$selected_kategori = "";

if (isset($_POST['pilih_ujian'])) {
    $selected_mapel = $_POST['id_mapel'];
    $selected_kategori = $_POST['kategori_ujian'];

    // Ambil soal berdasarkan mapel dan kategori, urutkan dari ID terkecil agar konsisten di layar
    $query_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE id_mapel = '$selected_mapel' AND kategori_ujian = '$selected_kategori' ORDER BY id ASC");
    
    if (mysqli_num_rows($query_soal) > 0) {
        $show_soal = true;
        while ($row = mysqli_fetch_assoc($query_soal)) {
            $soal_list[] = $row;
        }
    } else {
        echo "<script>alert('Maaf, belum ada soal untuk Mapel dan Jenis Ujian ini!'); window.location='ujian.php';</script>";
        exit;
    }
}

if (isset($_POST['submit_jawaban'])) {
    $id_mapel = $_POST['id_mapel'];
    $kategori_ujian = $_POST['kategori_ujian'];
    $jawaban_murid = $_POST['jawaban'] ?? []; 

    $total_soal = count($jawaban_murid);
    $jawaban_benar = 0;

    // Ambil nama mapel dulu dari database untuk menentukan kunci jawaban yang cocok
    $q_mapel = mysqli_query($koneksi, "SELECT nama_mapel FROM mapel WHERE id_mapel = '$id_mapel'");
    $r_mapel = mysqli_fetch_assoc($q_mapel);
    $nama_mapel_aktif = strtolower($r_mapel['nama_mapel'] ?? '');

    $kunci_array = [];

    // LINGKUNGAN KUNCI JAWABAN - SUDAH DISINKRONKAN DENGAN DATA KAMU
    if (strpos($nama_mapel_aktif, 'matematika') !== false || strpos($nama_mapel_aktif, 'mtk') !== false) {
        if ($kategori_ujian == 'harian') {
            $kunci_array = [1 => 'B', 2 => 'A', 3 => 'B', 4 => 'C', 5 => 'A'];
        } elseif ($kategori_ujian == 'kuis') {
            $kunci_array = [1 => 'B', 2 => 'C', 3 => 'B', 4 => 'A', 5 => 'C', 6 => 'B', 7 => 'C', 8 => 'C', 9 => 'C', 10 => 'B'];
        } elseif ($kategori_ujian == 'uts') {
            $kunci_array = [1 => 'C', 2 => 'A', 3 => 'C', 4 => 'A', 5 => 'B'];
        } elseif ($kategori_ujian == 'uas') {
            $kunci_array = [1 => 'A', 2 => 'B', 3 => 'B', 4 => 'C', 5 => 'C', 6 => 'C', 7 => 'A', 8 => 'B', 9 => 'A', 10 => 'A', 11 => 'C', 12 => 'B', 13 => 'A', 14 => 'B', 15 => 'B'];
        }
    } elseif (strpos($nama_mapel_aktif, 'ipa') !== false) {
        if ($kategori_ujian == 'harian') {
            $kunci_array = [1 => 'B', 2 => 'B', 3 => 'B', 4 => 'A', 5 => 'A', 6 => 'D', 7 => 'A', 8 => 'A', 9 => 'C', 10 => 'A', 11 => 'B', 12 => 'C', 13 => 'B', 14 => 'B', 15 => 'C'];
        } elseif ($kategori_ujian == 'kuis') {
            $kunci_array = [1 => 'A', 2 => 'D', 3 => 'B', 4 => 'C', 5 => 'A'];
        } elseif ($kategori_ujian == 'uts') {
            $kunci_array = [1 => 'C', 2 => 'C', 3 => 'B', 4 => 'C', 5 => 'B', 6 => 'C', 7 => 'B', 8 => 'B', 9 => 'A', 10 => 'A'];
        } elseif ($kategori_ujian == 'uas') {
            $kunci_array = [1 => 'C', 2 => 'C', 3 => 'B', 4 => 'C', 5 => 'B', 6 => 'C', 7 => 'B', 8 => 'B', 9 => 'B', 10 => 'B'];
        }
    } elseif (strpos($nama_mapel_aktif, 'ips') !== false) {
        if ($kategori_ujian == 'harian') {
            $kunci_array = [1 => 'B', 2 => 'A', 3 => 'B', 4 => 'C', 5 => 'A'];
        } elseif ($kategori_ujian == 'kuis') {
            $kunci_array = [1 => 'B', 2 => 'A', 3 => 'D', 4 => 'A', 5 => 'C'];
        } elseif ($kategori_ujian == 'uts') {
            $kunci_array = [1 => 'B', 2 => 'A', 3 => 'B', 4 => 'C', 5 => 'A', 6 => 'B', 7 => 'D', 8 => 'B', 9 => 'A', 10 => 'C'];
        } elseif ($kategori_ujian == 'uas') {
            $kunci_array = [1 => 'D', 2 => 'A', 3 => 'C', 4 => 'D', 5 => 'B', 6 => 'A', 7 => 'D', 8 => 'B', 9 => 'C', 10 => 'A'];
        }
    } elseif (strpos($nama_mapel_aktif, 'inggris') !== false || strpos($nama_mapel_aktif, 'english') !== false) {
        if ($kategori_ujian == 'harian') {
            $kunci_array = [1 => 'D', 2 => 'C', 3 => 'B', 4 => 'A'];
        } elseif ($kategori_ujian == 'kuis') {
            $kunci_array = [1 => 'C', 2 => 'B', 3 => 'A', 4 => 'B', 5 => 'A'];
        } elseif ($kategori_ujian == 'uts') {
            $kunci_array = [1 => 'C', 2 => 'B', 3 => 'C', 4 => 'B', 5 => 'A', 6 => 'A', 7 => 'C', 8 => 'B', 9 => 'B', 10 => 'A'];
        } elseif ($kategori_ujian == 'uas') {
            $kunci_array = [1 => 'D', 2 => 'B', 3 => 'B', 4 => 'B', 5 => 'A', 6 => 'A', 7 => 'B', 8 => 'B', 9 => 'C', 10 => 'A'];
        }
    }

    // PROSES PENCOCOKAN BERDASARKAN URUTAN DI LAYAR (1, 2, 3...)
    $nomor_urut = 1;
    foreach ($jawaban_murid as $id_soal => $jawaban_siswa) {
        $jawaban_siswa_bersih = strtoupper(trim($jawaban_siswa));
        $kunci_seharusnya = $kunci_array[$nomor_urut] ?? '';

        if ($jawaban_siswa_bersih == $kunci_seharusnya) {
            $jawaban_benar++;
        }
        $nomor_urut++;
    }

    $jawaban_salah = $total_soal - $jawaban_benar;
    $nilai_hitung = ($total_soal > 0) ? round(($jawaban_benar / $total_soal) * 100) : 0;
    $kolom_nilai = "nilai_" . $kategori_ujian; 

    // Simpan data nilai ke tabel raport_murid
    $cek_raport = mysqli_query($koneksi, "SELECT * FROM raport_murid WHERE user_id = '$user_id' AND id_mapel = '$id_mapel'");
    if (mysqli_num_rows($cek_raport) > 0) {
        mysqli_query($koneksi, "UPDATE raport_murid SET $kolom_nilai = '$nilai_hitung' WHERE user_id = '$user_id' AND id_mapel = '$id_mapel'");
    } else {
        mysqli_query($koneksi, "INSERT INTO raport_murid (user_id, id_mapel, $kolom_nilai) VALUES ('$user_id', '$id_mapel', '$nilai_hitung')");
    }

    // KUNCI PERBAIKAN UTAMA: Tambahkan 'kategori_ujian' ke dalam Query Insert tabel hasil_ujian
    mysqli_query($koneksi, "INSERT INTO hasil_ujian (user_id, id_mapel, kategori_ujian, jumlah_benar, jumlah_salah, nilai, tanggal) 
                            VALUES ('$user_id', '$id_mapel', '$kategori_ujian', '$jawaban_benar', '$jawaban_salah', '$nilai_hitung', NOW())");

    // Melempar id_mapel dan kategori ujian lewat query string URL ke hasil.php
    header("Location: hasil.php?nilai=" . $nilai_hitung . "&benar=" . $jawaban_benar . "&salah=" . $jawaban_salah . "&id_mapel=" . $id_mapel . "&kategori=" . $kategori_ujian);
    exit;
}

$query_mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ruang Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold mb-0 h1">Portal Ujian Online Siswa</span>
    </div>
</nav>

<div class="container" style="max-width: 800px;">
    
    <?php if (!$show_soal) { ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 text-center border-bottom">
                <h4 class="mb-0 fw-bold text-primary">Silakan Pilih Ujian Anda</h4>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Mata Pelajaran</label>
                        <select name="id_mapel" class="form-select" required>
                            <option value="">-- Pilih Mapel --</option>
                            <?php while($mp = mysqli_fetch_assoc($query_mapel)) { ?>
                                <option value="<?= $mp['id_mapel']; ?>"><?= htmlspecialchars($mp['nama_mapel']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Jenis Kategori Ujian</label>
                        <select name="kategori_ujian" class="form-select" required>
                            <option value="">-- Pilih Jenis Ujian --</option>
                            <option value="uas">UAS</option>
                            <option value="uts">UTS</option>
                            <option value="kuis">Kuis</option>
                            <option value="harian">Harian</option>
                        </select>
                    </div>

                    <button type="submit" name="pilih_ujian" class="btn btn-primary w-100 py-2.5 fw-bold fs-5">Mulai Ujian</button>
                </form>
            </div>
        </div>

    <?php } else { ?>
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="mb-0 fw-bold">Lembar Soal Kategori: <?= strtoupper($selected_kategori); ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <input type="hidden" name="id_mapel" value="<?= $selected_mapel; ?>">
                    <input type="hidden" name="kategori_ujian" value="<?= $selected_kategori; ?>">

                    <?php 
                    $no = 1;
                    foreach ($soal_list as $s) { ?>
                        <div class="mb-4 p-3 bg-light rounded border">
                            <p class="fw-bold mb-2"><?= $no; ?>. <?= htmlspecialchars($s['pertanyaan']); ?></p>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="jawaban[<?= $s['id']; ?>]" value="A" id="soal_<?= $s['id']; ?>_A" required>
                                <label class="form-check-label text-dark" for="soal_<?= $s['id']; ?>_A">A. <?= htmlspecialchars($s['pilihan_a']); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="jawaban[<?= $s['id']; ?>]" value="B" id="soal_<?= $s['id']; ?>_B">
                                <label class="form-check-label text-dark" for="soal_<?= $s['id']; ?>_B">B. <?= htmlspecialchars($s['pilihan_b']); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="jawaban[<?= $s['id']; ?>]" value="C" id="soal_<?= $s['id']; ?>_C">
                                <label class="form-check-label text-dark" for="soal_<?= $s['id']; ?>_C">C. <?= htmlspecialchars($s['pilihan_c']); ?></label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="jawaban[<?= $s['id']; ?>]" value="D" id="soal_<?= $s['id']; ?>_D">
                                <label class="form-check-label text-dark" for="soal_<?= $s['id']; ?>_D">D. <?= htmlspecialchars($s['pilihan_d']); ?></label>
                            </div>
                        </div>
                    <?php $no++; } ?>

                    <button type="submit" name="submit_jawaban" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow">Kirim Jawaban</button>
                </form>
            </div>
        </div>
    <?php } ?>

</div>
</body>
</html>