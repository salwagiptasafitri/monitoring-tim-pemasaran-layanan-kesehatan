<?php 
session_start();
include '../koneksi.php';
error_reporting(0);

// Proteksi harus login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Proteksi harus role manajer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manajer') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk Manajer.');
        window.location='index.php';
    </script>";
    exit;
}

// ================= TOTAL INSENTIF =================
$q_insentif = mysqli_query($koneksi,"
SELECT SUM(insentif) as total
FROM tb_insentif
");
$total_insentif = mysqli_fetch_assoc($q_insentif)['total'];

// ================= PROSPEK AKTIF (JUMLAH KUNJUNGAN SEMUA USER) =================
$q_kunjungan = mysqli_query($koneksi,"
SELECT COUNT(*) as total
FROM tb_marketing
");
$prospek_aktif = mysqli_fetch_assoc($q_kunjungan)['total'];

// ================= NILAI PENJUALAN (TOTAL PENDAPATAN SEMUA) =================
$q_pendapatan = mysqli_query($koneksi,"
SELECT SUM(pendapatan) as total
FROM tb_insentif
");
$total_pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'];

// ================= TOTAL TARGET =================
$q_target = mysqli_query($koneksi,"
SELECT SUM(target) as total
FROM tb_insentif
");
$total_target = mysqli_fetch_assoc($q_target)['total'];

// ================= PROGRES TARGET =================
$progres_target = ($total_target > 0) ? ($total_pendapatan / $total_target) * 100 : 0;

// ================= PIE CHART (STATUS: AKTIVITAS LAYANAN, NEGOSIASI, DEAL, REJECT) =================
$pie_label = ['Aktivitas Layanan', 'Negosiasi', 'Deal', 'Reject'];
$pie_data = [0, 0, 0, 0]; // Default 0 untuk masing-masing status

$q_pie = mysqli_query($koneksi,"
SELECT status, COUNT(*) as jumlah
FROM tb_marketing
GROUP BY status
");

while($d = mysqli_fetch_assoc($q_pie)){
    if ($d['status'] == 'Aktivitas Layanan') {
        $pie_data[0] = $d['jumlah'];
    } elseif ($d['status'] == 'Negosiasi') {
        $pie_data[1] = $d['jumlah'];
    } elseif ($d['status'] == 'Deal') {
        $pie_data[2] = $d['jumlah'];
    } elseif ($d['status'] == 'Reject') {
        $pie_data[3] = $d['jumlah'];
    }
}

// ================= LINE CHART =================
$line_label = [];
$line_data = [];

$q_line = mysqli_query($koneksi,"
SELECT DATE_FORMAT(tanggal_kunjungan,'%Y-%m') as bulan,
COUNT(*) as jumlah
FROM tb_marketing
GROUP BY bulan
ORDER BY bulan
");

while($d = mysqli_fetch_assoc($q_line)){
    $line_label[] = $d['bulan'];
    $line_data[] = $d['jumlah'];
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<title>Portal Monitoring Pemasaran</title>

<link rel="icon" type="image/png" href="../images/biofarma.png">
<link href="../css/styles.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --blue-dark: #0f2a44;
    --blue-main: #1e5aa8;
    --blue-light: #e8f1fb;
    --blue-soft: #3b82f6;
    --text-dark: #1f2937;
}

body {
    background-color: var(--blue-light);
    color: var(--text-dark);
}

/* TOP NAVBAR */
.sb-topnav {
    background: linear-gradient(90deg, var(--blue-dark), var(--blue-main));
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    padding: 0 24px;  
}

.sb-topnav .navbar-brand {
    color: #fff !important;
    font-weight: 600;
}

.sb-topnav img {
    height: 46px;
}
#sidebarToggle {
    margin-left: 70px !important;  /* Jarak tombol hamburger */
}
/* SIDEBAR */
.sb-sidenav {
    background-color: var(--blue-dark);
}

.sb-sidenav .sb-sidenav-menu-heading {
    color: #9db7d6;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.sb-sidenav .nav-link {
    color: #cbd5e1;
    font-size: 14px;
    padding: 12px 20px;
    border-radius: 6px;
    margin: 4px 10px;
}

.sb-sidenav .nav-link:hover,
.sb-sidenav .nav-link.active {
    background-color: var(--blue-main);
    color: #ffffff;
}

.sb-nav-link-icon {
    color: #93c5fd;
}

/* CONTENT */
.breadcrumb {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 10px 16px;
    font-size: 14px;
}

/* CARD */
.card {
    border-radius: 14px;
    border: none;
    box-shadow: 0 6px 18px rgba(15, 42, 68, 0.08);
    margin-bottom: 20px;
}

.card-header {
    background-color: #ffffff;
    font-weight: 600;
    color: var(--blue-dark);
    border-bottom: 1px solid #e5e7eb;
    padding: 15px 20px;
}

/* BRAND TEXT */
.gradient-text {
    font-size: 20px;
    font-weight: 600;
    background: linear-gradient(to right, #93c5fd, #ffffff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* STATISTIK CARDS */
.card.bg-primary .card-body,
.card.bg-success .card-body,
.card.bg-warning .card-body,
.card.bg-danger .card-body,
.card.bg-info .card-body {
    padding: 20px;
}

.card.bg-primary .card-body h5,
.card.bg-success .card-body h5,
.card.bg-warning .card-body h5,
.card.bg-danger .card-body h5,
.card.bg-info .card-body h5 {
    margin-bottom: 10px;
    font-size: 16px;
}

.card.bg-primary .card-body h3,
.card.bg-success .card-body h3,
.card.bg-warning .card-body h3,
.card.bg-danger .card-body h3,
.card.bg-info .card-body h3 {
    margin: 0;
    font-size: 28px;
    font-weight: bold;
}

/* CHART CARDS */
.card-body canvas {
    max-height: 300px;
}

/* TABLE */
.table th {
    background-color: var(--blue-light);
    color: var(--text-dark);
    font-weight: 600;
    text-align: center;
}

.table td {
    vertical-align: middle;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .row.mb-4 .col-md-3 {
        margin-bottom: 15px;
    }
    .card-body canvas {
        max-height: 250px;
    }
}
</style>
</head>

<body class="sb-nav-fixed">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-white shadow-sm px-4">

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="../assets/img/biofarma.png" alt="Logo" style="height:40px;">
        <span class="gradient-text ml-2 font-weight-bold">Portal Pemasaran</span>
    </a>

    <!-- Toggle Sidebar -->
    <button class="btn btn-link btn-sm ml-2 text-dark" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Right Menu -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Pilih Bulan -->
        <li class="nav-item mr-2">

            <select id="bulan" class="form-control form-control-sm shadow-sm" style="width:160px;">
                <option value="">Pilih Bulan</option>
                <option value="01">Januari</option>
                <option value="02">Februari</option>
                <option value="03">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
                <option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>

        </li>

        <!-- Download Button -->
        <li class="nav-item">

            <button onclick="downloadPDF()" 
                class="btn btn-danger btn-sm shadow-sm d-flex align-items-center">

                <i class="fas fa-file-pdf mr-2"></i>
                Download Laporan

            </button>

        </li>

    </ul>

</nav>



<div id="layoutSidenav">

<!-- SIDEBAR -->
<div id="layoutSidenav_nav">
<nav class="sb-sidenav accordion" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">

            <div class="sb-sidenav-menu-heading">Main</div>
            <a class="nav-link active" href="index.php">
                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                Dashboard
            </a>

            <a class="nav-link" href="aktivitas_kunjungan.php">
                <div class="sb-nav-link-icon"><i class="fas fa-map-marked-alt"></i></div>
                Aktivitas Kunjungan
            </a>

            <a class="nav-link" href="riwayat_aktivitas.php">
                <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                Riwayat Aktivitas
            </a>

            <a class="nav-link" href="insentif.php">
                <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                Insentif
            </a>
            <a class="nav-link" href="user.php">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                User
            </a>

            <div class="sb-sidenav-menu-heading">Akun</div>

            <a class="nav-link" href="profil.php">
                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                Profil
            </a>

        <a class="nav-link"
           href="logout.php"
           onclick="return confirm('Apakah Anda yakin ingin logout?')">
            <div class="sb-nav-link-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            Logout
        </a>


        </div>
    </div>
</nav>
</div>

<!-- CONTENT -->
<div id="layoutSidenav_content">
<main>
<div class="container-fluid">

<h1 class="mt-4">Dashboard Manajer Monitoring Pemasaran</h1>

<ol class="breadcrumb mb-4">
<li class="breadcrumb-item active">Ringkasan Statistik</li>
</ol>

<!-- ================= CARD STATISTIK ================= -->

<div class="row mb-4">

<div class="col-md-3">
<div class="card bg-success text-white">
<div class="card-body">
<h5>Total Insentif</h5>
<h3>Rp <?= number_format($total_insentif, 0, ',', '.') ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning text-white">
<div class="card-body">
<h5>Prospek Aktif</h5>
<h3><?= $prospek_aktif ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-danger text-white">
<div class="card-body">
<h5>Nilai Penjualan</h5>
<h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-info text-white">
<div class="card-body">
<h5>Target</h5>
<h3>Rp <?= number_format($total_target, 0, ',', '.') ?></h3>
</div>
</div>
</div>

</div>

<!-- ================= PROGRES TARGET ================= -->
<div class="row mb-4">
<div class="col-md-12">
<div class="card">
<div class="card-header">
<i class="fas fa-chart-bar mr-1"></i>
Progres Target
</div>
<div class="card-body">
<div class="progress">
<div class="progress-bar bg-success" role="progressbar" style="width: <?= number_format($progres_target, 2) ?>%;" aria-valuenow="<?= number_format($progres_target, 2) ?>" aria-valuemin="0" aria-valuemax="100">
<?= number_format($progres_target, 2) ?>%
</div>
</div>
<p class="mt-2">Progres: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?> / Rp <?= number_format($total_target, 0, ',', '.') ?></p>
</div>
</div>
</div>
</div>

<!-- ================= CHART ================= -->

<div class="row">

<div class="col-md-6">
<div class="card mb-4">

<div class="card-header">
<i class="fas fa-chart-pie mr-1"></i>
Prospek Klien/Calon Klien
</div>

<div class="card-body">
<?php if (array_sum($pie_data) > 0): ?>
<canvas id="pieChart"></canvas>
<?php else: ?>
<p class="text-center text-muted">Tidak ada data prospek untuk ditampilkan.</p>
<?php endif; ?>
</div>

</div>
</div>

<div class="col-md-6">
<div class="card mb-4">

<div class="card-header">
<i class="fas fa-chart-line mr-1"></i>
Prospek Bulanan
</div>

<div class="card-body">
<canvas id="lineChart"></canvas>
</div>

</div>
</div>

</div>

<!-- ================= TABLE INSENTIF ================= -->

<div class="card mb-4">

<div class="card-header d-flex justify-content-between align-items-center">
<i class="fas fa-coins"></i>
Informasi Insentif

</div>

<div class="card-body">

<table class="table table-bordered table-striped" id="dataTable">

<thead class="text-center">

<tr>
<th>No</th>
<th>Salesman</th>
<th>Layanan</th>
<th>Target</th>
<th>Pendapatan</th>
<th>Persentase</th>
<th>Insentif</th>
</tr>

</thead>

<tbody>

<?php
$no=1;

$q=mysqli_query($koneksi,"
SELECT i.*, u.nama
FROM tb_insentif i
LEFT JOIN tb_user u ON i.salesman=u.id_user
");

while($d=mysqli_fetch_assoc($q)){
?>

<tr>

<td class="text-center"><?= $no++ ?></td>

<td><?= $d['nama'] ?></td>

<td><?= $d['layanan'] ?></td>

<td class="text-end">
Rp <?= number_format($d['target'],0,',','.') ?>
</td>

<td class="text-end">
Rp <?= number_format($d['pendapatan'],0,',','.') ?>
</td>

<td class="text-center">
<?= number_format($d['persentase'],2) ?> %
</td>

<td class="text-end text-success">
Rp <?= number_format($d['insentif'],0,',','.') ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
</main>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
<?php if (array_sum($pie_data) > 0): ?>
new Chart(document.getElementById("pieChart"),{
    type:'pie',
    data:{
        labels:<?= json_encode($pie_label) ?>,
        datasets:[{
            data:<?= json_encode($pie_data) ?>,
            backgroundColor:[
                '#007bff', // Aktivitas Layanan - biru
                '#ffc107', // Negosiasi - kuning
                '#28a745', // Deal - hijau
                '#dc3545'  // Reject - merah
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
<?php endif; ?>

new Chart(document.getElementById("lineChart"),{
    type:'line',
    data:{
        labels:<?= json_encode($line_label) ?>,
        datasets:[{
            label:'Jumlah Kunjungan',
            data:<?= json_encode($line_data) ?>,
            borderColor:'#007bff',
            fill:false
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Inisialisasi DataTable untuk tabel insentif
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
        },
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "paging": true
    });
});

</script>

<script>

function downloadPDF(){

    var bulan = document.getElementById("bulan").value;

    // jika bulan kosong → semua data
    if(bulan==""){
        window.location.href="export_pdf.php";
    }
    else{
        window.location.href="export_pdf.php?bulan="+bulan;
    }

}

</script>


</body>
</html>