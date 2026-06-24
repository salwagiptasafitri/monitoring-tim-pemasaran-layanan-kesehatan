<?php
session_start();
include 'koneksi.php';

if (isset($_POST['submit'])) {

    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Ambil user berdasarkan email
    $query = mysqli_query($koneksi, "
        SELECT * FROM tb_user 
        WHERE email = '$email'
        LIMIT 1
    ");

    if (!$query) {
        die('Query error: ' . mysqli_error($koneksi));
    }

    $user = mysqli_fetch_assoc($query);

    if ($user) {

        // Cek verifikasi email
        if ($user['email_verified'] != 1) {
            echo "<script>
                alert('Email belum diverifikasi. Silakan cek inbox Gmail Anda.');
                window.location='index.php';
            </script>";
            exit;
        }

        // Verifikasi password
        if (password_verify($password, $user['password'])) {

            // SET SESSION
            $_SESSION['status_login'] = true;
            $_SESSION['user_id']      = $user['id_user'];
            $_SESSION['nama']         = $user['nama'];
            $_SESSION['email']        = $user['email'];
            $_SESSION['role']         = $user['role'];

            // UPDATE STATUS LOGIN DI DATABASE
            mysqli_query($koneksi, "
                UPDATE tb_user 
                SET status = 'login' 
                WHERE id_user = '{$user['id_user']}'
            ");

            // REDIRECT SESUAI ROLE
            if ($user['role'] == 'tim_marketing') {
                header("Location: tim_marketing/index.php");
                exit;
            } elseif ($user['role'] == 'admin') {
                header("Location: admin/index.php");
                exit;
            } elseif ($user['role'] == 'manajer') {
                header("Location: manajer/index.php");
                exit;
            } else {
                echo "<script>
                    alert('Role tidak dikenali!');
                    window.location='index.php';
                </script>";
                exit;
            }

        } else {
            echo "<script>
                alert('Password salah!');
                window.location='index.php';
            </script>";
            exit;
        }

    } else {
        echo "<script>
            alert('Email tidak terdaftar!');
            window.location='index.php';
        </script>";
        exit;
    }
}
?>
