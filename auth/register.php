<?php
session_start();
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "ujian_online");

$pesan = "";

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // Di ketentuan tugas, password berupa teks biasa (12345)
    $role = 'murid'; // Otomatis diset sebagai murid

    // Cek apakah username sudah pernah dipakai orang lain
    $cek_username = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_username) > 0) {
        $pesan = "<div class='alert alert-danger text-center'>Username sudah terpakai! Silakan cari nama lain.</div>";
    } else {
        // Masukkan data acak tadi ke database tabel users
        $query_daftar = mysqli_query($koneksi, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')");
        
        if ($query_daftar) {
            $pesan = "<div class='alert alert-success text-center'>Pendaftaran Berhasil! Silakan <a href='login.php' class='fw-bold'>Login di sini</a>.</div>";
        } else {
            $pesan = "<div class='alert alert-danger text-center'>Gagal mendaftar, coba lagi!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun Murid Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0 fw-bold">Daftar Akun Baru</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?= $pesan; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-content form-control" placeholder="Masukkan Nama Bebas/Acak" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Contoh: andi123" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100 py-2 fw-bold">Daftar Sekarang</button>
                    </form>
                    
                    <hr>
                    <p class="text-center mb-0 small">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>