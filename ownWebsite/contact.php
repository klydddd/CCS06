<?php
session_start();

// ── String variables (Controller) ────────────────────────────
$page_title = 'Contact — KLYDUi';
$form_heading = 'Get In Touch';
$form_subheading = 'Have a project in mind? Let\'s talk.';
$label_name = 'Your Name';
$label_email = 'Email Address';
$label_message = 'Message';
$placeholder_name = 'e.g. Jane Doe';
$placeholder_email = 'e.g. hello@example.com';
$placeholder_msg = 'Tell me about your project...';
$btn_submit = 'Send Message';
$thankyou_heading = 'Message Sent';
$thankyou_body = 'Thanks for reaching out. I\'ll get back to you soon.';
$btn_back = 'Send Another';
$nav_showcase_no = '01';
$nav_showcase_label = 'Showcase';
$nav_contact_no = '02';
$nav_contact_label = 'Contact';
$footer_status = 'Available for new projects';
$footer_year = '&copy; 2026'; // Raw HTML entity — echoed unescaped in view; keep as string literal only

// ── CSRF token ────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── State ─────────────────────────────────────────────────────
$sent = isset($_GET['sent']);
$errors = [];
$old = ['name' => '', 'email' => '', 'message' => ''];

// ── POST handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sent = false;

    // CSRF verification
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    // Sanitize raw input
    $name = trim(strip_tags($_POST['name'] ?? ''));
    $email = trim(strip_tags($_POST['email'] ?? ''));
    $message = trim(strip_tags($_POST['message'] ?? ''));

    // Preserve values for re-display on error
    $old = compact('name', 'email', 'message');

    // Validate
    if (strlen($name) < 2 || strlen($name) > 120) {
        $errors['name'] = 'Name must be between 2 and 120 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (strlen($message) < 10 || strlen($message) > 2000) {
        $errors['message'] = 'Message must be between 10 and 2000 characters.';
    }

    // Persist and redirect on success
    if (empty($errors)) {
        require_once __DIR__ . '/includes/db.php';

        $stmt = $conn->prepare(
            'INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $name, $email, $message);
        if (!$stmt->execute()) {
            $errors['message'] = 'Could not save your message. Please try again.';
        }
        $stmt->close();
        $conn->close();

        if (empty($errors)) {
            // Regenerate CSRF token to prevent reuse
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header('Location: contact.php?sent=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/landing.css">
    <link rel="stylesheet" href="styles/contact.css">
</head>
<body class="contact-body">

    <div class="landing-orb orb-1"></div>
    <div class="landing-orb orb-2"></div>
    <div class="landing-orb orb-3"></div>
    <div class="wall-texture"></div>
    <div class="noise-overlay"></div>

    <nav class="landing-nav">
        <a href="about.php" class="nav-link">
            <span class="nav-link-no"><?= htmlspecialchars($nav_showcase_no,    ENT_QUOTES, 'UTF-8') ?></span>
            <span class="nav-link-text"><?= htmlspecialchars($nav_showcase_label, ENT_QUOTES, 'UTF-8') ?></span>
            <div class="nav-link-hover"></div>
        </a>
        <a href="contact.php" class="nav-link">
            <span class="nav-link-no"><?= htmlspecialchars($nav_contact_no,    ENT_QUOTES, 'UTF-8') ?></span>
            <span class="nav-link-text"><?= htmlspecialchars($nav_contact_label, ENT_QUOTES, 'UTF-8') ?></span>
            <div class="nav-link-hover"></div>
        </a>
    </nav>

    <main class="contact-main">

        <?php if ($sent): ?>
        <!-- ── Thank You state ── -->
        <div class="contact-card thankyou-card">
            <div class="thankyou-icon">
                <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="26" cy="26" r="25" stroke="var(--accent)" stroke-width="1.5" class="check-circle"/>
                    <path d="M14 27l9 9 15-17" stroke="var(--accent)" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round" class="check-mark"/>
                </svg>
            </div>
            <h1 class="thankyou-heading"><?= htmlspecialchars($thankyou_heading, ENT_QUOTES, 'UTF-8') ?></h1>
            <p  class="thankyou-body">   <?= htmlspecialchars($thankyou_body,    ENT_QUOTES, 'UTF-8') ?></p>
            <a  class="btn-back" href="contact.php"><?= htmlspecialchars($btn_back, ENT_QUOTES, 'UTF-8') ?></a>
        </div>

        <?php else: ?>
        <!-- ── Contact form ── -->
        <div class="contact-card">
            <div class="form-header">
                <h1 class="form-heading">   <?= htmlspecialchars($form_heading,    ENT_QUOTES, 'UTF-8') ?></h1>
                <p  class="form-subheading"><?= htmlspecialchars($form_subheading, ENT_QUOTES, 'UTF-8') ?></p>
            </div>

            <form method="POST" action="contact.php" class="contact-form" novalidate>
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <!-- Name -->
                <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                    <label for="name" class="form-label">
                        <?= htmlspecialchars($label_name, ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input type="text" id="name" name="name" class="form-input"
                           placeholder="<?= htmlspecialchars($placeholder_name,  ENT_QUOTES, 'UTF-8') ?>"
                           value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>"
                           maxlength="120" required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                    <label for="email" class="form-label">
                        <?= htmlspecialchars($label_email, ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input type="email" id="email" name="email" class="form-input"
                           placeholder="<?= htmlspecialchars($placeholder_email, ENT_QUOTES, 'UTF-8') ?>"
                           value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>"
                           maxlength="254" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Message -->
                <div class="form-group <?= isset($errors['message']) ? 'has-error' : '' ?>">
                    <label for="message" class="form-label">
                        <?= htmlspecialchars($label_message, ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <textarea id="message" name="message" class="form-input form-textarea"
                              placeholder="<?= htmlspecialchars($placeholder_msg, ENT_QUOTES, 'UTF-8') ?>"
                              maxlength="2000" rows="5"
                              required><?= htmlspecialchars($old['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (isset($errors['message'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary btn-submit">
                    <span class="btn-text"><?= htmlspecialchars($btn_submit, ENT_QUOTES, 'UTF-8') ?></span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>
        </div>
        <?php endif; ?>

    </main>

    <div class="landing-footer">
        <div class="status-indicator">
            <div class="status-dot"></div>
            <span><?= htmlspecialchars($footer_status, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="year"><?= $footer_year ?></div>
    </div>

</body>
</html>