<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['user_id'];

$query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$id_user'");
$user = mysqli_fetch_assoc($query);

if (isset($_POST['simpan'])) {
    $nama  = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];

    if (!empty($pass1)) {
        if ($pass1 !== $pass2) {
            $error = "Konfirmasi password tidak cocok";
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE tb_user SET nama='$nama', email='$email', password='$hash' WHERE id_user='$id_user'");
            $success = "Profil berhasil diperbarui";
        }
    } else {
        mysqli_query($koneksi, "UPDATE tb_user SET nama='$nama', email='$email' WHERE id_user='$id_user'");
        $success = "Profil berhasil diperbarui";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Akun</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
:root{
    --dark-blue:#0f172a;
    --primary:#1e3a8a;
    --light:#3b82f6;
    --lighter:#60a5fa;
    --accent:#93c5fd;
    --white:#ffffff;
    --shadow: rgba(0, 0, 0, 0.1);
    --shadow-hover: rgba(0, 0, 0, 0.2);
}

*{
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
    margin: 0;
    padding: 0;
}

body{
    margin:0;
    min-height:100vh;
    background:linear-gradient(135deg, var(--dark-blue), var(--light));
    display:flex;
    justify-content:center;
    align-items:center;
    overflow-x: hidden;
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card{
    width:450px;
    background:var(--white);
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 40px 80px var(--shadow);
    position: relative;
    animation: slideUp 0.8s ease-out;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 50px 100px var(--shadow-hover);
}

.card-header{
    background:linear-gradient(135deg, var(--primary), var(--light));
    padding:50px 30px;
    text-align:center;
    color:var(--white);
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.avatar{
    width:100px;
    height:100px;
    border-radius:50%;
    background:linear-gradient(135deg, var(--white), var(--accent));
    color:var(--primary);
    font-size:40px;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: bounceIn 1s ease-out;
    position: relative;
    z-index: 1;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

.card-header h2{
    margin:0;
    font-size:24px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.card-header p{
    margin-top:8px;
    font-size:14px;
    opacity:0.9;
    position: relative;
    z-index: 1;
}

.card-body{
    padding:35px;
    position: relative;
}

.btn-back{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:12px 20px;
    border-radius:15px;
    font-size:15px;
    font-weight:600;
    color:var(--primary);
    background:linear-gradient(135deg, var(--accent), var(--lighter));
    text-decoration:none;
    margin-bottom:25px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.btn-back:hover{
    background:linear-gradient(135deg, var(--lighter), var(--accent));
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.form-group{
    display:flex;
    flex-direction:column;
    margin-bottom:20px;
    animation: fadeInUp 0.6s ease-out 0.3s both;
}

.form-group:nth-child(2) { animation-delay: 0.4s; }
.form-group:nth-child(3) { animation-delay: 0.5s; }
.form-group:nth-child(4) { animation-delay: 0.6s; }
.form-group:nth-child(5) { animation-delay: 0.7s; }

.form-group label{
    font-size:14px;
    font-weight:600;
    margin-bottom:8px;
    color:#1e293b;
}

.form-group input{
    padding:14px 16px;
    border-radius:15px;
    border:2px solid #e2e8f0;
    font-size:15px;
    outline:none;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.form-group input:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 4px rgba(59,130,246,0.15);
    background: var(--white);
    transform: scale(1.02);
}

.divider{
    text-align:center;
    font-size:13px;
    color:#64748b;
    margin:30px 0;
    position: relative;
    animation: fadeInUp 0.6s ease-out 0.8s both;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--accent), transparent);
}

.btn-save{
    width:100%;
    padding:16px;
    border:none;
    border-radius:18px;
    background:linear-gradient(135deg, var(--primary), var(--light));
    color:var(--white);
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(30,58,138,0.3);
    animation: fadeInUp 0.6s ease-out 0.9s both;
    position: relative;
    overflow: hidden;
}

.btn-save::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-save:hover::before {
    left: 100%;
}

.btn-save:hover{
    opacity:0.95;
    transform:translateY(-3px);
    box-shadow: 0 12px 35px rgba(30,58,138,0.4);
}

.alert{
    padding:14px 18px;
    border-radius:15px;
    font-size:14px;
    margin-bottom:20px;
    animation: slideDown 0.5s ease-out;
    position: relative;
    overflow: hidden;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-success{
    background:linear-gradient(135deg, #dcfce7, #bbf7d0);
    color:#166534;
    border-left: 5px solid #16a34a;
}

.alert-error{
    background:linear-gradient(135deg, #fee2e2, #fecaca);
    color:#991b1b;
    border-left: 5px solid #dc2626;
}

@media(max-width:480px){
    .card{
        width:95%;
        margin: 20px;
    }
    
    .card-header{
        padding:40px 20px;
    }
    
    .avatar{
        width:80px;
        height:80px;
        font-size:32px;
    }
    
    .card-header h2{
        font-size:20px;
    }
    
    .card-body{
        padding:25px;
    }
}
</style>
</head>

<body>

<div class="card">

    <div class="card-header">
        <div class="avatar"><?= strtoupper(substr($user['nama'],0,1)) ?></div>
        <h2><?= $user['nama'] ?></h2>
        <p>Pengaturan Profil Akun</p>
    </div>

    <div class="card-body">

        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="update_profil.php">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $user['nama'] ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" required>
            </div>

            <div class="divider">Ganti Password (Opsional)</div>

            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password Baru</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
            </div>

            <div class="form-group">
                <label><i class="fas fa-lock"></i> Konfirmasi Password Baru</label>
                <input type="password" name="password2" placeholder="Ulangi password baru">
            </div>

            <button type="submit" name="simpan" class="btn-save">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>

    </div>
</div>

</body>
</html>