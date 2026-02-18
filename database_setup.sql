-- InfinityFree Database Setup
-- 1. Open phpMyAdmin in InfinityFree.
-- 2. Select your database (e.g., if0_38XXXXXX_ezpay).
-- 3. Click "Import" and upload this file, or click "SQL" and paste this content.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    problem VARCHAR(100) NOT NULL,
    security_pin VARCHAR(255) NOT NULL,
    experience_level VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
