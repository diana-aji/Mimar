@extends('layouts.app')

@section('content')
@php
    $isArabic = app()->getLocale() === 'ar';
    $featuredServices = $featuredServices ?? collect();
    $sliders = $sliders ?? collect();
    $businessAccount = $businessAccount ?? null;

    $recentRequests = [
        [
            'title' => __('ui.request_1_title'),
            'meta' => __('ui.request_1_meta'),
            'status' => __('ui.status_in_progress'),
            'status_class' => 'status-progress',
        ],
        [
            'title' => __('ui.request_2_title'),
            'meta' => __('ui.request_2_meta'),
            'status' => __('ui.status_open'),
            'status_class' => 'status-open',
        ],
        [
            'title' => __('ui.request_3_title'),
            'meta' => __('ui.request_3_meta'),
            'status' => __('ui.status_completed'),
            'status_class' => 'status-complete',
        ],
    ];

    $activities = [
        __('ui.activity_1'),
        __('ui.activity_2'),
        __('ui.activity_3'),
        __('ui.activity_4'),
    ];

    $resolveServiceTitle = function ($service) use ($isArabic) {
        return $isArabic
            ? ($service->name_ar ?? $service->name_en ?? '—')
            : ($service->name_en ?? $service->name_ar ?? '—');
    };

    $resolveServiceDescription = function ($service) {
        return $service->description ?? '';
    };

    $resolveProviderName = function ($service) use ($isArabic) {
        if (! $service->businessAccount) {
            return $isArabic ? 'مزود خدمة' : 'Service provider';
        }

        return $isArabic
            ? ($service->businessAccount->name_ar ?? $service->businessAccount->name_en ?? 'مزود خدمة')
            : ($service->businessAccount->name_en ?? $service->businessAccount->name_ar ?? 'Service provider');
    };

    $resolveCategoryBadge = function ($service) use ($isArabic) {
        if ($service->subcategory) {
            return $isArabic
                ? ($service->subcategory->name_ar ?? $service->subcategory->name_en ?? 'خدمة')
                : ($service->subcategory->name_en ?? $service->subcategory->name_ar ?? 'Service');
        }

        if ($service->category) {
            return $isArabic
                ? ($service->category->name_ar ?? $service->category->name_en ?? 'خدمة')
                : ($service->category->name_en ?? $service->category->name_ar ?? 'Service');
        }

        return $isArabic ? 'خدمة' : 'Service';
    };

    $resolveServiceImage = function ($service) {
        return $service->primaryImage()?->path;
    };

    $resolveImageUrl = function ($image) {
        if (! $image) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return asset('storage/' . ltrim($image, '/'));
    };

    $fallbackImages = [
        'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
    ];

    $businessStatusLabel = function ($status) use ($isArabic) {
        return match ($status) {
            'approved' => $isArabic ? 'حساب الأعمال مفعل' : 'Business account active',
            'rejected' => $isArabic ? 'تم رفض الطلب' : 'Request rejected',
            default => $isArabic ? 'قيد المراجعة' : 'Under review',
        };
    };

    $businessStatusText = function ($status) use ($isArabic) {
        return match ($status) {
            'approved' => $isArabic
                ? 'يمكنك الآن إدارة خدماتك واستقبال الطلبات من خلال حساب الأعمال.'
                : 'You can now manage services and receive requests through your business account.',
            'rejected' => $isArabic
                ? 'تم رفض طلب حساب الأعمال السابق. يمكنك مراجعة البيانات وإرسال طلب جديد.'
                : 'Your previous business account request was rejected. You can review the details and submit a new request.',
            default => $isArabic
                ? 'طلب حساب الأعمال الخاص بك قيد المراجعة من قبل الإدارة.'
                : 'Your business account request is currently being reviewed by the admin team.',
        };
    };

    $businessStatusClass = function ($status) {
        return match ($status) {
            'approved' => 'business-status-approved',
            'rejected' => 'business-status-rejected',
            default => 'business-status-pending',
        };
    };
@endphp

<style>
    .dashboard-v2 { display:grid; gap:24px; }

    .hero-slider-v2 {
        position: relative;
        overflow: hidden;
        border-radius: 34px;
        min-height: 420px;
        box-shadow: 0 28px 70px rgba(15,23,42,0.18);
    }

    .hero-slide-v2 {
        position: absolute;
        inset: 0;
        opacity: 0;
        visibility: hidden;
        transition: opacity .7s ease, visibility .7s ease;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: end;
        isolation: isolate;
    }

    .hero-slide-v2.active {
        opacity: 1;
        visibility: visible;
        z-index: 1;
    }

    .hero-slide-v2::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(135deg, rgba(10,16,30,.90) 0%, rgba(21,35,67,.80) 45%, rgba(39,57,101,.70) 100%);
    }

    .hero-slide-v2::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
        background-size: 42px 42px;
        mask-image: linear-gradient(to bottom, rgba(0,0,0,.25), rgba(0,0,0,.92));
        pointer-events: none;
    }

    .hero-slide-inner-v2 {
        position: relative;
        z-index: 2;
        width: 100%;
        padding: 34px;
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 22px;
        align-items: end;
        color: white;
    }

    .hero-v2-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.14);
        margin-bottom: 16px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .hero-v2-badge::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d4a95f;
        box-shadow: 0 0 0 6px rgba(212,169,95,.14);
    }

    .hero-v2-title {
        margin: 0 0 14px;
        max-width: 760px;
        font-size: 46px;
        line-height: 1.04;
        letter-spacing: -0.05em;
        font-weight: 800;
    }

    .hero-v2-copy {
        margin: 0;
        max-width: 760px;
        color: rgba(255,255,255,0.84);
        font-size: 15px;
        line-height: 1.95;
    }

    .hero-v2-actions {
        margin-top: 20px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .hero-v2-btn-primary,
    .hero-v2-btn-secondary {
        height: 48px;
        padding: 0 18px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 14px;
        font-weight: 800;
        transition: .2s ease;
    }

    .hero-v2-btn-primary {
        background: white;
        color: #1f2f4d;
        box-shadow: 0 16px 28px rgba(255,255,255,0.12);
    }

    .hero-v2-btn-secondary {
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.16);
        color: white;
    }

    .hero-v2-panel {
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.12);
        backdrop-filter: blur(12px);
        border-radius: 26px;
        padding: 20px;
    }

    .hero-v2-panel-title {
        margin: 0 0 14px;
        font-size: 18px;
        font-weight: 800;
        color: white;
    }

    .hero-v2-list {
        display: grid;
        gap: 12px;
    }

    .hero-v2-list-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: rgba(255,255,255,0.86);
        font-size: 14px;
        line-height: 1.8;
    }

    .hero-v2-list-item::before {
        content: "•";
        font-size: 20px;
        line-height: 1;
        color: #d4a95f;
        margin-top: -1px;
    }

    .hero-dots-v2 {
        position: absolute;
        z-index: 5;
        bottom: 20px;
        inset-inline-start: 34px;
        display: flex;
        gap: 10px;
    }

    .hero-dot-v2 {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        background: rgba(255,255,255,0.35);
    }

    .hero-dot-v2.active {
        background: white;
        transform: scale(1.08);
    }

    .quick-shortcuts-v2 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .shortcut-card-v2 {
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 24px;
        padding: 22px;
        box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        text-decoration: none;
        color: inherit;
        transition: .2s ease;
    }

    .shortcut-card-v2:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 36px rgba(15,23,42,0.08);
    }

    .shortcut-icon-v2 {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        margin-bottom: 14px;
        background: rgba(68,88,219,0.08);
        color: #4458db;
        font-size: 20px;
        font-weight: 800;
    }

    .shortcut-title-v2 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 800;
        color: #24304d;
    }

    .shortcut-text-v2 {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
    }

    .business-cta-card {
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 28px;
        padding: 24px;
        box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 18px;
        align-items: center;
    }

    .business-cta-title {
        margin: 0 0 10px;
        font-size: 28px;
        font-weight: 800;
        color: #24304d;
        line-height: 1.1;
    }

    .business-cta-text {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.95;
    }

    .business-cta-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .business-cta-btn,
    .business-cta-btn-secondary {
        height: 44px;
        padding: 0 16px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
    }

    .business-cta-btn {
        background: linear-gradient(135deg, #4458db 0%, #243873 100%);
        color: white;
    }

    .business-cta-btn-secondary {
        background: #f8fafc;
        color: #334155;
        border: 1px solid rgba(15,23,42,0.08);
    }

    .business-status-box {
        padding: 18px;
        border-radius: 24px;
        border: 1px solid rgba(15,23,42,0.06);
    }

    .business-status-box h3 {
        margin: 0 0 10px;
        font-size: 18px;
        font-weight: 800;
        color: #24304d;
    }

    .business-status-box p {
        margin: 0;
        color: #475569;
        font-size: 14px;
        line-height: 1.9;
    }

    .business-status-approved {
        background: rgba(5,150,105,0.08);
    }

    .business-status-pending {
        background: rgba(245,158,11,0.10);
    }

    .business-status-rejected {
        background: rgba(239,68,68,0.08);
    }

    .stats-v2 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .stat-v2 {
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 24px;
        padding: 22px;
        box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        transition: .2s ease;
    }

    .stat-v2-label {
        display: block;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .stat-v2-number {
        font-size: 34px;
        font-weight: 800;
        color: #1f2f4d;
        letter-spacing: -0.04em;
        margin-bottom: 6px;
    }

    .stat-v2-note {
        color: #64748b;
        font-size: 12px;
        line-height: 1.8;
    }

    .dashboard-v2-grid {
        display: grid;
        grid-template-columns: 1.12fr 0.88fr;
        gap: 20px;
        align-items: start;
    }

    .panel-v2,
    .feature-card-v2,
    .cta-v2 {
        background: rgba(255,255,255,0.98);
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 28px;
        box-shadow: 0 12px 30px rgba(15,23,42,0.05);
    }

    .panel-v2 { padding: 24px; }

    .panel-v2-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .panel-v2-title {
        margin: 0;
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        color: #24304d;
    }

    .panel-v2-link {
        color: #4458db;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
    }

    .services-v2-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .feature-card-v2 {
        overflow: hidden;
        transition: .2s ease;
    }

    .feature-card-v2 img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    .feature-card-v2-body { padding: 18px; }

    .feature-card-v2-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(68,88,219,0.08);
        color: #4458db;
        font-size: 12px;
        font-weight: 700;
    }

    .feature-card-v2-title {
        margin: 0 0 8px;
        font-size: 20px;
        font-weight: 800;
        color: #24304d;
    }

    .feature-card-v2-text {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.85;
        min-height: 78px;
    }

    .feature-card-v2-footer {
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .feature-card-v2-provider {
        display: grid;
        gap: 2px;
    }

    .feature-card-v2-provider strong {
        font-size: 14px;
        color: #24304d;
    }

    .feature-card-v2-provider span {
        font-size: 12px;
        color: #64748b;
    }

    .feature-card-v2-price {
        font-size: 16px;
        font-weight: 800;
        color: #243873;
    }

    .feature-card-v2-actions {
        margin-top: 14px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-v2-primary,
    .btn-v2-secondary {
        height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }

    .btn-v2-primary {
        background: linear-gradient(135deg, #4458db 0%, #243873 100%);
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-v2-secondary {
        background: #f8fafc;
        color: #334155;
        border: 1px solid rgba(15,23,42,0.08);
    }

    .right-v2-stack {
        display: grid;
        gap: 18px;
    }

    .cta-v2 {
        padding: 24px;
        background: linear-gradient(135deg, #182848 0%, #243b73 100%);
        color: white;
        box-shadow: 0 24px 54px rgba(24,40,72,0.22);
    }

    .cta-v2 h3 {
        margin: 0 0 10px;
        font-size: 28px;
        line-height: 1.1;
        font-weight: 800;
    }

    .cta-v2 p {
        margin: 0;
        color: rgba(255,255,255,0.82);
        font-size: 14px;
        line-height: 1.9;
    }

    .cta-v2-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cta-v2-actions a {
        height: 42px;
        padding: 0 14px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
    }

    .cta-v2-actions .light {
        background: white;
        color: #243873;
    }

    .cta-v2-actions .ghost {
        background: rgba(255,255,255,0.10);
        color: white;
        border: 1px solid rgba(255,255,255,0.16);
    }

    .list-v2 {
        display: grid;
        gap: 14px;
    }

    .list-v2-item {
        display: grid;
        gap: 8px;
        padding: 16px;
        border-radius: 20px;
        background: #f8fafc;
        border: 1px solid rgba(15,23,42,0.06);
    }

    .list-v2-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .list-v2-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #24304d;
    }

    .list-v2-meta {
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
    }

    .status-pill-v2 {
        height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-open {
        background: rgba(59,130,246,0.10);
        color: #2563eb;
    }

    .status-progress {
        background: rgba(245,158,11,0.12);
        color: #d97706;
    }

    .status-complete {
        background: rgba(5,150,105,0.10);
        color: #059669;
    }

    .empty-services {
        padding: 20px;
        border-radius: 18px;
        background: #f8fafc;
        color: #64748b;
        font-size: 14px;
        line-height: 1.9;
        border: 1px dashed rgba(15,23,42,0.10);
    }

    @media (max-width: 1200px) {
        .stats-v2,
        .quick-shortcuts-v2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1100px) {
        .hero-slide-inner-v2,
        .dashboard-v2-grid,
        .business-cta-card {
            grid-template-columns: 1fr;
        }

        .services-v2-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 767px) {
        .hero-slider-v2 {
            min-height: 460px;
            border-radius: 26px;
        }

        .hero-slide-inner-v2,
        .panel-v2,
        .cta-v2,
        .business-cta-card {
            padding: 20px;
        }

        .hero-v2-title {
            font-size: 30px;
        }

        .stats-v2,
        .services-v2-grid,
        .quick-shortcuts-v2 {
            grid-template-columns: 1fr;
        }

        .panel-v2-title,
        .business-cta-title {
            font-size: 22px;
        }

        .hero-dots-v2 {
            inset-inline-start: 20px;
        }
    }
</style>

<div class="dashboard-v2">
    @if ($sliders->count())
        <section class="hero-slider-v2" id="heroSlider">
            @foreach ($sliders as $index => $slider)
                @php
                    $slideImage = \Illuminate\Support\Str::startsWith($slider->image, ['http://', 'https://'])
                        ? $slider->image
                        : asset('storage/' . ltrim($slider->image, '/'));

                    $slideTitle = $isArabic ? ($slider->title_ar ?? '') : ($slider->title_en ?? '');
                    $slideSubtitle = $isArabic ? ($slider->subtitle_ar ?? '') : ($slider->subtitle_en ?? '');
                    $slideButtonText = $isArabic
                        ? ($slider->button_text_ar ?: __('ui.start_estimation'))
                        : ($slider->button_text_en ?: __('ui.start_estimation'));
                    $slideButtonUrl = $slider->button_url ?: route('estimations.create');
                @endphp

                <div class="hero-slide-v2 {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $slideImage }}');">
                    <div class="hero-slide-inner-v2">
                        <div>
                            <span class="hero-v2-badge">{{ __('ui.workspace_overview') }}</span>
                            <h1 class="hero-v2-title">{{ $slideTitle }}</h1>
                            <p class="hero-v2-copy">{{ $slideSubtitle }}</p>

                            <div class="hero-v2-actions">
                                <a href="{{ $slideButtonUrl }}" class="hero-v2-btn-primary">{{ $slideButtonText }}</a>
                                <a href="{{ route('services.index') }}" class="hero-v2-btn-secondary">{{ __('ui.explore_services') }}</a>
                            </div>
                        </div>

                        <div class="hero-v2-panel">
                            <h3 class="hero-v2-panel-title">{{ __('ui.what_you_can_do_now') }}</h3>
                            <div class="hero-v2-list">
                                <div class="hero-v2-list-item">{{ __('ui.hero_action_1') }}</div>
                                <div class="hero-v2-list-item">{{ __('ui.hero_action_2') }}</div>
                                <div class="hero-v2-list-item">{{ __('ui.hero_action_3') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="hero-dots-v2">
                @foreach ($sliders as $index => $slider)
                    <button class="hero-dot-v2 {{ $index === 0 ? 'active' : '' }}" type="button" data-slide="{{ $index }}"></button>
                @endforeach
            </div>
        </section>
    @else
        <section class="hero-slider-v2">
            <div class="hero-slide-v2 active" style="background-image: url('https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1600&q=80');">
                <div class="hero-slide-inner-v2">
                    <div>
                        <span class="hero-v2-badge">{{ __('ui.workspace_overview') }}</span>
                        <h1 class="hero-v2-title">{{ __('ui.dashboard_hero_title') }}</h1>
                        <p class="hero-v2-copy">{{ __('ui.dashboard_hero_copy') }}</p>

                        <div class="hero-v2-actions">
                            <a href="{{ route('estimations.create') }}" class="hero-v2-btn-primary">{{ __('ui.start_estimation') }}</a>
                            <a href="{{ route('services.index') }}" class="hero-v2-btn-secondary">{{ __('ui.explore_services') }}</a>
                        </div>
                    </div>

                    <div class="hero-v2-panel">
                        <h3 class="hero-v2-panel-title">{{ __('ui.what_you_can_do_now') }}</h3>
                        <div class="hero-v2-list">
                            <div class="hero-v2-list-item">{{ __('ui.hero_action_1') }}</div>
                            <div class="hero-v2-list-item">{{ __('ui.hero_action_2') }}</div>
                            <div class="hero-v2-list-item">{{ __('ui.hero_action_3') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="quick-shortcuts-v2">
        <a href="{{ route('services.index') }}" class="shortcut-card-v2">
            <div class="shortcut-icon-v2">🏷</div>
            <h3 class="shortcut-title-v2">{{ $isArabic ? 'الخدمات' : 'Services' }}</h3>
            <p class="shortcut-text-v2">{{ $isArabic ? 'استعرض الخدمات المتوفرة وابحث عن الأنسب لمشروعك.' : 'Browse available services and find the best fit for your project.' }}</p>
        </a>

        <a href="{{ route('estimations.create') }}" class="shortcut-card-v2">
            <div class="shortcut-icon-v2">📐</div>
            <h3 class="shortcut-title-v2">{{ $isArabic ? 'التقدير الذكي' : 'Smart Estimation' }}</h3>
            <p class="shortcut-text-v2">{{ $isArabic ? 'احسب الكميات والتكلفة قبل البدء بالتنفيذ.' : 'Estimate quantities and cost before execution.' }}</p>
        </a>

        <a href="{{ route('orders.index') }}" class="shortcut-card-v2">
            <div class="shortcut-icon-v2">📦</div>
            <h3 class="shortcut-title-v2">{{ $isArabic ? 'الطلبات' : 'Orders' }}</h3>
            <p class="shortcut-text-v2">{{ $isArabic ? 'تابع طلباتك الحالية وتفاصيل حالتها بسهولة.' : 'Track your current requests and their latest status.' }}</p>
        </a>

        <a href="{{ route('chat.index') }}" class="shortcut-card-v2">
            <div class="shortcut-icon-v2">💬</div>
            <h3 class="shortcut-title-v2">{{ $isArabic ? 'المحادثات' : 'Chat' }}</h3>
            <p class="shortcut-text-v2">{{ $isArabic ? 'تواصل مع مزودي الخدمات ضمن مكان واحد.' : 'Communicate with service providers in one place.' }}</p>
        </a>
    </section>

    <section class="business-cta-card">
        <div>
            <h2 class="business-cta-title">
                @if (! $businessAccount)
                    {{ $isArabic ? 'ابدأ بطلب حساب أعمال' : 'Start your business account request' }}
                @else
                    {{ $businessStatusLabel($businessAccount->status) }}
                @endif
            </h2>

            <p class="business-cta-text">
                @if (! $businessAccount)
                    {{ $isArabic
                        ? 'حسابك الحالي هو حساب مستخدم عادي. إذا أردت نشر خدماتك واستقبال الطلبات داخل المنصة، قدّم طلب حساب أعمال من هنا.'
                        : 'Your current account is a regular user account. If you want to publish your services and receive requests on the platform, submit a business account request from here.' }}
                @else
                    {{ $businessStatusText($businessAccount->status) }}
                @endif
            </p>

            <div class="business-cta-actions">
                <a href="{{ route('business-account.index') }}" class="business-cta-btn">
                    {{ $isArabic ? 'فتح حساب الأعمال' : 'Open business account' }}
                </a>

                @if (! $businessAccount || $businessAccount->status === 'rejected')
                    <a href="{{ route('business-account.index', ['new' => 1]) }}" class="business-cta-btn-secondary">
                        {{ $isArabic ? 'إرسال طلب جديد' : 'Submit new request' }}
                    </a>
                @endif
            </div>
        </div>

        <div class="business-status-box {{ $businessAccount ? $businessStatusClass($businessAccount->status) : 'business-status-pending' }}">
            <h3>
                {{ ! $businessAccount
                    ? ($isArabic ? 'الحالة الحالية' : 'Current status')
                    : $businessStatusLabel($businessAccount->status) }}
            </h3>

            <p>
                @if (! $businessAccount)
                    {{ $isArabic
                        ? 'لا يوجد لديك طلب حساب أعمال حتى الآن. يمكنك البدء بإرسال البيانات والوثائق المطلوبة ليتم مراجعتها من الإدارة.'
                        : 'You do not have a business account request yet. You can start by submitting the required details and documents for admin review.' }}
                @else
                    {{ $businessStatusText($businessAccount->status) }}
                @endif
            </p>
        </div>
    </section>

    <section class="stats-v2">
        <div class="stat-v2">
            <span class="stat-v2-label">{{ __('ui.open_requests') }}</span>
            <div class="stat-v2-number">08</div>
            <div class="stat-v2-note">{{ __('ui.open_requests_note') }}</div>
        </div>

        <div class="stat-v2">
            <span class="stat-v2-label">{{ __('ui.matched_services') }}</span>
            <div class="stat-v2-number">{{ $featuredServices->count() }}</div>
            <div class="stat-v2-note">{{ __('ui.matched_services_note') }}</div>
        </div>

        <div class="stat-v2">
            <span class="stat-v2-label">{{ __('ui.active_conversations') }}</span>
            <div class="stat-v2-number">05</div>
            <div class="stat-v2-note">{{ __('ui.active_conversations_note') }}</div>
        </div>

        <div class="stat-v2">
            <span class="stat-v2-label">{{ __('ui.saved_estimates') }}</span>
            <div class="stat-v2-number">12</div>
            <div class="stat-v2-note">{{ __('ui.saved_estimates_note') }}</div>
        </div>
    </section>

    <section class="dashboard-v2-grid">
        <div class="panel-v2">
            <div class="panel-v2-header">
                <h2 class="panel-v2-title">{{ __('ui.featured_services') }}</h2>
                <a href="{{ route('services.index') }}" class="panel-v2-link">{{ __('ui.view_all') }}</a>
            </div>

            @if ($featuredServices->count())
                <div class="services-v2-grid">
                    @foreach ($featuredServices as $service)
                        @php
                            $title = $resolveServiceTitle($service);
                            $description = $resolveServiceDescription($service);
                            $providerName = $resolveProviderName($service);
                            $badge = $resolveCategoryBadge($service);
                            $image = $resolveImageUrl($resolveServiceImage($service));
                            $cardImage = $image ?: $fallbackImages[$loop->index % count($fallbackImages)];
                        @endphp

                        <article class="feature-card-v2">
                            <img src="{{ $cardImage }}" alt="{{ $title }}">

                            <div class="feature-card-v2-body">
                                <span class="feature-card-v2-badge">{{ $badge }}</span>

                                <h3 class="feature-card-v2-title">{{ $title }}</h3>
                                <p class="feature-card-v2-text">
                                    {{ \Illuminate\Support\Str::limit($description ?: ($isArabic ? 'لا يوجد وصف متاح حاليًا لهذه الخدمة.' : 'No description is currently available for this service.'), 120) }}
                                </p>

                                <div class="feature-card-v2-footer">
                                    <div class="feature-card-v2-provider">
                                        <strong>{{ $providerName }}</strong>
                                        <span>{{ __('ui.verified_provider') }}</span>
                                    </div>

                                    <div class="feature-card-v2-price">
                                        {{ $service->price ?? '—' }}
                                    </div>
                                </div>

                                <div class="feature-card-v2-actions">
                                    <a href="{{ route('services.show', $service->id) }}" class="btn-v2-secondary">
                                        {{ __('ui.details') }}
                                    </a>

                                    <form method="POST" action="{{ route('orders.store', $service->id) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn-v2-primary">
                                            {{ __('ui.send_request') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-services">
                    {{ $isArabic ? 'لا توجد خدمات مميزة متاحة حاليًا.' : 'No featured services are available right now.' }}
                </div>
            @endif
        </div>

        <div class="right-v2-stack">
            <div class="cta-v2">
                <h3>{{ __('ui.smart_estimation_title') }}</h3>
                <p>{{ __('ui.smart_estimation_copy') }}</p>

                <div class="cta-v2-actions">
                    <a href="{{ route('estimations.create') }}" class="light">{{ __('ui.start_now') }}</a>
                    <a href="{{ route('services.index') }}" class="ghost">{{ __('ui.browse_services') }}</a>
                </div>
            </div>

            <div class="panel-v2">
                <div class="panel-v2-header">
                    <h2 class="panel-v2-title">{{ __('ui.latest_requests') }}</h2>
                    <a href="{{ route('orders.index') }}" class="panel-v2-link">{{ __('ui.view_all') }}</a>
                </div>

                <div class="list-v2">
                    @foreach ($recentRequests as $request)
                        <div class="list-v2-item">
                            <div class="list-v2-head">
                                <h3 class="list-v2-title">{{ $request['title'] }}</h3>
                                <span class="status-pill-v2 {{ $request['status_class'] }}">{{ $request['status'] }}</span>
                            </div>

                            <div class="list-v2-meta">{{ $request['meta'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel-v2">
                <div class="panel-v2-header">
                    <h2 class="panel-v2-title">{{ __('ui.recent_activity') }}</h2>
                    <a href="{{ route('notifications.index') }}" class="panel-v2-link">{{ __('ui.notifications') }}</a>
                </div>

                <div class="list-v2">
                    @foreach ($activities as $activity)
                        <div class="list-v2-item">
                            <div class="list-v2-title">{{ $activity }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

@if ($sliders->count() > 1)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.getElementById('heroSlider');
        if (!slider) return;

        const slides = slider.querySelectorAll('.hero-slide-v2');
        const dots = slider.querySelectorAll('.hero-dot-v2');
        let current = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });

            current = index;
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', function () {
                showSlide(index);
            });
        });

        setInterval(function () {
            let next = current + 1;
            if (next >= slides.length) next = 0;
            showSlide(next);
        }, 4000);
    });
</script>
@endif
@endsection