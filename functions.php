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
            <?php
            // Security & utility functions

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            function generateCSRFToken() {
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                return $_SESSION['csrf_token'];
            }

            function verifyCSRFToken($token) {
                return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
            }

            function sanitize($input) {
                return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            }

            function esc($text) {
                return sanitize((string)$text);
            }

            function isValidEmail($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            }

            function isValidPhone($phone) {
                return preg_match('/^[\d\s\-\.\(\)\+]+$/', $phone) && strlen(preg_replace('/\D/', '', $phone)) >= 7;
            }

            function formatPrice($price) {
                return '$' . number_format($price, 2);
            }

            function isLoggedIn() {
                return !empty($_SESSION['user_id']);
            }

            function currentUser() {
                if (!isLoggedIn()) return null;
                return [
                    'id' => $_SESSION['user_id'] ?? null,
                    'name' => $_SESSION['user_name'] ?? null,
                    'email' => $_SESSION['user_email'] ?? null,
                    'role' => $_SESSION['user_role'] ?? 'user'
                ];
            }

            function requireLogin($redirect = 'login.php') {
                if (!isLoggedIn()) {
                    header('Location: ' . $redirect);
                    exit;
                }
            }

            function requireAdmin() {
                if (!isLoggedIn() || ($_SESSION['user_role'] ?? '') !== 'admin') {
                    header('HTTP/1.1 403 Forbidden');
                    echo 'Access denied.';
                    exit;
                }
            }

            function isAdmin() {
                return isLoggedIn() && (($_SESSION['user_role'] ?? '') === 'admin');
            }

            function getCartTotal($pdo, $sessionId) {
                $stmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?");
                $stmt->execute([$sessionId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['total'] ?? 0;
            }

            function getCartCount($pdo, $sessionId) {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE session_id = ?");
                $stmt->execute([$sessionId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['count'] ?? 0;
            }
