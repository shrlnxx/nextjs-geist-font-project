<?php
require_once 'config.php';
require_once 'koneksi.php';

// Ambil filter dari GET/POST
$filters = [];
if (isset($_GET['tanggal_dari']) && !empty($_GET['tanggal_dari'])) {
    $filters['tanggal_dari'] = $_GET['tanggal_dari'];
}
if (isset($_GET['tanggal_sampai']) && !empty($_GET['tanggal_sampai'])) {
    $filters['tanggal_sampai'] = $_GET['tanggal_sampai'];
}
if (isset($_GET['bulan']) && !empty($_GET['bulan'])) {
    $filters['bulan'] = $_GET['bulan'];
}
if (isset($_GET['tahun']) && !empty($_GET['tahun'])) {
    $filters['tahun'] = $_GET['tahun'];
}
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $filters['kategori'] = $_GET['kategori'];
}
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $filters['type'] = $_GET['type'];
}

// Ambil data transaksi dengan filter
$transactions = $db->getTransactions($filters);
$total_pemasukan = $db->getTotalPemasukan($filters);
$total_pengeluaran = $db->getTotalPengeluaran($filters);
$saldo = $total_pemasukan - $total_pengeluaran;

// Group transaksi berdasarkan kategori
$grouped_transactions = [];
foreach ($transactions as $trans) {
    $grouped_transactions[$trans['kategori']][] = $trans;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Laporan Keuangan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mosque me-2"></i><?php echo APP_NAME; ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-plus-circle me-1"></i>Input Transaksi
                </a>
                <a class="nav-link active" href="laporan.php">
                    <i class="fas fa-chart-line me-1"></i>Laporan Keuangan
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Filter Section -->
        <div class="filter-section fade-in">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>Filter Laporan
            </h5>
            
            <form id="filterForm" method="GET" action="laporan.php">
                <div class="row">
                    <!-- Filter Tanggal Range -->
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                        <input type="date" class="form-control auto-filter" id="tanggal_dari" name="tanggal_dari" 
                               value="<?php echo isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : ''; ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                        <input type="date" class="form-control auto-filter" id="tanggal_sampai" name="tanggal_sampai" 
                               value="<?php echo isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : ''; ?>">
                    </div>
                    
                    <!-- Filter Bulan/Tahun -->
                    <div class="col-md-2 mb-3">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select class="form-select auto-filter" id="bulan" name="bulan">
                            <option value="">Semua</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == $i) ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select class="form-select auto-filter" id="tahun" name="tahun">
                            <option value="">Semua</option>
                            <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                            <option value="<?php echo $year; ?>" <?php echo (isset($_GET['tahun']) && $_GET['tahun'] == $year) ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select auto-filter" id="kategori" name="kategori">
                            <option value="">Semua</option>
                            <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?php echo $kat; ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $kat) ? 'selected' : ''; ?>>
                                <?php echo $kat; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="type" class="form-label">Tipe Transaksi</label>
                        <select class="form-select auto-filter" id="type" name="type">
                            <option value="">Semua</option>
                            <option value="pemasukan" <?php echo (isset($_GET['type']) && $_GET['type'] == 'pemasukan') ? 'selected' : ''; ?>>
                                Pemasukan
                            </option>
                            <option value="pengeluaran" <?php echo (isset($_GET['type']) && $_GET['type'] == 'pengeluaran') ? 'selected' : ''; ?>>
                                Pengeluaran
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-md-9 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <button type="button" class="btn btn-secondary me-2" onclick="resetFilter()">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                        <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-1"></i>Export Excel
                        </button>
                        <button type="button" class="btn btn-info" onclick="printReport()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="summary-card summary-pemasukan fade-in">
                    <h3 class="currency">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h3>
                    <p><i class="fas fa-arrow-up me-1"></i>Total Pemasukan</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="summary-card summary-pengeluaran fade-in">
                    <h3 class="currency">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
                    <p><i class="fas fa-arrow-down me-1"></i>Total Pengeluaran</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="summary-card summary-saldo fade-in">
                    <h3 class="currency">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                    <p><i class="fas fa-wallet me-1"></i>Saldo Akhir</p>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Daftar Transaksi
                    <span class="badge bg-light text-dark ms-2"><?php echo count($transactions); ?> transaksi</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($transactions)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-4x mb-3"></i>
                    <h5>Tidak ada transaksi ditemukan</h5>
                    <p>Silakan ubah filter atau <a href="index.php">tambah transaksi baru</a></p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table id="transactionTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($transactions as $trans): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($trans['tanggal'])); ?></td>
                                <td>
                                    <span class="badge-<?php echo $trans['type']; ?>">
                                        <i class="fas fa-arrow-<?php echo $trans['type'] == 'pemasukan' ? 'up' : 'down'; ?> me-1"></i>
                                        <?php echo ucfirst($trans['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $trans['kategori']; ?></span>
                                </td>
                                <td class="currency text-<?php echo $trans['type']; ?>">
                                    <?php echo ($trans['type'] == 'pemasukan' ? '+' : '-') . ' Rp ' . number_format($trans['nominal'], 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php if ($trans['keterangan']): ?>
                                        <span data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($trans['keterangan']); ?>">
                                            <?php echo strlen($trans['keterangan']) > 30 ? substr($trans['keterangan'], 0, 30) . '...' : $trans['keterangan']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($trans['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Grouped by Category -->
        <?php if (!empty($grouped_transactions)): ?>
        <div class="card mt-4 fade-in">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Ringkasan per Kategori
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($grouped_transactions as $kategori => $trans_list): ?>
                    <?php
                    $kategori_pemasukan = 0;
                    $kategori_pengeluaran = 0;
                    foreach ($trans_list as $t) {
                        if ($t['type'] == 'pemasukan') {
                            $kategori_pemasukan += $t['nominal'];
                        } else {
                            $kategori_pengeluaran += $t['nominal'];
                        }
                    }
                    $kategori_saldo = $kategori_pemasukan - $kategori_pengeluaran;
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-title text-primary"><?php echo $kategori; ?></h6>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <small class="text-success">Masuk: Rp <?php echo number_format($kategori_pemasukan, 0, ',', '.'); ?></small>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <small class="text-danger">Keluar: Rp <?php echo number_format($kategori_pengeluaran, 0, ',', '.'); ?></small>
                                    </div>
                                    <div class="col-12">
                                        <strong class="<?php echo $kategori_saldo >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            Saldo: Rp <?php echo number_format($kategori_saldo, 0, ',', '.'); ?>
                                        </strong>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark"><?php echo count($trans_list); ?> transaksi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 no-print">
        <div class="container">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - 
                Sistem Keuangan Internal Pesantren
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
