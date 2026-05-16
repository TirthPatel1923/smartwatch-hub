<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/User.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$userModel = new User($pdo);
$errors = [];
$success = '';
$name = '';
$email = '';
$redirect = isset($_GET['redirect']) ? basename($_GET['redirect']) : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed. Please refresh the page and try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Please enter your full name.';
        }
        if (empty($email) || !isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            if ($userModel->findByEmail($email)) {
                $errors[] = 'An account with that email already exists.';
            } else {
                if ($userModel->create($name, $email, $password, 'user')) {
                    $userId = $pdo->lastInsertId();
                    $code = generateVerificationCode();
                    $expires = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
                    $stmt = $pdo->prepare("UPDATE users SET verification_code = ?, verification_expires = ? WHERE id = ?");
                    $stmt->execute([$code, $expires, $userId]);

                    if (sendVerificationEmail($email, $name, $code)) {
                        $success = 'Registration successful. A verification code has been sent to your email address.';
                    } else {
                        $success = 'Registration successful. Please verify your email address using the code: ' . esc($code);
                    }
                } else {
                    $errors[] = 'Unable to register your account at this time. Please try again later.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-LZN37f6MItjnjim9xk4FzuvO1S1XwF+Yz8LgY3E6RN1HnIkp6E4xIC4qFjm69m2V" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3gJwYpTPi32M30a5d6R08b0BfTT4yl7vHTER1Y1rVPo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6U5dORluXKokN8RyVb4Ydpe1w1P0j9Mbgq6Wv8Q6DkU2J/4x6rD" crossorigin="anonymous"></script>
    <style>
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .auth-card { max-width: 480px; width: 100%; background: rgba(15, 23, 42, 0.92); border: 1px solid rgba(0, 212, 255, 0.15); border-radius: 1rem; padding: 2rem; color: #e2e8f0; }
        .auth-card a { color: var(--primary); }
    </style>
</head>
<body>
<?php include 'navigation.php'; ?>
<div class="auth-page">
    <section class="auth-card shadow-sm">
        <h1 class="h3 mb-3 text-center">Create Account</h1>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <?php echo esc($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo esc($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo esc($name); ?>" required aria-required="true" placeholder="John Doe" minlength="2" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required aria-required="true" placeholder="name@example.com" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required aria-required="true" placeholder="Create a password" minlength="8" />
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required aria-required="true" placeholder="Repeat password" minlength="8" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="text-center text-muted mt-3 mb-0">Already have an account? <a href="login.php">Login here</a></p>
    </section>
</div>
<script>
    $(function () {
        $('#registerForm').on('submit', function (e) {
            var password = $('#password').val().trim();
            var confirmPassword = $('#confirm_password').val().trim();
            if (password.length < 8 || password !== confirmPassword) {
                e.preventDefault();
                var message = 'Please use a password with at least 8 characters and make sure the confirmation matches.';
                alert(message);
            }
        });
    });
</script>
</body>
</html>
