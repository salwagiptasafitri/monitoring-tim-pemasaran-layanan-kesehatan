<?php
require 'koneksi.php';

$token = $_GET['token'];

$query = mysqli_query($koneksi, "
    UPDATE tb_user 
    SET email_verified=1, verify_token=NULL
    WHERE verify_token='$token'
");

if (mysqli_affected_rows($koneksi) > 0) {
    echo "<script>alert('Akun berhasil diverifikasi. Silakan login'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Token tidak valid atau sudah digunakan'); window.location='index.php';</script>";
}
