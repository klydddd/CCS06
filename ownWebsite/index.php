<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLYDUi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/landing.css">
</head>
<body class="landing-body">
    <!-- Ambient glowing backgrounds -->
    <div class="landing-orb orb-1"></div>
    <div class="landing-orb orb-2"></div>
    <div class="landing-orb orb-3"></div>

    <div class="wall-texture"></div>
    <div class="noise-overlay"></div>

    <nav class="landing-nav">
        <a href="about.php" class="nav-link">
            <span class="nav-link-no">01</span>
            <span class="nav-link-text">Showcase</span>
            <div class="nav-link-hover"></div>
        </a>
        <a href="contact.php" class="nav-link">
            <span class="nav-link-no">02</span>
            <span class="nav-link-text">Contact</span>
            <div class="nav-link-hover"></div>
        </a>
    </nav>

    <main class="hero-container">
        <div class="hero-content">
            <div class="hero-label">PORTFOLIO & LABS</div>
            <h1 class="hero-title">KLYDU<span class="hero-title-accent">i</span></h1>
            <p class="hero-subtitle">Engineering elegant solutions through design, code, and innovation.</p>
            
            <div class="hero-actions">
                <a href="about.php" class="btn-primary">
                    <span class="btn-text">Enter Showcase</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </main>
    
    <div class="landing-footer">
        <div class="status-indicator">
            <div class="status-dot"></div>
            <span>Available for new projects</span>
        </div>
        <div class="year">&copy; 2026</div>
    </div>
</body>
</html>
