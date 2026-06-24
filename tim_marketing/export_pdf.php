<?php
session_start();
include '../koneksi.php';

require '../vendor/autoload.php';

use Dompdf\Dompdf;

// Ambil ID user dari session
$id_user = $_SESSION['user_id'];

// Query untuk mengambil data marketing berdasarkan user
$query = mysqli_query($koneksi, "
    SELECT m.*, u.nama AS nama_salesman
    FROM tb_marketing m
    LEFT JOIN tb_user u ON m.id_user = u.id_user
    WHERE m.id_user = '$id_user'
    ORDER BY m.id_marketing DESC
");

// Hitung total nilai untuk summary
$total_nilai = 0;
$query_copy = mysqli_query($koneksi, "
    SELECT SUM(nilai) AS total FROM tb_marketing WHERE id_user = '$id_user'
");
if ($row_total = mysqli_fetch_assoc($query_copy)) {
    $total_nilai = $row_total['total'];
}

// Mulai membangun HTML dengan styling yang lebih profesional
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Marketing</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 30px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
        }
        .company-info {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }
        h2 {
            color: #2c3e50;
            font-size: 28px;
            margin: 0;
            font-weight: bold;
        }
        .subtitle {
            font-size: 16px;
            color: #777;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #34495e;
            color: #fff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .summary {
            margin-top: 30px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            PT. Biofarma<br>
            Jl. Pasteur No. 28, Bandung 40161, Indonesia<br>
            Tel: (022) 2033755 | Email: info@biofarma.co.id
        </div>
        <h2>Laporan Aktivitas Marketing</h2>
        <div class="subtitle">Periode: ' . date('F Y') . '</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Salesman</th>
                <th>Klien</th>
                <th>Tanggal</th>
                <th>Layanan</th>
                <th>Produk</th>
                <th>Status</th>
                <th>Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
';

$no = 1;

// Loop untuk menambahkan data ke tabel
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '
            <tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td>' . htmlspecialchars($row['nama_salesman']) . '</td>
                <td>' . htmlspecialchars($row['nama_klien']) . '</td>
                <td>' . date('d-m-Y', strtotime($row['tanggal_kunjungan'])) . '</td>
                <td>' . htmlspecialchars($row['layanan_kes']) . '</td>
                <td>' . htmlspecialchars($row['jenis']) . '</td>
                <td>' . htmlspecialchars($row['status']) . '</td>
                <td style="text-align: right;">' . number_format($row['nilai'], 0, ',', '.') . '</td>
            </tr>
    ';
}

$html .= '
        </tbody>
    </table>
    <div class="summary">
        Total Nilai Aktivitas: Rp ' . number_format($total_nilai, 0, ',', '.') . '
    </div>
    <div class="footer">
        Laporan ini dihasilkan secara otomatis pada ' . date('d-m-Y H:i:s') . ' | Halaman 1 dari 1<br>
        Dokumen Rahasia - Hanya untuk Internal Perusahaan
    </div>
</body>
</html>
';

// Inisialisasi Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Stream PDF ke browser
$dompdf->stream("laporan_marketing.pdf", ["Attachment" => false]);
?>