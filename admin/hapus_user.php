<?php
session_start();
include '../koneksi.php';

// proteksi login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// hanya admin boleh hapus
if ($_SESSION['role'] != 'admin') {
    echo "<script>
        alert('Akses ditolak!');
        window.location='user.php';
    </script>";
    exit;
}

if (isset($_POST['hapus'])) {

    $id_user = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $id_login = $_SESSION['user_id'];

    // tidak boleh hapus diri sendiri
    if ($id_user == $id_login) {

        echo "<script>
            alert('Anda tidak dapat menghapus akun sendiri!');
            window.location='user.php';
        </script>";
        exit;
    }

    // cek apakah admin
    $cek = mysqli_query($koneksi, "
        SELECT role FROM tb_user 
        WHERE id_user = '$id_user'
        LIMIT 1
    ");

    $data = mysqli_fetch_assoc($cek);

    // tidak boleh hapus admin
    if ($data['role'] == 'admin') {

        echo "<script>
            alert('Admin tidak dapat dihapus!');
            window.location='user.php';
        </script>";
        exit;
    }

    // hapus user
    $hapus = mysqli_query($koneksi, "
        DELETE FROM tb_user
        WHERE id_user = '$id_user'
    ");

    if ($hapus) {

        echo "<script>
            alert('User berhasil dihapus');
            window.location='user.php';
        </script>";

    } else {

        echo "<script>
            alert('Gagal menghapus user');
            window.location='user.php';
        </script>";

    }

}
?>
