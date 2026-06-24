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
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk Admin.');
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
                                 <a class="nav-link " href="index.php">
                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                Dashboard
            </a>

            <a class="nav-link " href="user.php">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                User
            </a>

            <a class="nav-link " href="riwayat_aktivitas.php">
                <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                Riwayat Aktivitas
            </a>
            <a class="nav-link" href="riwayat_user.php">
            <div class="sb-nav-link-icon"><i class="fas fa-user-clock"></i></div>
            Riwayat User
        </a>

            <a class="nav-link active" href="insentif.php">
                <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                Insentif
            </a>

            <a class="nav-link" href="backup.php">
                <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                Backup
            </a>
                        <a class="nav-link" href="restore.php">
                <div class="sb-nav-link-icon"><i class="fas fa-upload"></i></div>
                Restore
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
        <i class="fas fa-table"></i> Data Monitoring Insentif 
    </h4>
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalInsentif">
        <i class="fas fa-plus"></i> Tambah Data
    </button>
</div>

    <div class="card">
        <div class="card-body table-responsive">
            <div class="mb-3">

    <a href="export_insentif_excel.php" class="btn btn-success btn-sm">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>

    <a href="export_insentif_pdf.php" class="btn btn-danger btn-sm" target="_blank">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>

</div>

<table id="datatablesSimple" class="table table-bordered table-striped">
    <thead class="text-center">
        <tr>
            <th>No</th>
            <th>Salesman</th>
            <th>Layanan</th>
            <th>Target</th>
            <th>Pendapatan</th>
            <th>Persentase</th>
            <th>Insentif Penjualan</th>
            <th>Total Insentif</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
    <?php
    $no = 1;

    // variabel total
    $total_target = 0;
    $total_pendapatan = 0;
    $total_insentif = 0;

    $query = mysqli_query($koneksi, "
        SELECT 
            i.id_insentif,
            i.layanan,
            i.target,
            i.pendapatan,
            i.persentase,
            i.insentif_penjualan,
            i.insentif,
            u.nama AS nama_salesman
        FROM tb_insentif i
        LEFT JOIN tb_user u ON i.salesman = u.id_user
        ORDER BY i.layanan DESC
    ");

    while ($row = mysqli_fetch_assoc($query)) {

        $pendapatan = (float)$row['pendapatan'];

        // hitung total
        $total_target += $row['target'];
        $total_pendapatan += $row['pendapatan'];
        $total_insentif += $row['insentif'];
    ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>

            <td><?= htmlspecialchars($row['nama_salesman']) ?></td>
            <td><?= htmlspecialchars($row['layanan']) ?></td>

            <td class="text-end">
                Rp <?= number_format($row['target'], 0, ',', '.') ?>
            </td>

            <td class="text-end">
                Rp <?= number_format($row['pendapatan'], 0, ',', '.') ?>
            </td>

            <td class="text-center">
                <?= number_format($row['persentase'], 2) ?> %
            </td>

            <td class="text-center">
                <?= number_format($row['insentif_penjualan'], 2) ?> %
            </td>

            <td class="text-end fw-bold text-success">
                Rp <?= number_format($row['insentif'], 0, ',', '.') ?>
            </td>

            <!-- AKSI -->
            <td class="text-center">

                <!-- EDIT -->
                <button class="btn btn-sm btn-warning"
                        data-toggle="modal"
                        data-target="#edit<?= $row['id_insentif'] ?>">
                    <i class="fas fa-edit"></i>
                </button>

                <!-- HAPUS -->
                <?php if ($pendapatan > 0) { ?>
                    <button class="btn btn-sm btn-secondary"
                            onclick="alert('Maaf, data sedang terisi dan tidak bisa dihapus!')">
                        <i class="fas fa-trash"></i>
                    </button>
                <?php } else { ?>
                    <a href="hapus_insentif.php?id=<?= $row['id_insentif'] ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin ingin menghapus data insentif ini?')">
                        <i class="fas fa-trash"></i>
                    </a>
                <?php } ?>

            </td>
        </tr>


        <!-- ================= MODAL EDIT INSENTIF ================= -->
        <div class="modal fade" id="edit<?= $row['id_insentif'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST" action="edit_insentif.php">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="fas fa-edit"></i> Edit Insentif
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="id_insentif" value="<?= $row['id_insentif'] ?>">

                            <div class="form-group">
                                <label>Target</label>
                                <input type="number"
                                       name="target"
                                       class="form-control"
                                       value="<?= $row['target'] ?>"
                                       required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit"
                                    name="update"
                                    class="btn btn-warning">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- ====================================================== -->

    <?php } ?>


    <!-- ============================================= -->

    </tbody>

    <tfoot>
            <!-- ================= BARIS TOTAL ================= -->
    <tr style="background: linear-gradient(90deg,#0d6efd,#0dcaf0); color:white; font-weight:bold;">
        <td colspan="3" class="text-center">
            TOTAL
        </td>

        <td class="text-end">
            Rp <?= number_format($total_target, 0, ',', '.') ?>
        </td>

        <td class="text-end">
            Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
        </td>

        <td class="text-center">
            —
        </td>

        <td class="text-center">
            —
        </td>

        <td class="text-end">
            Rp <?= number_format($total_insentif, 0, ',', '.') ?>
        </td>

        <td></td>
    </tr>

        
    </tfoot>
</table>



        </div>
    </div>

</div>

<?php
// ambil data user (salesman) dari tb_user
$data_user = mysqli_query($koneksi, "SELECT id_user, nama FROM tb_user WHERE role = 'tim_marketing' ORDER BY nama ASC");
?>

<div class="modal fade" id="modalInsentif" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <form method="POST" action="tambah_insentif.php">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            <i class="fas fa-money-check-alt"></i> Form Tambah Insentif
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <div class="row">

          <!-- Salesman -->
          <div class="col-md-6 mb-3">
              <label>Salesman</label>
              <select name="salesman" class="form-control" required>
                  <option value="">-- Pilih Salesman --</option>
                  <?php while($u = mysqli_fetch_assoc($data_user)) { ?>
                      <option value="<?= $u['id_user']; ?>">
                          <?= $u['nama']; ?>
                      </option>
                  <?php } ?>
              </select>
          </div>

          <!-- Layanan -->
          <div class="col-md-6 mb-3">
              <label>Layanan</label>
              <select name="layanan" class="form-control" required>
                  <option value="">-- Pilih Layanan --</option>
                  <option value="Vaksinasi & Imunisasi">Vaksinasi & Imunisasi</option>
                  <option value="Laboratorium Mikrobiologi">Laboratorium Mikrobiologi</option>
                  <option value="Klinik">Klinik</option>
              </select>
          </div>

          <!-- Target (bisa diisi) -->
          <div class="col-md-6 mb-3">
              <label>Target (Rp)</label>
              <input type="number" name="target" class="form-control" placeholder="contoh: 3750000000" required>
          </div>

          <!-- Pendapatan (kosong otomatis) -->
          <div class="col-md-6 mb-3">
              <label>Pendapatan (Rp)</label>
              <input type="text" name="pendapatan" class="form-control" value="" placeholder="(otomatis)" readonly>
          </div>

          <!-- Persentase (kosong otomatis) -->
          <div class="col-md-6 mb-3">
              <label>Persentase (%)</label>
              <input type="text" name="persentase" class="form-control" value="" placeholder="(otomatis)" readonly>
          </div>

          <!-- Insentif Penjualan (kosong otomatis) -->
          <div class="col-md-6 mb-3">
              <label>% Insentif Penjualan</label>
              <input type="text" name="insentif_penjualan" class="form-control" value="" placeholder="(otomatis)" readonly>
          </div>

          <!-- Insentif (kosong otomatis) -->
          <div class="col-md-12 mb-3">
              <label>Insentif (Rp)</label>
              <input type="text" name="insentif" class="form-control" value="" placeholder="(otomatis)" readonly>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" name="simpan" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
      </div>

      </form>

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
