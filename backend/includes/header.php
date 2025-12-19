<?php
require_once __DIR__ . '/config.php'; 
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
            /* --- Brand Identity --- */
            --color-primary: #0f8c04;       /* Deep Green */
            --color-primary-dark: #0a6302;
            --color-primary-light: #e6f4e5; /* Very light green for backgrounds */
            --color-primary-rgb: 15, 140, 4;

            --color-accent: #E67E22;        /* Earth Orange */
            --color-accent-hover: #D35400;

            /* --- Surface & UI --- */
            --color-bg: #F8FAFC;            /* Ultra light grey/blue tint */
            --color-surface: #FFFFFF;
            --color-surface-trans: rgba(255, 255, 255, 0.85);
            
            /* --- Typography --- */
            --color-heading: #0F172A;       /* Slate 900 */
            --color-text: #334155;          /* Slate 700 */
            --color-text-muted: #64748B;    /* Slate 500 */

            /* --- Dimensions & Physics --- */
            --header-height: 80px;
            --header-height-scrolled: 70px;
            --container-width: 1240px;
            --radius-sm: 8px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-pill: 100px;
            
            /* --- Shadows --- */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-green: 0 10px 25px -5px rgba(15, 140, 4, 0.25);
            
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* --- Global Reset & Base --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; font-size: 16px; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text);
            line-height: 1.7;
            padding-top: var(--header-height);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Merriweather', serif;
            color: var(--color-heading);
            line-height: 1.25;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        a { text-decoration: none; color: inherit; transition: var(--transition-smooth); }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }

        /* --- Utilities --- */
        .container { max-width: var(--container-width); margin: 0 auto; padding: 0 1.5rem; }
        .section-padding { padding: 6rem 0; }
        .text-center { text-align: center; }
        
        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.85rem 2rem;
            font-weight: 600;
            border-radius: var(--radius-pill);
            transition: var(--transition-smooth);
            cursor: pointer; border: none; font-size: 1rem;
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: #fff;
            box-shadow: var(--shadow-green);
        }
        .btn-primary:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(15, 140, 4, 0.4);
        }

        /* --- Navigation Bar --- */
        .site-header {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--header-height);
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            transition: var(--transition-smooth);
            border-bottom: 1px solid transparent;
            display: flex;
            align-items: center;
        }

        .site-header.scrolled {
            height: var(--header-height-scrolled);
            background: #ffffff;
            box-shadow: var(--shadow-md);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .nav-wrapper {
            display: flex; justify-content: space-between; align-items: center;
            width: 100%;
        }

        .brand {
            display: flex; align-items: center; gap: 0.75rem;
            font-family: 'Merriweather', serif;
            font-weight: 900; font-size: 1.5rem;
            color: var(--color-primary);
        }
        .brand img { height: 48px; width: 48px; border-radius: 50%; object-fit: cover; transition: var(--transition-smooth); }
        .site-header.scrolled .brand img { height: 40px; width: 40px; }

        .main-nav { display: flex; align-items: center; gap: 2rem; }
        
        .nav-link {
            font-weight: 500;
            color: var(--color-heading);
            position: relative;
            padding: 0.5rem 0;
            font-size: 0.95rem;
        }
        
        .nav-link::before {
            content: ''; position: absolute; bottom: 0; left: 50%;
            width: 0; height: 2px;
            background: var(--color-accent);
            transition: var(--transition-smooth);
            transform: translateX(-50%);
        }
        
        .nav-link:hover { color: var(--color-primary); }
        .nav-link:hover::before, .nav-link.active::before { width: 100%; }
        .nav-link.active { color: var(--color-primary); font-weight: 600; }

        .donate-btn-nav {
            background: var(--color-accent);
            color: white !important;
            padding: 0.6rem 1.5rem;
            border-radius: var(--radius-pill);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
        }
        .donate-btn-nav:hover {
            background: var(--color-accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.4);
        }

        /* Mobile Toggle */
        .nav-toggle { 
            display: none; 
            background: none; 
            border: none; 
            font-size: 1.6rem; 
            cursor: pointer; 
            color: var(--color-heading); 
            z-index: 1100;
            transition: var(--transition-smooth);
        }

        /* --- Mobile Responsive --- */
        @media (max-width: 992px) {
            .nav-toggle { display: block; }

            .main-nav {
                position: fixed; 
                top: 0; 
                right: -100%; 
                bottom: 0;
                width: 80%; 
                max-width: 300px;
                background: var(--color-surface);
                flex-direction: column; 
                justify-content: center;
                align-items: center;
                box-shadow: -10px 0 30px rgba(0,0,0,0.1);
                transition: right 0.4s cubic-bezier(0.77, 0, 0.175, 1);
                padding: 2rem;
                gap: 2rem;
                z-index: 1050;
            }

            .main-nav.active { right: 0; }
            
            .nav-link { font-size: 1.1rem; }

            .nav-backdrop {
                position: fixed; 
                inset: 0; 
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(4px); 
                z-index: 1040;
                opacity: 0; 
                pointer-events: none; 
                transition: opacity 0.3s;
            }
            .nav-backdrop.active { opacity: 1; pointer-events: all; }
        }

        /* --- Components --- */
        .card {
            background: var(--color-surface);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0,0,0,0.03);
            transition: var(--transition-smooth);
            overflow: hidden;
        }
        .card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
    </style>

</head>

<body>



<header class="site-header">
    <div class="container nav-wrapper">

        <!-- Brand -->
         <img 
            src="<?= $BASE_URL ?>/frontend/assets/images/Re-logo.png"
            alt="ReSEED Logo"
            loading="lazy"
        >


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

