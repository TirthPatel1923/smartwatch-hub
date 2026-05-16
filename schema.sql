-- SmartWatch Hub Database Schema
-- Complete schema for the e-commerce platform
-- Run this file to set up the database manually if needed
-- Note: The application auto-creates tables on first run

-- Create Database
CREATE DATABASE IF NOT EXISTS `smartwatch_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smartwatch_db`;

-- =====================================================
-- PRODUCTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Product unique identifier',
    `name` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Product name',
    `brand` VARCHAR(100) NOT NULL COMMENT 'Manufacturer brand',
    `category` VARCHAR(50) NOT NULL COMMENT 'Product category',
    `price` DECIMAL(10, 2) NOT NULL COMMENT 'Price in currency',
    `description` TEXT COMMENT 'Detailed product description',
    `features` TEXT COMMENT 'Comma-separated features list',
    `image` VARCHAR(255) COMMENT 'Product image URL',
    `colors` VARCHAR(255) COMMENT 'Available colors (comma-separated)',
    `stock` INT DEFAULT 10 COMMENT 'Available quantity in stock',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation timestamp',
    
    KEY `idx_brand` (`brand`),
    KEY `idx_category` (`category`),
    KEY `idx_price` (`price`),
    KEY `idx_stock` (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Smartwatch product catalog';

-- =====================================================
-- USER SUBMISSIONS TABLE (Contact Form)
-- =====================================================
CREATE TABLE IF NOT EXISTS `user_submissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Submission unique identifier',
    `name` VARCHAR(100) NOT NULL COMMENT 'Submitter full name',
    `email` VARCHAR(150) NOT NULL COMMENT 'Contact email address',
    `phone` VARCHAR(30) NOT NULL COMMENT 'Contact phone number',
    `favorite_model` VARCHAR(100) NOT NULL COMMENT 'Preferred smartwatch model',
    `message` TEXT NOT NULL COMMENT 'Enquiry message content',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Submission timestamp',
    
    KEY `idx_email` (`email`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer contact form submissions';

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'User unique identifier',
    `name` VARCHAR(100) NOT NULL COMMENT 'Full name',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email address',
    `password` VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    `role` ENUM('admin','user') NOT NULL DEFAULT 'user' COMMENT 'Role for access control',
    `email_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Has the user verified their email?',
    `verification_code` VARCHAR(100) DEFAULT NULL COMMENT 'Email verification or activation code',
    `verification_expires` DATETIME DEFAULT NULL COMMENT 'Expiration time for verification code',
    `otp_code` VARCHAR(10) DEFAULT NULL COMMENT 'One-time login code',
    `otp_expires` DATETIME DEFAULT NULL COMMENT 'Expiration time for OTP code',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',

    KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registered users and access roles';

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrator', 'admin@example.com', '$2y$10$zkCd0cuKAZWS19rjHtkobOVTEbDtVexHpsklJ6v9XT6S39q8DgeZi', 'admin');

-- =====================================================
-- ORDERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Order unique identifier',
    `customer_name` VARCHAR(150) NOT NULL COMMENT 'Customer full name',
    `customer_email` VARCHAR(150) NOT NULL COMMENT 'Customer email',
    `customer_phone` VARCHAR(30) COMMENT 'Customer phone number',
    `shipping_address` TEXT COMMENT 'Delivery address',
    `billing_address` TEXT COMMENT 'Billing address',
    `total_price` DECIMAL(10, 2) NOT NULL COMMENT 'Order total amount',
    `status` VARCHAR(50) DEFAULT 'pending' COMMENT 'Order status (pending, completed, cancelled)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Order creation timestamp',
    
    KEY `idx_customer_email` (`customer_email`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer orders';

-- =====================================================
-- ORDER ITEMS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Order item unique identifier',
    `order_id` INT NOT NULL COMMENT 'Reference to orders table',
    `product_id` INT NOT NULL COMMENT 'Reference to products table',
    `quantity` INT NOT NULL DEFAULT 1 COMMENT 'Item quantity',
    `price` DECIMAL(10, 2) NOT NULL COMMENT 'Item price at time of order',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Item added timestamp',
    
    KEY `idx_order_id` (`order_id`),
    KEY `idx_product_id` (`product_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) 
        REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual items in orders';

-- =====================================================
-- SHOPPING CART TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Cart item unique identifier',
    `session_id` VARCHAR(255) NOT NULL COMMENT 'PHP session identifier',
    `product_id` INT NOT NULL COMMENT 'Reference to products table',
    `quantity` INT NOT NULL DEFAULT 1 COMMENT 'Quantity in cart',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Item added timestamp',
    
    KEY `idx_session_id` (`session_id`),
    KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Session-based shopping cart';

-- =====================================================
-- SAMPLE DATA (Optional)
-- =====================================================
-- Uncomment to populate sample products

INSERT INTO `products` (`name`, `brand`, `category`, `price`, `description`, `features`, `image`, `colors`, `stock`) VALUES
('Galaxy Watch 6 Classic', 'Samsung', 'Premium', 299.99, 'Premium smartwatch with AMOLED display and 5-day battery life', 'AMOLED display, GPS, Heart rate monitoring, Water resistant 5ATM', 'placeholder-watch.jpg', 'Black, Silver, Gold', 15),
('Apple Watch Series 8', 'Apple', 'Premium', 399.99, 'Latest Apple Watch with advanced health features', 'Always-On Retina display, ECG app, Blood oxygen, Crash detection', 'placeholder-watch.jpg', 'Midnight, Starlight, Silver, Gold', 12),
('Fitbit Sense 2', 'Fitbit', 'Fitness', 199.99, 'Health and fitness tracker with stress management tools', 'Stress monitor, Sleep tracking, Fitness tracking, Swim proof', 'placeholder-watch.jpg', 'Black, White', 20),
('Garmin Epix', 'Garmin', 'Sports', 499.99, 'AMOLED sports smartwatch with multi-band GPS', 'Multi-band GPS, AMOLED display, Music storage, Endurance metrics', 'placeholder-watch.jpg', 'Titanium, Black', 10),
('Huami Amazfit GTR 4', 'Huami', 'Fitness', 179.99, 'Budget-friendly smartwatch with excellent battery life', 'AMOLED display, 24-day battery, SpO2, Sleep tracking', 'placeholder-watch.jpg', 'Black, Grey', 25);

-- =====================================================
-- VIEWS (Optional)
-- =====================================================
-- Customer order summary view
CREATE OR REPLACE VIEW `order_summary` AS
SELECT 
    o.id AS order_id,
    o.customer_name,
    o.customer_email,
    COUNT(oi.id) AS total_items,
    SUM(oi.quantity) AS total_quantity,
    o.total_price,
    o.status,
    o.created_at
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id
ORDER BY o.created_at DESC;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================
-- Already defined above in table creation
-- May add additional indexes as needed:
-- CREATE INDEX idx_cart_session_product ON cart(session_id, product_id);
-- CREATE INDEX idx_orders_status_date ON orders(status, created_at);

-- =====================================================
-- STORED PROCEDURES (Optional)
-- =====================================================
-- Procedure to get cart total for a session
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS `GetCartTotal`(IN p_session_id VARCHAR(255))
BEGIN
    SELECT 
        SUM(p.price * c.quantity) as total,
        COUNT(c.id) as item_count,
        SUM(c.quantity) as total_quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = p_session_id;
END$$
DELIMITER ;

-- =====================================================
-- DATA INTEGRITY NOTES
-- =====================================================
-- 1. Products table uses UNIQUE key on name to prevent duplicates
-- 2. Orders uses CASCADE delete for related order items
-- 3. All timestamps default to CURRENT_TIMESTAMP
-- 4. Prices stored as DECIMAL(10,2) for accuracy
-- 5. Session IDs stored as VARCHAR for flexibility
-- 6. UTF8MB4 charset for full Unicode support

-- =====================================================
-- UPGRADE NOTES
-- =====================================================
-- To add new features:
-- 1. Review constraints in order_items (foreign keys)
-- 2. Add indexes for new queries
-- 3. Create procedures for complex operations
-- 4. Update app config to enable new features

-- =====================================================
-- END OF SCHEMA
-- =====================================================
