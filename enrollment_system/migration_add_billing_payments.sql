-- Migration: Add billing_payments table for installment payments
CREATE TABLE IF NOT EXISTS billing_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    billing_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (billing_id) REFERENCES billing(id) ON DELETE CASCADE
);

-- To run: import this file in your database (phpMyAdmin or CLI)
