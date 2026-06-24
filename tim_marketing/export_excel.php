<?php
session_start();
include '../koneksi.php';

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
$query_total = mysqli_query($koneksi, "SELECT SUM(nilai) AS total FROM tb_marketing WHERE id_user = '$id_user'");
if ($row_total = mysqli_fetch_assoc($query_total)) {
    $total_nilai = $row_total['total'];
}

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul laporan
$sheet->setCellValue('A1', 'Laporan Aktivitas Marketing');
$sheet->mergeCells('A1:H1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
$sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

// Set header kolom
$headers = ['No', 'Salesman', 'Klien', 'Tanggal', 'Layanan', 'Produk', 'Status', 'Nilai (Rp)'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '2', $header);
    $col++;
}

// Styling header
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'],
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF34495E'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
];
$sheet->getStyle('A2:H2')->applyFromArray($headerStyle);

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);  // No
$sheet->getColumnDimension('B')->setWidth(15); // Salesman
$sheet->getColumnDimension('C')->setWidth(20); // Klien
$sheet->getColumnDimension('D')->setWidth(12); // Tanggal
$sheet->getColumnDimension('E')->setWidth(15); // Layanan
$sheet->getColumnDimension('F')->setWidth(15); // Produk
$sheet->getColumnDimension('G')->setWidth(10); // Status
$sheet->getColumnDimension('H')->setWidth(15); // Nilai

// Mulai dari baris 3 untuk data
$rowNumber = 3;
$no = 1;

// Loop untuk menambahkan data
while ($row = mysqli_fetch_assoc($query)) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['nama_salesman']);
    $sheet->setCellValue('C' . $rowNumber, $row['nama_klien']);
    $sheet->setCellValue('D' . $rowNumber, date('d-m-Y', strtotime($row['tanggal_kunjungan'])));
    $sheet->setCellValue('E' . $rowNumber, $row['layanan_kes']);
    $sheet->setCellValue('F' . $rowNumber, $row['jenis']);
    $sheet->setCellValue('G' . $rowNumber, $row['status']);
    $sheet->setCellValue('H' . $rowNumber, $row['nilai']);
    
    // Format nilai sebagai currency
    $sheet->getStyle('H' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
    
    $rowNumber++;
}

// Styling data rows
$dataRange = 'A3:H' . ($rowNumber - 1);
$sheet->getStyle($dataRange)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

// Zebra stripes untuk data rows
for ($i = 3; $i < $rowNumber; $i++) {
    if (($i - 2) % 2 == 0) {
        $sheet->getStyle('A' . $i . ':H' . $i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF9F9F9');
    }
}

// Right alignment untuk kolom No dan Nilai
$sheet->getStyle('A3:A' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('H3:H' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Tambahkan summary total nilai
$summaryRow = $rowNumber;
$sheet->setCellValue('G' . $summaryRow, 'Total Nilai:');
$sheet->setCellValue('H' . $summaryRow, $total_nilai);
$sheet->getStyle('G' . $summaryRow . ':H' . $summaryRow)->getFont()->setBold(true);
$sheet->getStyle('H' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('G' . $summaryRow . ':H' . $summaryRow)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFE9ECEF'],
    ],
]);

// Footer dengan tanggal pembuatan
$footerRow = $summaryRow + 2;
$sheet->setCellValue('A' . $footerRow, 'Laporan dihasilkan pada: ' . date('d-m-Y H:i:s'));
$sheet->mergeCells('A' . $footerRow . ':H' . $footerRow);
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(10)->getColor()->setARGB('FF777777');
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Buat writer dan output file
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="laporan_marketing.xlsx"');
$writer->save('php://output');
exit;
?>