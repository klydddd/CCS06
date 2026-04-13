# UPDATES.md — Implementation Log

## Lab 2: Contact Form & Admin Infrastructure

This document tracks what was added during the contact page implementation (April 13, 2026).

---

## Files Added

### 1. `includes/db.php` (507 bytes)
**Purpose:** Shared MySQLi connection for `contact.php` and `admin.php`

**What it does:**
- Creates a persistent connection to `portfolio_db` using XAMPP defaults (localhost, root, empty password)
- Handles connection errors with HTTP 500 response
- Sets UTF-8 charset on the connection

**Usage:** `require_once __DIR__ . '/includes/db.php';` — exposes `$conn` variable

**Commits:** `57f6e0f`

---

### 2. `styles/contact.css` (220 lines)
**Purpose:** Contact-page-only CSS extending the global design system

**Components styled:**
- `.contact-body` — page body with scrollable overflow (vs. gallery's fixed height)
- `.contact-main` — flex layout, centered, `min-height: 100vh`
- `.contact-card` — glass-morphism panel: `backdrop-filter: blur(20px)`, multi-layer shadows, accent glow
- `.form-header`, `.form-heading`, `.form-subheading` — form title area
- `.contact-form`, `.form-group`, `.form-label` — form structure
- `.form-input`, `.form-textarea` — text inputs with focus (gold border + glow) and error states (red border)
- `.form-error` — inline validation error messages
- `.btn-submit` — submit button extending `.btn-primary` from `landing.css`
- `.thankyou-card`, `.thankyou-icon`, `.thankyou-heading`, `.thankyou-body` — success state
- `.check-circle`, `.check-mark` — SVG checkmark draw-on animations (stroke-dasharray)
- `@keyframes drawCircle`, `@keyframes drawCheck` — smooth draw-in animations
- `@media (max-width: 768px)` — responsive breakpoint

**Design tokens:** All colors, fonts, easing, transitions use CSS custom properties from `global.css`

**Commits:** `d3364ef`, `bbdbfb6` (animation fixes)

---

### 3. `contact.php` (214 lines)
**Purpose:** Lab 2 requirement — variable-driven contact form with CSRF protection

#### Controller Block (top PHP, ~82 lines)

**String Variables (18 total):**
- Page title: `$page_title`
- Form text: `$form_heading`, `$form_subheading`
- Field labels: `$label_name`, `$label_email`, `$label_message`
- Placeholders: `$placeholder_name`, `$placeholder_email`, `$placeholder_msg`
- Button: `$btn_submit`
- Thank-you: `$thankyou_heading`, `$thankyou_body`, `$btn_back`
- Nav: `$nav_showcase_no`, `$nav_showcase_label`, `$nav_contact_no`, `$nav_contact_label`
- Footer: `$footer_status`, `$footer_year`

**CSRF Protection:**
- Session start: `session_start()`
- Token generation: `$_SESSION['csrf_token'] = bin2hex(random_bytes(32))`
- POST verification: `hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])`
- Regeneration after success to prevent reuse

**Validation & Sanitization:**
- Input sanitization: `trim(strip_tags($input))`
- Name: 2–120 characters, alphanumeric + spaces allowed
- Email: `FILTER_VALIDATE_EMAIL` + max 254 characters
- Message: 10–2000 characters
- Errors stored in `$errors` array, values preserved in `$old` for re-display

**Database Submission:**
- Prepared statement: `$stmt->bind_param('sss', $name, $email, $message)`
- Table: `contact_submissions(name, email, message)`
- Error handling: `if (!$stmt->execute())` prevents redirect on DB failure
- Deferred connection: `require_once` only on validation pass

**PRG Pattern:**
- POST success redirects to `contact.php?sent=1`
- `$sent` state detected from GET parameter
- Guard added: `$sent = false` in POST handler prevents query-string hijacking

#### View Block (HTML, ~130 lines)

**Conditionals:**
- `<?php if ($sent): ?>` — renders thank-you card
- `<?php else: ?>` — renders form

**Thank-You Card:**
- SVG checkmark (circle r=25 + path `M14 27l9 9 15-17`)
- Animated draw-on via stroke-dasharray/dashoffset
- Variables: `$thankyou_heading`, `$thankyou_body`, `$btn_back`

**Form:**
- `novalidate` attribute — server-side validation only
- CSRF hidden input: `htmlspecialchars($_SESSION['csrf_token'])`
- Three fields: name, email, message
- Field repopulation: `value="<?= htmlspecialchars($old['name']) ?>"`
- Error state: `class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>"`
- Error display: `<span class="form-error">` only if `isset($errors['name'])`

**Output Escaping:**
- Every PHP echo uses `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`
- Exception: `$footer_year` contains `&copy;` entity, echoed raw intentionally

**CSS Linking:**
- Loads `global.css`, `landing.css`, `contact.css` in cascade order

**Navigation:**
- Ambient orbs (`.landing-orb orb-1/2/3`)
- Gallery chrome nav and footer (reused from `landing.css`)

**Commits:** `f43e021` (controller), `bceb03e` (view), `9932e06` (execute guard), `0983049` (class cleanup), `65093dc` (DOM order)

---

## Design Decisions

### Hybrid Aesthetic
Contact page combines:
- **Landing style** — ambient orbs + glass-morphism form panel
- **Gallery style** — nav and footer chrome (consistent with `about.php`)
- **Result** — page feels like a focused overlay within the portfolio's visual language

### CSRF Token Rotation
Token regenerated after successful submission to:
- Prevent reuse across sessions
- Mitigate CSRF replay attacks
- Align with OWASP recommendations

### Deferred Database Connection
`require_once` placed inside the POST success branch (after validation) to:
- Avoid DB hit on GET requests or validation failures
- Keep the page fast for repeated visits
- Establish connection only when needed

### Error Message Placement
Validation errors display:
- Inline below the field with `has-error` CSS class
- Field repopulated with user's input for correction
- Non-validation errors (e.g., DB failure) also mapped to `$errors['message']` for consistency

---

## Outstanding Items

### `index.php` — Not Variable-Driven
The landing page has **no PHP controller block**. All visible text is hard-coded in HTML:
- Page title, headline, subtitle, CTA text
- Nav labels and numbers
- Status indicator text
- Copyright year

**Action needed:** Refactor `index.php` to follow the same Controller/View pattern as `contact.php`:
1. Extract all visible strings to PHP variables at the top
2. Echo them via `htmlspecialchars()` in the HTML
3. Aligns with Lab 2 requirement: "no hard-coded text is allowed in HTML"

### `admin.php` — Not Yet Implemented
The Lab 2 requirement lists `admin.php` (View, reply, delete contact form submissions) but it remains empty. This page will need:
1. DB query to fetch all rows from `contact_submissions`
2. Admin functions: reply (update `reply` field), view (display row detail), delete
3. Same variable-driven pattern as `contact.php`

---

## Testing Checklist

Manual smoke tests (Task 5) verify:

- [ ] Page renders without PHP errors
- [ ] Validation errors display inline for empty submission
- [ ] Valid submission: name, email, message all required
- [ ] PRG redirect to `?sent=1` on success
- [ ] Thank-you card renders with animated checkmark
- [ ] DB row created in `contact_submissions` table
- [ ] CSRF token mismatch returns 403
- [ ] Page refresh on `?sent=1` does not duplicate submission
- [ ] Form repopulation after validation error
- [ ] Responsive layout at 768px breakpoint

---

## Commits

| SHA | Message |
|-----|---------|
| `57f6e0f` | add shared MySQLi connection helper |
| `d3364ef` | add contact page CSS |
| `bbdbfb6` | fix checkmark stroke-dasharray values and btn-back display |
| `f43e021` | add contact.php controller block |
| `9932e06` | fix: reset sent flag in POST handler and guard execute() failure |
| `bceb03e` | add contact.php HTML view |
| `0983049` | remove orphaned form-card class; document footer_year escaping exception |
| `65093dc` | align background layer DOM order with index.php |

**Branch:** `main` — pushed to `https://github.com/klydddd/CCS06`

---

## References

- **Design Spec:** `docs/superpowers/specs/2026-04-13-contact-design.md`
- **Implementation Plan:** `docs/superpowers/plans/2026-04-13-contact-page.md`
- **Database Migration:** `Migration.sql` (creates `portfolio_db` and `contact_submissions` table)
- **Assignment Source:** Lab 2 — Variable-Driven PHP Portfolio
