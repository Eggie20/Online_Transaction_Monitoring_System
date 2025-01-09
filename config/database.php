<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gcash_monitoring');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);
    
    // Create transactions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        transaction_number VARCHAR(50) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        transaction_date DATE NOT NULL,
        transaction_time TIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create additional tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS transaction_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS transaction_status (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        color_code VARCHAR(7) DEFAULT '#000000',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Modify transactions table to include more fields
    $pdo->exec("ALTER TABLE transactions 
        ADD COLUMN IF NOT EXISTS category_id INT,
        ADD COLUMN IF NOT EXISTS status_id INT DEFAULT 1,
        ADD COLUMN IF NOT EXISTS reference_number VARCHAR(100),
        ADD COLUMN IF NOT EXISTS notes TEXT,
        ADD COLUMN IF NOT EXISTS sender_phone VARCHAR(20),
        ADD COLUMN IF NOT EXISTS receiver_phone VARCHAR(20),
        ADD FOREIGN KEY (category_id) REFERENCES transaction_categories(id),
        ADD FOREIGN KEY (status_id) REFERENCES transaction_status(id)
    ");

    // Insert default categories
    $pdo->exec("INSERT IGNORE INTO transaction_categories (name, description) VALUES 
        ('Send Money', 'GCash to GCash transfer'),
        ('Cash In', 'Adding money to GCash wallet'),
        ('Cash Out', 'Withdrawing money from GCash'),
        ('Pay Bills', 'Bill payment transactions'),
        ('Buy Load', 'Mobile load purchase')
    ");

    // Insert default status
    $pdo->exec("INSERT IGNORE INTO transaction_status (name, color_code) VALUES 
        ('Completed', '#28a745'),
        ('Pending', '#ffc107'),
        ('Failed', '#dc3545'),
        ('Refunded', '#17a2b8')
    ");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
