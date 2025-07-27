<?php
require_once 'config.php';
require_once 'koneksi.php';

$message = '';
$message_type = '';

// Proses form submission
if ($_POST) {
    try {
        $type = $_POST['type'];
        $nominal = str_replace(['.', ','], '', $_POST['nominal']); // Remove formatting
        $tanggal = $_POST['tanggal'];
        $kategori = $_POST['kategori'];
        $keterangan = $_POST['keterangan'];
        
        // Validasi input
        if (empty($type) || empty($nominal) || empty($tanggal) || empty($kategori)) {
            throw new Exception("Semua field wajib diisi kecuali keterangan!");
        }
        
        if (!is_numeric($nominal) || $nominal <= 0) {
            throw new Exception("Nominal harus berupa angka positif!");
        }
        
        if (!in_array($kategori, $kategori_list)) {
            throw new Exception("Kategori tidak valid!");
        }
        
        // Insert ke database
        if ($db->insertTransaction($type, $nominal, $tanggal, $kategori, $keterangan)) {
            $message = "Transaksi berhasil disimpan!";
            $message_type = "success";
        } else {
            throw new Exception("Gagal menyimpan transaksi!");
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Input Transaksi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-plus-circle me-1"></i>Input Transaksi
                </a>
                <a class="nav-link" href="laporan.php">
                    <i class="fas fa-chart-line me-1"></i>Laporan Keuangan
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Alert Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Form Input Transaksi -->
                <div class="card fade-in">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Input Transaksi Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="transactionForm" method="POST" action="index.php" onsubmit="return validateTransactionForm()">
                            <div class="row">
                                <!-- Tipe Transaksi -->
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">
                                        <i class="fas fa-exchange-alt me-1"></i>Tipe Transaksi
                                    </label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Pilih Tipe Transaksi</option>
                                        <option value="pemasukan" <?php echo (isset($_POST['type']) && $_POST['type'] == 'pemasukan') ? 'selected' : ''; ?>>
                                            Pemasukan
                                        </option>
                                        <option value="pengeluaran" <?php echo (isset($_POST['type']) && $_POST['type'] == 'pengeluaran') ? 'selected' : ''; ?>>
                                            Pengeluaran
                                        </option>
                                    </select>
                                </div>

                                <!-- Nominal -->
                                <div class="col-md-6 mb-3">
                                    <label for="nominal" class="form-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>Nominal (Rp)
                                    </label>
                                    <input type="text" class="form-control" id="nominal" name="nominal" 
                                           placeholder="Masukkan nominal" required
                                           value="<?php echo isset($_POST['nominal']) ? $_POST['nominal'] : ''; ?>">
                                    <div class="form-text">Masukkan angka tanpa titik atau koma</div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tanggal -->
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Tanggal
                                    </label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required
                                           value="<?php echo isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d'); ?>">
                                </div>

                                <!-- Kategori -->
                                <div class="col-md-6 mb-3">
                                    <label for="kategori" class="form-label">
                                        <i class="fas fa-tags me-1"></i>Kategori
                                    </label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($kategori_list as $kat): ?>
                                        <option value="<?php echo $kat; ?>" 
                                                <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == $kat) ? 'selected' : ''; ?>>
                                            <?php echo $kat; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>Keterangan
                                </label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                          placeholder="Masukkan keterangan transaksi (opsional)"><?php echo isset($_POST['keterangan']) ? $_POST['keterangan'] : ''; ?></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan Transaksi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mt-4">
                    <?php
                    $total_pemasukan = $db->getTotalPemasukan();
                    $total_pengeluaran = $db->getTotalPengeluaran();
                    $saldo = $total_pemasukan - $total_pengeluaran;
                    ?>
                    
                    <div class="col-md-4">
                        <div class="summary-card summary-pemasukan fade-in">
                            <h3 class="currency"><?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h3>
                            <p><i class="fas fa-arrow-up me-1"></i>Total Pemasukan</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="summary-card summary-pengeluaran fade-in">
                            <h3 class="currency"><?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
                            <p><i class="fas fa-arrow-down me-1"></i>Total Pengeluaran</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="summary-card summary-saldo fade-in">
                            <h3 class="currency"><?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                            <p><i class="fas fa-wallet me-1"></i>Saldo Akhir</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card mt-4 fade-in">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>5 Transaksi Terakhir
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $recent_transactions = $db->getTransactions();
                        $recent_transactions = array_slice($recent_transactions, 0, 5);
                        ?>
                        
                        <?php if (empty($recent_transactions)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada transaksi</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Kategori</th>
                                        <th>Nominal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_transactions as $trans): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($trans['tanggal'])); ?></td>
                                        <td>
                                            <span class="badge-<?php echo $trans['type']; ?>">
                                                <?php echo ucfirst($trans['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $trans['kategori']; ?></td>
                                        <td class="currency text-<?php echo $trans['type']; ?>">
                                            <?php echo ($trans['type'] == 'pemasukan' ? '+' : '-') . ' Rp ' . number_format($trans['nominal'], 0, ',', '.'); ?>
                                        </td>
                                        <td><?php echo $trans['keterangan'] ? substr($trans['keterangan'], 0, 50) . '...' : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="laporan.php" class="btn btn-info">
                                <i class="fas fa-eye me-1"></i>Lihat Semua Transaksi
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - 
                Sistem Keuangan Internal Pesantren
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
