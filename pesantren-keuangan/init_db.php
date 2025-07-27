<?php
require_once 'config.php';
require_once 'koneksi.php';

// Inisialisasi database dengan data contoh
try {
    // Cek apakah database sudah ada
    $db = new Database();
    $conn = $db->getConnection();
    
    // Cek apakah sudah ada data
    $stmt = $conn->query("SELECT COUNT(*) as total FROM transactions");
    $result = $stmt->fetch();
    
    if ($result['total'] == 0) {
        // Insert data contoh
        $sample_data = [
            ['pemasukan', 5000000, '2024-07-01', 'Administrasi', 'Dana awal bulan Juli'],
            ['pengeluaran', 750000, '2024-07-02', 'Perlengkapan', 'Pembelian alat tulis kantor'],
            ['pengeluaran', 500000, '2024-07-03', 'Kebersihan', 'Pembelian sabun dan deterjen'],
            ['pemasukan', 2000000, '2024-07-05', 'Pendidikan', 'Donasi untuk perpustakaan'],
            ['pengeluaran', 300000, '2024-07-07', 'Kesehatan', 'Obat-obatan P3K'],
            ['pengeluaran', 1200000, '2024-07-10', 'Keamanan', 'Perbaikan pagar pesantren'],
            ['pemasukan', 1500000, '2024-07-15', 'Organisasi', 'Dana kegiatan santri'],
            ['pengeluaran', 400000, '2024-07-18', 'Tim Media', 'Pembelian kamera untuk dokumentasi'],
            ['pengeluaran', 600000, '2024-07-20', 'Humas', 'Biaya acara tamu pesantren']
        ];
        
        foreach ($sample_data as $data) {
            $db->insertTransaction($data[0], $data[1], $data[2], $data[3], $data[4]);
        }
        
        echo "Database berhasil diinisialisasi dengan data contoh!<br>";
        echo "Total transaksi: " . count($sample_data) . "<br>";
        echo "<a href='index.php'>Kembali ke halaman utama</a>";
    } else {
        echo "Database sudah berisi data.<br>";
        echo "<a href='index.php'>Kembali ke halaman utama</a>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
