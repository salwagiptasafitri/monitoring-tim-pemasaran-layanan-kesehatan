<?php
session_start();
include '../koneksi.php';

/* =============================
   1. PROTEKSI LOGIN
============================= */
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../index.php");
    exit;
}

/* =============================
   2. VALIDASI ID
============================= */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location='aktivitas_kunjungan.php';
    </script>";
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =============================
   3. AMBIL DATA MARKETING
============================= */
$query = mysqli_query($koneksi, "
    SELECT *
    FROM tb_marketing
    WHERE id_marketing = '$id'
    LIMIT 1
");

if (!$query || mysqli_num_rows($query) == 0) {
    echo "<script>
        alert('Data tidak ditemukan');
        window.location='aktivitas_kunjungan.php';
    </script>";
    exit;
}

$data = mysqli_fetch_assoc($query);

$status   = trim($data['status']);
$nilai    = (float)$data['nilai'];
$layanan  = mysqli_real_escape_string($koneksi, $data['layanan_kes']);
$id_user  = $data['id_user'];
$bukti    = $data['bukti'];

/* =============================
   4. ROLLBACK INSENTIF
============================= */
if ($status == "Deal" || $status == "Aktivitas Layanan") {
    $queryIns = mysqli_query($koneksi, "
        SELECT *
        FROM tb_insentif
        WHERE salesman = '$id_user'
        AND layanan = '$layanan'
        LIMIT 1
    ");

    if ($queryIns && mysqli_num_rows($queryIns) > 0) {
        $ins = mysqli_fetch_assoc($queryIns);

        $pendapatan_lama = (float)$ins['pendapatan'];
        $target          = (float)$ins['target'];

        /* kurangi pendapatan */
        $pendapatan_baru = $pendapatan_lama - $nilai;

        if ($pendapatan_baru < 0) {
            $pendapatan_baru = 0;
        }

        /* hitung persentase */
        if ($target > 0) {
            $persentase = ($pendapatan_baru / $target) * 100;
        } else {
            $persentase = 0;
        }

        /* hitung insentif */
        if ($persentase >= 125) {
            $insentif_penjualan = 2.00;
            $insentif = 4000000;
        } elseif ($persentase >= 110) {
            $insentif_penjualan = 1.00;
            $insentif = 3000000;
        } elseif ($persentase >= 100) {
            $insentif_penjualan = 0.50;
            $insentif = 2000000;
        } elseif ($persentase >= 90) {
            $insentif_penjualan = 0.25;
            $insentif = 1500000;
        } else {
            $insentif_penjualan = 0;
            $insentif = 0;
        }

        mysqli_query($koneksi, "
            UPDATE tb_insentif SET
            pendapatan = '$pendapatan_baru',
            persentase = '$persentase',
            insentif_penjualan = '$insentif_penjualan',
            insentif = '$insentif'
            WHERE id_insentif = '{$ins['id_insentif']}'
        ");
    }
}

/* =============================
   5. HAPUS FILE BUKTI
============================= */
if (!empty($bukti)) {
    $path = "../uploads/".$bukti;

    if (file_exists($path)) {
        unlink($path);
    }
}

/* =============================
   6. HAPUS DATA MARKETING
============================= */
$hapus = mysqli_query($koneksi, "
    DELETE FROM tb_marketing
    WHERE id_marketing = '$id'
");

/* =============================
   7. CATAT KE RIWAYAT (JIKA BERHASIL HAPUS)
============================= */
if ($hapus) {
    $pekerjaan = mysqli_real_escape_string($koneksi, "menghapus aktivitas");
    mysqli_query($koneksi, "
        INSERT INTO tb_riwayat (id_user, pekerjaan)
        VALUES ('$id_user', '$pekerjaan')
    ");
}

/* =============================
   8. RESULT
============================= */
if ($hapus) {
    echo "<script>
        alert('Data berhasil dihapus');
        window.location='aktivitas_kunjungan.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus data');
        window.location='aktivitas_kunjungan.php';
    </script>";
}
?>