<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIDIKO — Sistem Informasi Digital Kolaboratif</title>

    <meta name="description"
          content="SIDIKO adalah platform SaaS manajemen sekolah modern untuk mengelola akademik, keuangan, siswa, guru, dan operasional sekolah dalam satu sistem.">

    @vite(['resources/css/app.css'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system,
                BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        a {
            text-decoration: none;
        }

        .page {
            min-height: 100vh;
            overflow: hidden;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            width: 100%;
            border-bottom: 1px solid rgba(226, 232, 240, .8);
            background: rgba(255, 255, 255, .88);
            backdrop-filter: blur(14px);
            position: relative;
            z-index: 10;
        }

        .nav-container {
            max-width: 1180px;
            margin: auto;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 18px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, .25);
        }

        .brand-name {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -.5px;
            color: #0f172a;
        }

        .brand-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: .2s ease;
        }

        .btn-login {
            color: #334155;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .btn-login:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-primary {
            color: white;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            box-shadow: 0 10px 25px rgba(37, 99, 235, .22);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .28);
        }

        /* =========================
           HERO
        ========================= */

        .hero {
            position: relative;
            padding: 95px 24px 80px;
        }

        .hero::before {
            content: "";
            position: absolute;
            width: 650px;
            height: 650px;
            background: rgba(59, 130, 246, .10);
            border-radius: 50%;
            filter: blur(80px);
            top: -260px;
            right: -180px;
            pointer-events: none;
        }

        .hero-container {
            max-width: 1180px;
            margin: auto;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            align-items: center;
            gap: 70px;
            position: relative;
            z-index: 1;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        .hero h1 {
            margin: 0;
            max-width: 700px;
            font-size: clamp(42px, 6vw, 68px);
            line-height: 1.04;
            letter-spacing: -3px;
            font-weight: 850;
        }

        .hero h1 span {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            max-width: 610px;
            margin-top: 25px;
            color: #64748b;
            font-size: 17px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .hero-note {
            margin-top: 18px;
            font-size: 12px;
            color: #94a3b8;
        }

        /* =========================
           DASHBOARD PREVIEW
        ========================= */

        .preview-wrapper {
            position: relative;
        }

        .preview-glow {
            position: absolute;
            inset: 30px;
            background: #3b82f6;
            filter: blur(70px);
            opacity: .12;
            border-radius: 40px;
        }

        .dashboard-preview {
            position: relative;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            box-shadow:
                0 30px 80px rgba(15, 23, 42, .12),
                0 10px 30px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .preview-header {
            height: 58px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 18px;
            gap: 7px;
        }

        .window-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        .preview-content {
            display: grid;
            grid-template-columns: 105px 1fr;
            min-height: 330px;
        }

        .preview-sidebar {
            background: #0f172a;
            padding: 18px 12px;
        }

        .mini-logo {
            width: 32px;
            height: 32px;
            background: #2563eb;
            border-radius: 9px;
            margin-bottom: 25px;
        }

        .mini-menu {
            height: 8px;
            border-radius: 5px;
            background: #334155;
            margin-bottom: 13px;
        }

        .mini-menu.active {
            background: #3b82f6;
        }

        .preview-main {
            padding: 22px;
            background: #f8fafc;
        }

        .preview-title {
            width: 130px;
            height: 13px;
            background: #0f172a;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .stat {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
        }

        .stat-line {
            width: 45px;
            height: 7px;
            background: #cbd5e1;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .stat-number {
            width: 65px;
            height: 13px;
            background: #0f172a;
            border-radius: 4px;
        }

        .chart {
            margin-top: 12px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            height: 135px;
            position: relative;
            overflow: hidden;
        }

        .chart-bars {
            position: absolute;
            left: 20px;
            right: 20px;
            bottom: 18px;
            height: 75px;
            display: flex;
            align-items: flex-end;
            gap: 9px;
        }

        .bar {
            flex: 1;
            border-radius: 5px 5px 0 0;
            background: #bfdbfe;
        }

        .bar:nth-child(2) { height: 55%; }
        .bar:nth-child(3) { height: 75%; }
        .bar:nth-child(4) { height: 45%; }
        .bar:nth-child(5) { height: 90%; }
        .bar:nth-child(6) { height: 65%; }
        .bar:nth-child(7) { height: 100%; }

        /* =========================
           FEATURES
        ========================= */

        .features {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 80px 24px;
        }

        .section-container {
            max-width: 1180px;
            margin: auto;
        }

        .section-heading {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 45px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 36px;
            letter-spacing: -1.5px;
        }

        .section-heading p {
            color: #64748b;
            line-height: 1.7;
            margin-top: 12px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .feature {
            padding: 25px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            transition: .2s ease;
        }

        .feature:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(15, 23, 42, .07);
        }

        .feature-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: #eff6ff;
            color: #2563eb;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .feature h3 {
            margin: 0;
            font-size: 16px;
        }

        .feature p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.65;
            margin-bottom: 0;
        }

        /* =========================
           FOOTER
        ========================= */

        footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 30px 24px;
        }

        .footer-container {
            max-width: 1180px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .footer-brand {
            color: white;
            font-weight: 800;
        }

        .footer-text {
            font-size: 12px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero {
                padding-top: 65px;
            }
        }

        @media (max-width: 600px) {
            .nav-container {
                padding: 16px;
            }

            .brand-subtitle {
                display: none;
            }

            .btn {
                padding: 10px 13px;
            }

            .hero {
                padding: 55px 18px;
            }

            .hero h1 {
                font-size: 42px;
                letter-spacing: -2px;
            }

            .hero-description {
                font-size: 15px;
            }

            .preview-content {
                grid-template-columns: 75px 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .footer-container {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<div class="page">

    {{-- ========================= NAVBAR ========================= --}}
    <header class="navbar">
        <div class="nav-container">

            <a href="{{ url('/') }}" class="brand">
                <div class="brand-logo">
                    S
                </div>

                <div>
                    <div class="brand-name">SIDIKO</div>
                    <div class="brand-subtitle">
                        Sistem Informasi Digital Kolaboratif
                    </div>
                </div>
            </a>

            <div class="nav-actions">

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="btn btn-primary">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="btn btn-login">
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="btn btn-primary">
                            Mulai Sekarang
                        </a>
                    @endif
                @endauth

            </div>

        </div>
    </header>


    {{-- ========================= HERO ========================= --}}
    <main>

        <section class="hero">

            <div class="hero-container">

                <div>

                    <div class="badge">
                        <span class="badge-dot"></span>
                        Platform Manajemen Sekolah Modern
                    </div>

                    <h1>
                        Kelola sekolah
                        <span>lebih mudah.</span>
                    </h1>

                    <p class="hero-description">
                        SIDIKO adalah platform SaaS yang membantu sekolah
                        mengelola data siswa, akademik, keuangan, pengguna,
                        dan operasional dalam satu sistem yang terintegrasi.
                    </p>

                    <div class="hero-actions">

                        @auth

                            <a href="{{ route('dashboard') }}"
                               class="btn btn-primary">
                                Buka Dashboard →
                            </a>

                        @else

                            <a href="{{ route('login') }}"
                               class="btn btn-primary">
                                Masuk ke SIDIKO →
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="btn btn-login">
                                    Buat Akun
                                </a>
                            @endif

                        @endauth

                    </div>

                    <div class="hero-note">
                        Satu platform untuk seluruh kebutuhan administrasi sekolah.
                    </div>

                </div>


                {{-- ========================= PREVIEW ========================= --}}

                <div class="preview-wrapper">

                    <div class="preview-glow"></div>

                    <div class="dashboard-preview">

                        <div class="preview-header">
                            <span class="window-dot"></span>
                            <span class="window-dot"></span>
                            <span class="window-dot"></span>
                        </div>

                        <div class="preview-content">

                            <aside class="preview-sidebar">

                                <div class="mini-logo"></div>

                                <div class="mini-menu active"></div>
                                <div class="mini-menu"></div>
                                <div class="mini-menu"></div>
                                <div class="mini-menu"></div>
                                <div class="mini-menu"></div>
                                <div class="mini-menu"></div>

                            </aside>

                            <div class="preview-main">

                                <div class="preview-title"></div>

                                <div class="stats">

                                    <div class="stat">
                                        <div class="stat-line"></div>
                                        <div class="stat-number"></div>
                                    </div>

                                    <div class="stat">
                                        <div class="stat-line"></div>
                                        <div class="stat-number"></div>
                                    </div>

                                    <div class="stat">
                                        <div class="stat-line"></div>
                                        <div class="stat-number"></div>
                                    </div>

                                </div>

                                <div class="chart">

                                    <div class="chart-bars">
                                        <div class="bar" style="height:40%"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- ========================= FEATURES ========================= --}}

        <section class="features">

            <div class="section-container">

                <div class="section-heading">

                    <h2>
                        Semua kebutuhan sekolah,
                        satu platform.
                    </h2>

                    <p>
                        SIDIKO dirancang dengan arsitektur modular sehingga
                        sistem dapat berkembang mengikuti kebutuhan sekolah.
                    </p>

                </div>


                <div class="feature-grid">

                    <div class="feature">

                        <div class="feature-icon">
                            S
                        </div>

                        <h3>Manajemen Siswa</h3>

                        <p>
                            Kelola identitas, data tahunan, rombel,
                            dan informasi siswa secara terstruktur.
                        </p>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            A
                        </div>

                        <h3>Akademik</h3>

                        <p>
                            Kelola data akademik sekolah dalam sistem
                            yang terintegrasi.
                        </p>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            $
                        </div>

                        <h3>Keuangan</h3>

                        <p>
                            Tagihan, pembayaran, kas, dan laporan
                            keuangan sekolah dalam satu tempat.
                        </p>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            R
                        </div>

                        <h3>Role & Permission</h3>

                        <p>
                            Setiap pengguna mendapatkan akses sesuai
                            peran dan kewenangannya.
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </main>


    {{-- ========================= FOOTER ========================= --}}

    <footer>

        <div class="footer-container">

            <div>
                <div class="footer-brand">
                    SIDIKO
                </div>

                <div class="footer-text">
                    Sistem Informasi Digital Kolaboratif
                </div>
            </div>

            <div class="footer-text">
                © {{ date('Y') }} SIDIKO. All rights reserved.
            </div>

        </div>

    </footer>

</div>

</body>
</html>