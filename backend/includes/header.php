<?php
require_once __DIR__ . '/config.php';

// Smart Active State Logic
// Handles query parameters (e.g., blog.php?page=2) so the link stays active
function isActive($pageName) {
    $currentUri = $_SERVER['REQUEST_URI'];
    $currentPath = parse_url($currentUri, PHP_URL_PATH);
    $fileName = basename($currentPath);
    
    // Handle Homepage (root / or index.php)
    if ($pageName === 'index.php' && ($fileName === '' || $fileName === 'index.php')) {
        return 'active';
    }
    
    // Handle other pages
    return ($fileName === $pageName) ? 'active' : '';
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
            /* --- Core Brand Colors --- */
            --primary: #0f8c04;        /* ReSEED Green */
            --primary-dark: #0a6302;
            --accent: #E67E22;         /* Earth/Sun Orange */
            --accent-hover: #D35400;

            /* --- UI Colors --- */
            --bg-body: #F8FAFC;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            
            /* --- Layout --- */
            --header-h: 80px;
            --header-h-scroll: 70px;
            --container-w: 1240px;
            --radius-pill: 50px;
            --shadow-nav: 0 4px 20px -5px rgba(0,0,0,0.1);
        }

        /* --- Critical Layout Reset --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        html { scroll-behavior: smooth; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            /* CRITICAL: Prevents content from hiding behind fixed header */
            padding-top: var(--header-h); 
            transition: padding-top 0.3s ease;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Merriweather', serif; font-weight: 700; }
        a { text-decoration: none; color: inherit; transition: 0.3s ease; }
        ul { list-style: none; }
        img { display: block; max-width: 100%; }

        /* --- Header Structure --- */
        .site-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            z-index: 999;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        /* Scrolled State */
        .site-header.scrolled {
            height: var(--header-h-scroll);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow-nav);
        }

        /* Update body padding when header shrinks to avoid jump */
        body.header-shrunk { padding-top: var(--header-h-scroll); }

        .container {
            width: 100%;
            max-width: var(--container-w);
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .nav-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Brand --- */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Merriweather', serif;
            font-weight: 900;
            font-size: 1.4rem;
            color: var(--primary);
        }
        
        .brand img {
            height: 45px;
            width: 45px;
            border-radius: 50%;
            object-fit: cover;
            transition: 0.3s;
        }
        
        .site-header.scrolled .brand img { transform: scale(0.9); }

        /* --- Desktop Navigation --- */
        .main-nav {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px; /* Space between icon and text */
            position: relative;
            padding: 5px 0;
        }

        .nav-link i { 
            color: var(--text-muted); 
            font-size: 0.9rem;
            transition: 0.3s; 
        }

        /* Hover & Active States */
        .nav-link:hover, .nav-link.active { color: var(--primary); }
        .nav-link:hover i, .nav-link.active i { color: var(--primary); transform: translateY(-2px); }

        /* Underline Effect */
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; width: 0; height: 2px;
            background: var(--accent);
            transition: 0.3s ease;
        }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        /* Donate Button */
        .btn-donate {
            background: var(--accent);
            color: #fff !important;
            padding: 10px 24px;
            border-radius: var(--radius-pill);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.25);
        }
        
        .btn-donate:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.35);
        }
        
        .btn-donate::after { display: none; } /* Remove underline */

        /* --- Mobile Toggle --- */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-main);
            cursor: pointer;
        }

        /* --- Mobile Responsive Styles --- */
        @media (max-width: 991px) {
            .nav-toggle { display: block; }

            .main-nav {
                position: fixed;
                top: 0; right: -100%; /* Hidden by default */
                width: 80%;
                max-width: 320px;
                height: 100vh;
                background: var(--surface);
                flex-direction: column;
                align-items: flex-start;
                padding: 80px 30px;
                box-shadow: -5px 0 30px rgba(0,0,0,0.1);
                transition: 0.4s cubic-bezier(0.77, 0, 0.175, 1);
                z-index: 1000;
            }

            .main-nav.open { right: 0; }

            .nav-link {
                font-size: 1.1rem;
                width: 100%;
                padding: 15px 0;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            
            .btn-donate { margin-top: 20px; width: 100%; justify-content: center; }

            /* Backdrop */
            .nav-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                backdrop-filter: blur(4px);
                opacity: 0;
                pointer-events: none;
                transition: 0.3s;
                z-index: 999;
            }
            .nav-backdrop.open { opacity: 1; pointer-events: all; }
        }
    </style>
</head>
<body>

<header class="site-header" id="mainHeader">
    <div class="container nav-wrapper">
        
        <a href="/" class="brand">
            <img src="/assets/images/Re-logo.jpeg" alt="ReSEED Logo">
            <span>ReSEED</span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle Navigation">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="main-nav" id="mainNav">
            
            <a href="/" class="nav-link <?= isActive('index.php'); ?>">
                <i class="fa-solid fa-house"></i> Home
            </a>

            <a href="/projects.php" class="nav-link <?= isActive('projects.php'); ?>">
                <i class="fa-solid fa-hand-holding-seedling"></i> Projects
            </a>

            <a href="/blog.php" class="nav-link <?= isActive('blog.php'); ?>">
                <i class="fa-solid fa-newspaper"></i> News
            </a>

            <a href="/gallery.php" class="nav-link <?= isActive('gallery.php'); ?>">
                <i class="fa-solid fa-images"></i> Gallery
            </a>
            
            <a href="#contact" class="nav-link open-contact-modal">
                <i class="fa-solid fa-envelope"></i> Contact
            </a>

            <a href="https://api.reseed.org/donate" target="_blank" class="btn-donate">
                <i class="fa-solid fa-heart"></i> Donate
            </a>
        </nav>

        <div class="nav-backdrop" id="navBackdrop"></div>
    </div>
</header>

<script>
    // --- Header Scroll Effect ---
    const header = document.getElementById('mainHeader');
    const body = document.body;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
            body.classList.add('header-shrunk');
        } else {
            header.classList.remove('scrolled');
            body.classList.remove('header-shrunk');
        }
    });

    // --- Mobile Menu Toggle ---
    const toggleBtn = document.getElementById('navToggle');
    const nav = document.getElementById('mainNav');
    const backdrop = document.getElementById('navBackdrop');

    function toggleMenu() {
        nav.classList.toggle('open');
        backdrop.classList.toggle('open');
        
        // Change icon from bars to X
        const icon = toggleBtn.querySelector('i');
        if (nav.classList.contains('open')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        } else {
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }
    }

    toggleBtn.addEventListener('click', toggleMenu);
    backdrop.addEventListener('click', toggleMenu);
</script>