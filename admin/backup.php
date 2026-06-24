<?php
include '../koneksi.php';
session_start();

if ($_SESSION['status_login'] != true) {
    echo "<script>
        alert('Anda harus login');
        document.location.href = 'index.php';
    </script>";
    exit;
}

// Konfigurasi database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'monitoring_pemasaran';

// Koneksi ke database
$koneksi = mysqli_connect($host, $user, $pass, $dbname);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Nama file backup
$date = date('Y-m-d_H-i-s');
$backup_folder = __DIR__ . '/backup/';
if (!file_exists($backup_folder)) {
    mkdir($backup_folder, 0777, true);
}

// ===========================
// Backup Database
// ===========================
$backup_sql_file = $backup_folder . "backup_{$dbname}_{$date}.sql";

$tables = [];
$result = mysqli_query($koneksi, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

$sql_dump = "";
foreach ($tables as $table) {
    $result = mysqli_query($koneksi, "SHOW CREATE TABLE $table");
    $row = mysqli_fetch_row($result);
    $sql_dump .= "-- Struktur tabel $table\n\n";
    $sql_dump .= $row[1] . ";\n\n";

    $result = mysqli_query($koneksi, "SELECT * FROM $table");
    $num_fields = mysqli_num_fields($result);

    if (mysqli_num_rows($result) > 0) {
        $sql_dump .= "-- Data tabel $table\n\n";
        while ($row = mysqli_fetch_row($result)) {
            $sql_dump .= "INSERT INTO $table VALUES(";
            for ($i = 0; $i < $num_fields; $i++) {
                $sql_dump .= isset($row[$i]) ? "'" . mysqli_real_escape_string($koneksi, $row[$i]) . "'" : "NULL";
                if ($i < ($num_fields - 1)) $sql_dump .= ", ";
            }
            $sql_dump .= ");\n";
        }
        $sql_dump .= "\n";
    }
}
file_put_contents($backup_sql_file, $sql_dump);

// ===========================
// Backup Folder Files
// ===========================
$folder_to_backup = realpath(__DIR__ . '/../uploads/');
// Cek apakah folder uploads punya file
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($folder_to_backup),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$hasFiles = false;
foreach ($files as $file) {
    if (!$file->isDir()) {
        $hasFiles = true;
        break;
    }
}

$backup_zip_file = null;
if ($hasFiles) {
    $zip = new ZipArchive();
    $backup_zip_file = $backup_folder . "backup_files_{$date}.zip";

    if ($zip->open($backup_zip_file, ZipArchive::CREATE) === TRUE) {
        // Reset iterator karena sebelumnya sudah di-loop
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder_to_backup),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folder_to_backup) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    }
}

// ===========================
// Download Kedua File
// ===========================
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="backup_bundle_'.$date.'.zip"');

$final_zip = $backup_folder . "backup_bundle_{$date}.zip";
$zip = new ZipArchive();
if ($zip->open($final_zip, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($backup_sql_file, basename($backup_sql_file));
    if ($backup_zip_file && file_exists($backup_zip_file)) {
        $zip->addFile($backup_zip_file, basename($backup_zip_file));
    }
    $zip->close();
}

readfile($final_zip);

// Hapus file setelah kirim
unlink($backup_sql_file);
if ($backup_zip_file && file_exists($backup_zip_file)) {
    unlink($backup_zip_file);
}
unlink($final_zip);

exit;
?>
