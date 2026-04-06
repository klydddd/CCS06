/**
 * KLYDUi — Portfolio Showcase
 * True infinite loop gallery with smooth centered navigation
 */

(function () {
    'use strict';

    // ── Project Data ──
    const projects = [
        {
            id: 1,
            title: 'DataVision',
            description: 'Real-time analytics dashboard with live data streams',
            tech: 'React • D3.js • WebSocket',
            image: 'images/project_1.png',
            plate: 'HackMNL 2025'
        },
        {
            id: 2,
            title: 'NeuralChat',
            description: 'AI-powered conversational interface with context memory',
            tech: 'Python • GPT-4 • FastAPI',
            image: 'images/project_2.png',
            plate: 'DevHack 2025'
        },
        {
            id: 3,
            title: 'VitalSync',
            description: 'IoT health monitoring with predictive alerts',
            tech: 'Flutter • Firebase • TensorFlow',
            image: 'images/project_3.png',
            plate: 'HealthHack 2025'
        },
        {
            id: 4,
            title: 'CodeForge',
            description: 'Collaborative real-time code editor with AI assist',
            tech: 'Next.js • WebRTC • Monaco',
            image: 'images/project_4.png',
            plate: 'BuildCon 2026'
        },
        {
            id: 5,
            title: 'ChainVault',
            description: 'Decentralized NFT marketplace with smart contracts',
            tech: 'Solidity • Ethers.js • IPFS',
            image: 'images/project_5.png',
            plate: 'Web3 Hack 2026'
        }
    ];

    // ── Config ──
    const CLONE_SETS = 3; // Triple the items: [clone set | original set | clone set]
    const totalFrames = projects.length * CLONE_SETS;
    const middleStart = projects.length; // Index where the "real" middle set begins

    // ── State ──
    let currentVirtualIndex = middleStart; // Start at first item of the middle set
    let currentProjectIndex = 0;          // Which project (0 to projects.length-1)
    let isTransitioning = false;
    let touchStartX = 0;

    // ── DOM References ──
    const galleryTrack = document.getElementById('galleryTrack');
    const projectTitle = document.getElementById('projectTitle');
    const projectDescription = document.getElementById('projectDescription');
    const projectTech = document.getElementById('projectTech');
    const projectNumber = document.getElementById('projectNumber');
    const projectInfo = document.getElementById('projectInfo');
    const counterCurrent = document.getElementById('counterCurrent');
    const counterTotal = document.getElementById('counterTotal');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const navProgress = document.getElementById('navProgress');

    // ── Measurements (cached, recalculated on resize) ──
    let frameWidth = 0;
    let gap = 0;
    let slotWidth = 0;

    function recalcMeasurements() {
        const firstFrame = galleryTrack.querySelector('.frame');
        if (!firstFrame) return;
        frameWidth = parseFloat(getComputedStyle(firstFrame).width);
        gap = parseFloat(getComputedStyle(galleryTrack).gap) || 40;
        slotWidth = frameWidth + gap;
    }

    // ── Build Gallery ──
    function buildGallery() {
        // Build 3 copies: [set0 clone | set1 original | set2 clone]
        for (let set = 0; set < CLONE_SETS; set++) {
            projects.forEach((project, pIndex) => {
                const frame = document.createElement('div');
                frame.className = 'frame';
                frame.dataset.projectIndex = pIndex;
                frame.dataset.virtualIndex = set * projects.length + pIndex;
                frame.innerHTML = `
                    <div class="frame-ambient"></div>
                    <div class="frame-light"></div>
                    <div class="frame-border"></div>
                    <div class="frame-mat"></div>
                    <div class="frame-image">
                        <img src="${project.image}" alt="${project.title}" loading="eager">
                    </div>
                    <div class="frame-glass"></div>
                    <div class="frame-plate">${project.plate}</div>
                `;
                galleryTrack.appendChild(frame);

                // Click to navigate to this frame
                frame.addEventListener('click', () => {
                    const vi = parseInt(frame.dataset.virtualIndex);
                    if (vi !== currentVirtualIndex && !isTransitioning) {
                        goTo(vi);
                    }
                });
            });
        }

        counterTotal.textContent = String(projects.length).padStart(2, '0');
    }

    // ── Position the track so virtualIndex is centered ──
    function setTrackPosition(virtualIndex, animate) {
        const viewportCenter = window.innerWidth / 2;
        const frameCenterOffset = frameWidth / 2;
        const offset = viewportCenter - frameCenterOffset - (virtualIndex * slotWidth);

        if (animate) {
            galleryTrack.classList.add('is-animated');
        } else {
            galleryTrack.classList.remove('is-animated');
        }

        galleryTrack.style.transform = `translateX(${offset}px)`;
    }

    // ── Silently jump to equivalent position in the middle set ──
    function normalizePosition() {
        // Calculate equivalent index in the middle set
        const projectIdx = currentProjectIndex;
        const normalizedVirtual = middleStart + projectIdx;

        if (currentVirtualIndex !== normalizedVirtual) {
            currentVirtualIndex = normalizedVirtual;
            // Instant reposition (no transition)
            galleryTrack.classList.remove('is-animated');
            setTrackPosition(currentVirtualIndex, false);
        }
    }

    // ── Update which frames show as active ──
    function updateActiveFrames() {
        const frames = galleryTrack.querySelectorAll('.frame');
        frames.forEach(f => {
            const pi = parseInt(f.dataset.projectIndex);
            if (pi === currentProjectIndex) {
                f.classList.add('is-active');
            } else {
                f.classList.remove('is-active');
            }
        });
    }

    // ── Navigate ──
    function goTo(targetVirtual) {
        if (isTransitioning) return;
        if (targetVirtual === currentVirtualIndex) return;

        isTransitioning = true;
        currentVirtualIndex = targetVirtual;
        currentProjectIndex = ((targetVirtual % projects.length) + projects.length) % projects.length;

        // Fade out info
        projectInfo.classList.add('is-transitioning');

        // Animate slide
        setTrackPosition(currentVirtualIndex, true);
        updateActiveFrames();
        updateCounter();
        updateNavIndicator();

        // After transition ends, normalize and update info
        setTimeout(() => {
            // Update project info text
            const project = projects[currentProjectIndex];
            projectTitle.textContent = project.title;
            projectDescription.textContent = project.description;
            projectTech.textContent = project.tech;
            projectNumber.textContent = String(project.id).padStart(2, '0');
            projectInfo.classList.remove('is-transitioning');

            // Silently jump back to middle set to maintain infinite loop
            normalizePosition();

            isTransitioning = false;
        }, 850); // Matches CSS transition duration
    }

    // ── Navigation helpers ──
    function goNext() {
        if (isTransitioning) return;
        goTo(currentVirtualIndex + 1);
    }

    function goPrev() {
        if (isTransitioning) return;
        goTo(currentVirtualIndex - 1);
    }

    // ── Update Counter ──
    function updateCounter() {
        counterCurrent.textContent = String(currentProjectIndex + 1).padStart(2, '0');
        counterCurrent.style.transform = 'scale(1.3)';
        counterCurrent.style.color = '#c9a96e';
        setTimeout(() => {
            counterCurrent.style.transform = 'scale(1)';
            counterCurrent.style.color = '';
        }, 300);
    }

    // ── Update Nav Indicator ──
    function updateNavIndicator() {
        const circumference = 2 * Math.PI * 40;
        const progress = (currentProjectIndex + 1) / projects.length;
        const offset = circumference * (1 - progress);
        navProgress.style.strokeDashoffset = offset;

        const navDot = document.getElementById('navDot');
        if (navDot) {
            const angle = (360 / projects.length) * currentProjectIndex;
            navDot.style.transform = `rotate(${angle}deg)`;
        }
    }

    // ── Bind Events ──
    function bindEvents() {
        prevBtn.addEventListener('click', () => {
            prevBtn.classList.add('clicked');
            setTimeout(() => prevBtn.classList.remove('clicked'), 600);
            goPrev();
        });

        nextBtn.addEventListener('click', () => {
            nextBtn.classList.add('clicked');
            setTimeout(() => nextBtn.classList.remove('clicked'), 600);
            goNext();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft' || e.key === 'a') goPrev();
            else if (e.key === 'ArrowRight' || e.key === 'd') goNext();
        });

        // Touch / swipe
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            const diff = touchStartX - e.changedTouches[0].screenX;
            if (diff > 60) goNext();
            else if (diff < -60) goPrev();
        }, { passive: true });

        // Mouse wheel
        let wheelCooldown = false;
        document.addEventListener('wheel', (e) => {
            if (wheelCooldown) return;
            const delta = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? e.deltaX : e.deltaY;
            if (Math.abs(delta) > 30) {
                wheelCooldown = true;
                if (delta > 0) goNext();
                else goPrev();
                setTimeout(() => { wheelCooldown = false; }, 900);
            }
        }, { passive: true });

        // Resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                recalcMeasurements();
                setTrackPosition(currentVirtualIndex, false);
            }, 100);
        });

        // Buttons always enabled
        prevBtn.disabled = false;
        nextBtn.disabled = false;
    }

    // ── Initialize ──
    function init() {
        buildGallery();
        recalcMeasurements();

        // Set initial state
        currentProjectIndex = 0;
        currentVirtualIndex = middleStart;

        const project = projects[0];
        projectTitle.textContent = project.title;
        projectDescription.textContent = project.description;
        projectTech.textContent = project.tech;
        projectNumber.textContent = String(project.id).padStart(2, '0');

        // Position centered on first item of middle set (no animation)
        setTrackPosition(currentVirtualIndex, false);
        updateActiveFrames();
        updateCounter();
        updateNavIndicator();
        bindEvents();

        // Entrance animation: stagger frame opacity
        const frames = galleryTrack.querySelectorAll('.frame');
        frames.forEach((frame) => {
            frame.style.opacity = '0';
        });
        // Slight delay then reveal all
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                frames.forEach((frame, i) => {
                    setTimeout(() => {
                        frame.style.opacity = '';
                    }, 100 + (i % projects.length) * 80);
                });
            });
        });
    }

    // ── Start ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
