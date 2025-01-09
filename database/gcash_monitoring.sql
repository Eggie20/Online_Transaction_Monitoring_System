-- Create database
CREATE DATABASE IF NOT EXISTS gcash_monitoring;
USE gcash_monitoring;

-- Create transaction_categories table
CREATE TABLE IF NOT EXISTS transaction_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create transaction_status table
CREATE TABLE IF NOT EXISTS transaction_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    color_code VARCHAR(7) DEFAULT '#000000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create main transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    transaction_number VARCHAR(50) NOT NULL,
    reference_number VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    category_id INT,
    status_id INT DEFAULT 1,
    notes TEXT,
    sender_phone VARCHAR(20),
    receiver_phone VARCHAR(20),
    transaction_date DATE NOT NULL,
    transaction_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES transaction_categories(id),
    FOREIGN KEY (status_id) REFERENCES transaction_status(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default transaction categories
INSERT INTO transaction_categories (name, description) VALUES 
    ('Send Money', 'GCash to GCash transfer'),
    ('Cash In', 'Adding money to GCash wallet'),
    ('Cash Out', 'Withdrawing money from GCash'),
    ('Pay Bills', 'Bill payment transactions'),
    ('Buy Load', 'Mobile load purchase');

-- Insert default transaction status
INSERT INTO transaction_status (name, color_code) VALUES 
    ('Completed', '#28a745'),
    ('Pending', '#ffc107'),
    ('Failed', '#dc3545'),
    ('Refunded', '#17a2b8');

-- Create indexes for better performance
CREATE INDEX idx_transaction_date ON transactions(transaction_date);
CREATE INDEX idx_transaction_number ON transactions(transaction_number);
CREATE INDEX idx_reference_number ON transactions(reference_number);
CREATE INDEX idx_category ON transactions(category_id);
CREATE INDEX idx_status ON transactions(status_id);

-- Optional: Insert sample transaction data
INSERT INTO transactions (
    name, 
    transaction_number, 
    reference_number,
    amount, 
    category_id,
    status_id,
    notes,
    sender_phone,
    receiver_phone,
    transaction_date,
    transaction_time
) VALUES 
    ('Juan Dela Cruz', 'TXN-001', 'REF-001', 1000.00, 1, 1, 'Send money to family', '09123456789', '09187654321', CURDATE(), CURTIME()),
    ('Maria Santos', 'TXN-002', 'REF-002', 500.00, 2, 1, 'Cash in via bank', '09234567890', NULL, CURDATE(), CURTIME()),
    ('Pedro Penduko', 'TXN-003', 'REF-003', 200.00, 5, 1, 'Load purchase', '09345678901', NULL, CURDATE(), CURTIME()); 