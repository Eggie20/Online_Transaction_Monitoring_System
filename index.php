<?php
require_once 'config/database.php';
require_once 'functions/transactions.php';
require_once 'functions/export.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['export'])) {
        exportToExcel($pdo);
    } else {
        $name = $_POST['name'] ?? '';
        $transactionNumber = $_POST['transaction_number'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $date = $_POST['date'] ?? date('Y-m-d');
        $time = $_POST['time'] ?? date('H:i');
        $transactionMethod = $_POST['transaction_method'] ?? '';
        
        addTransaction($pdo, $name, $transactionNumber, $amount, $date, $time, $transactionMethod);
    }
}

$transactions = getAllTransactions($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Transaction Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between w-100">
                <!-- Title and Logos Container -->
                <div class="d-flex align-items-center">
                    <div class="navbar-brand-container d-flex align-items-center">
                        <!-- Logo at the left -->
                        <div class="brand-logo me-3">
                            <div class="navbar-logo-fallback">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <span class="navbar-brand">GCash & Maya Transaction Monitoring</span>
                    </div>
                    
                    <!-- Payment Method Text Logos -->
                    <div class="payment-logos ms-3">
                        <div class="payment-logo-text gcash">GCash</div>
                        <div class="payment-logo-text maya">Maya</div>
                    </div>
                </div>

                <!-- Digital Clock -->
                <div class="datetime-display">
                    <div id="digital-clock"></div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Transaction</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Transaction Method</label>
                                <select name="transaction_method" class="form-control" required>
                                    <option value="">Select Transaction Method</option>
                                    <option value="gcash">GCash</option>
                                    <option value="maya">Maya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Transaction Number</label>
                                <input type="text" name="transaction_number" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time</label>
                                <input type="time" name="time" class="form-control" value="<?= date('H:i') ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Transaction</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Transaction History</h5>
                            <div class="d-flex align-items-center gap-3">
                                <!-- Search Box -->
                                <div class="search-container">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search transactions...">
                                </div>
                                
                                <!-- Filter Dropdown -->
                                <select id="filterSelect" class="form-select">
                                    <option value="">Filter by...</option>
                                    <option value="name">Name</option>
                                    <option value="method">Transaction Method</option>
                                    <option value="amount">Amount</option>
                                    <option value="date">Date</option>
                                </select>
                                
                                <!-- Export Button -->
                                <form method="POST" class="mb-0">
                                    <input type="hidden" name="export" value="1">
                                    <button type="submit" class="btn btn-success">Export to Excel</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table" id="transactionTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Transaction Method</th>
                                        <th>Transaction #</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                    $perPage = 10;
                                    $start = ($page - 1) * $perPage;
                                    
                                    // Get paginated transactions
                                    $transactions = getPaginatedTransactions($pdo, $start, $perPage);
                                    $totalTransactions = getTotalTransactions($pdo);
                                    
                                    foreach($transactions as $transaction): 
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($transaction['name']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($transaction['transaction_method'])) ?></td>
                                        <td><?= htmlspecialchars($transaction['transaction_number']) ?></td>
                                        <td><?= formatAmount($transaction['amount']) ?></td>
                                        <td><?= date('M d, Y', strtotime($transaction['transaction_date'])) ?></td>
                                        <td><?= date('h:i A', strtotime($transaction['transaction_time'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="pagination-info">
                                Showing <?= $start + 1 ?> to <?= min($start + $perPage, $totalTransactions) ?> of <?= $totalTransactions ?> entries
                            </div>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary me-2">Previous</a>
                                <?php endif; ?>
                                
                                <?php if ($start + $perPage < $totalTransactions): ?>
                                    <a href="?page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary">Next</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="digital-clock"></div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
