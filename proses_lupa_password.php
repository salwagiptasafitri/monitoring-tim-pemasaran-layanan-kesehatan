<?php
date_default_timezone_set('Asia/Jakarta');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = mysqli_real_escape_string($koneksi, $_POST['email']);

    // CEK EMAIL
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE email='$email' LIMIT 1");

    if(mysqli_num_rows($cek) == 0){
        echo "<script>alert('Email tidak ditemukan');window.location='lupa_password.php';</script>";
        exit;
    }

    $data = mysqli_fetch_assoc($cek);
    $nama = $data['nama'];

    // BUAT TOKEN
    $token = bin2hex(random_bytes(32));
    $expired = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // SIMPAN TOKEN
    $update = mysqli_query($koneksi,"
        UPDATE tb_user 
        SET 
            reset_token='$token',
            reset_expire='$expired'
        WHERE email='$email'
    ");

    if(!$update){
        die("Gagal simpan token: " . mysqli_error($koneksi));
    }

    // LINK RESET
    $link = "http://localhost/monitoring_marketing/reset_password.php?token=".$token;

    $mail = new PHPMailer(true);

    try {

        // SMTP CONFIG
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'digitalpmarsip@gmail.com';
        $mail->Password   = 'wrgnuadcejmobabb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // PENGIRIM
        $mail->setFrom('digitalpmarsip@gmail.com', 'Portal Monitoring');

        // PENERIMA
        $mail->addAddress($email, $nama);

        // EMAIL
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Portal Monitoring';

        $mail->Body = "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reset Password</title>
            <style>
                body {
                    font-family: 'Roboto', Arial, sans-serif;
                    background-color: #f4f6f9;
                    margin: 0;
                    padding: 0;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(90deg, #1e3a8a, #87ceeb);
                    padding: 20px;
                    text-align: center;
                    color: #ffffff;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 700;
                }
                .content {
                    padding: 30px;
                    text-align: center;
                }
                .content h3 {
                    font-size: 20px;
                    margin-bottom: 10px;
                    color: #333;
                }
                .content p {
                    font-size: 16px;
                    line-height: 1.6;
                    margin-bottom: 20px;
                    color: #555;
                }
                .button {
                    display: inline-block;
                    background: linear-gradient(135deg, #1e3a8a 0%, #87ceeb 100%);
                    color: #ffffff;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 8px;
                    font-size: 16px;
                    font-weight: 500;
                    margin: 20px 0;
                    transition: background 0.3s;
                }
                .button:hover {
                    background: linear-gradient(135deg, #1a2e6c 0%, #6bb6d6 100%);
                }
                .footer {
                    background-color: #f4f6f9;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #777;
                }
                .footer p {
                    margin: 5px 0;
                }
                @media (max-width: 600px) {
                    .container {
                        margin: 10px;
                    }
                    .content {
                        padding: 20px;
                    }
                    .header h1 {
                        font-size: 20px;
                    }
                    .button {
                        padding: 12px 20px;
                        font-size: 14px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1><i style='margin-right: 10px;'>&#128274;</i>Reset Password</h1>
                </div>
                <div class='content'>
                    <h3>Halo $nama</h3>
                    <p>Kami menerima permintaan untuk mereset password akun Anda. Silakan klik tombol di bawah untuk melanjutkan proses reset password:</p>
                    <a href='$link' class='button'>Reset Password</a>
                    <p><strong>Link berlaku sampai:</strong> $expired</p>
                    <p>Jika Anda tidak meminta reset password, abaikan email ini. Untuk keamanan, jangan bagikan link ini kepada siapa pun.</p>
                </div>
                <div class='footer'>
                    <p>Terima kasih,<br>Tim Support</p>
                    <p>Jika Anda memiliki pertanyaan, hubungi kami di <a href='mailto:razidalfas@gmail.com' style='color: #1e3a8a;'>razidalfas@gmail.com</a></p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->send();

        echo "<script>
        alert('Link reset password berhasil dikirim');
        window.location='index.php';
        </script>";

    } catch (Exception $e) {

        echo "Gagal kirim email: {$mail->ErrorInfo}";
    }

}
?>
