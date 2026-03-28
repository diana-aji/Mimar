<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Mi'mar Admin" }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar: #0f172a;
            --sidebar-soft: #1e293b;
            --bg-main: #f7f8fc;
            --white: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --line: rgba(17, 24, 39, 0.08);
            --shadow-soft: 0 14px 30px rgba(17, 24, 39, 0.06);
        }

        body {
            margin: 0;
            font-family: "Cairo", "Tajawal", sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
        }

        * {
            box-sizing: border-box;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, var(--sidebar), var(--sidebar-soft));
            color: white;
            padding: 26px 18px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            text-decoration: none;
            color: white;
        }

        .admin-brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .admin-brand-name {
            font-size: 22px;
            font-weight: 800;
        }

        .admin-brand-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.72);
            margin-top: 2px;
        }

        .admin-role-badge {
            margin-top: 18px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.08);
            font-size: 12px;
            color: rgba(255,255,255,0.84);
            line-height: 1.8;
        }

        .admin-role-badge strong {
            color: white;
            font-size: 13px;
        }

        .admin-nav {
            display: grid;
            gap: 8px;
            margin-top: 18px;
        }

        .admin-nav a {
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 14px;
            font-weight: 700;
            transition: .18s ease;
        }

        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .admin-nav-group-title {
            margin-top: 14px;
            margin-bottom: 6px;
            padding-inline: 6px;
            color: rgba(255,255,255,0.55);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .admin-sidebar-footer {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: grid;
            gap: 10px;
        }

        .admin-side-btn {
            height: 42px;
            padding: 0 14px;
            border-radius: 12px;
            border: none;
            background: rgba(255,255,255,0.08);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            cursor: pointer;
        }

        .admin-main {
            padding: 24px;
        }

        .admin-topbar {
            min-height: 76px;
            background: white;
            border-radius: 22px;
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .admin-topbar-title {
            font-weight: 800;
            font-size: 20px;
            color: var(--text-main);
        }

        .admin-topbar-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        .admin-topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-topbar-btn {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: white;
            color: var(--text-main);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        @media (max-width: 992px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }

            .admin-main {
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    @php
        $isArabic = app()->getLocale() === 'ar';
        $user = auth()->user();
        $roleName = $user?->getRoleNames()?->first();
    @endphp

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="{{ route('dashboard') }}" class="admin-brand">
                <div class="admin-brand-logo">M</div>
                <div>
                    <div class="admin-brand-name">Mi'mar</div>
                    <div class="admin-brand-sub">
                        {{ $isArabic ? 'لوحة إدارة المنصة' : 'Platform Admin Panel' }}
                    </div>
                </div>
            </a>

            <div class="admin-role-badge">
                <div>{{ $isArabic ? 'الحساب الحالي' : 'Current account' }}</div>
                <strong>{{ $user?->name ?? '—' }}</strong>
                <div>
                    {{ $isArabic ? 'الدور:' : 'Role:' }}
                    <strong>{{ $roleName ?? '—' }}</strong>
                </div>
            </div>

            <nav class="admin-nav">
                <div class="admin-nav-group-title">{{ $isArabic ? 'الرئيسية' : 'Main' }}</div>

                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    {{ $isArabic ? 'لوحة التحكم' : 'Dashboard' }}
                </a>

                @can('view-users')
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'المستخدمون' : 'Users' }}
                    </a>
                @endcan

                @can('view-roles')
                    <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'إدارة الأدوار' : 'Roles' }}
                    </a>
                @endcan

                <div class="admin-nav-group-title">{{ $isArabic ? 'الإدارة' : 'Management' }}</div>

                @can('view-categories')
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'التصنيفات' : 'Categories' }}
                    </a>
                @endcan

                @can('view-subcategories')
                    <a href="{{ route('admin.subcategories.index') }}" class="{{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'التصنيفات الفرعية' : 'Subcategories' }}
                    </a>
                @endcan

                @can('view-cities')
                    <a href="{{ route('admin.cities.index') }}" class="{{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'المدن' : 'Cities' }}
                    </a>
                @endcan

                @can('view-sliders')
                    <a href="{{ route('admin.sliders.index') }}" class="{{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'السلايدر' : 'Sliders' }}
                    </a>
                @endcan

                @can('view-services')
                    <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'الخدمات' : 'Services' }}
                    </a>
                @endcan

                @can('view-business-accounts')
                    <a href="{{ route('admin.business-accounts.index') }}" class="{{ request()->routeIs('admin.business-accounts.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'حسابات الأعمال' : 'Business Accounts' }}
                    </a>
                @endcan

                @can('view-reports')
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'البلاغات' : 'Reports' }}
                    </a>
                @endcan

                <div class="admin-nav-group-title">{{ $isArabic ? 'أدوات إضافية' : 'Utilities' }}</div>

                @can('view-estimation-types')
                    <a href="{{ route('estimations.create') }}" class="{{ request()->routeIs('estimations.*') ? 'active' : '' }}">
                        {{ $isArabic ? 'التقدير الذكي' : 'Estimations' }}
                    </a>
                @endcan

                <a href="{{ route('home') }}" class="admin-topbar-btn">
                    {{ $isArabic ? 'الواجهة الرئيسية' : 'Home' }}
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <a href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}" class="admin-side-btn">
                    {{ $isArabic ? 'English' : 'العربية' }}
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="admin-side-btn" style="width:100%;">
                        {{ $isArabic ? 'تسجيل الخروج' : 'Logout' }}
                    </button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-topbar">
                <div>
                    <div class="admin-topbar-title">
                        {{ $title ?? ($isArabic ? 'لوحة الإدارة' : 'Admin Panel') }}
                    </div>
                    <div class="admin-topbar-subtitle">
                        {{ $isArabic ? 'نظام إدارة منصة Mi\'mar' : 'Mi\'mar platform management system' }}
                    </div>
                </div>

                <div class="admin-topbar-actions">
                    <a href="{{ route('home') }}" class="admin-topbar-btn">
                        {{ $isArabic ? 'الواجهة الرئيسية' : 'Home' }}
                    </a>

                    <a href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}" class="admin-topbar-btn">
                        {{ $isArabic ? 'EN' : 'AR' }}
                    </a>
                </div>
            </div>

            @yield('content')
        </main>
    </div>
</body>
</html>