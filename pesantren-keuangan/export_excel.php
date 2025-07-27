<?php
require_once 'config.php';
require_once 'koneksi.php';

// Check if PhpSpreadsheet is available
if (!file_exists('vendor/autoload.php')) {
    die('PhpSpreadsheet tidak ditemukan. Silakan install dengan: composer require phpoffice/phpspreadsheet');
}

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Ambil filter dari POST
$filters = [];
if (isset($_POST['tanggal_dari']) && !empty($_POST['tanggal_dari'])) {
    $filters['tanggal_dari'] = $_POST['tanggal_dari'];
}
if (isset($_POST['tanggal_sampai']) && !empty($_POST['tanggal_sampai'])) {
    $filters['tanggal_sampai'] = $_POST['tanggal_sampai'];
}
if (isset($_POST['bulan']) && !empty($_POST['bulan'])) {
    $filters['bulan'] = $_POST['bulan'];
}
if (isset($_POST['tahun']) && !empty($_POST['tahun'])) {
    $filters['tahun'] = $_POST['tahun'];
}
if (isset($_POST['kategori']) && !empty($_POST['kategori'])) {
    $filters['kategori'] = $_POST['kategori'];
}
if (isset($_POST['type']) && !empty($_POST['type'])) {
    $filters['type'] = $_POST['type'];
}

// Ambil data transaksi dengan filter
$transactions = $db->getTransactions($filters);
$total_pemasukan = $db->getTotalPemasukan($filters);
$total_pengeluaran = $db->getTotalPengeluaran($filters);
$saldo = $total_pemasukan - $total_pengeluaran;

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul dokumen
$sheet->setTitle('Laporan Keuangan');

// Header informasi
$sheet->setCellValue('A1', APP_NAME);
$sheet->setCellValue('A2', 'Laporan Keuangan Pesantren');
$sheet->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i:s'));

// Filter info
$filter_info = 'Filter: ';
if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
    $filter_info .= 'Periode ' . date('d/m/Y', strtotime($filters['tanggal_dari'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_sampai']));
} elseif (!empty($filters['bulan']) && !empty($filters['tahun'])) {
    $bulan_nama = date('F', mktime(0, 0, 0, $filters['bulan'], 1));
    $filter_info .= $bulan_nama . ' ' . $filters['tahun'];
} else {
    $filter_info .= 'Semua Data';
}

if (!empty($filters['kategori'])) {
    $filter_info .= ', Kategori: ' . $filters['kategori'];
}
if (!empty($filters['type'])) {
    $filter_info .= ', Tipe: ' . ucfirst($filters['type']);
}

$sheet->setCellValue('A4', $filter_info);

// Ringkasan keuangan
$sheet->setCellValue('A6', 'RINGKASAN KEUANGAN');
$sheet->setCellValue('A7', 'Total Pemasukan:');
$sheet->setCellValue('B7', 'Rp ' . number_format($total_pemasukan, 0, ',', '.'));
$sheet->setCellValue('A8', 'Total Pengeluaran:');
$sheet->setCellValue('B8', 'Rp ' . number_format($total_pengeluaran, 0, ',', '.'));
$sheet->setCellValue('A9', 'Saldo Akhir:');
$sheet->setCellValue('B9', 'Rp ' . number_format($saldo, 0, ',', '.'));

// Header tabel transaksi
$row = 11;
$sheet->setCellValue('A' . $row, 'DAFTAR TRANSAKSI');

$row += 2;
$headers = ['No', 'Tanggal', 'Tipe', 'Kategori', 'Nominal', 'Keterangan'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . $row, $header);
    $col++;
}

// Data transaksi
$row++;
$no = 1;
foreach ($transactions as $trans) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($trans['tanggal'])));
    $sheet->setCellValue('C' . $row, ucfirst($trans['type']));
    $sheet->setCellValue('D' . $row, $trans['kategori']);
    $sheet->setCellValue('E' . $row, ($trans['type'] == 'pemasukan' ? '' : '-') . 'Rp ' . number_format($trans['nominal'], 0, ',', '.'));
    $sheet->setCellValue('F' . $row, $trans['keterangan']);
    $row++;
}

// Styling
// Header utama
$sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A2:F2')->getFont()->setBold(true)->setSize(14);

// Ringkasan keuangan
$sheet->getStyle('A6:B6')->getFont()->setBold(true)->setSize(12);
$sheet->getStyle('A7:A9')->getFont()->setBold(true);
$sheet->getStyle('B7')->getFont()->getColor()->setRGB('27AE60'); // Green for pemasukan
$sheet->getStyle('B8')->getFont()->getColor()->setRGB('E74C3C'); // Red for pengeluaran
$sheet->getStyle('B9')->getFont()->setBold(true);

// Header tabel
$sheet->getStyle('A11:F11')->getFont()->setBold(true)->setSize(12);
$header_row = 13;
$sheet->getStyle('A' . $header_row . ':F' . $header_row)->getFont()->setBold(true);
$sheet->getStyle('A' . $header_row . ':F' . $header_row)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setRGB('667EEA');
$sheet->getStyle('A' . $header_row . ':F' . $header_row)->getFont()->getColor()->setRGB('FFFFFF');

// Border untuk tabel
$last_row = $row - 1;
$sheet->getStyle('A' . $header_row . ':F' . $last_row)->getBorders()->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

// Alignment
$sheet->getStyle('A' . $header_row . ':F' . $header_row)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A' . ($header_row + 1) . ':A' . $last_row)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('E' . ($header_row + 1) . ':E' . $last_row)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Auto width
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Merge cells untuk header
$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:F2');
$sheet->mergeCells('A3:F3');
$sheet->mergeCells('A4:F4');
$sheet->mergeCells('A6:F6');
$sheet->mergeCells('A11:F11');

// Center alignment untuk merged cells
$sheet->getStyle('A1:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A6:F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A11:F11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Buat nama file
$filename = 'Laporan_Keuangan_Pesantren_' . date('Y-m-d_H-i-s');
if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
    $bulan_nama = date('F', mktime(0, 0, 0, $filters['bulan'], 1));
    $filename = 'Laporan_Keuangan_' . $bulan_nama . '_' . $filters['tahun'];
} elseif (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
    $filename = 'Laporan_Keuangan_' . date('Y-m-d', strtotime($filters['tanggal_dari'])) . '_to_' . date('Y-m-d', strtotime($filters['tanggal_sampai']));
}

// Set headers untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
header('Cache-Control: max-age=0');

// Buat writer dan output
$writer = new Xlsx($spreadsheet);

// Clean output buffer
if (ob_get_contents()) {
    ob_end_clean();
}

try {
    $writer->save('php://output');
} catch (Exception $e) {
    die('Error creating Excel file: ' . $e->getMessage());
}

exit;
?>
