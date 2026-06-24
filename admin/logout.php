<?php
session_start();
include '../koneksi.php';

/* cek apakah user login */
if(isset($_SESSION['user_id'])){

    $id_user = $_SESSION['user_id'];

    /* update status menjadi logout */
    mysqli_query($koneksi, "
        UPDATE tb_user 
        SET status = 'logout'
        WHERE id_user = '$id_user'
    ");
}

/* hancurkan session */
session_destroy();

/* redirect ke login */
header("Location: ../index.php");
exit;
?>
