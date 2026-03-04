<?php
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] === 'ar') ? 'ar' : 'en';
}
$lang = $_SESSION['lang'] ?? 'en';
$isArabic = ($lang === 'ar');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $isArabic ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dalili — <?= $isArabic ? 'مرحباً بك في الأردن' : 'Welcome to Jordan' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: <?= $isArabic ? "'Cairo', 'Poppins'" : "'Poppins', 'Cairo'" ?>, sans-serif;
            background: #0A0A14;
            color: #E8E8F0;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
        }

        /* ─── Particle Canvas ─── */
        #particles {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        /* ─── Background Video / Image Overlay ─── */
        .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(135deg, rgba(10,10,20,0.92) 0%, rgba(15,15,26,0.80) 40%, rgba(231,76,60,0.15) 100%),
                url('https://images.unsplash.com/photo-1563656157432-67560011e209?w=1920&q=80') center/cover no-repeat;
            animation: bgPan 25s ease-in-out infinite alternate;
        }

        @keyframes bgPan {
            0%   { background-position: center center; transform: scale(1); }
            100% { background-position: 30% 60%; transform: scale(1.08); }
        }

        /* ─── Radial Glow ─── */
        .glow-orb {
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            z-index: 2;
            pointer-events: none;
        }
        .glow-orb.red    { background: #E74C3C; top: -100px; right: -100px; animation: orbFloat 8s ease-in-out infinite alternate; }
        .glow-orb.blue   { background: #3498DB; bottom: -150px; left: -100px; animation: orbFloat 10s ease-in-out infinite alternate-reverse; }
        .glow-orb.gold   { background: #F39C12; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 300px; height: 300px; animation: orbPulse 6s ease-in-out infinite; }

        @keyframes orbFloat {
            0%   { transform: translate(0, 0); }
            100% { transform: translate(30px, -40px); }
        }
        @keyframes orbPulse {
            0%, 100% { opacity: 0.08; transform: translate(-50%,-50%) scale(1); }
            50%      { opacity: 0.20; transform: translate(-50%,-50%) scale(1.3); }
        }

        /* ─── Main Content ─── */
        .welcome-container {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            width: 100vw;
            text-align: center;
            padding: 2rem;
        }

        /* ─── Compass Spinner ─── */
        .compass-ring {
            width: 100px;
            height: 100px;
            border: 2px solid rgba(231,76,60,0.3);
            border-top-color: #E74C3C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            opacity: 0;
            animation: fadeSlideUp 1s 0.3s ease forwards, compassSpin 4s linear infinite;
        }
        .compass-ring i {
            font-size: 2rem;
            color: #E74C3C;
            animation: compassCounter 4s linear infinite;
        }

        @keyframes compassSpin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes compassCounter {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(-360deg); }
        }

        /* ─── Logo ─── */
        .welcome-logo {
            font-size: 4.5rem;
            font-weight: 900;
            letter-spacing: -2px;
            line-height: 1;
            margin-bottom: 0.5rem;
            opacity: 0;
            animation: fadeSlideUp 1s 0.6s ease forwards;
        }
        .welcome-logo span {
            background: linear-gradient(135deg, #E74C3C, #F39C12, #3498DB);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ─── Arabic Sub-logo ─── */
        .welcome-logo-ar {
            font-size: 2rem;
            font-weight: 700;
            color: rgba(232,232,240,0.5);
            letter-spacing: 4px;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeSlideUp 1s 0.8s ease forwards;
        }

        /* ─── Divider ─── */
        .welcome-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeSlideUp 1s 1s ease forwards;
        }
        .welcome-divider .line {
            width: 50px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(231,76,60,0.6), transparent);
        }
        .welcome-divider i {
            color: #F39C12;
            font-size: 0.8rem;
        }

        /* ─── Tagline ─── */
        .welcome-tagline {
            font-size: 1.15rem;
            font-weight: 300;
            color: #A0A0B8;
            max-width: 500px;
            line-height: 1.8;
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeSlideUp 1s 1.2s ease forwards;
        }

        /* ─── CTA Button ─── */
        .start-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 18px 48px;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            background: linear-gradient(135deg, #E74C3C, #C0392B);
            border: none;
            border-radius: 60px;
            cursor: pointer;
            overflow: hidden;
            text-decoration: none;
            opacity: 0;
            animation: fadeSlideUp 1s 1.5s ease forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .start-btn::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 62px;
            background: linear-gradient(135deg, #E74C3C, #F39C12, #3498DB, #E74C3C);
            background-size: 300% 300%;
            z-index: -1;
            animation: borderGlow 4s ease infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .start-btn:hover::before { opacity: 1; }
        .start-btn:hover {
            transform: translateY(-3px) scale(1.04);
            box-shadow: 0 12px 40px rgba(231,76,60,0.4);
        }
        .start-btn:active { transform: scale(0.97); }
        .start-btn .arrow {
            display: inline-flex;
            transition: transform 0.3s ease;
        }
        .start-btn:hover .arrow {
            transform: translateX(<?= $isArabic ? '-6px' : '6px' ?>);
        }

        @keyframes borderGlow {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ─── Bottom Hint ─── */
        .welcome-hint {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: #6C6C88;
            font-size: 0.75rem;
            opacity: 0;
            animation: fadeSlideUp 1s 2s ease forwards;
        }
        .welcome-hint .scroll-dot {
            width: 20px;
            height: 32px;
            border: 2px solid rgba(108,108,136,0.4);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }
        .welcome-hint .scroll-dot::after {
            content: '';
            width: 3px;
            height: 6px;
            background: #E74C3C;
            border-radius: 2px;
            animation: scrollBounce 2s ease-in-out infinite;
        }

        @keyframes scrollBounce {
            0%, 100% { transform: translateY(0); opacity: 1; }
            50%      { transform: translateY(8px); opacity: 0.3; }
        }

        /* ─── Language Toggle ─── */
        .lang-toggle {
            position: absolute;
            top: 2rem;
            <?= $isArabic ? 'left' : 'right' ?>: 2rem;
            z-index: 20;
            padding: 8px 18px;
            border: 1px solid rgba(231,76,60,0.3);
            border-radius: 20px;
            background: rgba(15,15,26,0.6);
            backdrop-filter: blur(10px);
            color: #A0A0B8;
            font-size: 0.85rem;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            opacity: 0;
            animation: fadeSlideUp 1s 0.3s ease forwards;
        }
        .lang-toggle:hover {
            border-color: #E74C3C;
            color: #E8E8F0;
            background: rgba(231,76,60,0.1);
        }

        /* ─── Shared Animation ─── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Transition Overlay ─── */
        .page-transition {
            position: fixed;
            inset: 0;
            z-index: 999;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-transition .curtain-left,
        .page-transition .curtain-right {
            position: absolute;
            top: 0;
            width: 0;
            height: 100%;
            background: #0A0A14;
        }
        .page-transition .curtain-left  { left: 0; }
        .page-transition .curtain-right { right: 0; }
        .page-transition .trans-logo {
            opacity: 0;
            font-size: 3rem;
            font-weight: 900;
            z-index: 1000;
            background: linear-gradient(135deg, #E74C3C, #F39C12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-transition.active .curtain-left,
        .page-transition.active .curtain-right {
            animation: curtainClose 0.6s ease forwards;
        }
        .page-transition.active .trans-logo {
            animation: transLogoShow 0.4s 0.5s ease forwards;
        }

        @keyframes curtainClose {
            to { width: 50%; }
        }
        @keyframes transLogoShow {
            from { opacity: 0; transform: scale(0.7); }
            to   { opacity: 1; transform: scale(1); }
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .welcome-logo { font-size: 3rem; }
            .welcome-logo-ar { font-size: 1.5rem; }
            .welcome-tagline { font-size: 1rem; }
            .start-btn { padding: 15px 36px; font-size: 1rem; }
            .glow-orb { width: 250px; height: 250px; }
        }
        @media (max-width: 480px) {
            .welcome-logo { font-size: 2.4rem; }
            .welcome-logo-ar { font-size: 1.2rem; }
            .welcome-tagline { font-size: 0.9rem; padding: 0 1rem; }
            .start-btn { padding: 14px 30px; font-size: 0.95rem; }
        }
    </style>
</head>
<body>

    <!-- Language Toggle -->
    <a class="lang-toggle" href="?lang=<?= $isArabic ? 'en' : 'ar' ?>">
        <i class="fas fa-globe"></i>&nbsp; <?= $isArabic ? 'English' : 'العربية' ?>
    </a>

    <!-- Particle Canvas -->
    <canvas id="particles"></canvas>

    <!-- Background & Glow -->
    <div class="bg-layer"></div>
    <div class="glow-orb red"></div>
    <div class="glow-orb blue"></div>
    <div class="glow-orb gold"></div>

    <!-- Main Content -->
    <div class="welcome-container">
        <div class="compass-ring">
            <i class="fas fa-compass"></i>
        </div>

        <div class="welcome-logo"><span><?= $isArabic ? 'دليلي' : 'Dalili' ?></span></div>
        <div class="welcome-logo-ar"><?= $isArabic ? 'دليلك إلى الأردن' : 'YOUR GUIDE TO JORDAN' ?></div>

        <div class="welcome-divider">
            <div class="line"></div>
            <i class="fas fa-star"></i>
            <div class="line"></div>
        </div>

        <p class="welcome-tagline">
            <?= $isArabic
                ? 'اكتشف جمال المملكة الأردنية الهاشمية — من البتراء الوردية إلى رمال وادي رم الذهبية، دع دليلي يقودك في رحلة لا تُنسى.'
                : 'Discover the beauty of the Hashemite Kingdom — from the rose-red city of Petra to the golden sands of Wadi Rum, let Dalili guide your unforgettable journey.'
            ?>
        </p>

        <a href="#" class="start-btn" id="startJourney" onclick="startTransition(event)">
            <?= $isArabic ? '<span class="arrow"><i class="fas fa-arrow-left"></i></span>' : '' ?>
            <?= $isArabic ? 'ابدأ الرحلة' : 'Start the Journey' ?>
            <?= !$isArabic ? '<span class="arrow"><i class="fas fa-arrow-right"></i></span>' : '' ?>
        </a>

    </div>

    <!-- Page Transition Overlay -->
    <div class="page-transition" id="pageTransition">
        <div class="curtain-left"></div>
        <div class="curtain-right"></div>
        <div class="trans-logo"><?= $isArabic ? 'دليلي' : 'Dalili' ?></div>
    </div>

    <script>
    // ─── Particles ───
    (function() {
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        let particles = [];
        const PARTICLE_COUNT = 80;

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        class Particle {
            constructor() { this.reset(); }
            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5;
                this.speedX = (Math.random() - 0.5) * 0.4;
                this.speedY = (Math.random() - 0.5) * 0.4;
                this.opacity = Math.random() * 0.5 + 0.1;
                this.color = ['#E74C3C','#F39C12','#3498DB','#E8E8F0'][Math.floor(Math.random()*4)];
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }
            draw() {
                ctx.globalAlpha = this.opacity;
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        for (let i = 0; i < PARTICLE_COUNT; i++) particles.push(new Particle());

        function connectParticles() {
            for (let a = 0; a < particles.length; a++) {
                for (let b = a + 1; b < particles.length; b++) {
                    const dx = particles[a].x - particles[b].x;
                    const dy = particles[a].y - particles[b].y;
                    const dist = Math.sqrt(dx*dx + dy*dy);
                    if (dist < 120) {
                        ctx.globalAlpha = (1 - dist/120) * 0.08;
                        ctx.strokeStyle = '#E74C3C';
                        ctx.lineWidth = 0.5;
                        ctx.beginPath();
                        ctx.moveTo(particles[a].x, particles[a].y);
                        ctx.lineTo(particles[b].x, particles[b].y);
                        ctx.stroke();
                    }
                }
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => { p.update(); p.draw(); });
            connectParticles();
            requestAnimationFrame(animate);
        }
        animate();
    })();

    // ─── Page Transition ───
    function startTransition(e) {
        e.preventDefault();
        localStorage.setItem('dalili_welcomed', '1');
        const overlay = document.getElementById('pageTransition');
        overlay.classList.add('active');
        setTimeout(() => {
            window.location.href = './';
        }, 1000);
    }

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            startTransition(e);
        }
    });
    </script>
</body>
</html>
