<?php
include '../koneksi.php';

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Query untuk mengambil data user
$query = mysqli_query($koneksi, "
    SELECT nama, email, role, status
    FROM tb_user
    ORDER BY nama ASC
");

// Hitung total user untuk summary
$total_users = mysqli_num_rows($query);

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul laporan
$sheet->setCellValue('A1', 'Data User');
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
$sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

// Set tanggal export
$sheet->setCellValue('A2', 'Tanggal Export: ' . date('d-m-Y'));
$sheet->mergeCells('A2:E2');
$sheet->getStyle('A2')->getFont()->setSize(12)->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Set header kolom
$headers = ['No', 'Nama', 'Email', 'Role', 'Status'];
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
$sheet->getStyle('A4:E4')->applyFromArray($headerStyle);

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);  // No
$sheet->getColumnDimension('B')->setWidth(20); // Nama
$sheet->getColumnDimension('C')->setWidth(25); // Email
$sheet->getColumnDimension('D')->setWidth(15); // Role
$sheet->getColumnDimension('E')->setWidth(10); // Status

// Mulai dari baris 5 untuk data
$rowNumber = 5;
$no = 1;

// Loop untuk menambahkan data
while ($row = mysqli_fetch_assoc($query)) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['nama']);
    $sheet->setCellValue('C' . $rowNumber, $row['email']);
    $sheet->setCellValue('D' . $rowNumber, $row['role']);
    $sheet->setCellValue('E' . $rowNumber, $row['status']);
    
    $rowNumber++;
}

// Styling data rows
$dataRange = 'A5:E' . ($rowNumber - 1);
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
        $sheet->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF9F9F9');
    }
}

// Center alignment untuk kolom No
$sheet->getStyle('A5:A' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Tambahkan summary total user
$summaryRow = $rowNumber;
$sheet->setCellValue('D' . $summaryRow, 'Total User:');
$sheet->setCellValue('E' . $summaryRow, $total_users);
$sheet->getStyle('D' . $summaryRow . ':E' . $summaryRow)->getFont()->setBold(true);
$sheet->getStyle('D' . $summaryRow . ':E' . $summaryRow)->applyFromArray([
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
$sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(10)->getColor()->setARGB('FF777777');
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Buat writer dan output file
$filename = "Data_User_" . date('Y-m-d') . ".xlsx";
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>