<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$order_id = $_SESSION['order_id'] ?? null;
$order_total = $_SESSION['order_total'] ?? 0;

if (!$order_id) {
    header("Location: index.php");
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Get order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Clear session
unset($_SESSION['order_id']);
unset($_SESSION['order_total']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmation - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="container">
    <div style="text-align: center; margin: 3rem 0;">
        <i class="fas fa-check-circle" style="font-size: 5rem; color: var(--success); margin-bottom: 1rem; display: block;"></i>
        <h2 style="color: var(--success); font-size: 2rem; margin-bottom: 0.5rem;">Order Placed Successfully!</h2>
        <p style="font-size: 1.1rem; color: #cbd5e1; margin-bottom: 2rem;">Thank you for your purchase</p>
    </div>

    <div class="cart-container">
        <div>
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Order Details</h3>
            
            <div style="background: rgba(30, 41, 59, 0.4); border: 2px solid rgba(0, 212, 255, 0.1); border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
                <div class="cart-summary-item">
                    <span>Order ID:</span>
                    <span style="font-weight: bold; color: var(--primary);">#<?php echo intval($order['id']); ?></span>
                </div>
                <div class="cart-summary-item">
                    <span>Date:</span>
                    <span><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="cart-summary-item">
                    <span>Status:</span>
                    <span style="background: rgba(16, 185, 129, 0.2); color: #a7f3d0; padding: 0.25rem 0.75rem; border-radius: 4px;">Completed</span>
                </div>
            </div>

            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Ordered Items</h3>
            <div style="background: rgba(30, 41, 59, 0.4); border: 2px solid rgba(0, 212, 255, 0.1); border-radius: 12px; padding: 1.5rem;">
                <?php foreach ($order_items as $item): ?>
                    <div class="cart-summary-item" style="padding: 1rem 0; border-bottom: 1px solid rgba(0, 212, 255, 0.1);">
                        <div>
                            <p style="margin: 0; margin-bottom: 0.25rem;" ><?php echo esc($item['product_name']); ?></p>
                            <p style="margin: 0; color: #cbd5e1; font-size: 0.9rem;">Quantity: <?php echo intval($item['quantity']); ?></p>
                        </div>
                        <span style="font-weight: bold;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3 style="margin-top: 2rem; margin-bottom: 1.5rem; color: var(--primary);">Shipping Information</h3>
            <div style="background: rgba(30, 41, 59, 0.4); border: 2px solid rgba(0, 212, 255, 0.1); border-radius: 12px; padding: 2rem;">
                <p><strong>Name:</strong> <?php echo esc($order['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo esc($order['customer_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo esc($order['customer_phone']); ?></p>
                <p><strong>Address:</strong><br><?php echo nl2br(esc($order['address'])); ?></p>
            </div>
        </div>

        <div class="cart-summary">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Payment Summary</h3>
            
            <div class="cart-summary-item">
                <span>Subtotal:</span>
                <span><?php echo formatPrice($order['total_price'] * 0.818); ?></span>
            </div>
            <div class="cart-summary-item">
                <span>Shipping:</span>
                <span>$10.00</span>
            </div>
            <div class="cart-summary-item">
                <span>Tax:</span>
                <span><?php echo formatPrice($order['total_price'] - ($order['total_price'] * 0.818) - 10); ?></span>
            </div>

            <div class="cart-summary-divider"></div>

            <div class="cart-total">
                <span>Total Paid:</span>
                <span><?php echo formatPrice($order['total_price']); ?></span>
            </div>

            <div style="background: rgba(16, 185, 129, 0.1); border: 2px solid rgba(16, 185, 129, 0.2); border-radius: 8px; padding: 1rem; margin-top: 1rem; text-align: center; color: #a7f3d0;">
                <i class="fas fa-shield-alt"></i><br>
                Payment Secured
            </div>

            <a href="index.php" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1.5rem;">
                <i class="fas fa-shopping-cart"></i> Continue Shopping
            </a>
        </div>
    </div>

    <div style="text-align: center; margin-top: 3rem;">
        <p style="color: #cbd5e1;">A confirmation email has been sent to <strong><?php echo esc($order['customer_email']); ?></strong></p>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
