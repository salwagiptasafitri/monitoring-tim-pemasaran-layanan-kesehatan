<?php
include '../koneksi.php';

if (!isset($_POST['update'])) {
    header("Location: riwayat_aktivitas.php");
    exit;
}

$id_marketing = mysqli_real_escape_string($koneksi, $_POST['id_marketing']);
$status_baru  = mysqli_real_escape_string($koneksi, $_POST['status']);

/* =====================================
   1. Ambil data marketing
===================================== */
$q = mysqli_query($koneksi, "
    SELECT * FROM tb_marketing
    WHERE id_marketing = '$id_marketing'
    LIMIT 1
");

if (!$q || mysqli_num_rows($q) == 0) {
    echo "<script>
        alert('Data marketing tidak ditemukan!');
        window.location='riwayat_aktivitas.php';
    </script>";
    exit;
}

$m = mysqli_fetch_assoc($q);
$status_lama = trim($m['status']);
$id_user = $m['id_user']; // Ambil id_user untuk riwayat

/* =====================================
   2. VALIDASI TRANSISI STATUS
===================================== */
$valid = false;

/* Penawaran → Negosiasi */
if (
    ($status_lama == '' || $status_lama == 'Penawaran & Kunjungan')
    && $status_baru == 'Negosiasi / Follow Up'
) {
    $valid = true;
}

/* Negosiasi → Deal / Reject */
elseif (
    $status_lama == 'Negosiasi / Follow Up'
    && in_array($status_baru, ['Deal','Reject'])
) {
    $valid = true;
}

/* Deal → Aktivitas Layanan */
elseif (
    $status_lama == 'Deal'
    && $status_baru == 'Aktivitas Layanan'
) {
    $valid = true;
}

/* STATUS FINAL TIDAK BOLEH BERUBAH */
elseif (
    in_array($status_lama, ['Reject','Aktivitas Layanan'])
) {
    $valid = false;
}

if (!$valid) {
    echo "<script>
        alert('Perubahan status tidak valid!');
        window.location='riwayat_aktivitas.php';
    </script>";
    exit;
}

/* =====================================
   3. UPDATE STATUS MARKETING
===================================== */
$update = mysqli_query($koneksi, "
    UPDATE tb_marketing
    SET status = '$status_baru'
    WHERE id_marketing = '$id_marketing'
");

/* =====================================
   4. HITUNG INSENTIF HANYA SAAT MASUK DEAL
===================================== */
if ($status_baru == 'Deal') {

    $nilai   = (float)$m['nilai'];
    $layanan = mysqli_real_escape_string($koneksi, $m['layanan_kes']);
    $id_user = $m['id_user'];

    /* Cari / buat tb_insentif */
    $qi = mysqli_query($koneksi, "
        SELECT * FROM tb_insentif
        WHERE salesman = '$id_user'
          AND layanan  = '$layanan'
        LIMIT 1
    ");

    if ($qi && mysqli_num_rows($qi) > 0) {
        $ins = mysqli_fetch_assoc($qi);
    } else {
        mysqli_query($koneksi, "
            INSERT INTO tb_insentif
            (layanan, salesman, target, pendapatan, persentase, insentif_penjualan, insentif)
            VALUES
            ('$layanan', '$id_user', 0, 0, 0, 0, 0)
        ");
        $id_insentif = mysqli_insert_id($koneksi);
        $qi2 = mysqli_query($koneksi, "
            SELECT * FROM tb_insentif
            WHERE id_insentif = '$id_insentif'
        ");
        $ins = mysqli_fetch_assoc($qi2);
    }

    /* Hitung pendapatan */
    $pendapatan_baru = $ins['pendapatan'] + $nilai;
    $target = (float)$ins['target'];

    $persentase = ($target > 0)
        ? ($pendapatan_baru * 100) / $target
        : 0;

    /* Hitung insentif */
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
        UPDATE tb_insentif
        SET pendapatan = '$pendapatan_baru',
            persentase = '$persentase',
            insentif_penjualan = '$insentif_penjualan',
            insentif = '$insentif'
        WHERE id_insentif = '{$ins['id_insentif']}'
    ");
}

/* =====================================
   5. CATAT KE RIWAYAT (JIKA UPDATE BERHASIL)
===================================== */
if ($update) {
    $pekerjaan = mysqli_real_escape_string($koneksi, "update riwayat aktivitas klien");
    mysqli_query($koneksi, "
        INSERT INTO tb_riwayat (id_user, pekerjaan)
        VALUES ('$id_user', '$pekerjaan')
    ");
}

header("Location: riwayat_aktivitas.php");
exit;
?>