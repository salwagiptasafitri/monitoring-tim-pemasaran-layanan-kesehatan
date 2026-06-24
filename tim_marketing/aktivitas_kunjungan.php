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
        <i class="fas fa-table"></i> Data Aktivitas Marketing
    </h4>
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAktivitas">
        <i class="fas fa-plus"></i> Tambah Aktivitas
    </button>
</div>

    <div class="card">
        <div class="card-body table-responsive">
            <div class="mb-3">

<a href="export_pdf.php" class="btn btn-danger" target="_blank">
<i class="fas fa-file-pdf"></i> Export PDF
</a>

<a href="export_excel.php" class="btn btn-success">
<i class="fas fa-file-excel"></i> Export Excel
</a>

</div>
<?php
$id_user_login = $_SESSION['user_id']; // ambil ID user login
?>
            <table id="datatablesSimple" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Salesman</th>
                        <th>Klien</th>
                        <th>Tanggal</th>
                        <th>Layanan</th>
                        <th>Produk/Jasa</th>
                        <th>Aktivitas</th>
                        <th>Nilai</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no=1;
                $query = mysqli_query($koneksi, "
    SELECT 
        m.*,
        u.nama AS nama_salesman
    FROM tb_marketing m
    LEFT JOIN tb_user u ON m.id_user = u.id_user
    WHERE m.id_user = '$id_user_login'
    ORDER BY m.id_marketing DESC
");

                while($row=mysqli_fetch_assoc($query)){
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_salesman']) ?></td>
                        <td><?= htmlspecialchars($row['nama_klien']) ?></td>
                        <td><?= date('d-m-Y',strtotime($row['tanggal_kunjungan'])) ?></td>
                        <td><?= htmlspecialchars($row['layanan_kes']) ?></td>
                        <td><?= htmlspecialchars($row['jenis']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td class="text-right">
                            Rp <?= number_format($row['nilai'],0,',','.') ?>
                        </td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['bukti'])) { ?>
                                <a href="../uploads/<?= $row['bukti'] ?>" target="_blank">
                                    <img src="../uploads/<?= $row['bukti'] ?>"
                                         class="img-thumbnail bukti-img-small"
                                         alt="Bukti Aktivitas">
                                </a>
                            <?php } else { ?>
                                <span class="badge bg-secondary">Tidak Ada</span>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                <button class="btn btn-sm btn-warning"
                        data-toggle="modal"
                        data-target="#editModal<?= $row['id_marketing'] ?>">
                    <i class="fas fa-edit"></i>
                </button>

                <a href="hapus_aktivitas.php?id=<?= $row['id_marketing'] ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                   <i class="fas fa-trash"></i>
                </a>

            </td>

                    </tr>
<?php
$is_final = in_array($row['status'], ['Deal','Reject','Aktivitas Layanan']);
?>

<div class="modal fade" id="editModal<?= $row['id_marketing'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="edit_aktivitas.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Edit Aktivitas
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id_marketing" value="<?= $row['id_marketing'] ?>">

                    <div class="row">

                        <!-- Salesman -->
                        <div class="col-md-6 mb-3">
                            <label>Nama Salesman</label>
                            <input type="text" class="form-control"
                                   value="<?= htmlspecialchars($row['nama_salesman']) ?>" readonly>
                        </div>

                        <!-- Klien -->
                        <div class="col-md-6 mb-3">
                            <label>Nama Klien</label>
                            <input type="text" name="nama_klien" class="form-control"
                                   value="<?= htmlspecialchars($row['nama_klien']) ?>" required>
                        </div>

                        <!-- Tanggal -->
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Kunjungan</label>
                            <input type="date" name="tanggal_kunjungan" class="form-control"
                                   value="<?= $row['tanggal_kunjungan'] ?>" required>
                        </div>

                        <!-- LAYANAN -->
                        <div class="col-md-6 mb-3">
                            <label>Layanan</label>

                            <?php if($is_final): ?>
                                
                                <!-- readonly -->
                                <input type="text"
                                       class="form-control"
                                       value="<?= htmlspecialchars($row['layanan_kes']) ?>"
                                       readonly>

                                <!-- hidden -->
                                <input type="hidden"
                                       name="layanan_kes"
                                       value="<?= htmlspecialchars($row['layanan_kes']) ?>">

                            <?php else: ?>

                                <!-- select jika boleh edit -->
                                <select name="layanan_kes" id="layanan_kes" class="form-control" required>

                                    <option value="">-- Pilih --</option>

                                    <option value="Vaksinasi & Imunisasi"
                                        <?= $row['layanan_kes']=='Vaksinasi & Imunisasi'?'selected':'' ?>>
                                        Vaksinasi & Imunisasi
                                    </option>

                                    <option value="Laboratorium Mikrobiologi"
                                        <?= $row['layanan_kes']=='Laboratorium Mikrobiologi'?'selected':'' ?>>
                                        Laboratorium Mikrobiologi
                                    </option>

                                    <option value="Klinik"
                                        <?= $row['layanan_kes']=='Klinik'?'selected':'' ?>>
                                        Klinik
                                    </option>

                                </select>

                            <?php endif; ?>

                        </div>

                        <!-- Produk -->
                        <div class="col-md-6 mb-3">
                            <label>Produk / Jasa</label>
                            <input type="text" name="jenis_produk" class="form-control"
                                   value="<?= htmlspecialchars($row['jenis']) ?>" required>
                        </div>

                        <!-- Nilai -->
                        <div class="col-md-6 mb-3">
                            <label>Nilai (Rp)</label>
                            <input type="number" name="nilai" class="form-control"
                                   value="<?= $row['nilai'] ?>"
                                   <?= $is_final ? 'readonly' : '' ?>>
                        </div>

                        <!-- Aktivitas (SELALU READONLY kecuali mau diubah manual) -->
                        <div class="col-12 mb-3">
                            <label>Aktivitas</label>
                            <input type="text" name="aktivitas" class="form-control"
                                   value="<?= $row['status'] ?>"
                                   readonly>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12 mb-3">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($row['keterangan'] ?? '') ?></textarea>
                        </div>

                        <!-- Bukti -->
                        <div class="col-12 mb-3">
                            <label>Bukti (Foto)</label>
                            <input type="file" name="bukti" class="form-control-file">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto</small>
                        </div>

                        <!-- Preview -->
                        <?php if (!empty($row['bukti'])) { ?>
                        <div class="col-12 text-center">
                            <img src="../uploads/<?= $row['bukti'] ?>" class="img-thumbnail" width="120">
                        </div>
                        <?php } ?>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
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

<div class="modal fade" id="modalAktivitas" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <form method="POST" enctype="multipart/form-data" action="tambah_aktivitas.php">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            <i class="fas fa-map-marked-alt"></i> Form Aktivitas Kunjungan
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">

        <div class="row">

        <div class="col-md-6 mb-3">
            <label>Nama Salesman</label>
            <input type="text" class="form-control" value="<?= $_SESSION['nama']; ?>" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label>Nama Klien</label>
            <input type="text" name="nama_klien" class="form-control" required>
        </div>

        <div class="col-md-4 mb-3">
            <label>Tanggal Kunjungan</label>
            <input type="date" name="tanggal_kunjungan" class="form-control" required>
        </div>

        <div class="col-md-4 mb-3">
            <label>Layanan Kesehatan</label>
            <select name="layanan_kes" id="layanan_kes_tambah" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="Vaksinasi & Imunisasi">Vaksinasi & Imunisasi</option>
                <option value="Laboratorium Mikrobiologi">Laboratorium Mikrobiologi</option>
                <option value="Klinik">Klinik</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label>Aktivitas</label>
            <input type="text" name="aktivitas" class="form-control" value="Penawaran & Kunjungan" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label>Jenis Produk / Jasa</label>
             <select name="jenis" id="jenis_produk" class="form-control" required>
                <option value="">-- Pilih Produk / Jasa --</option>
             </select>
        </div>

        <div class="col-md-6 mb-3">
            <label>Nilai (Rp)</label>
            <input type="text" name="nilai" class="form-control" placeholder="10.000.000" required>
        </div>

        <div class="col-md-6 mb-3">
            <label>Bukti (Foto)</label>
            <input type="file" name="bukti" class="form-control" accept="image/*" required>
        </div>

        <div class="col-md-6 mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"></textarea>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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

<script>
$(document).ready(function() {

    const dataProduk = {

        "Vaksinasi & Imunisasi": [
            "Vaksin Hepatitis B",
            "Vaksin Flu Bio",
            "Vaksin Influenza (Fluhualan)",
            "Vaksin Anti Kanker Serviks (Cervarix)",
            "Vaksin Anti Rabies (Verorab)",
            "Vaksin Havrix Dewasa",
            "Vaksin Menivax",
            "Vaksin Varicella (BCHT) - Loyalty Program",
            "Vaksin Covid-19 (Indovac)",
            "Vaksin Thypim Vi (Thypoid)",
            "Vaksin Havrix Anak",
            "Vaksin Engerix-B Anak",
            "Vaksin Bio TT",
            "Vaksin Anti Kanker Serviks (Gardasil)",
            "Vaksin Td",
            "Vaksin Dengue (Qdenga)",
            "Vaksin Vaxigrip Tetra"
        ],

        "Laboratorium Mikrobiologi": [
            "Aerob Sample Plat",
            "Mac Conkey Agar",
            "Uji Pirogen",
            "HPV-hr qPCR",
            "Efektivitas Zat Aktif Bakteri Aerob (1 Bakteri)",
            "Aerob Sample Non Plate",
            "Makanan Catering Otomatis",
            "Identifikasi Yeast",
            "Rectal Swab",
            "Identifikasi Kapang (Mould) Sample Non Plate (Industri)",
            "Identifikasi Jamur Sample Plate",
            "Anaerob Sample Plat",
            "E. Coli",
            "Anaerob Sample Non Plate",
            "SARS-CoV-2 RNA",
            "Identifikasi Jamur Sample Plate/Non Plate - Mikroskopis (Yeast & Mould)",
            "Foto Slide/Cultur"
        ],

        "Klinik": [
            "HBsAg (Rapid)",
            "Asam Urat",
            "Glukosa Puasa",
            "Hematologi Rutin +Diff",
            "SGOT",
            "SGPT",
            "Kreatinin",
            "Ureum",
            "Triglyserida",
            "Urine Rutin",
            "HDL Cholesterol",
            "Cholesterol Total",
            "LDL Cholesterol Direk",
            "MCU Paket Bio Silver",
            "LED 1 Jam",
            "SARS CoV-2 Antigen Test",
            "Multidrug Test",
            "DADA Thorax",
            "Tes Kehamilan"
        ]

    };


    // EVENT CHANGE
    $('#layanan_kes_tambah').on('change', function() {

        let layanan = $(this).val();
        let produk = $('#jenis_produk');

        produk.html('<option value="">-- Pilih Produk / Jasa --</option>');

        if (dataProduk[layanan]) {
            dataProduk[layanan].forEach(function(item) {
                produk.append('<option value="'+item+'">'+item+'</option>');
            });
        }

    });

});
</script>

<script>
$('#modalAktivitas').on('shown.bs.modal', function () {
    $('#layanan_kes_tambah').trigger('change');
});
</script>

</body>
</html>
