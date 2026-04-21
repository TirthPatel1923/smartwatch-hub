<?php
require_once __DIR__ . '/config.php';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get products that need restocking (stock <= 5)
    $stmt = $pdo->prepare("SELECT id, name, stock FROM products WHERE stock <= 5");
    $stmt->execute();
    $lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $restockedCount = 0;
    $restockAmount = 10; // Restock to 10 units

    foreach ($lowStockProducts as $product) {
        // Update stock to restock amount
        $updateStmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $updateStmt->execute([$restockAmount, $product['id']]);
        $restockedCount++;

        // Log the restocking action (you could create a restock_log table for this)
        error_log("Restocked product: {$product['name']} (ID: {$product['id']}) from {$product['stock']} to {$restockAmount} units");
    }

    echo "Automatic restocking completed. Restocked {$restockedCount} products.\n";

} catch (PDOException $e) {
    error_log('Restock Error: ' . $e->getMessage());
    echo 'Restock Error: ' . htmlspecialchars($e->getMessage()) . "\n";
}
?>