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
$email = trim($_GET['email'] ?? '');
$code = trim($_GET['code'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token validation failed. Please refresh the page and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');

        if (empty($email) || !isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($code)) {
            $errors[] = 'Please enter the verification code.';
        }

        if (empty($errors)) {
            $user = $userModel->findByEmail($email);
            if (!$user) {
                $errors[] = 'No account found for that email address.';
            } elseif ($user['email_verified']) {
                $success = 'Your email is already verified. You can log in now.';
            } elseif ($user['verification_code'] !== $code) {
                $errors[] = 'The verification code does not match. Please try again.';
            } elseif (new DateTime() > new DateTime($user['verification_expires'])) {
                $errors[] = 'The verification code has expired. Request a new code from the login page.';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_code = NULL, verification_expires = NULL WHERE id = ?");
                $stmt->execute([$user['id']]);
                $success = 'Email verified successfully. You can now log in.';
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
    <title>Verify Email - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-LZN37f6MItjnjim9xk4FzuvO1S1XwF+Yz8LgY3E6RN1HnIkp6E4xIC4qFjm69m2V" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
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
        <h1 class="h3 mb-3 text-center">Verify Your Email</h1>

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

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo esc($email); ?>" required aria-required="true" placeholder="name@example.com" />
            </div>
            <div class="mb-3">
                <label for="code" class="form-label">Verification code</label>
                <input type="text" class="form-control" id="code" name="code" value="<?php echo esc($code); ?>" required aria-required="true" placeholder="123456" maxlength="6" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify Email</button>
        </form>

        <p class="text-center text-muted mt-3 mb-0">Need a new code? Go to <a href="login.php">Login</a> and request a verification email.</p>
    </section>
</div>
</body>
</html>
