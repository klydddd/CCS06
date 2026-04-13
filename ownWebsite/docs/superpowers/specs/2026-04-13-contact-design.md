# contact.php — Design Spec
**Date:** 2026-04-13  
**Project:** KLYDUi Portfolio — Lab 2 (Variable-Driven PHP)  
**Status:** Approved

---

## Overview

Build `contact.php` as a fully variable-driven PHP contact form that persists submissions to `contact_submissions` (MySQL) for admin review via `admin.php`. No email delivery. Full CSRF protection. Hybrid visual aesthetic: gallery chrome nav/footer with a glass-morphism form panel over ambient orbs.

---

## 1. Architecture

### Pattern
Controller / View split per `CLAUDE.md`:
- **Controller** — top PHP block: session, variables, CSRF, DB, POST handler, GET state detection
- **View** — HTML block: display only, no logic, all strings echoed from variables

### Files
| File | Role |
|---|---|
| `contact.php` | Main page — form + thank you state |
| `includes/db.php` | Shared MySQLi connection (used by `contact.php` and `admin.php`) |

### Request flow (PRG pattern)
```
GET /contact.php
  → render form (generate CSRF token → $_SESSION['csrf_token'])

POST /contact.php
  → verify CSRF token
  → validate inputs
  → sanitize inputs
  → INSERT into contact_submissions (prepared statement)
  → redirect → /contact.php?sent=1

GET /contact.php?sent=1
  → render Thank You state ($sent = true)
```

---

## 2. PHP Variables (Controller data layer)

All visible strings stored as PHP variables — no hard-coded text in HTML.

| Variable | Value |
|---|---|
| `$page_title` | `"Contact — KLYDUi"` |
| `$form_heading` | `"Get In Touch"` |
| `$form_subheading` | `"Have a project in mind? Let's talk."` |
| `$label_name` | `"Your Name"` |
| `$label_email` | `"Email Address"` |
| `$label_message` | `"Message"` |
| `$placeholder_name` | `"e.g. Jane Doe"` |
| `$placeholder_email` | `"e.g. hello@example.com"` |
| `$placeholder_message` | `"Tell me about your project..."` |
| `$btn_submit` | `"Send Message"` |
| `$thankyou_heading` | `"Message Sent"` |
| `$thankyou_body` | `"Thanks for reaching out. I'll get back to you soon."` |
| `$btn_back` | `"Send Another"` |
| `$nav_showcase_no` | `"01"` |
| `$nav_showcase_label` | `"Showcase"` |
| `$nav_contact_no` | `"02"` |
| `$nav_contact_label` | `"Contact"` |
| `$footer_status` | `"Available for new projects"` |
| `$footer_year` | `"© 2026"` |

---

## 3. Form Fields & Validation

| Field | Input type | Validation rules | DB column |
|---|---|---|---|
| Name | `text` | required · 2–120 chars · `strip_tags()` | `name VARCHAR(120)` |
| Email | `email` | required · `FILTER_VALIDATE_EMAIL` · max 254 chars | `email VARCHAR(254)` |
| Message | `textarea` | required · 10–2000 chars · `strip_tags()` | `message TEXT` |
| CSRF token | `hidden` | must match `$_SESSION['csrf_token']` | — |

Errors are displayed inline below each field (PHP variable-driven error strings).

---

## 4. Security

| Layer | Mechanism |
|---|---|
| CSRF | `bin2hex(random_bytes(32))` stored in `$_SESSION['csrf_token']` · verified on POST · regenerated after successful insert |
| Output escaping | All `echo` calls use `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` |
| SQL injection | MySQLi prepared statement — `bind_param('sss', $name, $email, $message)` |
| Double-submit | PRG redirect to `?sent=1` after successful insert |
| Input sanitization | `trim()` + `strip_tags()` before DB insert |

---

## 5. Visual Design

### Aesthetic
**Hybrid** — gallery chrome (nav + footer) for site consistency; glass-morphism form panel over landing-style ambient orbs for focused, atmospheric feel.

### Layout
- `body`: `overflow: auto` (scrollable, unlike gallery's `overflow: hidden`)
- Background: wall texture → 3 ambient orbs (gold, purple, green) → noise overlay → content
- Nav: `.landing-nav` chrome (top-right glass pill links) from `landing.css`
- Footer: `.landing-footer` chrome (bottom bar, status dot + year)
- Main: flexbox column, `min-height: 100vh`, centered, `max-width: 560px`

### Form card
- Background: `var(--glass)` with `backdrop-filter: blur(20px)`
- Border: `1px solid var(--glass-border)`
- Border-radius: `12px`
- Shadow: multi-layer dark drop shadow + subtle `var(--accent-glow)` ambient
- Padding: `clamp(28px, 5vw, 48px)`

### Input states
| State | Style |
|---|---|
| Default | `background: var(--frame)` · `border: 1px solid var(--frame-edge)` |
| Focus | `border-color: var(--accent)` · `box-shadow: 0 0 0 3px var(--accent-glow)` |
| Error | `border-color: #e05c5c` · inline message in `--font-mono` 0.65rem below field |

### Submit button
Inherits `.btn-primary` from `landing.css` — white fill → gold on hover, arrow SVG, lift transform.

### Thank You state
Same glass card replaces form:
- Animated gold checkmark SVG (CSS stroke draw-on)
- `$thankyou_heading` in `--font-display`
- `$thankyou_body` in `--font-body` secondary color
- `$btn_back` ghost link back to `contact.php`

### Animation
Form card enters with `pageEnter` keyframe from `global.css` (fade up, 0.4s delay).

---

## 6. CSS Strategy

New styles go in `styles/contact.css` (loaded only by `contact.php`). Reuses:
- `global.css` — CSS custom properties, `.wall-texture`, `pageEnter` keyframe
- `landing.css` — `.landing-nav`, `.landing-footer`, `.btn-primary`, `.landing-orb`, `.noise-overlay`

No new CSS variables — uses existing design tokens exclusively.

---

## 7. Out of Scope

- Email delivery (`mail()` / PHPMailer) — submissions stored in DB only
- CAPTCHA — not required by assignment
- File attachments
- Real-time validation (JS) — server-side only per assignment constraints
