<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['status_login'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['simpan'])) {

    // pastikan session user ada
    if (!isset($_SESSION['user_id'])) {
        die('Session user tidak ditemukan, silakan login ulang');
    }

    $id_user           = $_SESSION['user_id'];
    $nama_klien        = $_POST['nama_klien'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $layanan_kes       = $_POST['layanan_kes'];
    $jenis_produk      = $_POST['jenis'];
    $aktivitas         = $_POST['aktivitas'];
    $nilai             = str_replace('.', '', $_POST['nilai']);
    $keterangan        = $_POST['keterangan'];

    // Upload bukti
    $bukti = '';
    if (!empty($_FILES['bukti']['name'])) {

        $nama_file = $_FILES['bukti']['name'];
        $tmp       = $_FILES['bukti']['tmp_name'];

        $bukti = time() . '_' . $nama_file;

        move_uploaded_file($tmp, "../uploads/" . $bukti);
    }

    // INSERT KE tb_marketing
    $query = mysqli_query($koneksi, "
        INSERT INTO tb_marketing 
        (id_user, nama_klien, tanggal_kunjungan, bukti, layanan_kes, jenis, nilai, keterangan, status)
        VALUES 
        ('$id_user',
         '$nama_klien',
         '$tanggal_kunjungan',
         '$bukti',
         '$layanan_kes',
         '$jenis_produk',
         '$nilai',
         '$keterangan',
         '$aktivitas')
    ");

    if ($query) {

        // INSERT KE tb_riwayat
        $kerjaan = "Menambahkan aktivitas marketing";

        mysqli_query($koneksi, "
            INSERT INTO tb_riwayat
            (id_user, pekerjaan)
            VALUES
            ('$id_user', '$kerjaan')
        ");

        header("Location: aktivitas_kunjungan.php?status=berhasil");
        exit;

    } else {

        echo "Error: " . mysqli_error($koneksi);
        exit;

    }

}
?>
