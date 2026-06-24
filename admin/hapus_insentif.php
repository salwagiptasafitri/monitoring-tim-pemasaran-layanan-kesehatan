<?php
session_start();
include '../koneksi.php';

$id = $_GET['id'];

// Ambil pendapatan
$q = mysqli_query($koneksi, "
    SELECT pendapatan 
    FROM tb_insentif 
    WHERE id_insentif = '$id'
");
$data = mysqli_fetch_assoc($q);

if ($data['pendapatan'] > 0) {
    echo "<script>
        alert('Maaf, data insentif sudah memiliki pendapatan dan tidak bisa dihapus!');
        window.location='insentif.php';
    </script>";
    exit;
}

// Jika aman → hapus
$hapus = mysqli_query($koneksi, "
    DELETE FROM tb_insentif 
    WHERE id_insentif = '$id'
");

if ($hapus) {
    echo "<script>
        alert('Data insentif berhasil dihapus');
        window.location='insentif.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus data');
        history.back();
    </script>";
}
?>
