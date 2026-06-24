<?php
session_start();
include '../koneksi.php';

if(isset($_POST['simpan'])){

    $salesman = $_POST['salesman']; // id_user
    $layanan  = mysqli_real_escape_string($koneksi, $_POST['layanan']);
    $target   = str_replace(['.', ','], '', $_POST['target']); // antisipasi format angka

    // kolom lain masih kosong / default 0
    $pendapatan         = 0;
    $persentase         = 0;
    $insentif_penjualan = 0;
    $insentif           = 0;

    $insert = mysqli_query($koneksi, "
        INSERT INTO tb_insentif
        (layanan, salesman, target, pendapatan, persentase, insentif_penjualan, insentif)
        VALUES
        ('$layanan', '$salesman', '$target', '$pendapatan', '$persentase', '$insentif_penjualan', '$insentif')
    ");

    if($insert){
        echo "<script>
            alert('Data insentif berhasil ditambahkan!');
            window.location='insentif.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menambahkan data: ".mysqli_error($koneksi)."');
            window.location='insentif.php';
        </script>";
    }
}
?>
