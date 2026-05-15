<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusinessDesk — Plateforme de gestion commerciale nouvelle génération</title>
    <meta name="description" content="BusinessDesk : devis, factures, bons de livraison et bons de commande pour SARL marocaines. Multi-sociétés, PDF illimités, ICE, conformité fiscale.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-0: #050816;
            --bg-1: #0a0f24;
            --bg-2: #111835;
            --neon-cyan: #00d4ff;
            --neon-violet: #a855f7;
            --neon-pink: #ec4899;
            --neon-blue: #3b82f6;
            --text-100: #f8fafc;
            --text-200: #cbd5e1;
            --text-400: #94a3b8;
            --text-600: #64748b;
            --border: rgba(148, 163, 184, 0.12);
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-0);
            color: var(--text-100);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Background grid + glow ===== */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 800px 600px at 20% 0%, rgba(0, 212, 255, 0.18), transparent 60%),
                radial-gradient(ellipse 700px 500px at 80% 30%, rgba(168, 85, 247, 0.15), transparent 60%),
                radial-gradient(ellipse 600px 400px at 50% 100%, rgba(236, 72, 153, 0.10), transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(148, 163, 184, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.05) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            pointer-events: none;
            z-index: 0;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 1; }

        /* ===== Navbar ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(5, 8, 22, 0.75);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
            color: var(--text-100);
            text-decoration: none;
        }
        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 24px rgba(0, 212, 255, 0.4);
            overflow: hidden;
        }
        .brand-logo img { width: 38px; height: 38px; object-fit: contain; }
        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .nav-link {
            color: var(--text-200);
            text-decoration: none;
            font-size: 0.92rem;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-link:hover { color: var(--text-100); background: rgba(255,255,255,0.04); }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-blue));
            color: #001220;
            font-weight: 700;
            font-size: 0.92rem;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.25s;
            box-shadow: 0 0 0 0 rgba(0, 212, 255, 0);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 212, 255, 0.5);
        }
        .btn-login svg { width: 16px; height: 16px; }

        /* ===== Hero ===== */
        .hero {
            padding: 160px 0 100px;
            text-align: center;
            position: relative;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border: 1px solid rgba(0, 212, 255, 0.3);
            background: rgba(0, 212, 255, 0.06);
            border-radius: 999px;
            font-size: 0.82rem;
            color: var(--neon-cyan);
            margin-bottom: 28px;
            font-weight: 500;
        }
        .badge-dot {
            width: 6px; height: 6px;
            background: var(--neon-cyan);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--neon-cyan);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        h1.hero-title {
            font-size: clamp(2.4rem, 6vw, 4.5rem);
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -0.04em;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gradient-text {
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-violet), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }
        .hero-subtitle {
            font-size: clamp(1.05rem, 2vw, 1.25rem);
            color: var(--text-400);
            max-width: 720px;
            margin: 0 auto 40px;
            line-height: 1.65;
        }
        .hero-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 16px 32px;
            font-size: 1rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-violet));
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            box-shadow: 0 10px 40px rgba(168, 85, 247, 0.35);
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 50px rgba(168, 85, 247, 0.6);
        }
        .btn-ghost {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 16px 28px;
            font-size: 1rem;
            font-weight: 600;
            background: rgba(255,255,255,0.04);
            color: var(--text-100);
            text-decoration: none;
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: all 0.3s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: rgba(148, 163, 184, 0.3); }

        /* ===== Hero visual (mock dashboard) ===== */
        .hero-visual {
            margin-top: 80px;
            position: relative;
            max-width: 980px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-visual::before {
            content: '';
            position: absolute;
            inset: -40px;
            background: radial-gradient(ellipse, rgba(0, 212, 255, 0.25), transparent 60%);
            filter: blur(40px);
            z-index: -1;
        }
        .dashboard-mock {
            background: linear-gradient(180deg, rgba(17, 24, 53, 0.9), rgba(10, 15, 36, 0.95));
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 20px;
            padding: 24px;
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .mock-header {
            display: flex; align-items: center; gap: 8px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 24px;
        }
        .mock-dot { width: 12px; height: 12px; border-radius: 50%; }
        .mock-dot.r { background: #ff5f57; }
        .mock-dot.y { background: #febc2e; }
        .mock-dot.g { background: #28c840; }
        .mock-url {
            margin-left: 16px;
            background: rgba(255,255,255,0.04);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.78rem;
            color: var(--text-400);
            font-family: 'SF Mono', 'Monaco', monospace;
        }

        .mock-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .mock-stat {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            text-align: left;
        }
        .mock-stat-label {
            font-size: 0.72rem;
            color: var(--text-400);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }
        .mock-stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-100);
        }
        .mock-stat-trend {
            font-size: 0.75rem;
            margin-top: 6px;
            color: #4ade80;
            font-weight: 600;
        }
        .mock-stat-trend.danger { color: #f87171; }

        .mock-table {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .mock-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr 100px;
            padding: 12px 16px;
            font-size: 0.85rem;
            border-bottom: 1px solid var(--border);
            align-items: center;
            text-align: left;
        }
        .mock-row.head {
            background: rgba(255,255,255,0.03);
            color: var(--text-400);
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .mock-row:last-child { border-bottom: none; }
        .mock-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .pill-green { background: rgba(74, 222, 128, 0.15); color: #4ade80; }
        .pill-amber { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
        .pill-blue  { background: rgba(96, 165, 250, 0.15); color: #60a5fa; }

        /* ===== Features ===== */
        .section { padding: 100px 0; position: relative; }
        .section-head { text-align: center; margin-bottom: 60px; }
        .eyebrow {
            display: inline-block;
            color: var(--neon-cyan);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 16px;
        }
        h2.section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 18px;
        }
        .section-lead {
            font-size: 1.1rem;
            color: var(--text-400);
            max-width: 640px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }
        .feature-card {
            position: relative;
            background: linear-gradient(180deg, rgba(17, 24, 53, 0.6), rgba(10, 15, 36, 0.4));
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            transition: all 0.3s;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: -1px; left: -1px; right: -1px; bottom: -1px;
            background: linear-gradient(135deg, transparent, rgba(0, 212, 255, 0.3), transparent);
            border-radius: 18px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: -1;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(0, 212, 255, 0.3);
        }
        .feature-card:hover::before { opacity: 1; }
        .feature-icon {
            width: 50px; height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(168, 85, 247, 0.15));
            border: 1px solid rgba(0, 212, 255, 0.3);
            display: flex; align-items: center; justify-content: center;
            color: var(--neon-cyan);
            margin-bottom: 20px;
        }
        .feature-icon svg { width: 24px; height: 24px; }
        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-100);
        }
        .feature-card p { color: var(--text-400); font-size: 0.95rem; line-height: 1.6; }

        /* ===== Stats band ===== */
        .stats-band {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(168, 85, 247, 0.05));
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 60px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 30px;
            text-align: center;
        }
        .stat-num {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 900;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-violet));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        .stat-label {
            margin-top: 8px;
            color: var(--text-400);
            font-size: 0.9rem;
        }

        /* ===== CTA ===== */
        .cta {
            text-align: center;
            padding: 100px 24px;
            position: relative;
        }
        .cta-card {
            max-width: 760px;
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(17, 24, 53, 0.8), rgba(10, 15, 36, 0.8));
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 24px;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }
        .cta-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at top, rgba(0, 212, 255, 0.2), transparent 60%);
            pointer-events: none;
        }
        .cta h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 16px;
            position: relative;
        }
        .cta p { color: var(--text-400); margin-bottom: 30px; position: relative; font-size: 1.05rem; }

        /* ===== Footer ===== */
        .footer {
            border-top: 1px solid var(--border);
            padding: 40px 24px;
            text-align: center;
            color: var(--text-600);
            font-size: 0.85rem;
        }
        .footer a { color: var(--text-400); text-decoration: none; margin: 0 8px; }
        .footer a:hover { color: var(--neon-cyan); }

        /* ===== Mobile ===== */
        @media (max-width: 768px) {
            .nav-link { display: none; }
            .mock-stats { grid-template-columns: repeat(2, 1fr); }
            .mock-row { grid-template-columns: 1fr 1fr; font-size: 0.75rem; }
            .mock-row > :nth-child(n+3) { display: none; }
            .mock-url { display: none; }
            .hero { padding: 120px 0 60px; }
            .cta-card { padding: 40px 24px; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-inner">
        <a href="#" class="brand">
            <div class="brand-logo"><img src="{{ asset('images/logo.svg') }}" alt="BusinessDesk"></div>
            <span>BusinessDesk</span>
        </a>
        <div class="nav-actions">
            <a href="#features" class="nav-link">Fonctionnalités</a>
            <a href="#stats" class="nav-link">Chiffres</a>
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('manager.dashboard') }}" class="btn-login">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Mon espace
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Se connecter
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="badge"><span class="badge-dot"></span> Plateforme nouvelle génération</div>
        <h1 class="hero-title">
            La gestion commerciale<br>
            <span class="gradient-text">réinventée pour 2026</span>
        </h1>
        <p class="hero-subtitle">
            Devis, factures, bons de livraison et bons de commande conformes à la fiscalité marocaine.
            Pilotez plusieurs sociétés depuis une interface unique, taillée pour la performance.
        </p>
        <div class="hero-actions">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('manager.dashboard') }}" class="btn-primary">
                    Accéder à mon espace
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">
                    Se connecter maintenant
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            @endauth
            <a href="#features" class="btn-ghost">
                Découvrir les fonctionnalités
            </a>
        </div>

        <!-- Mock dashboard -->
        <div class="hero-visual">
            <div class="dashboard-mock">
                <div class="mock-header">
                    <span class="mock-dot r"></span>
                    <span class="mock-dot y"></span>
                    <span class="mock-dot g"></span>
                    <span class="mock-url">businessdesk.app / manager / dashboard</span>
                </div>
                <div class="mock-stats">
                    <div class="mock-stat">
                        <div class="mock-stat-label">CA du mois</div>
                        <div class="mock-stat-value">842 650 DH</div>
                        <div class="mock-stat-trend">↗ +24.5%</div>
                    </div>
                    <div class="mock-stat">
                        <div class="mock-stat-label">Factures émises</div>
                        <div class="mock-stat-value">47</div>
                        <div class="mock-stat-trend">↗ +12</div>
                    </div>
                    <div class="mock-stat">
                        <div class="mock-stat-label">Devis en attente</div>
                        <div class="mock-stat-value">18</div>
                        <div class="mock-stat-trend">↗ +3</div>
                    </div>
                    <div class="mock-stat">
                        <div class="mock-stat-label">À encaisser</div>
                        <div class="mock-stat-value">156 200 DH</div>
                        <div class="mock-stat-trend danger">↘ -8%</div>
                    </div>
                </div>
                <div class="mock-table">
                    <div class="mock-row head">
                        <span>N°</span>
                        <span>Client</span>
                        <span>Montant</span>
                        <span>Date</span>
                        <span>Statut</span>
                    </div>
                    <div class="mock-row">
                        <span>F-2026/047</span>
                        <span>SARL Atlas Construction</span>
                        <span>124 800,00 DH</span>
                        <span>12/05/2026</span>
                        <span><span class="mock-pill pill-green">Payée</span></span>
                    </div>
                    <div class="mock-row">
                        <span>F-2026/046</span>
                        <span>Maroc Telecom Services</span>
                        <span>86 400,00 DH</span>
                        <span>10/05/2026</span>
                        <span><span class="mock-pill pill-amber">Partielle</span></span>
                    </div>
                    <div class="mock-row">
                        <span>D-2026/052</span>
                        <span>OCP Group Logistics</span>
                        <span>210 000,00 DH</span>
                        <span>08/05/2026</span>
                        <span><span class="mock-pill pill-blue">Envoyé</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Fonctionnalités</span>
            <h2 class="section-title">Tout ce dont votre <span class="gradient-text">SARL</span> a besoin</h2>
            <p class="section-lead">Une plateforme complète, pensée pour la conformité fiscale marocaine et la productivité de votre équipe.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <h3>Devis professionnels</h3>
                <p>Créez des devis percutants en quelques clics, avec calcul automatique de la TVA, remises et conversion en facture en un instant.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <h3>Factures conformes</h3>
                <p>Numérotation automatique, montant en lettres, ICE, mentions légales : vos factures respectent toutes les exigences de la DGI.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect x="9" y="11" width="14" height="10" rx="1"/><polyline points="12 17 14 19 18 15"/></svg>
                </div>
                <h3>Bons de livraison & commande</h3>
                <p>Suivez vos expéditions avec des bons de livraison clairs et générez des bons de commande pour vos fournisseurs.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h3>Multi-sociétés</h3>
                <p>Gérez plusieurs SARL depuis un seul compte, avec un sélecteur de société instantané et des données strictement cloisonnées.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Suivi des règlements</h3>
                <p>Enregistrez les paiements partiels, identifiez en un coup d'œil ce qui reste à encaisser et relancez vos clients à temps.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                </div>
                <h3>PDF personnalisables</h3>
                <p>Quatre thèmes de PDF au choix, intégration du logo, RIB, conditions générales : chaque document reflète votre image.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </div>
                <h3>Gestion des marchés</h3>
                <p>Suivez vos appels d'offres et marchés publics avec pièces jointes, statuts et exports vers tableur en un clic.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3>Sécurité de niveau bancaire</h3>
                <p>Mots de passe hashés, sessions sécurisées, isolation stricte des données entre sociétés. Vos informations sont protégées.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <h3>Performance instantanée</h3>
                <p>Interface réactive, recherche en temps réel, autocomplétion intelligente : zéro temps perdu, 100% de productivité.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section id="stats" class="stats-band">
    <div class="container">
        <div class="stats-grid">
            <div>
                <div class="stat-num">100%</div>
                <div class="stat-label">Conforme DGI Maroc</div>
            </div>
            <div>
                <div class="stat-num">∞</div>
                <div class="stat-label">Documents illimités</div>
            </div>
            <div>
                <div class="stat-num">4</div>
                <div class="stat-label">Thèmes PDF inclus</div>
            </div>
            <div>
                <div class="stat-num">24/7</div>
                <div class="stat-label">Disponibilité</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="cta-card">
        <h2>Prêt à passer à la <span class="gradient-text">vitesse supérieure</span> ?</h2>
        <p>Connectez-vous à votre espace et reprenez le contrôle de votre gestion commerciale dès maintenant.</p>
        @auth
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('manager.dashboard') }}" class="btn-primary">
                Accéder à mon espace
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-primary">
                Se connecter
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        @endauth
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div>© {{ date('Y') }} BusinessDesk — Plateforme de gestion commerciale pour SARL marocaines</div>
    <div style="margin-top:8px">
        <a href="{{ route('login') }}">Connexion</a> ·
        <a href="#features">Fonctionnalités</a> ·
        <a href="#stats">Chiffres</a>
    </div>
</footer>

</body>
</html>
