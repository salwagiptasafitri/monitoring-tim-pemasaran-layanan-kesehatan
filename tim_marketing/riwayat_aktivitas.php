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
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tim_marketing') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk tim marketing.');
        window.location='index.php';
    </script>";
    exit;
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
<!-- DataTables Bootstrap -->
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet">

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
}

.card-header {
    background-color: #ffffff;
    font-weight: 600;
    color: var(--blue-dark);
    border-bottom: 1px solid #e5e7eb;
}

/* BRAND TEXT */
.gradient-text {
    font-size: 20px;
    font-weight: 600;
    background: linear-gradient(to right, #93c5fd, #ffffff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.bukti-img-small {
    width: 40px;
    height: 40px;
    object-fit: cover;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}

.bukti-img-small:hover {
    transform: scale(1.2);
}

.badge {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 12px;
}

</style>
</head>

<body class="sb-nav-fixed">

<!-- TOP NAV -->
<nav class="sb-topnav navbar navbar-expand px-4">
    <a class="navbar-brand d-flex align-items-center mr-4" href="index.php">
        <img src="../assets/img/biofarma.png" alt="Logo">
        <span class="gradient-text ml-3">Portal Pemasaran</span>
    </a>

    <button class="btn btn-link btn-sm ml-3" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<div id="layoutSidenav">

<!-- SIDEBAR -->
<div id="layoutSidenav_nav">
<nav class="sb-sidenav accordion" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">

            <div class="sb-sidenav-menu-heading">Main</div>
            <a class="nav-link" href="index.php">
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

            <a class="nav-link" href="backup.php">
                <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                Backup
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
<div class="container-fluid px-4 mt-4">

    <h4 class="mb-4">
        <i class="fas fa-table"></i> Data Riwayat Aktivitas
    </h4>

    <div class="card">
        <?php
$id_user_login = $_SESSION['user_id']; // ambil ID user login
?>
        <div class="card-body table-responsive">

          <table id="datatablesSimple" class="table table-bordered table-striped">
<thead>
<tr>
    <th>No</th>
    <th>Salesman</th>
    <th>Klien</th>
    <th>Tanggal</th>
    <th>Layanan</th>
    <th>Produk/Jasa</th>
    <th>Nilai</th>
    <th>Keterangan</th>
    <th>Status</th>
    <th>Bukti</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php
$no = 1;
$query = mysqli_query($koneksi, "
    SELECT m.*, u.nama AS nama_salesman
    FROM tb_marketing m
    LEFT JOIN tb_user u ON m.id_user = u.id_user WHERE m.id_user = '$id_user_login'
    ORDER BY m.id_marketing DESC
");

while($row = mysqli_fetch_assoc($query)){
$status = trim($row['status']);
?>

<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama_salesman']) ?></td>
<td><?= htmlspecialchars($row['nama_klien']) ?></td>
<td><?= date('d-m-Y', strtotime($row['tanggal_kunjungan'])) ?></td>
<td><?= htmlspecialchars($row['layanan_kes']) ?></td>
<td><?= htmlspecialchars($row['jenis']) ?></td>
<td class="text-right">Rp <?= number_format($row['nilai'],0,',','.') ?></td>
<td><?= htmlspecialchars($row['keterangan']) ?></td>

<!-- STATUS -->
<td class="text-center">
<?php
if ($status == '' || $status == 'Penawaran & Kunjungan') {
    echo '<span class="badge bg-secondary">Penawaran & Kunjungan</span>';
} elseif ($status == 'Negosiasi / Follow Up') {
    echo '<span class="badge bg-warning text-dark">Negosiasi</span>';
} elseif ($status == 'Deal') {
    echo '<span class="badge bg-success">Deal</span>';
} elseif ($status == 'Aktivitas Layanan') {
    echo '<span class="badge bg-primary">Aktivitas Layanan</span>';
} elseif ($status == 'Reject') {
    echo '<span class="badge bg-danger">Reject</span>';
}
?>
</td>

<!-- BUKTI -->
<td class="text-center">
<?php if (!empty($row['bukti'])) { ?>
    <a href="../uploads/<?= $row['bukti'] ?>" target="_blank">
        <img src="../uploads/<?= $row['bukti'] ?>" class="img-thumbnail" style="width:40px">
    </a>
<?php } else { ?>
    <span class="badge bg-secondary">-</span>
<?php } ?>
</td>

<!-- AKSI -->
<!-- AKSI -->
<td class="text-center">
<?php
if (in_array($status, ['Reject', 'Aktivitas Layanan'])) {
    echo '<span class="badge bg-success">Final</span>';
} else {
?>
    <button class="btn btn-outline-warning btn-sm"
        data-toggle="modal"
        data-target="#edit<?= $row['id_marketing'] ?>">
        <i class="fas fa-sync-alt"></i> Ubah Status
    </button>
<?php } ?>
</td>

</tr>

<!-- ================= MODAL UPDATE STATUS ================= -->
<div class="modal fade" id="edit<?= $row['id_marketing'] ?>" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST" action="edit_riwayat.php">

<div class="modal-header">
    <h5 class="modal-title">Update Status Aktivitas</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">
<input type="hidden" name="id_marketing" value="<?= $row['id_marketing'] ?>">

<?php if (in_array($status, ['Reject','Aktivitas Layanan'])) { ?>

    <div class="alert alert-info text-center">
        Status <strong><?= $status ?></strong> sudah final dan tidak bisa diubah.
    </div>

<?php } else { ?>

    <div class="form-group">
        <label>Status Selanjutnya</label>
        <select name="status" class="form-control" required>
            <option value="">-- Pilih Status --</option>

            <?php if ($status == '' || $status == 'Penawaran & Kunjungan') { ?>
                <option value="Negosiasi / Follow Up">Negosiasi / Follow Up</option>

            <?php } elseif ($status == 'Negosiasi / Follow Up') { ?>
                <option value="Deal">Deal</option>
                <option value="Reject">Reject</option>

            <?php } elseif ($status == 'Deal') { ?>
                <option value="Aktivitas Layanan">Aktivitas Layanan Kesehatan</option>
            <?php } ?>

        </select>
    </div>

<?php } ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

    <?php if (!in_array($status, ['Reject','Aktivitas Layanan'])) { ?>
        <button type="submit" name="update" class="btn btn-primary">Simpan</button>
    <?php } ?>
</div>

</form>
</div>
</div>
</div>


<?php } ?>
</tbody>
</table>

        </div>
    </div>
</div>
</main>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
<!-- DATATABLES -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#datatablesSimple').DataTable({
        pageLength: 10,
        ordering: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            zeroRecords: "Data tidak ditemukan"
        }
    });
});
</script>

</body>
</html>
