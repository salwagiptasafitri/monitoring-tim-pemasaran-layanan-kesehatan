<?php
session_start();
include '../koneksi.php';

// proteksi login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// hanya admin boleh edit user
if ($_SESSION['role'] != 'admin') {
    echo "<script>
        alert('Akses ditolak!');
        window.location='user.php';
    </script>";
    exit;
}

if (isset($_POST['update'])) {

    $id_user = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $role    = mysqli_real_escape_string($koneksi, $_POST['role']);

    // cek user exists
    $cek = mysqli_query($koneksi, "
        SELECT * FROM tb_user 
        WHERE id_user = '$id_user'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) == 0) {

        echo "<script>
            alert('User tidak ditemukan!');
            window.location='user.php';
        </script>";
        exit;
    }

    // update role
    $update = mysqli_query($koneksi, "
        UPDATE tb_user 
        SET role = '$role'
        WHERE id_user = '$id_user'
    ");

    if ($update) {

        echo "<script>
            alert('Role user berhasil diupdate');
            window.location='user.php';
        </script>";

    } else {

        echo "<script>
            alert('Gagal update user');
            window.location='user.php';
        </script>";

    }

}
?>
