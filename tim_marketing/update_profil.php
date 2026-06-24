<?php
session_start();
include "../koneksi.php";

/* ===============================
   PROTEKSI LOGIN
================================ */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

/* ===============================
   VALIDASI SUBMIT
================================ */
if (!isset($_POST['simpan'])) {
    header("Location: profil.php");
    exit;
}

$id_user = $_SESSION['user_id'];

/* ===============================
   AMBIL & AMANKAN INPUT
================================ */
$nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
$email    = mysqli_real_escape_string($koneksi, trim($_POST['email']));
$password = $_POST['password'];
$password2= $_POST['password2'];

/* ===============================
   VALIDASI DASAR
================================ */
if ($nama == '' || $email == '') {
    echo "<script>
        alert('Nama dan Email wajib diisi!');
        window.location='profil.php';
    </script>";
    exit;
}

/* ===============================
   CEK EMAIL DUPLIKAT
================================ */
$cek = mysqli_query($koneksi, "
    SELECT id_user FROM tb_user
    WHERE email='$email' AND id_user != '$id_user'
");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>
        alert('Email sudah digunakan oleh user lain!');
        window.location='profil.php';
    </script>";
    exit;
}

/* ===============================
   UPDATE DENGAN / TANPA PASSWORD
================================ */
$update_success = false;

if ($password != '') {

    if ($password !== $password2) {
        echo "<script>
            alert('Konfirmasi password tidak cocok!');
            window.location='profil.php';
        </script>";
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $update = mysqli_query($koneksi, "
        UPDATE tb_user SET
            nama='$nama',
            email='$email',
            password='$hash'
        WHERE id_user='$id_user'
    ");
    if ($update) {
        $update_success = true;
    }

} else {

    $update = mysqli_query($koneksi, "
        UPDATE tb_user SET
            nama='$nama',
            email='$email'
        WHERE id_user='$id_user'
    ");
    if ($update) {
        $update_success = true;
    }
}

/* ===============================
   CATAT KE RIWAYAT (JIKA UPDATE BERHASIL)
================================ */
if ($update_success) {
    $pekerjaan = mysqli_real_escape_string($koneksi, "update profil");
    mysqli_query($koneksi, "
        INSERT INTO tb_riwayat (id_user, pekerjaan)
        VALUES ('$id_user', '$pekerjaan')
    ");
}

/* ===============================
   UPDATE SESSION NAMA (OPSIONAL)
================================ */
$_SESSION['nama'] = $nama;

/* ===============================
   REDIRECT
================================ */
echo "<script>
    alert('Profil berhasil diperbarui');
    window.location='profil.php';
</script>";
exit;
?>