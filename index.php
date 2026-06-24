<?php
session_start();
include 'koneksi.php';
error_reporting(0);

// Jika sudah login
if (!empty($_SESSION['status_login']) && $_SESSION['status_login'] === true) {
    header("Location: tim_marketing/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Monitoring Tim Pemasaran</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root {
  --primary: #1e3a8a; /* Biru tua untuk teks utama */
  --accent: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%); /* Gradien biru muda ke biru tua */
  --bg: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); /* Background gradien biru sangat muda */
  --border: #cbd5e1; /* Border abu-abu biru */
  --text: #1e293b; /* Teks gelap */
  --text-muted: #64748b; /* Teks muted */
  --shadow: 0 20px 40px rgba(30, 58, 138, 0.15); /* Shadow lebih dalam */
  --card-bg: rgba(255, 255, 255, 0.98);
  --success: #10b981; /* Hijau untuk sukses */
  --error: #ef4444; /* Merah untuk error */
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
}

body {
  height: 100vh;
  background: var(--bg);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  position: relative;
  color: var(--text);
}

/* Animated Background dengan pola geometris */
body::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: 
    radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(30, 58, 138, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 40%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
  animation: float 30s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-20px) rotate(1deg); }
  66% { transform: translateY(10px) rotate(-1deg); }
}

/* CARD */
.auth-card {
  width: 100%;
  max-width: 450px;
  background: var(--card-bg);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(59, 130, 246, 0.15);
  border-radius: 24px;
  padding: 48px;
  box-shadow: var(--shadow);
  animation: slideIn 1s ease-out;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.auth-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 30px 60px rgba(30, 58, 138, 0.2);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(60px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* HEADER */
.auth-header {
  text-align: center;
  margin-bottom: 40px;
}

.auth-header .logo {
  width: 90px;
  height: auto;
  margin-bottom: 20px;
  filter: drop-shadow(0 4px 8px rgba(30, 58, 138, 0.2));
  animation: fadeInLogo 1.2s ease-out;
}

@keyframes fadeInLogo {
  from { opacity: 0; transform: scale(0.8) rotate(-5deg); }
  to { opacity: 1; transform: scale(1) rotate(0deg); }
}

.auth-header h1 {
  font-size: 28px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.auth-header p {
  font-size: 16px;
  color: var(--text-muted);
  line-height: 1.5;
}

/* FORM */
.auth-card .input-group {
  position: relative;
  margin-bottom: 24px;
}

.auth-card .input-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: var(--text);
  margin-bottom: 8px;
}

.auth-card input {
  width: 100%;
  padding: 18px 20px 18px 50px;
  border-radius: 12px;
  border: 2px solid var(--border);
  font-size: 16px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  outline: none;
}

.auth-card input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.auth-card input::placeholder {
  color: var(--text-muted);
}

.auth-card .input-group .input-icon {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  font-size: 18px;
}

.auth-card .input-group .toggle-password {
  position: absolute;
  right: 18px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: var(--text-muted);
  transition: color 0.3s ease;
  font-size: 18px;
}

.auth-card .input-group .toggle-password:hover {
  color: #3b82f6;
}

/* BUTTON */
.auth-card button {
  width: 100%;
  padding: 18px;
  border-radius: 12px;
  border: none;
  background: var(--accent);
  color: #ffffff;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  margin-top: 8px;
}

.auth-card button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.6s;
}

.auth-card button:hover::before {
  left: 100%;
}

.auth-card button:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
}

.auth-card button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* ALERTS */
.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
  font-weight: 500;
}

.alert.success {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid var(--success);
  color: var(--success);
}

.alert.error {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid var(--error);
  color: var(--error);
}

/* LINK */
.auth-footer {
  text-align: center;
  margin-top: 32px;
  font-size: 15px;
  color: var(--text-muted);
}

.auth-footer a {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  position: relative;
}

.auth-footer a::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: #3b82f6;
  transition: width 0.3s ease;
}

.auth-footer a:hover::after {
  width: 100%;
}

.auth-footer a:hover {
  color: var(--primary);
}

/* TOGGLE */
.hidden {
  display: none;
}

.form-container {
  animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

/* FOOTER */
.auth-footer-small {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 12px;
  color: var(--text-muted);
  text-align: center;
}

/* Responsive */
@media (max-width: 480px) {
  .auth-card {
    margin: 20px;
    padding: 32px 24px;
    max-width: 100%;
  }
  
  .auth-header h1 {
    font-size: 24px;
  }
  
  .auth-header .logo {
    width: 70px;
  }
  
  .auth-header p {
    font-size: 14px;
  }
  
  .auth-card input {
    padding: 16px 18px 16px 45px;
  }
  
  .auth-card .input-group .input-icon,
  .auth-card .input-group .toggle-password {
    font-size: 16px;
  }
}
</style>
</head>
<body>

<div class="auth-card">

  <!-- HEADER -->
  <div class="auth-header">
    <img src="assets/img/biofarma.png" alt="Biofarma Logo" class="logo">
    <h1>Portal Monitoring Tim Pemasaran</h1>
    <p>Silakan masuk menggunakan akun Anda untuk mengakses dashboard</p>
  </div>

  <!-- ALERTS (untuk pesan error/sukses jika diperlukan) -->
  <?php if (isset($_GET['error'])): ?>
    <div class="alert error">
      <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
  <?php endif; ?>
  <?php if (isset($_GET['success'])): ?>
    <div class="alert success">
      <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
  <?php endif; ?>

  <!-- LOGIN -->
  <div id="loginForm" class="form-container">
    <form method="POST" action="ck_login.php" id="loginFormElement">
      <div class="input-group">
        <label for="loginEmail">Email</label>
        <i class="fas fa-envelope input-icon"></i>
        <input type="email" id="loginEmail" name="email" placeholder="Masukkan email Anda" required>
      </div>
      <div class="input-group">
        <label for="loginPassword">Password</label>
        <i class="fas fa-lock input-icon"></i>
        <input type="password" id="loginPassword" name="password" placeholder="Masukkan password Anda" required>
        <i class="fas fa-eye toggle-password" onclick="togglePassword('loginPassword', this)"></i>
      </div>
      <button type="submit" name="submit" id="loginBtn">Masuk</button>
    </form>

    <div class="auth-footer">
      Belum punya akun? <a href="#" onclick="toggleForm()">Daftar sekarang</a> <br>
  <a href="lupa_password.php">Lupa password?</a>

    </div>

  </div>

  <!-- REGISTER -->
  <div id="registerForm" class="hidden form-container">
    <form method="POST" action="registrasi.php" id="registerFormElement">
      <div class="input-group">
        <label for="registerName">Nama Lengkap</label>
        <i class="fas fa-user input-icon"></i>
        <input type="text" id="registerName" name="nama" placeholder="Masukkan nama lengkap Anda" required>
      </div>
      <div class="input-group">
        <label for="registerEmail">Email</label>
        <i class="fas fa-envelope input-icon"></i>
        <input type="email" id="registerEmail" name="email" placeholder="Masukkan email Anda" required>
      </div>
      <div class="input-group">
        <label for="registerPassword">Password</label>
        <i class="fas fa-lock input-icon"></i>
        <input type="password" id="registerPassword" name="password" placeholder="Masukkan password Anda" required>
        <i class="fas fa-eye toggle-password" onclick="togglePassword('registerPassword', this)"></i>
      </div>
      <button type="submit" name="register" id="registerBtn">Daftar</button>
    </form>

    <div class="auth-footer">
      Sudah punya akun? <a href="#" onclick="toggleForm()">Masuk di sini</a>

    </div>

  </div>

</div>

<div class="auth-footer-small">
  &copy; 2026 Biofarma. Semua hak dilindungi.
</div>

<script>
function toggleForm() {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  
  loginForm.classList.toggle('hidden');
  registerForm.classList.toggle('hidden');
  
  // Reset animations
  if (!loginForm.classList.contains('hidden')) {
    loginForm.style.animation = 'none';
    setTimeout(() => { loginForm.style.animation = 'fadeIn 0.6s ease'; }, 10);
  } else {
    registerForm.style.animation = 'none';
    setTimeout(() => { registerForm.style.animation = 'fadeIn 0.6s ease'; }, 10);
  }
}

function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

// Simple form validation
document.getElementById('loginFormElement').addEventListener('submit', function(e) {
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;
  
  if (!email || !password) {
    e.preventDefault();
    alert('Harap isi semua field.');
  }
});

document.getElementById('registerFormElement').addEventListener('submit', function(e) {
  const name = document.getElementById('registerName').value;
  const email = document.getElementById('registerEmail').value;
  const password = document.getElementById('registerPassword').value;
  
  if (!name || !email || !password) {
    e.preventDefault();
    alert('Harap isi semua field.');
  }
});
</script>

</body>
</html>