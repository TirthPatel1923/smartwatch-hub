<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$success = '';
$formData = ['name' => '', 'email' => '', 'phone' => '', 'favorite_model' => '', 'message' => ''];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token validation failed. Please try again.';
    } else {
        // Collect and sanitize input
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $favorite_model = trim($_POST['favorite_model'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Store data for repopulation if there are errors
        $formData = compact('name', 'email', 'phone', 'favorite_model', 'message');

        // Validate all fields
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters long.';
        }

        if (!isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (!isValidPhone($phone)) {
            $errors[] = 'Phone number must be at least 10 digits and contain only numbers, spaces, hyphens, and parentheses.';
        }

        if (empty($favorite_model) || strlen($favorite_model) < 2) {
            $errors[] = 'Please tell us which smartwatch model interests you.';
        }

        if (empty($message) || strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters long.';
        }

        // If no validation errors, save to database
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO user_submissions (name, email, phone, favorite_model, message)
                    VALUES (?, ?, ?, ?, ?)
                ");
                if ($stmt->execute([$name, $email, $phone, $favorite_model, $message])) {
                    $success = 'Thank you! Your enquiry has been received. We will contact you soon.';
                    $formData = ['name' => '', 'email' => '', 'phone' => '', 'favorite_model' => '', 'message' => ''];
                } else {
                    $errors[] = 'Failed to submit your enquiry. Please try again.';
                }
            } catch (PDOException $e) {
                $errors[] = 'Database error. Please try again later.';
                error_log('Database error: ' . $e->getMessage());
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
    <title>Contact Us - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .contact-container { max-width: 700px; margin: 3rem auto; padding: 0 1rem; }
        .contact-form { background: rgba(30, 41, 59, 0.4); border: 2px solid rgba(0, 212, 255, 0.1); border-radius: 12px; padding: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--primary); font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; 
            padding: 0.75rem; 
            background: rgba(15, 23, 42, 0.5); 
            border: 2px solid rgba(0, 212, 255, 0.3); 
            border-radius: 6px; 
            color: #e2e8f0; 
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .required { color: var(--danger); }
        .form-group small { display: block; margin-top: 0.25rem; color: #cbd5e1; }
        .form-actions { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem; }
        .form-actions button { flex: 1; min-width: 150px; }
        @media (max-width: 600px) {
            .contact-form { padding: 1.5rem; }
            .form-actions { flex-direction: column; }
            .form-actions button { width: 100%; }
        }
        .error-summary { background: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.5); border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem; color: #fca5a5; }
        .error-summary h3 { margin-top: 0; margin-bottom: 0.5rem; }
        .error-summary ul { margin: 0; padding-left: 1.5rem; }
        .error-summary li { margin-bottom: 0.25rem; }
        .success-message { background: rgba(16, 185, 129, 0.1); border: 2px solid rgba(16, 185, 129, 0.5); border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem; color: #a7f3d0; }
        .success-message i { margin-right: 0.5rem; }
    </style>
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="hero">
    <div class="hero-content">
        <h1>Get in Touch</h1>
        <p>Have a question about our smartwatches? We'd love to hear from you.</p>
    </div>
</div>

<div class="contact-container">
    <div class="contact-form">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem; text-align: center;">
            <i class="fas fa-envelope"></i> Contact Us
        </h2>

        <?php if ($success): ?>
            <div class="success-message" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo esc($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-summary" role="alert" aria-live="polite" aria-label="Form errors">
                <h3 style="margin-bottom: 0.5rem;"><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo esc($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />

            <div class="form-group">
                <label for="name">
                    Full Name <span class="required" aria-label="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo esc($formData['name']); ?>" 
                    required 
                    aria-required="true"
                    minlength="2"
                    maxlength="100"
                    placeholder="John Doe"
                />
                <small>Minimum 2 characters</small>
            </div>

            <div class="form-group">
                <label for="email">
                    Email Address <span class="required" aria-label="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo esc($formData['email']); ?>" 
                    required 
                    aria-required="true"
                    maxlength="150"
                    placeholder="john@example.com"
                />
                <small>We'll never share your email</small>
            </div>

            <div class="form-group">
                <label for="phone">
                    Phone Number <span class="required" aria-label="required">*</span>
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="<?php echo esc($formData['phone']); ?>" 
                    required 
                    aria-required="true"
                    maxlength="30"
                    placeholder="+1 (555) 123-4567"
                />
                <small>Minimum 10 digits</small>
            </div>

            <div class="form-group">
                <label for="favorite_model">
                    Which smartwatch model interests you? <span class="required" aria-label="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="favorite_model" 
                    name="favorite_model" 
                    value="<?php echo esc($formData['favorite_model']); ?>" 
                    required 
                    aria-required="true"
                    minlength="2"
                    maxlength="100"
                    placeholder="e.g., Galaxy Watch, Apple Watch Ultra"
                    list="models"
                />
                <datalist id="models">
                    <option value="Apple Watch">
                    <option value="Samsung Galaxy Watch">
                    <option value="Garmin Forerunner">
                    <option value="Fitbit">
                    <option value="Other">
                </datalist>
                <small>Tell us which model you're interested in</small>
            </div>

            <div class="form-group">
                <label for="message">
                    Message <span class="required" aria-label="required">*</span>
                </label>
                <textarea 
                    id="message" 
                    name="message" 
                    required 
                    aria-required="true"
                    minlength="10"
                    maxlength="1000"
                    placeholder="Tell us about your enquiry..."
                ><?php echo esc($formData['message']); ?></textarea>
                <small>Minimum 10 characters, maximum 1000</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-redo"></i> Clear
                </button>
            </div>
        </form>

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(0, 212, 255, 0.2); text-align: center;">
            <p style="color: #cbd5e1; margin-bottom: 0.5rem;">
                <i class="fas fa-info-circle"></i> This is a contact form for enquiries. We'll get back to you as soon as possible.
            </p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <a href="index.php" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Shop
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
