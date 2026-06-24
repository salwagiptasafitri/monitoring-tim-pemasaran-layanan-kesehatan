<?php
include '../koneksi.php';

require '../vendor/autoload.php';

use Dompdf\Dompdf;

// Query untuk mengambil data user
$query = mysqli_query($koneksi, "
    SELECT nama, email, role, status
    FROM tb_user
    ORDER BY nama ASC
");

// Hitung total user untuk summary (opsional)
$total_users = mysqli_num_rows($query);

// Mulai membangun HTML dengan styling yang lebih profesional
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User</title>
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
        h3 {
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
        <h3>Data User</h3>
        <div class="subtitle">Daftar Lengkap Pengguna Sistem</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
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
                <td>' . htmlspecialchars($row['nama']) . '</td>
                <td>' . htmlspecialchars($row['email']) . '</td>
                <td>' . htmlspecialchars($row['role']) . '</td>
                <td>' . htmlspecialchars($row['status']) . '</td>
            </tr>
    ';
}

$html .= '
        </tbody>
    </table>
    <div class="summary">
        Total User: ' . $total_users . '
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

// Stream PDF sebagai attachment (download)
$dompdf->stream("Data_User.pdf", ["Attachment" => true]);
?>