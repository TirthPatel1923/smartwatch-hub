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
$info = '';
$email = '';
$redirect = isset($_GET['redirect']) ? basename($_GET['redirect']) : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed. Please refresh the page and try again.';
    } else {
        $action = $_POST['action'] ?? 'login_password';
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }

        $user = $userModel->findByEmail($email);

        if ($action === 'send_otp') {
            if (empty($errors)) {
                if (!$user) {
                    $info = 'If the email is registered, a login code will be sent shortly.';
                } elseif (!$user['email_verified']) {
                    $code = generateVerificationCode();
                    $expires = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
                    $stmt = $pdo->prepare("UPDATE users SET verification_code = ?, verification_expires = ? WHERE id = ?");
                    $stmt->execute([$code, $expires, $user['id']]);
                    sendVerificationEmail($email, $user['name'], $code);
                    $info = 'Your account is not verified yet. A verification code has been sent to your email.';
                } else {
                    $otp = generateVerificationCode();
                    $expires = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
                    $stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE id = ?");
                    $stmt->execute([$otp, $expires, $user['id']]);
                    if (sendLoginOTPEmail($email, $user['name'], $otp)) {
                        $info = 'A one-time login code has been sent to your email address.';
                    } else {
                        $info = 'A login code was generated. Please check your email; if email delivery is not configured, use the code: ' . esc($otp);
                    }
                }
            }
        } elseif ($action === 'verify_otp') {
            $otp = trim($_POST['otp'] ?? '');
            if (empty($otp)) {
                $errors[] = 'Please enter the OTP code sent to your email.';
            }
            if (empty($errors)) {
                if (!$user || $user['otp_code'] !== $otp) {
                    $errors[] = 'Invalid OTP code. Please check your email and try again.';
                } elseif (new DateTime() > new DateTime($user['otp_expires'])) {
                    $errors[] = 'The OTP code has expired. Please request a new code.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['last_active'] = time();

                    $stmt = $pdo->prepare("UPDATE users SET otp_code = NULL, otp_expires = NULL WHERE id = ?");
                    $stmt->execute([$user['id']]);

                    header('Location: ' . $redirect);
                    exit;
                }
            }
        } else {
            $password = trim($_POST['password'] ?? '');
            if (empty($password)) {
                $errors[] = 'Please enter your password.';
            }
            if (empty($errors)) {
                $user = $userModel->verifyCredentials($email, $password);
                if (!$user) {
                    $errors[] = 'Invalid email or password. Please try again.';
                } elseif (!$user['email_verified']) {
                    $code = generateVerificationCode();
                    $expires = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
                    $stmt = $pdo->prepare("UPDATE users SET verification_code = ?, verification_expires = ? WHERE id = ?");
                    $stmt->execute([$code, $expires, $user['id']]);
                    sendVerificationEmail($email, $user['name'], $code);
                    $errors[] = 'Your email address is not verified. We have sent a verification code to your email.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['last_active'] = time();

                    header('Location: ' . $redirect);
                    exit;
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
    <title>Login - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-LZN37f6MItjnjim9xk4FzuvO1S1XwF+Yz8LgY3E6RN1HnIkp6E4xIC4qFjm69m2V" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3gJwYpTPi32M30a5d6R08b0BfTT4yl7vHTER1Y1rVPo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6U5dORluXKokN8RyVb4Ydpe1w1P0j9Mbgq6Wv8Q6DkU2J/4x6rD" crossorigin="anonymous"></script>
    <style>
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .auth-card { max-width: 420px; width: 100%; background: rgba(15, 23, 42, 0.92); border: 1px solid rgba(0, 212, 255, 0.15); border-radius: 1rem; padding: 2rem; color: #e2e8f0; }
        .auth-card a { color: var(--primary); }
    </style>
</head>
<body>
<?php include 'navigation.php'; ?>
<div class="auth-page">
    <section class="auth-card shadow-sm">
        <h1 class="h3 mb-3 text-center">Sign in</h1>

        <?php if ($info): ?>
            <div class="alert alert-info" role="alert">
                <?php echo esc($info); ?>
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

        <form id="loginForm" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
            <input type="hidden" name="action" value="login_password" />
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo esc($email); ?>" required aria-required="true" placeholder="name@example.com" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required aria-required="true" placeholder="Enter your password" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <hr class="my-4" />

        <h2 class="h5 mb-3 text-center">Login with Email Code</h2>
        <form id="otpRequestForm" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
            <input type="hidden" name="action" value="send_otp" />
            <div class="mb-3">
                <label for="email_otp" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email_otp" name="email" value="<?php echo esc($email); ?>" required aria-required="true" placeholder="name@example.com" />
            </div>
            <button type="submit" class="btn btn-outline-primary w-100">Send login code</button>
        </form>

        <form id="otpVerifyForm" method="post" class="mt-3" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
            <input type="hidden" name="action" value="verify_otp" />
            <div class="mb-3">
                <label for="email_verify" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email_verify" name="email" value="<?php echo esc($email); ?>" required aria-required="true" placeholder="name@example.com" />
            </div>
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP code</label>
                <input type="text" class="form-control" id="otp" name="otp" placeholder="123456" maxlength="6" />
            </div>
            <button type="submit" class="btn btn-secondary w-100">Verify OTP</button>
        </form>

        <p class="text-center text-muted mt-3 mb-0">Don't have an account? <a href="register.php">Register now</a></p>
    </section>
</div>
<script>
    $(function () {
        $('#loginForm').on('submit', function (e) {
            var email = $('#email').val().trim();
            var password = $('#password').val().trim();
            var errors = [];
            if (!email) {
                errors.push('Please enter your email address.');
            }
            if (!password) {
                errors.push('Please enter your password.');
            }
            if (errors.length) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });
    });
</script>
</body>
</html>
