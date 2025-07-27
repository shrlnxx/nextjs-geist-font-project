<?php
require_once 'config.php';

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            if (DB_TYPE === 'mysql') {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            } else {
                // SQLite
                $dsn = "sqlite:" . SQLITE_PATH;
                $this->connection = new PDO($dsn);
                
                // Buat tabel jika belum ada (untuk SQLite)
                $this->createSQLiteTables();
            }
            
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
    
    private function createSQLiteTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT CHECK(type IN ('pemasukan', 'pengeluaran')) NOT NULL,
            nominal DECIMAL(15,2) NOT NULL,
            tanggal DATE NOT NULL,
            kategori TEXT CHECK(kategori IN ('Perlengkapan', 'Administrasi', 'Pendidikan', 'Keamanan', 'Kesehatan', 'Kebersihan', 'Tim Media', 'Organisasi', 'Humas')) NOT NULL,
            keterangan TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->connection->exec($sql);
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Fungsi untuk insert transaksi baru
    public function insertTransaction($type, $nominal, $tanggal, $kategori, $keterangan) {
        try {
            $sql = "INSERT INTO transactions (type, nominal, tanggal, kategori, keterangan) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$type, $nominal, $tanggal, $kategori, $keterangan]);
        } catch (PDOException $e) {
            throw new Exception("Error inserting transaction: " . $e->getMessage());
        }
    }
    
    // Fungsi untuk mengambil semua transaksi dengan filter
    public function getTransactions($filters = []) {
        try {
            $sql = "SELECT * FROM transactions WHERE 1=1";
            $params = [];
            
            // Filter berdasarkan tanggal
            if (!empty($filters['tanggal_dari'])) {
                $sql .= " AND tanggal >= ?";
                $params[] = $filters['tanggal_dari'];
            }
            
            if (!empty($filters['tanggal_sampai'])) {
                $sql .= " AND tanggal <= ?";
                $params[] = $filters['tanggal_sampai'];
            }
            
            // Filter berdasarkan bulan dan tahun
            if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
                $sql .= " AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
                $params[] = $filters['bulan'];
                $params[] = $filters['tahun'];
            }
            
            // Filter berdasarkan kategori
            if (!empty($filters['kategori'])) {
                $sql .= " AND kategori = ?";
                $params[] = $filters['kategori'];
            }
            
            // Filter berdasarkan tipe
            if (!empty($filters['type'])) {
                $sql .= " AND type = ?";
                $params[] = $filters['type'];
            }
            
            $sql .= " ORDER BY tanggal DESC, created_at DESC";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error fetching transactions: " . $e->getMessage());
        }
    }
    
    // Fungsi untuk menghitung total pemasukan
    public function getTotalPemasukan($filters = []) {
        try {
            $sql = "SELECT COALESCE(SUM(nominal), 0) as total FROM transactions WHERE type = 'pemasukan'";
            $params = [];
            
            if (!empty($filters['tanggal_dari'])) {
                $sql .= " AND tanggal >= ?";
                $params[] = $filters['tanggal_dari'];
            }
            
            if (!empty($filters['tanggal_sampai'])) {
                $sql .= " AND tanggal <= ?";
                $params[] = $filters['tanggal_sampai'];
            }
            
            if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
                $sql .= " AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
                $params[] = $filters['bulan'];
                $params[] = $filters['tahun'];
            }
            
            if (!empty($filters['kategori'])) {
                $sql .= " AND kategori = ?";
                $params[] = $filters['kategori'];
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Error calculating total pemasukan: " . $e->getMessage());
        }
    }
    
    // Fungsi untuk menghitung total pengeluaran
    public function getTotalPengeluaran($filters = []) {
        try {
            $sql = "SELECT COALESCE(SUM(nominal), 0) as total FROM transactions WHERE type = 'pengeluaran'";
            $params = [];
            
            if (!empty($filters['tanggal_dari'])) {
                $sql .= " AND tanggal >= ?";
                $params[] = $filters['tanggal_dari'];
            }
            
            if (!empty($filters['tanggal_sampai'])) {
                $sql .= " AND tanggal <= ?";
                $params[] = $filters['tanggal_sampai'];
            }
            
            if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
                $sql .= " AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
                $params[] = $filters['bulan'];
                $params[] = $filters['tahun'];
            }
            
            if (!empty($filters['kategori'])) {
                $sql .= " AND kategori = ?";
                $params[] = $filters['kategori'];
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Error calculating total pengeluaran: " . $e->getMessage());
        }
    }
    
    // Fungsi untuk menghitung saldo
    public function getSaldo($filters = []) {
        $pemasukan = $this->getTotalPemasukan($filters);
        $pengeluaran = $this->getTotalPengeluaran($filters);
        return $pemasukan - $pengeluaran;
    }
}

// Inisialisasi database
$db = new Database();
?>
