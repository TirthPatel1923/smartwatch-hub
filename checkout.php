<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$sessionId = session_id();
$errors = [];
$success = '';

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
");
$stmt->execute([$sessionId]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

// Calculate totals
$subtotal = getCartTotal($pdo, $sessionId);
$shipping = 10.00;
$tax = ($subtotal + $shipping) * 0.1;
$total = $subtotal + $shipping + $tax;

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed';
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $card_name = trim($_POST['card_name'] ?? '');
    $card_number = trim($_POST['card_number'] ?? '');
    $card_expiry = trim($_POST['card_expiry'] ?? '');
    $card_cvv = trim($_POST['card_cvv'] ?? '');

    // Validation
    if (empty($name)) { $errors[] = 'Name is required'; }
    if (!isValidEmail($email)) { $errors[] = 'Valid email is required'; }
    if (!isValidPhone($phone)) { $errors[] = 'Valid phone is required'; }
    if (empty($address)) { $errors[] = 'Address is required'; }
    if (empty($card_name)) { $errors[] = 'Cardholder name is required'; }
    if (empty($card_number) || strlen(preg_replace('/\D/', '', $card_number)) < 13) { $errors[] = 'Valid card number is required'; }
    if (empty($card_expiry) || !preg_match('/\d{2}\/\d{2}/', $card_expiry)) { $errors[] = 'Valid expiry date is required (MM/YY)'; }
    if (empty($card_cvv) || !preg_match('/^\d{3,4}$/', $card_cvv)) { $errors[] = 'Valid CVV is required'; }

    if (empty($errors)) {
        try {
            // Begin transaction
            $pdo->beginTransaction();

            // Create order
            $stmt = $pdo->prepare("
                INSERT INTO orders (customer_name, customer_email, customer_phone, address, total_price, status)
                VALUES (?, ?, ?, ?, ?, 'completed')
            ");
            $stmt->execute([$name, $email, $phone, $address, $total]);
            $order_id = $pdo->lastInsertId();

            // Add order items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($cart_items as $item) {
                $stmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);

                // Update product stock
                $update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stock->execute([$item['quantity'], $item['product_id']]);
            }

            // Clear cart
            $clear = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
            $clear->execute([$sessionId]);

            $pdo->commit();

            $_SESSION['order_id'] = $order_id;
            $_SESSION['order_total'] = $total;
            header("Location: order-confirmation.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Payment processing failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-LZN37f6MItjnjim9xk4FzuvO1S1XwF+Yz8LgY3E6RN1HnIkp6E4xIC4qFjm69m2V" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="container">
    <h2>Checkout</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <ul style="margin: 0;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo esc($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="cart-container">
        <div style="flex: 2;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Billing Information</h3>
            <form method="post">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required />
                    </div>
                    <div>
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="phone">Phone *</label>
                        <input type="tel" id="phone" name="phone" required />
                    </div>
                </div>

                <label for="address">Shipping Address *</label>
                <textarea id="address" name="address" rows="3" required></textarea>

                <h3 style="margin-top: 2rem; margin-bottom: 1.5rem; color: var(--primary);">Payment Information</h3>

                <label for="card_name">Cardholder Name *</label>
                <input type="text" id="card_name" name="card_name" required />

                <label for="card_number">Card Number *</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required />

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="card_expiry">Expiry Date *</label>
                        <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" required />
                    </div>
                    <div>
                        <label for="card_cvv">CVV *</label>
                        <input type="text" id="card_cvv" name="card_cvv" placeholder="123" required />
                    </div>
                </div>

                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Complete Purchase
                    </button>
                    <a href="cart.php" class="btn btn-small" style="border: 2px solid var(--primary);">Back to Cart</a>
                </div>
            </form>
        </div>

        <div class="cart-summary">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Order Summary</h3>
            
            <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem;">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-summary-item" style="align-items: center;">
                        <span><?php echo esc($item['name']); ?> x<?php echo intval($item['quantity']); ?></span>
                        <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary-divider"></div>

            <div class="cart-summary-item">
                <span>Subtotal:</span>
                <span><?php echo formatPrice($subtotal); ?></span>
            </div>
            <div class="cart-summary-item">
                <span>Shipping:</span>
                <span><?php echo formatPrice($shipping); ?></span>
            </div>
            <div class="cart-summary-item">
                <span>Tax (10%):</span>
                <span><?php echo formatPrice($tax); ?></span>
            </div>

            <div class="cart-summary-divider"></div>

            <div class="cart-total">
                <span>Total:</span>
                <span><?php echo formatPrice($total); ?></span>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
