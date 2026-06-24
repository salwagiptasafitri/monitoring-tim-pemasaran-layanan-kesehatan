<?php
date_default_timezone_set('Asia/Jakarta');

require 'koneksi.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    // amankan input
    $token = mysqli_real_escape_string($koneksi, $_POST['token']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // cek token valid dan belum expired
    $cek = mysqli_query($koneksi,"
        SELECT id_user 
        FROM tb_user 
        WHERE reset_token='$token'
        AND reset_expire >= NOW()
        LIMIT 1
    ");

    if(mysqli_num_rows($cek)==0){

        echo "<script>
        alert('Token tidak valid atau expired');
        window.location='index.php';
        </script>";

        exit;
    }

    // update password
    $update = mysqli_query($koneksi,"
        UPDATE tb_user SET
        password='$password',
        reset_token=NULL,
        reset_expire=NULL
        WHERE reset_token='$token'
    ");

    if($update){

        echo "<script>
        alert('Password berhasil direset');
        window.location='index.php';
        </script>";

    }else{

        echo "Gagal reset password: " . mysqli_error($koneksi);

    }

}else{

    header("Location: index.php");
    exit;

}
?>
