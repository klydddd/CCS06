# contact.php Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a fully variable-driven PHP contact form that stores submissions in MySQL, uses CSRF protection, and renders a hybrid gallery-chrome + glass-morphism UI consistent with the KLYDUi design system.

**Architecture:** Single-file PRG pattern — `contact.php` handles GET (render form), POST (validate → insert → redirect), and `?sent=1` (thank you state). A shared `includes/db.php` provides the MySQLi connection for both `contact.php` and `admin.php`. All visible strings are PHP variables; HTML contains only tags and `echo` calls.

**Tech Stack:** PHP 8.x, MySQLi (prepared statements), vanilla CSS (design tokens from `global.css` + `landing.css`), no external JS dependencies.

---

## File Map

| Action | Path | Responsibility |
|---|---|---|
| Create | `includes/db.php` | Shared MySQLi connection — `$conn` variable |
| Create | `styles/contact.css` | Contact-page-only layout, form card, input states, thank you card, checkmark animation |
| Write | `contact.php` | Controller (PHP block) + View (HTML block) |

---

## Task 1: Database connection file

**Files:**
- Create: `includes/db.php`

**Prerequisite:** Run `Migration.sql` in phpMyAdmin (or MySQL CLI) to create `portfolio_db` and `contact_submissions` if you haven't already.

```sql
-- Quick verify in phpMyAdmin SQL tab:
SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'portfolio_db';
-- Expected: contact_submissions appears in results
```

- [ ] **Step 1.1: Create the `includes/` directory and `db.php`**

Create `includes/db.php` with this exact content:

```php
<?php
// ── Database connection ───────────────────────────────────────
// Default XAMPP credentials — change $pass if yours differs.
$host   = 'localhost';
$dbname = 'portfolio_db';
$user   = 'root';
$pass   = '';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
```

- [ ] **Step 1.2: Verify the connection in a throwaway test**

Create a temporary file at the project root called `_dbtest.php`:

```php
<?php
require_once __DIR__ . '/includes/db.php';
echo $conn->server_info; // e.g. "8.0.30"
$conn->close();
```

Visit `http://localhost/CCS06/ownWebsite/_dbtest.php` in your browser.

Expected: a MySQL version string (e.g. `8.0.30`), no error message.

- [ ] **Step 1.3: Delete the test file and commit**

Delete `_dbtest.php`, then:

```bash
git add includes/db.php
git commit -m "add shared MySQLi connection helper"
```

---

## Task 2: Contact page CSS

**Files:**
- Create: `styles/contact.css`

This file is loaded only by `contact.php`. It extends `global.css` (design tokens, `pageEnter` keyframe) and `landing.css` (`.landing-nav`, `.landing-footer`, `.landing-orb`, `.btn-primary`, `.noise-overlay`).

- [ ] **Step 2.1: Create `styles/contact.css`**

```css
/* ═══════════════════════════════════════════════════════════
   KLYDUi — Contact Page Styles
   ═══════════════════════════════════════════════════════════ */

/* ── Body ── */
.contact-body {
    background: var(--wall-dark);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    overflow: auto;
    user-select: text;
}

/* ── Main Layout ── */
.contact-main {
    position: relative;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 100px 20px 80px;
    width: 100%;
}

/* ── Card (shared by form + thank you) ── */
.contact-card {
    width: 100%;
    max-width: 560px;
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: clamp(28px, 5vw, 48px);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow:
        0 24px 64px rgba(0, 0, 0, 0.5),
        0 8px 24px rgba(0, 0, 0, 0.3),
        0 0 80px var(--accent-glow);
    animation: pageEnter 0.8s 0.4s var(--ease-out-expo) both;
}

/* ── Form Header ── */
.form-header {
    margin-bottom: 36px;
}

.form-heading {
    font-family: var(--font-display);
    font-size: clamp(1.8rem, 4vw, 2.4rem);
    font-weight: 700;
    letter-spacing: -0.5px;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.form-subheading {
    font-family: var(--font-body);
    font-size: 0.9rem;
    color: var(--text-secondary);
    letter-spacing: 0.3px;
}

/* ── Form Groups ── */
.contact-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-family: var(--font-mono);
    font-size: 0.65rem;
    letter-spacing: 2px;
    color: var(--text-muted);
    text-transform: uppercase;
}

/* ── Inputs ── */
.form-input {
    font-family: var(--font-body);
    font-size: 0.9rem;
    color: var(--text-primary);
    background: var(--frame);
    border: 1px solid var(--frame-edge);
    border-radius: 6px;
    padding: 12px 16px;
    outline: none;
    transition:
        border-color var(--transition-fast),
        box-shadow var(--transition-fast);
    width: 100%;
}

.form-input::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

.form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-glow);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

/* ── Error states ── */
.form-group.has-error .form-input {
    border-color: #e05c5c;
    box-shadow: 0 0 0 3px rgba(224, 92, 92, 0.2);
}

.form-error {
    font-family: var(--font-mono);
    font-size: 0.65rem;
    letter-spacing: 0.5px;
    color: #e05c5c;
}

/* ── Submit button ── */
.btn-submit {
    width: 100%;
    justify-content: center;
    margin-top: 8px;
}

/* ── Thank You card ── */
.thankyou-card {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.thankyou-icon {
    width: 64px;
    height: 64px;
}

.thankyou-icon svg {
    width: 100%;
    height: 100%;
}

/* Animated checkmark draw-on */
.check-circle {
    stroke-dasharray: 157;
    stroke-dashoffset: 157;
    animation: drawCircle 0.6s 0.2s var(--ease-out-expo) forwards;
}

.check-mark {
    stroke-dasharray: 30;
    stroke-dashoffset: 30;
    animation: drawCheck 0.4s 0.7s var(--ease-out-expo) forwards;
}

@keyframes drawCircle {
    to { stroke-dashoffset: 0; }
}

@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}

.thankyou-heading {
    font-family: var(--font-display);
    font-size: clamp(1.8rem, 4vw, 2.4rem);
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.5px;
}

.thankyou-body {
    font-family: var(--font-body);
    font-size: 0.95rem;
    color: var(--text-secondary);
    line-height: 1.6;
    max-width: 380px;
}

.btn-back {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--accent);
    text-decoration: none;
    border: 1px solid var(--accent);
    padding: 10px 24px;
    border-radius: 30px;
    transition: all var(--transition-fast);
    opacity: 0.8;
}

.btn-back:hover {
    background: rgba(201, 169, 110, 0.1);
    opacity: 1;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px var(--accent-glow);
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .contact-main {
        padding: 80px 16px 70px;
    }
}
```

- [ ] **Step 2.2: Commit**

```bash
git add styles/contact.css
git commit -m "add contact page CSS"
```

---

## Task 3: contact.php — Controller block

**Files:**
- Write: `contact.php` (PHP block only — stop before `?>` and the HTML)

This task writes the entire top PHP block. The file will be syntactically incomplete until Task 4 adds the HTML view. Do not visit the page in a browser until Task 4 is done.

- [ ] **Step 3.1: Write the controller block**

Replace the contents of `contact.php` with the following. The file ends with the closing `?>` — the HTML view follows in Task 4.

```php
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
```

---

## Task 4: contact.php — View block

**Files:**
- Append to: `contact.php` (HTML block, after the closing `?>`)

- [ ] **Step 4.1: Append the HTML view**

Append the following directly after the `?>` at the end of `contact.php`:

```php
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

    <div class="wall-texture"></div>
    <div class="landing-orb orb-1"></div>
    <div class="landing-orb orb-2"></div>
    <div class="landing-orb orb-3"></div>
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
        <div class="contact-card form-card">
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
```

- [ ] **Step 4.2: Commit**

```bash
git add contact.php
git commit -m "implement contact.php with CSRF protection and DB persistence"
```

---

## Task 5: End-to-end smoke tests

Visit `http://localhost/CCS06/ownWebsite/contact.php` and run through each check.

- [ ] **5.1 — Page renders without PHP errors**

Expected: form card visible, ambient orbs in background, nav top-right, footer bottom, no white error boxes.

- [ ] **5.2 — Validation errors display inline**

Submit the form with all fields empty.

Expected: three inline error messages appear below their respective fields with a red border on each input. Form values are NOT reset (fields show what was typed).

- [ ] **5.3 — Valid submission triggers PRG redirect**

Fill in:
- Name: `Test User`
- Email: `test@example.com`
- Message: `This is a test message for the contact form.`

Click Send. Expected: browser redirects to `contact.php?sent=1` and the Thank You card appears with the animated gold checkmark.

- [ ] **5.4 — Row appears in the database**

In phpMyAdmin, run:

```sql
SELECT * FROM portfolio_db.contact_submissions ORDER BY id DESC LIMIT 1;
```

Expected: a row with `name = 'Test User'`, `email = 'test@example.com'`, `is_read = 0`, `reply = NULL`.

- [ ] **5.5 — CSRF rejection**

In browser DevTools → Network tab, copy the raw POST request. Replay it with the `csrf_token` field changed to `invalid`. Expected: `403` response with body `Invalid CSRF token.`

- [ ] **5.6 — Refresh on thank-you page does not re-insert**

On `contact.php?sent=1`, press F5 / Cmd+R.

Expected: page simply re-renders the thank you card — no duplicate row is added to the DB (PRG prevents re-POST).

- [ ] **5.7 — Commit if any cosmetic fixes were made**

```bash
git add contact.php styles/contact.css
git commit -m "fix contact page cosmetics after smoke test"
```
