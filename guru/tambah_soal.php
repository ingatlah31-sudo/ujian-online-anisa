<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}

$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");
$pesan = "";

if (isset($_POST['tambah'])) {
    // Mengamankan input dari tanda petik (') agar tidak error SQL syntax
    $id_mapel       = mysqli_real_escape_string($koneksi, $_POST['id_mapel']);
    $kategori_ujian = mysqli_real_escape_string($koneksi, $_POST['kategori_ujian']);
    $pertanyaan     = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    $pilihan_a      = mysqli_real_escape_string($koneksi, $_POST['pilihan_a']);
    $pilihan_b      = mysqli_real_escape_string($koneksi, $_POST['pilihan_b']);
    $pilihan_c      = mysqli_real_escape_string($koneksi, $_POST['pilihan_c']);
    $pilihan_d      = mysqli_real_escape_string($koneksi, $_POST['pilihan_d']);
    $jawaban_benar  = mysqli_real_escape_string($koneksi, $_POST['jawaban_benar']);

    // Simpan ke database
    $query = mysqli_query($koneksi, "INSERT INTO soal (id_mapel, kategori_ujian, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) VALUES ('$id_mapel', '$kategori_ujian', '$pertanyaan', '$pilihan_a', '$pilihan_b', '$pilihan_c', '$pilihan_d', '$jawaban_benar')");

    if ($query) {
        echo "<script>
                alert('Soal Ujian Berhasil Ditambahkan!');
                window.location='soal.php';
              </script>";
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal menambahkan soal: " . mysqli_error($koneksi) . "</div>";
    }
}

// Ambil data mapel untuk dropdown
$query_mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal Ujian - Panel Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 700px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0 fw-bold">Tambah Soal Ujian Baru</h5>
        </div>
        <div class="card-body p-4">
            <?= $pesan; ?>
            
            <form id="form-tambah-soal" action="" method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mata Pelajaran</label>
                        <select name="id_mapel" id="id-select-mapel" class="form-select" required>
                            <option value="">-- Pilih Mapel --</option>
                            <?php while($mp = mysqli_fetch_assoc($query_mapel)) { ?>
                                <option value="<?= $mp['id_mapel']; ?>"><?= htmlspecialchars($mp['nama_mapel']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kategori Ujian</label>
                        <select name="kategori_ujian" class="form-select" required>
                            <option value="">-- Pilih Jenis Ujian --</option>
                            <option value="uas">UAS</option>
                            <option value="uts">UTS</option>
                            <option value="kuis">Kuis</option>
                            <option value="harian">Harian</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pertanyaan Soal</label>
                    <textarea name="pertanyaan" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pilihan A</label>
                        <input type="text" name="pilihan_a" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pilihan B</label>
                        <input type="text" name="pilihan_b" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pilihan C</label>
                        <input type="text" name="pilihan_c" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pilihan D</label>
                        <input type="text" name="pilihan_d" class="form-control" required>
                    </div>
                </div>

                <div class="mb-4" style="max-width: 200px;">
                    <label class="form-label fw-bold">Jawaban Benar</label>
                    <select name="jawaban_benar" class="form-select" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <button type="submit" name="tambah" class="btn btn-dark w-100 py-2 fw-bold">Simpan Soal</button>
                <a href="soal.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
    // Mengatur autofill mapel pilihan terakhir
    var mapelTerakhir = localStorage.getItem('mapel_pilihan_guru');
    if (mapelTerakhir) {
        var idAngkaOnly = mapelTerakhir.replace('mapel-', '');
        $('#id-select-mapel').val(idAngkaOnly);
    }

    // Kode LocalStorage dipindahkan ke sini agar dibaca SETELAH jQuery siap
    $('#form-tambah-soal').on('submit', function() {
        var mapelVal = $('#id-select-mapel').val();
        localStorage.setItem('mapel_pilihan_guru', 'mapel-' + mapelVal);
    });
});
</script>
</body>
</html>s