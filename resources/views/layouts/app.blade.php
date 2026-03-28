<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Mi'mar" }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --line: rgba(15,23,42,0.08);

            --primary: #4458db;
            --primary-dark: #243873;
            --primary-soft: rgba(68,88,219,0.08);

            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;

            --shadow-soft: 0 12px 30px rgba(15,23,42,0.05);
            --shadow-main: 0 18px 40px rgba(15,23,42,0.08);

            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "IBM Plex Sans Arabic", "Inter", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(68,88,219,0.06), transparent 20%),
                radial-gradient(circle at bottom right, rgba(15,23,42,0.04), transparent 24%),
                var(--bg);
            color: var(--text);
        }

        a, button, input {
            font-family: inherit;
        }

        .app-shell {
            min-height: 100vh;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.82);
            border-bottom: 1px solid rgba(15,23,42,0.06);
        }

        .app-header-inner {
            max-width: 1360px;
            margin: 0 auto;
            min-height: 78px;
            padding: 14px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
            flex-shrink: 0;
        }

        .app-brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
        }

        .app-brand-name {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #24304d;
        }

        .app-brand-sub {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .app-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1;
        }

        .app-nav a {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            transition: .18s ease;
            white-space: nowrap;
        }

        .app-nav a:hover,
        .app-nav a.active {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .app-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-icon-btn {
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(15,23,42,0.04);
            transition: .18s ease;
        }

        .header-icon-btn:hover {
            background: #f8fafc;
        }

        .logout-btn {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: none;
            background: #fff1f2;
            color: var(--danger);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .18s ease;
        }

        .logout-btn:hover {
            background: #ffe4e6;
        }

        .app-content {
            max-width: 1360px;
            margin: 0 auto;
            padding: 24px 22px 34px;
        }

        @media (max-width: 1023px) {
            .app-header-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .app-nav,
            .app-header-actions {
                width: 100%;
            }
        }

        @media (max-width: 767px) {
            .app-header-inner {
                padding: 14px 16px;
            }

            .app-nav {
                justify-content: flex-start;
            }

            .app-content {
                padding: 18px 16px 24px;
            }
        }
    </style>
</head>
<body>
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <div class="app-shell">
        <header class="app-header">
            <div class="app-header-inner">
                <a href="{{ auth()->check() ? route('home') : route('welcome') }}" class="app-brand">
                    <div class="app-brand-mark">M</div>
                    <div>
                        <h1 class="app-brand-name">Mi'mar</h1>
                        <p class="app-brand-sub">
                            {{ $isArabic ? 'منصة الخدمات العقارية والتقدير الذكي' : 'Real Estate Services & Smart Estimation' }}
                        </p>
                    </div>
                </a>

                <nav class="app-nav">
                    @guest
                        <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'active' : '' }}">
                            {{ $isArabic ? 'الرئيسية' : 'Home' }}
                        </a>

                        <a href="{{ route('login') }}" class="{{ request()->routeIs('login') || request()->routeIs('otp.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'دخول المستخدم' : 'User Login' }}
                        </a>

                        <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
                            {{ $isArabic ? 'إنشاء حساب' : 'Register' }}
                        </a>
                    @endguest

                    @auth
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            {{ $isArabic ? 'الرئيسية' : 'Home' }}
                        </a>

                        <a href="{{ route('business-account.index') }}" class="{{ request()->routeIs('business-account.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'حساب الأعمال' : 'Business Account' }}
                        </a>

                        <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'التصنيفات' : 'Categories' }}
                        </a>

                        <a href="{{ route('estimations.create') }}" class="{{ request()->routeIs('estimations.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'التقدير الذكي' : 'Estimations' }}
                        </a>

                        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'الطلبات' : 'Orders' }}
                        </a>

                        <a href="{{ route('favorites.index') }}" class="{{ request()->routeIs('favorites.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'المفضلة' : 'Favorites' }}
                        </a>

                        <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            {{ $isArabic ? 'المحادثات' : 'Chat' }}
                        </a>

                        <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                            {{ $isArabic ? 'حسابي' : 'Profile' }}
                        </a>
                    @endauth
                </nav>

                <div class="app-header-actions">
                    <a href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}" class="header-icon-btn">
                        {{ $isArabic ? 'EN' : 'AR' }}
                    </a>

                    @auth
                        <a href="{{ route('notifications.index') }}" class="header-icon-btn">🔔</a>

                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="logout-btn">
                                {{ $isArabic ? 'تسجيل الخروج' : 'Logout' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="header-icon-btn">
                            {{ $isArabic ? 'دخول' : 'Login' }}
                        </a>

                        <a href="{{ route('register') }}" class="header-icon-btn">
                            {{ $isArabic ? 'إنشاء حساب' : 'Register' }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="app-content">
            @yield('content')
        </main>
    </div>
</body>
</html>