<?php
include '../koneksi.php'; // pastikan koneksi database sudah benar

// Buat folder temporary upload backup kalau belum ada
$upload_dir = __DIR__ . '/backup_restore_temp/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Jika user submit form
if (isset($_POST['restore'])) {
    $errors = [];

    // Cek file SQL
    if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] == 0) {
        $sql_tmp = $_FILES['sql_file']['tmp_name'];
        $sql_name = $upload_dir . basename($_FILES['sql_file']['name']);
        move_uploaded_file($sql_tmp, $sql_name);
    } else {
        $errors[] = "File SQL tidak diupload.";
    }

    // Cek file ZIP
    if (isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] == 0) {
        $zip_tmp = $_FILES['zip_file']['tmp_name'];
        $zip_name = $upload_dir . basename($_FILES['zip_file']['name']);
        move_uploaded_file($zip_tmp, $zip_name);
    } else {
        $errors[] = "File ZIP tidak diupload.";
    }

    if (empty($errors)) {
        // === STEP 1: RESTORE DATABASE ===
        $queries = file_get_contents($sql_name);

        // Tambahkan DROP TABLE IF EXISTS otomatis sebelum CREATE TABLE
        $queries = preg_replace('/CREATE TABLE `(.*?)`/i', 'DROP TABLE IF EXISTS `$1`; CREATE TABLE `$1`', $queries);

        if (mysqli_multi_query($koneksi, $queries)) {
            do {
                mysqli_store_result($koneksi);
            } while (mysqli_next_result($koneksi));
            echo "<div class='alert success'>✅ Database berhasil direstore!</div>";
        } else {
            echo "<div class='alert error'>❌ Gagal restore database: " . mysqli_error($koneksi) . "</div>";
        }

        // === STEP 2: RESTORE FILES ===
        $zip = new ZipArchive();
        if ($zip->open($zip_name) === TRUE) {
            $extract_to = __DIR__ . '/uploads/'; // folder tujuan ekstrak
            if (!file_exists($extract_to)) {
                mkdir($extract_to, 0777, true);
            }
            $zip->extractTo($extract_to);
            $zip->close();
            echo "<div class='alert success'>✅ File uploads berhasil direstore!</div>";
        } else {
            echo "<div class='alert error'>❌ Gagal membuka file ZIP.</div>";
        }

        // Hapus file upload temporary
        unlink($sql_name);
        unlink($zip_name);
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert error'>❌ $error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore Backup - Sistem Manajemen</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #42A5F5 0%, #1565C0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            max-width: 600px;
            width: 100%;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #42A5F5, #1565C0);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        h2 i {
            color: #1565C0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }

        .file-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 20px;
            background: #f7fafc;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-input-wrapper:hover {
            border-color: #42A5F5;
            background: #edf2f7;
        }

        .file-input-wrapper i {
            font-size: 24px;
            color: #a0aec0;
            margin-right: 15px;
        }

        .file-input-wrapper span {
            color: #718096;
            font-weight: 500;
        }

        input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-name {
            margin-top: 8px;
            font-size: 14px;
            color: #38a169;
            font-weight: 500;
        }

        button {
            width: 100%;
            background: linear-gradient(135deg, #42A5F5 0%, #1565C0 100%);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(66, 165, 245, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        .alert {
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        .alert.success {
            background-color: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert.error {
            background-color: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-text {
            text-align: center;
            margin-top: 20px;
            color: #718096;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
                margin: 20px;
            }

            h2 {
                font-size: 24px;
            }

            .file-input-wrapper {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-upload"></i> Restore Backup</h2>
    
    <?php if (isset($_POST['restore'])): ?>
        <!-- Alerts will be displayed here by PHP -->
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data" id="restoreForm">
        <div class="form-group">
            <label for="sql_file">File Database (.sql)</label>
            <div class="file-input-wrapper">
                <i class="fas fa-database"></i>
                <span>Klik untuk memilih file SQL</span>
                <input type="file" name="sql_file" id="sql_file" accept=".sql" required>
            </div>
            <div class="file-name" id="sqlFileName"></div>
        </div>
        
        <div class="form-group">
            <label for="zip_file">File Uploads (.zip)</label>
            <div class="file-input-wrapper">
                <i class="fas fa-file-archive"></i>
                <span>Klik untuk memilih file ZIP</span>
                <input type="file" name="zip_file" id="zip_file" accept=".zip" required>
            </div>
            <div class="file-name" id="zipFileName"></div>
        </div>
        
        <button type="submit" name="restore">
            <i class="fas fa-play"></i>
            Mulai Restore
        </button>
    </form>
    
    <div class="info-text">
        <i class="fas fa-info-circle"></i>
        Pastikan file backup valid dan sistem memiliki izin yang cukup untuk melakukan restore.
    </div>
</div>

<script>
    // Update file name display when file is selected
    document.getElementById('sql_file').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : '';
        document.getElementById('sqlFileName').textContent = fileName;
    });

    document.getElementById('zip_file').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : '';
        document.getElementById('zipFileName').textContent = fileName;
    });

    // Add loading state to button (optional enhancement)
    document.getElementById('restoreForm').addEventListener('submit', function() {
        const button = this.querySelector('button');
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        button.disabled = true;
    });
</script>

</body>
</html>