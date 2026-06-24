<?php
include '../koneksi.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

// Inisialisasi variabel total
$total_target = 0;
$total_pendapatan = 0;
$total_insentif = 0;

// Query untuk mengambil data insentif
$query = mysqli_query($koneksi, "
    SELECT 
        i.*,
        u.nama AS nama_salesman
    FROM tb_insentif i
    LEFT JOIN tb_user u ON i.salesman = u.id_user
    ORDER BY i.layanan DESC
");

// Hitung total dari query (untuk memastikan akurasi)
while ($row = mysqli_fetch_assoc($query)) {
    $total_target += $row['target'];
    $total_pendapatan += $row['pendapatan'];
    $total_insentif += $row['insentif'];
}

// Reset pointer query untuk loop HTML
mysqli_data_seek($query, 0);

// Mulai membangun HTML dengan styling yang lebih profesional
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Insentif</title>
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
        .total-row {
            background-color: #e9ecef !important;
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
        <h2>Laporan Data Insentif</h2>
        <div class="subtitle">Ringkasan Insentif Penjualan Salesman</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Salesman</th>
                <th>Layanan</th>
                <th>Target (Rp)</th>
                <th>Pendapatan (Rp)</th>
                <th>Persentase (%)</th>
                <th>Insentif Penjualan (%)</th>
                <th>Total Insentif (Rp)</th>
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
                <td>' . htmlspecialchars($row['layanan']) . '</td>
                <td style="text-align: right;">Rp ' . number_format($row['target'], 0, ',', '.') . '</td>
                <td style="text-align: right;">Rp ' . number_format($row['pendapatan'], 0, ',', '.') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($row['persentase']) . ' %</td>
                <td style="text-align: center;">' . htmlspecialchars($row['insentif_penjualan']) . ' %</td>
                <td style="text-align: right;">Rp ' . number_format($row['insentif'], 0, ',', '.') . '</td>
            </tr>
    ';
}

$html .= '
            <tr class="total-row">
                <td colspan="3" style="text-align: center; font-weight: bold;">TOTAL</td>
                <td style="text-align: right; font-weight: bold;">Rp ' . number_format($total_target, 0, ',', '.') . '</td>
                <td style="text-align: right; font-weight: bold;">Rp ' . number_format($total_pendapatan, 0, ',', '.') . '</td>
                <td style="text-align: center; font-weight: bold;">-</td>
                <td style="text-align: center; font-weight: bold;">-</td>
                <td style="text-align: right; font-weight: bold;">Rp ' . number_format($total_insentif, 0, ',', '.') . '</td>
            </tr>
        </tbody>
    </table>
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

// Stream PDF ke browser (tidak sebagai attachment, sesuai kode asli)
$dompdf->stream("Laporan_Insentif.pdf", ["Attachment" => false]);
?>