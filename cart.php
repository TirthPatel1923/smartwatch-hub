<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$sessionId = session_id();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_quantity') {
        $cart_id = intval($_POST['cart_id'] ?? 0);
        $quantity = max(1, intval($_POST['quantity'] ?? 1));
        
        if ($cart_id > 0) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
            $stmt->execute([$quantity, $cart_id, $sessionId]);
        }
    } elseif ($action === 'remove_item') {
        $cart_id = intval($_POST['cart_id'] ?? 0);
        if ($cart_id > 0) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
            $stmt->execute([$cart_id, $sessionId]);
        }
    } elseif ($action === 'clear_cart') {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$sessionId]);
    }

    header("Location: cart.php");
    exit;
}

// Get cart items with product details
$stmt = $pdo->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.brand, p.price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$sessionId]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_total = getCartTotal($pdo, $sessionId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-LZN37f6MItjnjim9xk4FzuvO1S1XwF+Yz8LgY3E6RN1HnIkp6E4xIC4qFjm69m2V" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="container">
    <h2>Shopping Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 3rem;">
            <i class="fas fa-shopping-cart" style="font-size: 4rem; color: rgba(0, 212, 255, 0.3); margin-bottom: 1rem; display: block;"></i>
            <p style="font-size: 1.2rem; color: #cbd5e1; margin-bottom: 2rem;">Your cart is empty</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="<?php echo esc($item['image'] ?? 'placeholder-watch.jpg'); ?>" alt="<?php echo esc($item['name']); ?>" />
                        </div>
                        <div class="cart-item-details" style="flex: 1;">
                            <h3><?php echo esc($item['name']); ?></h3>
                            <p><strong><?php echo esc($item['brand']); ?></strong></p>
                            <p>Price: <?php echo formatPrice($item['price']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <form method="post" class="cart-item-form" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                                <input type="hidden" name="action" value="update_quantity" />
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                                <input type="hidden" name="cart_id" value="<?php echo intval($item['id']); ?>" />
                                <input type="number" name="quantity" value="<?php echo intval($item['quantity']); ?>" min="1" max="10" style="width: 60px; padding: 0.5rem;" />
                                <button type="submit" class="btn btn-small" style="padding: 0.5rem 1rem;">Update</button>
                            </form>
                            <form method="post" style="margin-top: 0.5rem;">
                                <input type="hidden" name="action" value="remove_item" />
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                                <input type="hidden" name="cart_id" value="<?php echo intval($item['id']); ?>" />
                                <button type="submit" class="btn btn-small" style="background: var(--danger); color: white; padding: 0.5rem 1rem;">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                            <p style="margin-top: 1rem; font-size: 1.2rem; font-weight: bold; color: var(--primary);">
                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Order Summary</h3>
                
                <div class="cart-summary-item">
                    <span>Subtotal:</span>
                    <span><?php echo formatPrice($cart_total); ?></span>
                </div>
                <div class="cart-summary-item">
                    <span>Shipping:</span>
                    <span>$10.00</span>
                </div>
                <div class="cart-summary-item">
                    <span>Tax (10%):</span>
                    <span><?php echo formatPrice(($cart_total + 10) * 0.1); ?></span>
                </div>

                <div class="cart-summary-divider"></div>

                <div class="cart-total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($cart_total + 10 + (($cart_total + 10) * 0.1)); ?></span>
                </div>

                <a href="checkout.php" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem; margin-bottom: 1rem;">
                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                </a>

                <form method="post" style="margin-top: 1rem;">
                    <input type="hidden" name="action" value="clear_cart" />
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                    <button type="submit" class="btn btn-small" style="width: 100%; background: var(--danger); color: white; justify-content: center;">
                        Clear Cart
                    </button>
                </form>

                <a href="index.php" class="btn btn-small" style="width: 100%; justify-content: center; margin-top: 0.5rem; border: 2px solid var(--primary);">
                    Continue Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
