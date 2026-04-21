<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

$colorOptions = array_map('trim', explode(',', $product['colors'] ?? 'Default'));
$selectedColor = $_GET['color'] ?? $colorOptions[0];
$selectedColor = in_array($selectedColor, $colorOptions) ? $selectedColor : $colorOptions[0];

// Add to cart from details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }
    
    $sessionId = session_id();
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    if ($product_id > 0) {
        $stmt = $pdo->prepare('SELECT id FROM cart WHERE session_id = ? AND product_id = ?');
        $stmt->execute([session_id(), $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $pdo->prepare('UPDATE cart SET quantity = quantity + ? WHERE id = ?');
            $stmt->execute([$quantity, $existing['id']]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO cart (product_id, quantity, session_id) VALUES (?, ?, ?)');
            $stmt->execute([$product_id, $quantity, session_id()]);
        }

        $_SESSION['cart_message'] = 'Product added to cart!';
        header('Location: cart.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo esc($product['name']); ?> - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="container" style="padding: 3rem 2rem;">
    <a href="index.php" class="btn btn-small" style="margin-bottom: 1rem;"><i class="fas fa-arrow-left"></i> Back to Shop</a>

    <div class="product-detail-grid">
        <div class="product-detail-image">
            <div class="big-image" style="background-image: url('<?php echo esc($product['image']); ?>');"></div>
            <div class="color-choices">
                <?php foreach ($colorOptions as $color): ?>
                    <a href="?id=<?php echo intval($product['id']); ?>&color=<?php echo urlencode($color); ?>" class="color-chip <?php echo $selectedColor === $color ? 'active' : ''; ?>" title="<?php echo esc($color); ?>">
                        <?php echo esc($color); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="product-detail-content">
            <span class="brand"><?php echo esc($product['brand']); ?></span>
            <span class="category"><?php echo esc($product['category']); ?></span>
            <h1><?php echo esc($product['name']); ?></h1>
            <p class="price"><?php echo formatPrice($product['price']); ?></p>
            <p class="stock"><?php echo intval($product['stock']) > 0 ? 'In stock: ' . intval($product['stock']) : 'Out of stock'; ?></p>
            <p class="description"><?php echo esc($product['description']); ?></p>

            <h4>Features</h4>
            <ul>
                <?php foreach (explode(',', $product['features']) as $feature): ?>
                    <li><?php echo esc(trim($feature)); ?></li>
                <?php endforeach; ?>
            </ul>

            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                <form method="post" style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="hidden" name="action" value="add_to_cart" />
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                    <input type="hidden" name="product_id" value="<?php echo intval($product['id']); ?>" />

                    <input type="number" name="quantity" value="1" min="1" max="<?php echo intval($product['stock']); ?>" style="width: 70px; padding: 0.55rem; border-radius: 6px;" />
                    <button type="submit" class="btn btn-primary" <?php echo intval($product['stock']) === 0 ? 'disabled' : ''; ?> >
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            </div>

            <p style="margin-top: 1rem; font-size: 0.9rem; color: #94a3b8;">Selected color: <strong><?php echo esc($selectedColor); ?></strong></p>
        </div>
    </div>

    <hr style="border-top: 1px solid rgba(0, 212, 255, .15); margin: 2rem 0;" />

    <h3>More Smartwatches</h3>
    <div class="products-grid" style="margin-top: 1rem;">
        <?php
        $all = $pdo->query('SELECT id, name, price, brand, image, stock FROM products WHERE id != ' . intval($product['id']) . ' LIMIT 4')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($all as $p):
        ?>
            <a href="product.php?id=<?php echo intval($p['id']); ?>" class="product-card">
                <div class="product-image"><img src="<?php echo esc($p['image']); ?>" alt="<?php echo esc($p['name']); ?>" /></div>
                <div class="product-info">
                    <h4><?php echo esc($p['name']); ?></h4>
                    <p><?php echo formatPrice($p['price']); ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>