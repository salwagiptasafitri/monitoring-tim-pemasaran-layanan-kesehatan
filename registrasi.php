<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role     = 'tim_marketing';
    $status   = 'logout';
    $token    = bin2hex(random_bytes(32));

    // CEK EMAIL
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Email sudah terdaftar'); window.history.back();</script>";
        exit;
    }

    // SIMPAN USER (BELUM VERIFIKASI)
    mysqli_query($koneksi, "
        INSERT INTO tb_user 
        (nama,email,password,role,status,verify_token,email_verified)
        VALUES 
        ('$nama','$email','$password','$role','$status','$token',0)
    ");

    // ================== KIRIM EMAIL ==================
    $mail = new PHPMailer(true);
    try {
        // KONFIGURASI SMTP (TETAP)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'digitalpmarsip@gmail.com';
        $mail->Password   = 'wrgnuadcejmobabb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // PENGIRIM & PENERIMA
        $mail->setFrom('digitalpmarsip@gmail.com', 'Portal Monitoring Tim Pemasaran');
        $mail->addAddress($email, $nama);

        // LINK VERIFIKASI
        $link = "http://localhost/monitoring_marketing/verifikasi_email.php?token=$token";

        // EMAIL HTML (CORPORATE)
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Akun Portal Monitoring Tim Pemasaran';
        $mail->Body = "
<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
</head>
<body style='margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif'>
  <table width='100%' cellpadding='0' cellspacing='0' style='padding:30px 0'>
    <tr>
      <td align='center'>
        <table width='100%' style='max-width:620px;background:#ffffff;
          border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.08)'>
          
          <!-- HEADER -->
          <tr>
            <td style='background:#0a2540;padding:22px 30px;border-radius:10px 10px 0 0'>
              <h2 style='margin:0;color:#ffffff;font-size:22px'>
                Portal Monitoring Tim Pemasaran
              </h2>
            </td>
          </tr>

          <!-- CONTENT -->
          <tr>
            <td style='padding:30px;color:#1f2937;font-size:15px;line-height:1.7'>
              <p>Yth. <b>$nama</b>,</p>

              <p>
                Terima kasih telah melakukan pendaftaran pada
                <b>Portal Monitoring Tim Pemasaran</b>.
              </p>

              <p>
                Untuk mengaktifkan akun Anda, silakan klik tombol
                verifikasi di bawah ini:
              </p>

              <p style='text-align:center;margin:35px 0'>
                <a href='$link'
                   style='background:#1e40af;color:#ffffff;
                   padding:14px 32px;
                   text-decoration:none;
                   border-radius:8px;
                   font-weight:600;
                   display:inline-block'>
                  Verifikasi Akun
                </a>
              </p>

              <p>
                Jika Anda tidak merasa melakukan pendaftaran,
                silakan abaikan email ini.
              </p>

              <p style='margin-top:30px'>
                Hormat kami,<br>
                <b>Administrator<br>
                Portal Monitoring Tim Pemasaran</b>
              </p>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style='background:#f9fafb;
              padding:18px;
              text-align:center;
              font-size:12px;
              color:#6b7280;
              border-radius:0 0 10px 10px'>
              &copy; " . date('Y') . " Portal Monitoring Tim Pemasaran. All rights reserved.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
";

        $mail->send();
        echo "<script>alert('Link verifikasi telah dikirim ke email Anda'); window.location='index.php';</script>";

    } catch (Exception $e) {
        echo "Email gagal dikirim. Error: {$mail->ErrorInfo}";
    }

} else {
    header("Location: index.php");
    exit;
}
?>
