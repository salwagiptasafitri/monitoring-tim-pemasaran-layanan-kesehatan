<?php
date_default_timezone_set('Asia/Jakarta');

require 'koneksi.php';

// cek token ada atau tidak
if(!isset($_GET['token']) || empty($_GET['token'])){
    die("Token tidak valid");
}

// amankan token
$token = mysqli_real_escape_string($koneksi, $_GET['token']);

// cek token di database
$query = mysqli_query($koneksi,"
    SELECT id_user, email, reset_expire 
    FROM tb_user 
    WHERE reset_token='$token'
    LIMIT 1
");

if(mysqli_num_rows($query) == 0){
    die("Token tidak ditemukan");
}

$data = mysqli_fetch_assoc($query);

// cek expired manual (lebih akurat)
if(strtotime($data['reset_expire']) < time()){
    die("Token sudah expired");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Roboto', Arial, sans-serif;
    background: linear-gradient(135deg, #1e3a8a 0%, #87ceeb 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    color: #333;
}

.card {
    background: white;
    padding: 40px;
    border-radius: 15px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    text-align: center;
    position: relative;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #1e3a8a, #87ceeb);
    border-radius: 15px 15px 0 0;
}

h3 {
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    text-align: left;
    margin-bottom: 5px;
    font-weight: 500;
    color: #555;
}

.input-container {
    position: relative;
    margin-bottom: 20px;
}

.input-container i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
}

input {
    width: 100%;
    padding: 15px 15px 15px 45px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
}

button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #1e3a8a 0%, #87ceeb 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

button:hover {
    background: linear-gradient(135deg, #1a2e6c 0%, #6bb6d6 100%);
    transform: translateY(-2px);
}

button:active {
    transform: translateY(0);
}

.back-link {
    margin-top: 20px;
    font-size: 14px;
}

.back-link a {
    color: #1e3a8a;
    text-decoration: none;
}

.back-link a:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .card {
        padding: 30px 20px;
        margin: 20px;
    }
    h3 {
        font-size: 20px;
    }
}
</style>
</head>
<body>

<div class="card">
    <h3><i class="fas fa-lock"></i> Reset Password</h3>
    <p>Masukkan password baru Anda untuk melanjutkan.</p>

    <form action="proses_reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <label for="password">Password Baru</label>
        <div class="input-container">
            <i class="fas fa-key"></i>
            <input type="password" id="password" name="password" required placeholder="Masukkan password baru">
        </div>

        <button type="submit"><i class="fas fa-save"></i> Reset Password</button>
    </form>

    <div class="back-link">
        <a href="login.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
    </div>
</div>

</body>
</html>