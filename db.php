<?php
require_once __DIR__ . '/config.php';

try {
    // Create PDO with no default db to ensure database exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    // User submissions table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `user_submissions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `phone` VARCHAR(30) NOT NULL,
            `favorite_model` VARCHAR(100) NOT NULL,
            `message` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Products table for smartwatch catalog
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(150) NOT NULL,
            `brand` VARCHAR(100) NOT NULL,
            `category` VARCHAR(50) NOT NULL,
            `price` DECIMAL(10, 2) NOT NULL,
            `description` TEXT,
            `features` TEXT,
            `image` VARCHAR(255),
            `colors` VARCHAR(255),
            `stock` INT DEFAULT 10,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Users table for authentication and role-based access
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
            `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
            `verification_code` VARCHAR(100) DEFAULT NULL,
            `verification_expires` DATETIME DEFAULT NULL,
            `otp_code` VARCHAR(10) DEFAULT NULL,
            `otp_expires` DATETIME DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Seed default administrator account if it does not exist
    $adminEmail = 'admin@example.com';
    $adminPasswordHash = password_hash('Admin@123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, email_verified) VALUES (?, ?, ?, 'admin', 1)");
        $stmt->execute(['Administrator', $adminEmail, $adminPasswordHash]);
    }

    // Add missing user verification columns if table already exists
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0");
    } catch (Exception $e) {
        // Column might already exist
    }
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN verification_code VARCHAR(100) DEFAULT NULL");
    } catch (Exception $e) {
        // Column might already exist
    }
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN verification_expires DATETIME DEFAULT NULL");
    } catch (Exception $e) {
        // Column might already exist
    }
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN otp_code VARCHAR(10) DEFAULT NULL");
    } catch (Exception $e) {
        // Column might already exist
    }
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN otp_expires DATETIME DEFAULT NULL");
    } catch (Exception $e) {
        // Column might already exist
    }

    // Add unique key on name if not exists
    try {
        $pdo->exec("ALTER TABLE products ADD UNIQUE KEY unique_name (name)");
    } catch (Exception $e) {
        // Key might already exist
    }

    // Add category column if not exists
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category VARCHAR(50) NOT NULL DEFAULT 'General'");
    } catch (Exception $e) {
        // Column might already exist
    }

    // Add colors column if not exists
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN colors VARCHAR(255)");
    } catch (Exception $e) {
        // Column might already exist
    }

    // Cart table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `cart` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL,
            `quantity` INT DEFAULT 1,
            `session_id` VARCHAR(255),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Orders table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `customer_name` VARCHAR(100) NOT NULL,
            `customer_email` VARCHAR(150) NOT NULL,
            `customer_phone` VARCHAR(30),
            `address` TEXT,
            `total_price` DECIMAL(12, 2) NOT NULL,
            `status` ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Order items table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `order_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `order_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `product_name` VARCHAR(150),
            `quantity` INT,
            `price` DECIMAL(10, 2),
            FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Insert sample products (will skip if already exist due to unique key)
    $sampleProducts = [
        ['Apple Watch Ultra', 'Apple', 'Luxury', 799.99, 'Premium sports smartwatch with titanium body and extreme durability', 'Always-on display, Fitness+, ECG, Blood oxygen', 'https://images.unsplash.com/photo-1661956600686-a77f1dfb0db2?auto=format&fit=crop&w=1200&q=80', 'Silver,Space Black,Gold', 15],
        ['Samsung Galaxy Watch 6', 'Samsung', 'Fitness', 399.99, 'Advanced health & fitness tracker with sleek interface', 'AMOLED display, SpO2, Sleep tracking, 5+ days battery', 'https://images.unsplash.com/photo-1613750629688-186d2bc308a9?auto=format&fit=crop&w=1200&q=80', 'Black,Gray,Pink Gold', 20],
        ['Garmin Epix Gen 2', 'Garmin', 'Sports', 599.99, 'Premium outdoor smartwatch built for adventure', 'AMOLED display, Multi-GNSS, Training metrics, 11 days battery', 'https://images.unsplash.com/photo-1615882624230-b41cdab97f33?auto=format&fit=crop&w=1200&q=80', 'Black,Titanium,Sand', 12],
        ['Fitbit Sense 2', 'Fitbit', 'Fitness', 299.99, 'Health-focused wearable with smart notifications', 'EDA sensor, Stress management, 6-day battery', 'https://images.unsplash.com/photo-1616401783188-20bf6a34d54f?auto=format&fit=crop&w=1200&q=80', 'Black,Platinum,Graphite', 25],
        ['Huawei Watch 4', 'Huawei', 'Everyday', 349.99, 'Sleek AMOLED smartwatch for everyday use', 'Health monitoring, Sleep tracking, 2-week battery', 'https://images.unsplash.com/photo-1603791440384-56cd371ee9a7?auto=format&fit=crop&w=1200&q=80', 'Midnight,Beige,Steel', 18],
        ['Amazfit GTS 4', 'Amazfit', 'Budget', 179.99, 'Budget-friendly AMOLED watch with sports tracking', '150+ sports modes, 14-day battery, SpO2 tracking', 'https://images.unsplash.com/photo-1615652047274-597b814e8232?auto=format&fit=crop&w=1200&q=80', 'Black,Blue,Green', 30],
        ['Wear OS by Google', 'Fossil', 'Everyday', 249.99, 'Google Wear OS powered smartwatch for daily tasks', 'Google Assistant, Google Maps integration, 24hr battery', 'https://images.unsplash.com/photo-1580127841973-efc3f8254301?auto=format&fit=crop&w=1200&q=80', 'Brown,Black,Silver', 16],
        ['OnePlus Watch 2', 'OnePlus', 'Performance', 429.99, 'Performance smartwatch with fluid OS', 'Snapdragon 4100+ processor, 500 apps, 100+ sport modes', 'https://images.unsplash.com/photo-1668890157164-4f7a538d5b57?auto=format&fit=crop&w=1200&q=80', 'Black,Steel,Gold', 14],
        ['Apple Watch Series 9', 'Apple', 'Luxury', 399.99, 'Latest Apple Watch with advanced health features', 'Double Tap gesture, Precision Finding, Crash Detection', 'https://images.unsplash.com/photo-1551816230-ef5deaed4a26?auto=format&fit=crop&w=1200&q=80', 'Midnight,Starlight,Product Red', 0],
        ['Samsung Galaxy Watch 5', 'Samsung', 'Fitness', 279.99, 'Compact fitness tracker with bioelectrical impedance analysis', 'Body composition analysis, Advanced sleep coaching, 40+ exercise modes', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1200&q=80', 'Graphite,Silver,Sapphire', 22],
        ['Garmin Forerunner 265', 'Garmin', 'Sports', 449.99, 'GPS running watch with advanced training metrics', 'Wrist-based heart rate, PacePro technology, 13+ hours GPS battery', 'https://images.unsplash.com/photo-1544117519-31a4b719223d?auto=format&fit=crop&w=1200&q=80', 'Black,White,Blue', 8],
        ['Fitbit Versa 4', 'Fitbit', 'Fitness', 229.99, 'Advanced fitness and health tracker', 'Active Zone Minutes, Cardio Fitness, 6+ day battery', 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1200&q=80', 'Black,Lunar White,Pink Clay', 0],
        ['Huawei Watch GT 3', 'Huawei', 'Everyday', 199.99, 'Long-lasting battery smartwatch for daily health tracking', '14-day battery, 100+ sports modes, SpO2 monitoring', 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?auto=format&fit=crop&w=1200&q=80', 'Black,Brown,Green', 17],
        ['Amazfit Bip 3 Pro', 'Amazfit', 'Budget', 79.99, 'Affordable fitness tracker with AMOLED display', '14-day battery, 60+ sports modes, Blood oxygen monitoring', 'https://images.unsplash.com/photo-1542496658-e33a6dcca2e6?auto=format&fit=crop&w=1200&q=80', 'Black,Blue,Pink', 35],
        ['TicWatch Pro 3', 'Mobvoi', 'Performance', 299.99, 'Dual-display smartwatch with Snapdragon Wear 4100', 'TicMotion, 3-day battery, Google services integration', 'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=1200&q=80', 'Gunmetal,Flame Red', 11],
        ['Withings ScanWatch', 'Withings', 'Health', 279.99, 'Hybrid smartwatch focused on health monitoring', 'ECG, SpO2, Temperature sensor, 30-day battery', 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?auto=format&fit=crop&w=1200&q=80', 'White,Black', 9],
        ['Coros Pace 3', 'Coros', 'Sports', 249.99, 'Lightweight GPS running watch with long battery life', '7-day battery, Training status, Recovery time, 200+ sports modes', 'https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?auto=format&fit=crop&w=1200&q=80', 'Black,White,Blue', 13],
        ['Polar Vantage V3', 'Polar', 'Sports', 499.99, 'Advanced sports watch with AI-powered training guidance', 'Training Load Pro, Nightly Recharge, FuelWise, 7-day battery', 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1200&q=80', 'Black,White,Orange', 7],
        ['Suunto 9 Peak', 'Suunto', 'Sports', 399.99, 'Rugged outdoor GPS watch for extreme conditions', 'Titanium bezel, Sapphire glass, 25-day battery, Deep depth gauge', 'https://images.unsplash.com/photo-1544117519-31a4b719223d?auto=format&fit=crop&w=1200&q=80', 'Titanium,Black', 6],
        ['Mobvoi TicWatch E3', 'Mobvoi', 'Budget', 149.99, 'Affordable Wear OS smartwatch with Google Assistant', 'Google services, 2-day battery, Swim-proof, Health monitoring', 'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=1200&q=80', 'Black,Silver,Gold', 21],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, brand, category, price, description, features, image, colors, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($sampleProducts as $product) {
        $stmt->execute($product);
    }

    // Update categories for existing products that have default 'General'
    $updates = [
        'Apple' => 'Luxury',
        'Samsung' => 'Fitness',
        'Garmin' => 'Sports',
        'Fitbit' => 'Fitness',
        'Huawei' => 'Everyday',
        'Amazfit' => 'Budget',
        'Fossil' => 'Everyday',
        'OnePlus' => 'Performance',
    ];
    foreach ($updates as $brand => $cat) {
        $pdo->prepare("UPDATE products SET category = ? WHERE brand = ? AND category = 'General'")->execute([$cat, $brand]);
    }

} catch (PDOException $e) {
    die('DB Error: ' . htmlspecialchars($e->getMessage()));
}
