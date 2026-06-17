<?php
session_start();

// 1. Validasi pastikan yang masuk adalah guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// 3. Proses Hapus Soal
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $delete = mysqli_query($koneksi, "DELETE FROM soal WHERE id = '$id_hapus'");
    if ($delete) {
        echo "<script>
                alert('Soal berhasil dihapus!');
                window.location='soal.php';
              </script>";
        exit;
    }
}

// 4. Ambil semua data mapel untuk dropdown global di atas
$query_mapel_list = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapel_array = [];
while ($m = mysqli_fetch_assoc($query_mapel_list)) {
    $mapel_array[] = $m;
}

// 5. Ambil data soal dipisah berdasarkan kategori utama
$query_harian = mysqli_query($koneksi, "SELECT soal.*, mapel.nama_mapel FROM soal JOIN mapel ON soal.id_mapel = mapel.id_mapel WHERE soal.kategori_ujian = 'harian' ORDER BY soal.id DESC");
$query_kuis   = mysqli_query($koneksi, "SELECT soal.*, mapel.nama_mapel FROM soal JOIN mapel ON soal.id_mapel = mapel.id_mapel WHERE soal.kategori_ujian = 'kuis' ORDER BY soal.id DESC");
$query_uts    = mysqli_query($koneksi, "SELECT soal.*, mapel.nama_mapel FROM soal JOIN mapel ON soal.id_mapel = mapel.id_mapel WHERE soal.kategori_ujian = 'uts' ORDER BY soal.id DESC");
$query_uas    = mysqli_query($koneksi, "SELECT soal.*, mapel.nama_mapel FROM soal JOIN mapel ON soal.id_mapel = mapel.id_mapel WHERE soal.kategori_ujian = 'uas' ORDER BY soal.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Soal - Panel Guru</title>
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
        <h2 class="fw-bold text-dark">Kelola Bank Soal Ujian</h2>
        <div>
            <a href="dashboard.php" class="btn btn-secondary fw-bold me-2">⬅ Dashboard</a>
            <a href="tambah_soal.php" class="btn btn-primary fw-bold">+ Tambah Soal Baru</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="fw-bold text-secondary mb-1">Pilih Mata Pelajaran:</label>
                    <select class="form-select fw-bold text-dark border-primary" id="filter-mapel-global">
                        <option value="semua">-- Tampilkan Semua Mapel --</option>
                        <?php foreach ($mapel_array as $m) { ?>
                            <option value="mapel-<?= $m['id_mapel']; ?>"><?= htmlspecialchars($m['nama_mapel']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="soalTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold text-uppercase" id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian" type="button" role="tab">📖 Harian</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-uppercase" id="kuis-tab" data-bs-toggle="tab" data-bs-target="#kuis" type="button" role="tab">⏱️ Kuis</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-uppercase" id="uts-tab" data-bs-toggle="tab" data-bs-target="#uts" type="button" role="tab">📝 UTS</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-uppercase" id="uas-tab" data-bs-toggle="tab" data-bs-target="#uas" type="button" role="tab">🎓 UAS</button>
        </li>
    </ul>

    <div class="tab-content" id="soalTabContent">
        
        <div class="tab-pane fade show active" id="harian" role="tabpanel">
            <?php tampilkanTabelSoal('harian', $query_harian); ?>
        </div>

        <div class="tab-pane fade" id="kuis" role="tabpanel">
            <?php tampilkanTabelSoal('kuis', $query_kuis); ?>
        </div>

        <div class="tab-pane fade" id="uts" role="tabpanel">
            <?php tampilkanTabelSoal('uts', $query_uts); ?>
        </div>

        <div class="tab-pane fade" id="uas" role="tabpanel">
            <?php tampilkanTabelSoal('uas', $query_uas); ?>
        </div>

    </div>
</div>

<?php
// FUNGSI UNTUK MENAMPILKAN TABEL SOAL
function tampilkanTabelSoal($kategori, $query_data) { ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Mapel</th>
                            <th>Pertanyaan Soal</th>
                            <th width="8%">A</th>
                            <th width="8%">B</th>
                            <th width="8%">C</th>
                            <th width="8%">D</th>
                            <th width="5%">Kunci</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($query_data) > 0) {
                            while ($row = mysqli_fetch_assoc($query_data)) { ?>
                                <tr class="baris-soal-induk mapel-<?= $row['id_mapel']; ?>" data-kategori="<?= $kategori; ?>">
                                    <td class="text-center nomor-urut"><?= $no++; ?></td>
                                    <td class="fw-bold text-primary text-center"><?= htmlspecialchars($row['nama_mapel']); ?></td>
                                    <td><?= htmlspecialchars($row['pertanyaan']); ?></td>
                                    <td><?= htmlspecialchars($row['pilihan_a']); ?></td>
                                    <td><?= htmlspecialchars($row['pilihan_b']); ?></td>
                                    <td><?= htmlspecialchars($row['pilihan_c']); ?></td>
                                    <td><?= htmlspecialchars($row['pilihan_d']); ?></td>
                                    <td class="text-center fw-bold text-success"><?= strtoupper($row['jawaban_benar']); ?></td>
                                    <td class="text-center">
                                        <a href="edit_soal.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm fw-bold me-1">Edit</a>
                                        <a href="soal.php?hapus=<?= $row['id']; ?>" onclick="localStorage.setItem('mapel_pilihan_guru', $('#filter-mapel-global').val()); return confirm('Yakin ingin menghapus soal ini?')" class="btn btn-danger btn-sm fw-bold">Hapus</a>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada soal di kategori ini.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    // 1. Ambil memori mapel terakhir saat halaman selesai loading/refresh
    var mapelTerakhir = localStorage.getItem('mapel_pilihan_guru');
    if (mapelTerakhir) {
        $('#filter-mapel-global').val(mapelTerakhir);
    }

    // Jalankan filter pertama kali
    jalankanFilterGlobal();

    // 2. Fungsi Utama Filter
    function jalankanFilterGlobal() {
        var mapelPilihan = $('#filter-mapel-global').val();
        
        if(mapelPilihan === 'semua') {
            $('.baris-soal-induk').show();
        } else {
            $('.baris-soal-induk').hide();
            $('.baris-soal-induk.' + mapelPilihan).show();
        }
        
        // Perbaiki nomor urut tabel
        var daftarKategori = ['harian', 'kuis', 'uts', 'uas'];
        daftarKategori.forEach(function(kat){
            var urutan = 1;
            $('.baris-soal-induk[data-kategori="' + kat + '"]:visible').each(function(){
                $(this).find('.nomor-urut').text(urutan++);
            });
        });
    }

    // Jalankan filter ketika dropdown diubah manual
    $('#filter-mapel-global').on('change', function(){
        // Ikut simpan ke memori saat diganti manual
        localStorage.setItem('mapel_pilihan_guru', $(this).val());
        jalankanFilterGlobal();
    });

    // Jalankan filter otomatis saat berpindah tab kuis/uts/uas
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        jalankanFilterGlobal();
    });
});
</script>
</body>
</html>