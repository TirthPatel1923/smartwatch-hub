<?php
// Security & utility functions

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Sanitize for HTML display
function esc($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone
function isValidPhone($phone) {
    return preg_match('/^[\d\s\-\.\(\)\+]+$/', $phone) && strlen($phone) >= 10;
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Get cart total
function getCartTotal($pdo, $sessionId) {
    $stmt = $pdo->prepare("
        SELECT SUM(p.price * c.quantity) as total 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.session_id = ?
    ");
    $stmt->execute([$sessionId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

// Get cart count
function getCartCount($pdo, $sessionId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?? 0;
}
