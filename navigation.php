<?php
// Navigation bar included in templates
require_once __DIR__ . '/functions.php';
$sessionId = session_id();
$cart_count_nav = getCartCount($pdo, $sessionId);
?>
<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="navbar-container">
        <a href="index.php" class="navbar-brand" aria-label="Return to home">
            <i class="fas fa-watch" aria-hidden="true"></i> <?php echo esc(SITE_NAME); ?>
        </a>
        <div class="navbar-menu" role="menubar">
            <a href="index.php" role="menuitem">Shop</a>
            <a href="contact.php" role="menuitem">Contact</a>
            <a href="cart.php" class="cart-link" role="menuitem" aria-label="Shopping cart<?php echo $cart_count_nav > 0 ? ', ' . intval($cart_count_nav) . ' items' : ''; ?>">
                <i class="fas fa-shopping-cart" aria-hidden="true"></i> Cart
                <?php if ($cart_count_nav > 0): ?>
                    <span class="cart-badge" aria-label="Number of items in cart"><?php echo intval($cart_count_nav); ?></span>
                <?php endif; ?>
            </a>
            <a href="admin.php" role="menuitem">Admin</a>
        </div>
    </div>
</nav>
