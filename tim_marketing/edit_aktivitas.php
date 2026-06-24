<?php
session_start();
include '../koneksi.php';

if (isset($_POST['update'])) {

    $id = $_POST['id_marketing'];

    // cek session user
    if (!isset($_SESSION['user_id'])) {
        die('Session user tidak ditemukan, silakan login ulang');
    }

    $id_user = $_SESSION['user_id'];

    $klien   = $_POST['nama_klien'];
    $tgl     = $_POST['tanggal_kunjungan'];
    $layanan = $_POST['layanan_kes'];
    $produk  = $_POST['jenis_produk'];
    $aktiv   = $_POST['aktivitas'];
    $nilai   = str_replace('.', '', $_POST['nilai']);

    // ambil bukti lama
    $old = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT bukti FROM tb_marketing WHERE id_marketing='$id'")
    );

    $bukti_lama = $old['bukti'];

    // upload bukti baru jika ada
    if (!empty($_FILES['bukti']['name'])) {

        $namaFile = time() . '_' . $_FILES['bukti']['name'];
        $tmp      = $_FILES['bukti']['tmp_name'];

        move_uploaded_file($tmp, "../uploads/" . $namaFile);

        // hapus file lama
        if (!empty($bukti_lama) && file_exists("../uploads/" . $bukti_lama)) {
            unlink("../uploads/" . $bukti_lama);
        }

        $bukti = $namaFile;

    } else {

        $bukti = $bukti_lama;

    }

    // update tb_marketing
    $update = mysqli_query($koneksi, "
        UPDATE tb_marketing SET
            id_user='$id_user',
            nama_klien='$klien',
            tanggal_kunjungan='$tgl',
            layanan_kes='$layanan',
            jenis='$produk',
            nilai='$nilai',
            bukti='$bukti',
            status='$aktiv'
        WHERE id_marketing='$id'
    ");

    if ($update) {

        // INSERT ke tb_riwayat
        $kerjaan = "Mengupdate aktivitas marketing untuk klien: $klien";

        mysqli_query($koneksi, "
            INSERT INTO tb_riwayat
            (id_user, pekerjaan)
            VALUES
            ('$id_user', '$kerjaan')
        ");

        echo "<script>
                alert('Data berhasil diperbarui');
                window.location='aktivitas_kunjungan.php';
              </script>";

    } else {

        echo "<script>
                alert('Gagal memperbarui data');
                window.history.back();
              </script>";

    }

}
?>
