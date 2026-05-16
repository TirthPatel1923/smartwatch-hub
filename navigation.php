<?php
// Navigation bar included in templates
require_once __DIR__ . '/functions.php';
$sessionId = session_id();
$cart_count_nav = getCartCount($pdo, $sessionId);
$loggedIn = isLoggedIn();
$currentUser = currentUser();
$isAdmin = isAdmin();
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
            <?php if ($loggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin.php" role="menuitem">Admin</a>
                <?php endif; ?>
                <a href="logout.php" role="menuitem">Logout</a>
                <span class="nav-user">Hello, <?php echo esc($currentUser['name'] ?? 'Member'); ?></span>
            <?php else: ?>
                <a href="login.php" role="menuitem">Login</a>
                <a href="register.php" role="menuitem">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
