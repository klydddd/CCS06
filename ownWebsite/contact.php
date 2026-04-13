<?php
session_start();

// ── String variables (Controller) ────────────────────────────
$page_title         = 'Contact — KLYDUi';
$form_heading       = 'Get In Touch';
$form_subheading    = 'Have a project in mind? Let\'s talk.';
$label_name         = 'Your Name';
$label_email        = 'Email Address';
$label_message      = 'Message';
$placeholder_name   = 'e.g. Jane Doe';
$placeholder_email  = 'e.g. hello@example.com';
$placeholder_msg    = 'Tell me about your project...';
$btn_submit         = 'Send Message';
$thankyou_heading   = 'Message Sent';
$thankyou_body      = 'Thanks for reaching out. I\'ll get back to you soon.';
$btn_back           = 'Send Another';
$nav_showcase_no    = '01';
$nav_showcase_label = 'Showcase';
$nav_contact_no     = '02';
$nav_contact_label  = 'Contact';
$footer_status      = 'Available for new projects';
$footer_year        = '&copy; 2026';

// ── CSRF token ────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── State ─────────────────────────────────────────────────────
$sent   = isset($_GET['sent']);
$errors = [];
$old    = ['name' => '', 'email' => '', 'message' => ''];

// ── POST handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF verification
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    // Sanitize raw input
    $name    = trim(strip_tags($_POST['name']    ?? ''));
    $email   = trim(strip_tags($_POST['email']   ?? ''));
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
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // Regenerate CSRF token to prevent reuse
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: contact.php?sent=1');
        exit;
    }
}
?>
