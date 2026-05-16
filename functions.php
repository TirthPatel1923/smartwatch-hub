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

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user',
    ];
}

function isAdmin(): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin(string $redirect = 'login.php'): void {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($redirect));
        exit;
    }
}

function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['PHP_SELF']));
        exit;
    }
}

// Sanitize for HTML display
function esc($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function generateVerificationCode(int $length = 6): string {
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= random_int(0, 9);
    }
    return $code;
}

function sendEmail(string $to, string $subject, string $body): bool {
    // Use SMTP via PHPMailer when MAIL_HOST is configured and composer autoload exists
    $mailHost = env('MAIL_HOST', '');
    if ($mailHost && file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $mailHost;
                $mail->SMTPAuth = (bool) env('MAIL_SMTP_AUTH', true);
                $mail->Username = env('MAIL_USERNAME', '');
                $mail->Password = env('MAIL_PASSWORD', '');
                $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
                $mail->Port = (int) env('MAIL_PORT', 587);
                if ((bool) env('MAIL_DEBUG', false)) {
                    $mail->SMTPDebug = 2;
                }
                $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->send();
                return true;
            } catch (Throwable $e) {
                error_log('PHPMailer error: ' . $e->getMessage());
                // fall through to mail() fallback
            }
        } else {
            error_log('PHPMailer class not found after autoload.');
        }
    }

    // Fallback to PHP mail()
    $headers = [];
    $headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    return mail($to, $subject, $body, implode("\r\n", $headers));
}

function sendVerificationEmail(string $email, string $name, string $code): bool {
    $subject = 'Verify your SmartWatch Hub account';
    $url = SITE_URL . 'verify.php?email=' . urlencode($email) . '&code=' . urlencode($code);
    $body = '<h2>Verify your email</h2>' .
        '<p>Hi ' . esc($name) . ',</p>' .
        '<p>Use the code below to verify your account:</p>' .
        '<p style="font-size: 1.5rem; font-weight: bold;">' . esc($code) . '</p>' .
        '<p>Or click this link:<br><a href="' . esc($url) . '">' . esc($url) . '</a></p>' .
        '<p>If you did not register, please ignore this message.</p>';
    return sendEmail($email, $subject, $body);
}

function sendLoginOTPEmail(string $email, string $name, string $code): bool {
    $subject = 'Your SmartWatch Hub login code';
    $body = '<h2>Your login code</h2>' .
        '<p>Hi ' . esc($name) . ',</p>' .
        '<p>Enter this code to sign in:</p>' .
        '<p style="font-size: 1.5rem; font-weight: bold;">' . esc($code) . '</p>' .
        '<p>This code expires in 15 minutes.</p>';
    return sendEmail($email, $subject, $body);
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
