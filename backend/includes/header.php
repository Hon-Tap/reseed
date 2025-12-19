<?php
$configPath = __DIR__ . '/config.php';
$examplePath = __DIR__ . '/config.example.php';

if (file_exists($configPath)) {
    require_once $configPath;
} elseif (file_exists($examplePath)) {
    require_once $examplePath;
} else {
    die('Configuration file missing.');
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ReSEED — Restoring Hope, Reseeding Life</title>

    <meta name="description" content="South Sudanese social enterprise dedicated to restoring livelihoods, regenerating ecosystems, and rebuilding communities.">

    

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">



<style>
    :root {
    --header-height: 80px;
    --header-height-scrolled: 70px;

    --nav-bg: rgba(255,255,255,0.95);
    --nav-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* ---------------- HEADER ---------------- */

.site-header {
    position: fixed;
    inset: 0 0 auto 0;
    height: var(--header-height);
    background: var(--nav-bg);
    backdrop-filter: blur(8px);
    z-index: 2000; /* HARD FIX */
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.site-header.scrolled {
    height: var(--header-height-scrolled);
    box-shadow: var(--nav-shadow);
}

/* ---------------- NAV WRAPPER ---------------- */

.nav-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

/* ---------------- BRAND ---------------- */

.brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-family: 'Merriweather', serif;
    font-weight: 900;
    font-size: 1.4rem;
    color: var(--primary);
    z-index: 2100;
}

.brand img {
    height: 46px;
    width: 46px;
    object-fit: cover;
    border-radius: 50%;
}

/* ---------------- NAV LINKS ---------------- */

.main-nav {
    display: flex;
    align-items: center;
    gap: 2rem;
    z-index: 2100; /* ABOVE BACKDROP */
}

.nav-link {
    font-weight: 500;
    color: var(--text-main);
    position: relative;
    padding: 0.5rem 0;
    cursor: pointer;
}

.nav-link::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 0;
    height: 2px;
    background: var(--accent);
    transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
    width: 100%;
}

.nav-link.active {
    color: var(--primary);
}

/* ---------------- CTA ---------------- */

.btn-primary {
    background: var(--primary);
    color: white;
    padding: 0.65rem 1.6rem;
    border-radius: 999px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

/* ---------------- MOBILE ---------------- */

.nav-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.6rem;
    cursor: pointer;
    z-index: 2200;
}

.nav-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.45);
    opacity: 0;
    pointer-events: none;
    z-index: 2050;
    transition: opacity 0.3s ease;
}

/* CRITICAL DESKTOP FIX */
@media (min-width: 993px) {
    .nav-backdrop {
        display: none;
    }
}

@media (max-width: 992px) {

    .nav-toggle {
        display: block;
    }

    .main-nav {
        position: fixed;
        top: 0;
        right: -100%;
        height: 100vh;
        width: 80%;
        max-width: 320px;
        background: white;
        flex-direction: column;
        justify-content: center;
        gap: 2rem;
        transition: right 0.4s ease;
    }

    .main-nav.active {
        right: 0;
    }

    .nav-backdrop.active {
        opacity: 1;
        pointer-events: all;
    }
}

</style>

</head>

<body>



<header class="site-header">
    <div class="container nav-wrapper">

        <!-- Brand -->
        <a href="<?= $BASE_URL ?>/index.php" class="brand">
            <img 
                src="<?= $BASE_URL ?>/assets/images/Re-logo.png" 
                alt="ReSEED Logo"
                loading="lazy"
            >
            <span>ReSEED</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="nav-toggle" aria-label="Open Navigation">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Navigation -->
        <nav class="main-nav">
            <?php
                $current_page = basename($_SERVER['PHP_SELF']);

                function isActive(string $page, string $current): string {
                    return $page === $current ? 'active' : '';
                }
            ?>

            <a href="<?= $BASE_URL ?>/index.php"
               class="nav-link <?= isActive('index.php', $current_page); ?>">
                Home
            </a>

            <a href="<?= $BASE_URL ?>/projects.php"
               class="nav-link <?= isActive('projects.php', $current_page); ?>">
                Projects
            </a>

            <a href="<?= $BASE_URL ?>/blog.php"
               class="nav-link <?= isActive('blog.php', $current_page); ?>">
                News
            </a>

            <a href="<?= $BASE_URL ?>/gallery.php"
               class="nav-link <?= isActive('gallery.php', $current_page); ?>">
                Gallery
            </a>

            <a href="#contact" class="nav-link open-contact-modal">
                Contact
            </a>

            <a href="https://api.reseed.org/donate"
               class="btn btn-primary"
               target="_blank"
               rel="noopener">
                Donate Now
            </a>
        </nav>

        <div class="nav-backdrop"></div>
    </div>
</header>

