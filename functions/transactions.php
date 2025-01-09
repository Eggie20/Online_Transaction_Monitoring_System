<?php
function addTransaction($pdo, $name, $transactionNumber, $amount, $date, $time, $transactionMethod) {
    $sql = "INSERT INTO transactions (name, transaction_number, amount, transaction_date, transaction_time, transaction_method) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $transactionNumber, $amount, $date, $time, $transactionMethod]);
}

function getAllTransactions($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM transactions ORDER BY transaction_date DESC, transaction_time DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function formatAmount($amount) {
    return '₱' . number_format($amount, 2);
}

// Add this function for transaction time
function generateTransactionTime() {
    date_default_timezone_set('Asia/Manila');
    $transaction_time = date('Y-m-d H:i:s');
    
    return array(
        'timestamp' => $transaction_time,
        'formatted' => date('F j, Y g:i:s A', strtotime($transaction_time))
    );
}

// Update your transaction processing function
function processNewTransaction($data) {
    $time_data = generateTransactionTime();
    
    // Add the transaction time to your data
    $data['transaction_time'] = $time_data['timestamp'];
    
    // Display the formatted time with animation
    echo '<div class="transaction-time">' . $time_data['formatted'] . '</div>';
    
    // Rest of your transaction processing code...
}

/**
 * Get paginated transactions
 */
function getPaginatedTransactions($pdo, $start, $perPage) {
    $stmt = $pdo->prepare("
        SELECT * FROM transactions 
        ORDER BY transaction_date DESC, transaction_time DESC 
        LIMIT :start, :perPage
    ");
    
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get total number of transactions
 */
function getTotalTransactions($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM transactions");
    return $stmt->fetchColumn();
}
?>
