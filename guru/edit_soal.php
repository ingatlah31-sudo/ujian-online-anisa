<?php
session_start();
if($_SESSION['role'] != 'guru') {
    header("Location: ../auth/login.php");
    exit;
}
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

// Ambil ID soal yang akan diedit dari URL
$id = (int)$_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM soal WHERE id=$id");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan, balikkan ke halaman soal
if(!$data) {
    header("Location: soal.php");
    exit;
}

if(isset($_POST['update'])) {
    $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    $a = mysqli_real_escape_string($koneksi, $_POST['pilihan_a']);
    $b = mysqli_real_escape_string($koneksi, $_POST['pilihan_b']);
    $c = mysqli_real_escape_string($koneksi, $_POST['pilihan_c']);
    $d = mysqli_real_escape_string($koneksi, $_POST['pilihan_d']);
    $kunci = $_POST['jawaban_benar'];

    // Query Update data soal berdasarkan ID
    mysqli_query($koneksi, "UPDATE soal SET pertanyaan='$pertanyaan', pilihan_a='$a', pilihan_b='$b', pilihan_c='$c', pilihan_d='$d', jawaban_benar='$kunci' WHERE id=$id");
    header("Location: soal.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Soal - Ujian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Edit Data Soal</h4>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Pertanyaan</label>
                    <textarea name="pertanyaan" class="form-control" rows="3" required><?= htmlspecialchars($data['pertanyaan']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilihan A</label>
                    <input type="text" name="pilihan_a" value="<?= htmlspecialchars($data['pilihan_a']); ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilihan B</label>
                    <input type="text" name="pilihan_b" value="<?= htmlspecialchars($data['pilihan_b']); ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilihan C</label>
                    <input type="text" name="pilihan_c" value="<?= htmlspecialchars($data['pilihan_c']); ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilihan D</label>
                    <input type="text" name="pilihan_d" value="<?= htmlspecialchars($data['pilihan_d']); ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jawaban Benar</label>
                    <select name="jawaban_benar" class="form-select" required>
                        <option value="A" <?= $data['jawaban_benar'] == 'A' ? 'selected' : ''; ?>>A</option>
                        <option value="B" <?= $data['jawaban_benar'] == 'B' ? 'selected' : ''; ?>>B</option>
                        <option value="C" <?= $data['jawaban_benar'] == 'C' ? 'selected' : ''; ?>>C</option>
                        <option value="D" <?= $data['jawaban_benar'] == 'D' ? 'selected' : ''; ?>>D</option>
                    </select>
                </div>
                <button type="submit" name="update" class="btn btn-warning">Update Soal</button>
                <a href="soal.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>