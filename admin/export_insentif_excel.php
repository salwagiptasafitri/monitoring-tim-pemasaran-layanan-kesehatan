<?php
include '../koneksi.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul laporan
$sheet->setCellValue('A1', 'Laporan Data Insentif');
$sheet->mergeCells('A1:H1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
$sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

// Set tanggal export
$sheet->setCellValue('A2', 'Tanggal Export: ' . date('d-m-Y'));
$sheet->mergeCells('A2:H2');
$sheet->getStyle('A2')->getFont()->setSize(12)->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Set header kolom
$headers = ['No', 'Salesman', 'Layanan', 'Target (Rp)', 'Pendapatan (Rp)', 'Persentase (%)', 'Insentif Penjualan (%)', 'Total Insentif (Rp)'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '4', $header);
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
$sheet->getStyle('A4:H4')->applyFromArray($headerStyle);

// Auto size kolom
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Mulai dari baris 5 untuk data
$rowNumber = 5;
$no = 1;
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

// Loop untuk menambahkan data
while ($row = mysqli_fetch_assoc($query)) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['nama_salesman']);
    $sheet->setCellValue('C' . $rowNumber, $row['layanan']);
    $sheet->setCellValue('D' . $rowNumber, $row['target']);
    $sheet->setCellValue('E' . $rowNumber, $row['pendapatan']);
    $sheet->setCellValue('F' . $rowNumber, $row['persentase'] . ' %');
    $sheet->setCellValue('G' . $rowNumber, $row['insentif_penjualan'] . ' %');
    $sheet->setCellValue('H' . $rowNumber, $row['insentif']);
    
    // Format nilai sebagai currency
    $sheet->getStyle('D' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('E' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('H' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
    
    $total_target += $row['target'];
    $total_pendapatan += $row['pendapatan'];
    $total_insentif += $row['insentif'];
    
    $rowNumber++;
}

// Styling data rows
$dataRange = 'A5:H' . ($rowNumber - 1);
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
for ($i = 5; $i < $rowNumber; $i++) {
    if (($i - 4) % 2 == 0) {
        $sheet->getStyle('A' . $i . ':H' . $i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF9F9F9');
    }
}

// Right alignment untuk kolom nilai
$sheet->getStyle('D5:D' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('E5:E' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('H5:H' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Center alignment untuk kolom No, Persentase, Insentif Penjualan
$sheet->getStyle('A5:A' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F5:F' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('G5:G' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Baris TOTAL
$sheet->setCellValue('C' . $rowNumber, 'TOTAL');
$sheet->setCellValue('D' . $rowNumber, $total_target);
$sheet->setCellValue('E' . $rowNumber, $total_pendapatan);
$sheet->setCellValue('H' . $rowNumber, $total_insentif);

// Styling baris total
$sheet->getStyle('C' . $rowNumber . ':H' . $rowNumber)->getFont()->setBold(true);
$sheet->getStyle('D' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('E' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('H' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('C' . $rowNumber . ':H' . $rowNumber)->applyFromArray([
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
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
]);
$sheet->getStyle('C' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Footer dengan tanggal pembuatan
$footerRow = $rowNumber + 2;
$sheet->setCellValue('A' . $footerRow, 'Laporan dihasilkan pada: ' . date('d-m-Y H:i:s'));
$sheet->mergeCells('A' . $footerRow . ':H' . $footerRow);
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(10)->getColor()->setARGB('FF777777');
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Buat writer dan output file
$filename = "Laporan_Insentif_" . date('Y-m-d') . ".xlsx";
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>