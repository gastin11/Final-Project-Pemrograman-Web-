<?php
include_once ("koneksi.php");
$query = "SELECT * FROM tb_pembayaran";
$hasil = mysqli_query($koneksi, $query);

// Mulai session
session_start();

if (isset($_SESSION['expire_time']) && $_SESSION['expire_time'] < time()) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit;
} else {
    $_SESSION['expire_time'] = time() + (5 * 60);
}

// Periksa apakah session nama_admin telah diset atau belum
if (!isset($_SESSION['nama_admin'])) {
    echo "<script>
        alert('Lakukan Login Terlebih Dahulu');
        document.location.href = 'login.php';
        </script>";
    exit;
}

// Ambil level pengguna dari sesi
$level_admin = $_SESSION['level_admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Laporan Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets-dashboard/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="./asset-login/logo-login.png">
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3 d-flex align-items-center" href="dashboard.php">
            <img src="./assets-dashboard/img/logo.jpg" alt="Logo" class="rounded-circle me-2" width="40" height="40">
            SiArisan
        </a>            
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Text-->
        <span class="navbar-text ms-auto me-3 my-2 my-md-0">
        Halo, 
        <?php
            if (isset($_SESSION['nama_admin'])) {
                echo $_SESSION['nama_admin']; 
            }
        ?>
        </span>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-footer">
                    <hr>
                    <div class="d-flex flex-row align-items-center">
                    <?php
                        // Ambil nama admin dari sesi
                        $nama_admin = $_SESSION['nama_admin'];
                        
                        // Ambil gambar admin dari database
                        $query = "SELECT img_admin FROM admin WHERE nama_admin = '$nama_admin'";
                        $result = mysqli_query($koneksi, $query);
                        
                        if ($row = mysqli_fetch_assoc($result)) {
                            $img_admin = $row['img_admin'];
                            echo '<img src="data:image/jpeg;base64,'.base64_encode($img_admin).'" alt="Admin Image" class="rounded-circle me-2" width="40" height="40">';
                        } else {
                            // Jika gambar tidak ditemukan, tampilkan gambar placeholder atau pesan kesalahan
                            echo '<img src="path_to_placeholder_image.jpg" alt="Admin Image" class="rounded-circle me-2" width="40" height="40">';
                        }
                    ?>
                        <div class="small">
                            <div>Login sebagai:</div>
                            <div>
                                <?php
                                if (isset($_SESSION['level_admin'])) {
                                    echo $_SESSION['level_admin']; 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <?php if ($level_admin == 'ketua'): ?>
                        <a class="nav-link" href="kelolaanggota.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                            Kelola Anggota
                        </a>
                        <?php endif; ?>
                        <?php if ($level_admin == 'ketua' || $level_admin == 'sekretaris'): ?>
                        <a class="nav-link" href="./pertemuan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Pertemuan
                        </a>
                        <?php endif; ?>
                        <?php if ($level_admin == 'bendahara'|| $level_admin == 'ketua'): ?>
                        <a class="nav-link" href="./pembayaran.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-check-alt"></i></div>
                            Pembayaran
                        </a>
                        <?php endif; ?>
                        <?php if ($level_admin == 'ketua' || $level_admin == 'sekretaris'): ?>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                            Laporan
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link fs-6 fw-lighter" href="./laporanpertemuan.php">Laporan Pertemuan</a>
                                <a class="nav-link fs-6 fw-lighter" href="./laporanpembayaran.php">Laporan Pembayaran</a>
                            </nav>
                        </div>
                        <?php endif; ?>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Laporan Pembayaran</h1>
                    <ol class="breadcrumb mb=4">
                        <li class="breadcrumb-item active">Laporan Pembayaran</li>
                    </ol>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4">Cetak laporan pembayaran</h5>
                            <form action="pembayaran_excel.php" method="post">
                                <label for="exampleFormControlInput1" class="form-label">Pilih Bulan</label>
                                <input type="month" class="form-control mb-4" id="exampleFormControlInput1" name="ttanggal" required>
                                <label for="status_pembayaran" class="form-label mb-2">Status Pembayaran</label>
                                <select name="status_pembayaran" id="status_pembayaran" class="form-control mb-4" required>
                                    <option value="">--Pilih Berdasarkan Status Pembayaran--</option>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Belum Lunas">Belum Lunas</option>
                                </select>
                                <button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> &nbsp Excel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-5">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-center small">
                        <div class="text-muted">Copyright &copy; Kelompok 1 Paralel F 2024</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets-dashboard/js/scripts.js"></script>
</body>
</html>
