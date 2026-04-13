# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Running the site

This is a XAMPP-hosted PHP site. Serve it through Apache:
- Start Apache via the XAMPP Control Panel
- Visit `http://localhost/CCS06/ownWebsite/` in a browser
- No build step, bundler, or package manager — all files are served directly

## Assignment requirements (Lab 2)

This is a **variable-driven portfolio** — no hard-coded text is allowed in HTML. Every visible string must be stored in a PHP variable and `echo`ed into the layout. Treat each file's top PHP block as the **Controller** (data definitions) and the HTML below as the **View** (display only).

| Page | Required content |
|------|-----------------|
| `index.php` | Page title, headline, sub-headline, CTA button text |
| `about.php` | Full name, biography, skills array (loop to display), education details |
| `contact.php` | Form labels, "Thank You" response after submit |
| `admin.php` | View, reply, and delete contact form submissions |

## Architecture

This is a pure PHP/CSS/JS portfolio site with no frameworks or dependencies beyond Google Fonts.

**Pages:**
- `index.php` — Landing page hero with ambient orb effects and CTA to the showcase
- `about.php` — About Me page (full name, bio, skills, education)
- `contact.php` — Contact form with post-submit thank-you response
- `admin.php` — Secure page listing contact submissions with reply/view/delete actions

**Assets:**
- `styles/global.css` — All styles for `about.php` (gallery wall aesthetic, frame components, nav)
- `styles/landing.css` — Landing-page-only styles; extends `global.css` via cascade
- `styles/contact.css` — Contact page styles (glass-morphism form card, input states, checkmark animation)
- `scripts/portfolio.js` — Self-contained IIFE driving the infinite gallery on `about.php`
- `images/` — Project screenshots (`project_1.png` … `project_5.png`)

**Shared Includes:**
- `includes/db.php` — MySQLi connection helper (`$conn`) used by `contact.php` and `admin.php`

## Key design system (CSS custom properties in `global.css :root`)

- **Accent color:** `--accent: #c9a96e` (warm gold) — used for highlights, active states, progress ring
- **Fonts:** `--font-display` (Outfit), `--font-body` (Space Grotesk), `--font-mono` (JetBrains Mono)
- **Frame sizing:** `--frame-width`, `--frame-height`, `--frame-gap`, `--frame-border` — all use `clamp()` for fluid scaling
- **Easing curves:** `--ease-out-expo`, `--ease-out-quart`, `--ease-in-out` — reuse these for consistent motion

## Gallery mechanics (`portfolio.js`)

Project data lives in the `projects` array at the top of the file — add/edit projects there.

The gallery uses a **triple-clone infinite loop** (`CLONE_SETS = 3`): the DOM renders three copies of every frame `[clone | originals | clone]`. After each animated slide, `normalizePosition()` silently snaps back to the middle set so the loop feels seamless.

**Critical coupling:** the `setTimeout` delay in `goTo()` (currently `850ms`) must match the CSS `transition` duration on `.gallery-track.is-animated` (`0.8s` + headroom). If you change the CSS transition, update the JS timeout accordingly.

Navigation is supported via arrow buttons, `←`/`→` keyboard keys, `A`/`D` keys, touch swipe (threshold 60px), and mouse wheel (threshold 30 delta, 900ms cooldown).

## Contact Form (`contact.php`)

The contact page follows the Controller/View pattern strictly:

**Controller block (top PHP):**
- All visible strings defined as PHP variables (form labels, placeholders, error messages, thank-you text)
- Session start and CSRF token generation: `bin2hex(random_bytes(32))` stored in `$_SESSION['csrf_token']`
- POST handler: validates name (2–120 chars), email (FILTER_VALIDATE_EMAIL + max 254 chars), message (10–2000 chars)
- Input sanitization: `trim(strip_tags(...))` before DB insert
- Prepared statement: `bind_param('sss', ...)` prevents SQL injection
- PRG pattern: successful submission redirects to `?sent=1` to prevent double-submit
- On validation failure: errors stored in `$errors` array, values preserved in `$old` for re-display

**View block (HTML):**
- Conditional rendering: `<?php if ($sent): ?>` shows thank-you card, `<?php else: ?>` shows form
- All PHP echoes use `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` for XSS protection
- Exception: `$footer_year` contains raw HTML entity (`&copy;`) and is echoed unescaped intentionally
- Form has `novalidate` attribute — server-side validation only
- CSRF token in hidden input, field values from `$old[...]` on error

**Database:**
- Submissions stored in `portfolio_db.contact_submissions` table
- Columns: `id (PK), name, email, message, reply, is_read, submitted_at, replied_at`
- DB connection via `require_once __DIR__ . '/includes/db.php'`
- Connection deferred until validation passes (no DB hit on page load or validation failure)

## Security Notes

- **CSRF:** `hash_equals()` prevents timing attacks; token regenerated after successful submission
- **SQL Injection:** Prepared statements with type-safe `bind_param()` — never interpolate user input
- **XSS:** All user data and dynamic variables escaped via `htmlspecialchars(ENT_QUOTES, UTF-8)` before HTML output
- **Data validation:** Three-layer enforcement — HTML `maxlength`, PHP `strlen()`, MySQL column width
- **Error handling:** Failed `$stmt->execute()` prevents redirect and redisplays form with error message
