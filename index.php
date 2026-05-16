<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$sessionId = session_id();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    
    if ($product_id > 0) {
        // Check if product exists
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        
        if ($stmt->fetch()) {
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$sessionId, $product_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Update quantity
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
                $stmt->execute([$quantity, $existing['id']]);
            } else {
                // Add new item
                $stmt = $pdo->prepare("INSERT INTO cart (product_id, quantity, session_id) VALUES (?, ?, ?)");
                $stmt->execute([$product_id, $quantity, $sessionId]);
            }
            $_SESSION['cart_message'] = 'Product added to cart!';
        }
    }
}

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

// Get total count for pagination
$count_stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$total_products = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_products / $per_page);

// Get paginated products
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cart_count = getCartCount($pdo, $sessionId);
$cart_message = $_SESSION['cart_message'] ?? '';
unset($_SESSION['cart_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Premium Smartwatches - Shop the latest smartwatch collection with exclusive brands and models." />
    <meta name="keywords" content="smartwatch, apple watch, galaxy watch, wearable" />
    <title><?php echo esc(SITE_NAME); ?> - Premium Smartwatches</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-LZN37f6MItjnjim9xk4FzuvO1S1XwF+Yz8LgY3E6RN1HnIkp6E4xIC4qFjm69m2V" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .skip-link { position: absolute; top: -40px; left: 0; background: var(--primary); color: var(--dark); padding: 8px; text-decoration: none; z-index: 100; }
        .skip-link:focus { top: 0; }
    </style>
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<?php include 'navigation.php'; ?>

<div class="hero">
    <div class="hero-content">
        <h1>Premium Smartwatches</h1>
        <p>Experience the future on your wrist. Shop the latest smartwatch collection.</p>
        <a href="#products" class="btn btn-primary">Shop Now</a>
    </div>
</div>

<div class="container" id="main-content" role="main">
    <?php if ($cart_message): ?>
        <div class="alert alert-success" role="alert" aria-live="polite">
            <i class="fas fa-check-circle"></i> <?php echo esc($cart_message); ?>
        </div>
    <?php endif; ?>

    <section id="products" class="products-section" aria-label="Products Catalog">
        <h2>Our Collection</h2>
        <div class="mb-4">
            <label for="searchInput" class="form-label visually-hidden">Search products</label>
            <input id="searchInput" type="search" class="form-control form-control-lg" placeholder="Search by name, brand, or category" aria-label="Search products" />
        </div>
        <div class="products-grid" role="list">
            <?php foreach ($products as $product): ?>
                <article class="product-card" data-search="<?php echo esc(strtolower($product['name'] . ' ' . $product['brand'] . ' ' . $product['category'])); ?>" role="listitem" aria-label="<?php echo esc($product['name']); ?> by <?php echo esc($product['brand']); ?>">
                    <div class="product-image">
                        <a href="product.php?id=<?php echo intval($product['id']); ?>" aria-label="View <?php echo esc($product['name']); ?> details">
                            <img src="<?php echo esc($product['image'] ?? 'placeholder-watch.jpg'); ?>" alt="<?php echo esc($product['name']); ?>" loading="lazy" />
                        </a>
                        <span class="stock-badge <?php echo intval($product['stock']) > 5 ? 'in-stock' : 'low-stock'; ?>" aria-live="polite">
                            <?php echo intval($product['stock']) > 0 ? 'In Stock' : 'Out of Stock'; ?>
                        </span>
                    </div>
                    <div class="product-info">
                        <span class="brand" role="doc-subtitle"><?php echo esc($product['brand']); ?></span>
                        <span class="category"><?php echo esc($product['category']); ?></span>
                        <h3><a href="product.php?id=<?php echo intval($product['id']); ?>" style="color: inherit; text-decoration: none;"><?php echo esc($product['name']); ?></a></h3>
                        <p class="description"><?php echo esc(substr($product['description'], 0, 60) . '...'); ?></p>
                        <div class="features" role="list" aria-label="Features">
                            <?php 
                            $features = explode(',', $product['features'] ?? '');
                            foreach (array_slice($features, 0, 2) as $feature):
                            ?>
                                <span class="feature-tag" role="listitem"><?php echo esc(trim($feature)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="product-footer">
                            <span class="price" aria-label="Price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if (intval($product['stock']) > 0): ?>
                                <form method="post" action="" class="add-to-cart-form" role="group" aria-label="Add to cart">
                                    <input type="hidden" name="action" value="add_to_cart" />
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                                    <input type="hidden" name="product_id" value="<?php echo intval($product['id']); ?>" />
                                    <button type="submit" class="btn btn-small" aria-label="Add <?php echo esc($product['name']); ?> to cart">
                                        <i class="fas fa-shopping-cart" aria-hidden="true"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-small" disabled aria-label="Out of stock">Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav class="pagination" role="navigation" aria-label="Pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline" aria-label="Go to previous page">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>" 
                   class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>"
                   aria-current="<?php echo $i === $page ? 'page' : 'false'; ?>"
                   aria-label="Go to page <?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline" aria-label="Go to next page">Next &raquo;</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </section>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3gJwYpTPi32M30a5d6R08b0BfTT4yl7vHTER1Y1rVPo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6U5dORluXKokN8RyVb4Ydpe1w1P0j9Mbgq6Wv8Q6DkU2J/4x6rD" crossorigin="anonymous"></script>
<script>
$(function() {
    $('.hero a[href="#products"]').on('click', function(e) {
        e.preventDefault();
        document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
    });

    $('#searchInput').on('input', function() {
        var query = $(this).val().toLowerCase();
        $('.product-card').each(function() {
            var text = $(this).data('search') || '';
            $(this).toggle(text.indexOf(query) !== -1);
        });
    });
});
</script>

</body>
</html>
