<?php
session_start();
include '../koneksi.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

// ================== AMBIL FILTER BULAN ==================
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = date('Y');

// ================== BUAT KONDISI WHERE ==================
$where = "";

if($bulan != "" && is_numeric($bulan)){
    $where = "WHERE MONTH(m.tanggal_kunjungan) = '$bulan'
              AND YEAR(m.tanggal_kunjungan) = '$tahun'";
}

// ================== QUERY DATA ==================
$query = mysqli_query($koneksi, "
    SELECT m.*, u.nama AS nama_salesman
    FROM tb_marketing m
    LEFT JOIN tb_user u ON m.id_user = u.id_user
    $where
    ORDER BY m.tanggal_kunjungan ASC
");

// ================== QUERY TOTAL ==================
$query_total = mysqli_query($koneksi, "
    SELECT SUM(m.nilai) AS total
    FROM tb_marketing m
    $where
");

$row_total = mysqli_fetch_assoc($query_total);
$total_nilai = $row_total['total'] ?? 0;

// ================== TENTUKAN TEKS PERIODE ==================
if($bulan != "" && is_numeric($bulan)){
    $nama_bulan = date("F", mktime(0,0,0,$bulan,10));
    $periode = $nama_bulan . " " . $tahun;
}else{
    $periode = "Semua Periode";
}

// ================== MULAI HTML ==================
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Laporan Marketing</title>
<style>
    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        margin: 30px;
        font-size: 11px;
        color: #333;
        line-height: 1.4;
    }
    .header {
        text-align: center;
        border-bottom: 3px solid #003366; /* Biru tua */
        padding-bottom: 15px;
        margin-bottom: 25px;
        background-color: #dbeafe; /* Biru muda */
        padding: 20px;
        border-radius: 8px;
    }
    .header h2 {
        margin: 0;
        font-size: 18px;
        color: #003366; /* Biru tua */
        font-weight: bold;
    }
    .header p {
        margin: 5px 0;
        font-size: 12px;
        color: #1e3a8a; /* Biru tua lebih gelap */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        vertical-align: middle;
    }
    th {
        background-color: #003366; /* Solid biru tua (ganti dari gradient untuk kompatibilitas Dompdf) */
        color: white;
        text-align: center;
        font-weight: bold;
        font-size: 10px;
    }
    tbody tr:nth-child(even) {
        background-color: #f0f8ff; /* Biru muda sangat pucat */
    }
    tbody tr:hover {
        background-color: #dbeafe; /* Biru muda */
    }
    .text-center {
        text-align: center;
    }
    .text-right {
        text-align: right;
    }
    .summary {
        margin-top: 25px;
        text-align: right;
        font-weight: bold;
        font-size: 14px;
        background-color: #dbeafe; /* Biru muda */
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #003366; /* Biru tua */
        color: #003366; /* Biru tua */
    }
    .footer {
        margin-top: 50px;
        font-size: 9px;
        text-align: center;
        color: #666;
        border-top: 1px solid #003366; /* Biru tua */
        padding-top: 10px;
    }
    .no-data {
        font-style: italic;
        color: #999;
    }
</style>
</head>
<body>

<div class="header">
    <h2>LAPORAN AKTIVITAS MARKETING</h2>
    <p>PT. Biofarma</p>
    <p>Periode: ' . $periode . '</p>
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

if(mysqli_num_rows($query) > 0){
    while ($row = mysqli_fetch_assoc($query)) {
        $html .= '
        <tr>
            <td class="text-center">' . $no++ . '</td>
            <td>' . htmlspecialchars($row['nama_salesman']) . '</td>
            <td>' . htmlspecialchars($row['nama_klien']) . '</td>
            <td class="text-center">' . date('d-m-Y', strtotime($row['tanggal_kunjungan'])) . '</td>
            <td>' . htmlspecialchars($row['layanan_kes']) . '</td>
            <td>' . htmlspecialchars($row['jenis']) . '</td>
            <td class="text-center">' . htmlspecialchars($row['status']) . '</td>
            <td class="text-right">' . number_format($row['nilai'], 0, ',', '.') . '</td>
        </tr>
        ';
    }
} else {
    $html .= '
    <tr>
        <td colspan="8" class="text-center no-data">
            Tidak ada data tersedia
        </td>
    </tr>
    ';
}

$html .= '
</tbody>
</table>

<div class="summary">
    Total Nilai: Rp ' . number_format($total_nilai, 0, ',', '.') . '
</div>

<div class="footer">
    Dicetak pada ' . date('d-m-Y H:i:s') . ' <br>
    Sistem Monitoring Marketing
</div>

</body>
</html>
';

// ================== GENERATE PDF ==================
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Tampilkan di browser
$dompdf->stream("laporan_marketing.pdf", ["Attachment" => false]);