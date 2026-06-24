<?php
session_start();
include '../koneksi.php';

if (isset($_POST['update'])) {

    $id_insentif = $_POST['id_insentif'];
    $target = $_POST['target'];

    // Ambil pendapatan
    $q = mysqli_query($koneksi, "
        SELECT pendapatan 
        FROM tb_insentif 
        WHERE id_insentif = '$id_insentif'
    ");
    $data = mysqli_fetch_assoc($q);
    $pendapatan = $data['pendapatan'];

    // ================= HITUNG PERSENTASE =================
    if ($target > 0) {
        $persentase = ($pendapatan / $target) * 100;
    } else {
        $persentase = 0;
    }

    // ================= KETENTUAN INSENTIF =================
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

    // ================= UPDATE DATABASE =================
    $update = mysqli_query($koneksi, "
        UPDATE tb_insentif SET
            target = '$target',
            persentase = '$persentase',
            insentif_penjualan = '$insentif_penjualan',
            insentif = '$insentif'
        WHERE id_insentif = '$id_insentif'
    ");

    if ($update) {
        echo "<script>
            alert('Insentif berhasil diperbarui otomatis sesuai ketentuan');
            window.location='insentif.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal memperbarui insentif');
            history.back();
        </script>";
    }
}
?>
