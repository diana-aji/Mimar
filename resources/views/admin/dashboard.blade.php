@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';

        $stats = [
            [
                'label' => $isArabic ? 'الخدمات' : 'Services',
                'value' => '128',
                'note' => $isArabic ? 'إجمالي الخدمات داخل المنصة' : 'Total services on the platform',
                'icon' => '🛠️',
            ],
            [
                'label' => $isArabic ? 'حسابات الأعمال' : 'Business Accounts',
                'value' => '36',
                'note' => $isArabic ? 'مزودو الخدمة والحسابات التجارية' : 'Providers and business accounts',
                'icon' => '🏢',
            ],
            [
                'label' => $isArabic ? 'الطلبات' : 'Orders',
                'value' => '54',
                'note' => $isArabic ? 'طلبات نشطة وقابلة للمتابعة' : 'Active and trackable orders',
                'icon' => '📦',
            ],
            [
                'label' => $isArabic ? 'التقديرات' : 'Estimations',
                'value' => '22',
                'note' => $isArabic ? 'نتائج تقدير محفوظة' : 'Saved estimation results',
                'icon' => '📐',
            ],
        ];

        $quickActions = [
            [
                'title' => $isArabic ? 'إدارة الخدمات' : 'Manage Services',
                'text' => $isArabic ? 'استعراض الخدمات وإدارتها ومتابعة حالتها.' : 'Browse, review, and manage listed services.',
                'link' => route('admin.services.index'),
                'button' => $isArabic ? 'فتح الخدمات' : 'Open Services',
            ],
            [
                'title' => $isArabic ? 'حسابات الأعمال' : 'Business Accounts',
                'text' => $isArabic ? 'متابعة مقدمي الخدمات والبيانات التجارية.' : 'Track providers and business profile information.',
                'link' => route('admin.business-accounts.index'),
                'button' => $isArabic ? 'عرض الحسابات' : 'View Accounts',
            ],
            [
                'title' => $isArabic ? 'إدارة المستخدمين' : 'Manage Users',
                'text' => $isArabic ? 'إضافة وتعديل وحذف المستخدمين والمدراء.' : 'Create, update, and manage users and admins.',
                'link' => route('admin.users.index'),
                'button' => $isArabic ? 'فتح المستخدمين' : 'Open Users',
            ],
            [
                'title' => $isArabic ? 'إدارة الأدوار' : 'Manage Roles',
                'text' => $isArabic ? 'عرض الأدوار، تعديلها، حذفها، وتحديد الصلاحيات الخاصة بها.' : 'View, edit, delete roles, and assign permissions.',
                'link' => route('admin.roles.index'),
                'button' => $isArabic ? 'فتح الأدوار' : 'Open Roles',
            ],
        ];

        $recentActivities = [
            [
                'title' => $isArabic ? 'تمت إضافة خدمة جديدة ضمن قسم التشطيبات' : 'A new service was added under Finishing',
                'time' => $isArabic ? 'منذ 10 دقائق' : '10 minutes ago',
            ],
            [
                'title' => $isArabic ? 'تم تسجيل حساب أعمال جديد' : 'A new business account has been registered',
                'time' => $isArabic ? 'منذ 25 دقيقة' : '25 minutes ago',
            ],
            [
                'title' => $isArabic ? 'تم إنشاء تقدير جديد لمشروع سكني' : 'A new residential estimation was created',
                'time' => $isArabic ? 'منذ ساعة' : '1 hour ago',
            ],
            [
                'title' => $isArabic ? 'تم تحديث حالة أحد الطلبات' : 'An order status was updated',
                'time' => $isArabic ? 'منذ ساعتين' : '2 hours ago',
            ],
        ];
    @endphp

    <style>
        .admin-dashboard {
            display: grid;
            gap: 22px;
        }

        .dashboard-hero {
            background: linear-gradient(135deg, #151d38 0%, #23346f 100%);
            color: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 24px 54px rgba(24, 40, 72, 0.22);
            position: relative;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            top: -100px;
            inset-inline-end: -80px;
            background: radial-gradient(circle, rgba(255,255,255,0.14), transparent 65%);
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            bottom: -90px;
            inset-inline-start: -70px;
            background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 65%);
        }

        .dashboard-hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 22px;
            align-items: center;
        }

        .dashboard-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.14);
            margin-bottom: 14px;
            font-size: 12px;
            font-weight: 800;
        }

        .dashboard-title {
            margin: 0 0 12px;
            font-size: 38px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .dashboard-copy {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.9;
            max-width: 700px;
        }

        .dashboard-hero-side {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 22px;
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .dashboard-hero-side h3 {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 800;
        }

        .dashboard-hero-list {
            display: grid;
            gap: 10px;
        }

        .dashboard-hero-list div {
            font-size: 14px;
            color: rgba(255,255,255,0.86);
            line-height: 1.8;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            border: 1px solid rgba(15,23,42,0.06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(109,93,246,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .stat-value {
            font-size: 34px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
            letter-spacing: -0.04em;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .stat-note {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 20px;
            align-items: start;
        }

        .dashboard-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            border: 1px solid rgba(15,23,42,0.06);
        }

        .dashboard-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .dashboard-card-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .dashboard-card-head span {
            color: #6b7280;
            font-size: 13px;
        }

        .quick-actions {
            display: grid;
            gap: 14px;
        }

        .quick-action-item {
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 20px;
            padding: 18px;
            background: #fafbff;
        }

        .quick-action-item h3 {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 800;
            color: #1f2937;
        }

        .quick-action-item p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.8;
        }

        .quick-action-item a {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, #6D5DF6, #8d81ff);
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .activity-list {
            display: grid;
            gap: 12px;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .activity-item strong {
            display: block;
            margin-bottom: 4px;
            color: #1f2937;
            font-size: 14px;
        }

        .activity-item span {
            color: #6b7280;
            font-size: 12px;
            white-space: nowrap;
        }

        .activity-text {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .dashboard-hero-content,
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .dashboard-hero,
            .dashboard-card {
                padding: 20px;
                border-radius: 20px;
            }

            .dashboard-title {
                font-size: 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .activity-item {
                flex-direction: column;
            }
        }
    </style>

    <div class="admin-dashboard">
        <section class="dashboard-hero">
            <div class="dashboard-hero-content">
                <div>
                    <span class="dashboard-badge">
                        {{ $isArabic ? 'لوحة الإدارة' : 'Admin Workspace' }}
                    </span>

                    <h1 class="dashboard-title">
                        {{ $isArabic ? 'إدارة منصة Mi\'mar من مكان واحد' : 'Manage the Mi\'mar platform from one place' }}
                    </h1>

                    <p class="dashboard-copy">
                        {{ $isArabic
                            ? 'من هذه اللوحة يمكنك متابعة الخدمات، حسابات الأعمال، الطلبات، المستخدمين، والأدوار ضمن تجربة إدارة مرتبة وواضحة.'
                            : 'From this dashboard you can monitor services, business accounts, orders, users, and roles in one organized management experience.' }}
                    </p>
                </div>

                <div class="dashboard-hero-side">
                    <h3>{{ $isArabic ? 'وصول سريع' : 'Quick Access' }}</h3>
                    <div class="dashboard-hero-list">
                        <div>{{ $isArabic ? '• متابعة حالة الخدمات والطلبات' : '• Monitor services and orders status' }}</div>
                        <div>{{ $isArabic ? '• إدارة الحسابات التجارية والمستخدمين' : '• Manage business accounts and users' }}</div>
                        <div>{{ $isArabic ? '• إدارة الأدوار والصلاحيات' : '• Manage roles and permissions' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            @foreach ($stats as $stat)
                <div class="stat-card">
                    <div class="stat-icon">{{ $stat['icon'] }}</div>
                    <div class="stat-value">{{ $stat['value'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                    <div class="stat-note">{{ $stat['note'] }}</div>
                </div>
            @endforeach
        </section>

        <section class="dashboard-grid">
            <div class="dashboard-card">
                <div class="dashboard-card-head">
                    <h2>{{ $isArabic ? 'اختصارات الإدارة' : 'Admin Shortcuts' }}</h2>
                    <span>{{ $isArabic ? 'تنقل سريع داخل المنصة' : 'Quick navigation inside the platform' }}</span>
                </div>

                <div class="quick-actions">
                    @foreach ($quickActions as $action)
                        <div class="quick-action-item">
                            <h3>{{ $action['title'] }}</h3>
                            <p>{{ $action['text'] }}</p>
                            <a href="{{ $action['link'] }}">{{ $action['button'] }}</a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-head">
                    <h2>{{ $isArabic ? 'النشاط الأخير' : 'Recent Activity' }}</h2>
                    <span>{{ $isArabic ? 'آخر التحديثات على المنصة' : 'Latest platform updates' }}</span>
                </div>

                <div class="activity-list">
                    @foreach ($recentActivities as $activity)
                        <div class="activity-item">
                            <div>
                                <strong>{{ $activity['title'] }}</strong>
                                <div class="activity-text">
                                    {{ $isArabic ? 'تم تسجيل هذا الحدث ضمن النظام الإداري.' : 'This event has been recorded in the admin system.' }}
                                </div>
                            </div>
                            <span>{{ $activity['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection