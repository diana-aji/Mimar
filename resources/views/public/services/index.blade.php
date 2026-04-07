@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $favoriteIds = $favoriteIds ?? [];
        $displayCurrency = session('display_currency', 'SYP');

        $statusLabel = function ($status) use ($isArabic) {
            return match ($status) {
                'approved' => $isArabic ? 'مقبولة' : 'Approved',
                'pending' => $isArabic ? 'قيد المراجعة' : 'Pending review',
                'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                default => $status ?? '—',
            };
        };

        $statusClass = function ($status) {
            return match ($status) {
                'approved' => 'service-status-approved',
                'pending' => 'service-status-pending',
                'rejected' => 'service-status-rejected',
                default => '',
            };
        };

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

        $resolveCategoryLabel = function ($service) use ($isArabic) {
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

        $fallbackImages = [
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
        ];
    @endphp

    <style>
        .services-shell { display: grid; gap: 22px; }

        .services-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            background:
                linear-gradient(135deg, rgba(12,18,32,.88) 0%, rgba(20,32,61,.76) 45%, rgba(34,52,95,.68) 100%),
                radial-gradient(circle at top right, rgba(255,255,255,0.10), transparent 22%);
            box-shadow: 0 26px 60px rgba(15,23,42,0.18);
            color: white;
            padding: 30px;
        }

        .services-hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
            align-items: end;
        }

        .services-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            margin-bottom: 16px;
            font-size: 12px;
            font-weight: 700;
        }

        .services-badge::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d4a95f;
        }

        .services-title {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.08;
            letter-spacing: -0.04em;
            font-weight: 800;
        }

        .services-copy {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.95;
            max-width: 720px;
        }

        .services-mini-panel {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .services-mini-panel h3 {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 800;
        }

        .services-mini-list { display: grid; gap: 10px; }

        .services-mini-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
        }

        .services-mini-item strong {
            color: white;
            font-size: 18px;
        }

        .services-grid-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 26px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            padding: 22px;
        }

        .services-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .panel-title {
            margin: 0;
            font-size: 24px;
            line-height: 1.1;
            font-weight: 800;
            color: #24304d;
        }

        .services-count {
            color: #64748b;
            font-size: 14px;
        }

        .display-currency-form {
            margin: 0;
        }

        .display-currency-select {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: #f8fafc;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            outline: none;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .service-card {
            background: #fff;
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15,23,42,0.05);
        }

        .service-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .service-body { padding: 18px; }

        .service-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .service-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(68,88,219,0.08);
            color: #4458db;
            font-size: 12px;
            font-weight: 700;
        }

        .service-id {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .service-owner-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid rgba(5,150,105,0.14);
        }

        .service-title {
            margin: 0 0 8px;
            font-size: 22px;
            font-weight: 800;
            color: #1f2f4d;
            line-height: 1.2;
        }

        .service-text {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.85;
            min-height: 78px;
        }

        .service-meta {
            margin-top: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .service-meta-box {
            padding: 12px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .service-meta-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .service-meta-box strong {
            color: #24304d;
            font-size: 14px;
            font-weight: 800;
        }

        .service-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .service-status-approved {
            background: rgba(5,150,105,0.10);
            color: #059669;
        }

        .service-status-pending {
            background: rgba(245,158,11,0.12);
            color: #d97706;
        }

        .service-status-rejected {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
        }

        .service-actions {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .service-btn-primary,
        .service-btn-secondary {
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .service-btn-primary {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
        }

        .service-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }

        .empty-state {
            padding: 28px;
            text-align: center;
            color: #64748b;
            font-size: 15px;
            line-height: 1.9;
            border: 1px dashed rgba(15,23,42,0.12);
            border-radius: 20px;
            background: #fbfcff;
        }

        .pagination-wrap { margin-top: 18px; }

        @media (max-width: 1100px) {
            .services-hero-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 767px) {
            .services-title { font-size: 30px; }
            .services-grid { grid-template-columns: 1fr; }
            .service-meta { grid-template-columns: 1fr; }
        }
    </style>

    <div class="services-shell">
        <section class="services-hero">
            <div class="services-hero-grid">
                <div>
                    <span class="services-badge">{{ $isArabic ? 'سوق الخدمات' : 'Service Marketplace' }}</span>

                    <h1 class="services-title">
                        {{ $isArabic
                            ? 'استكشف الخدمات المتاحة داخل المنصة بطريقة أوضح وأكثر احترافية'
                            : 'Explore the available services on the platform with a clearer and more professional experience' }}
                    </h1>

                    <p class="services-copy">
                        {{ $isArabic
                            ? 'جميع الخدمات هنا مربوطة بالخدمات الحقيقية وصورها الأساسية ضمن تجربة عرض مرتبة وواضحة.'
                            : 'All services here are connected to real records and their primary images in a clean and polished listing experience.' }}
                    </p>
                </div>

                <div class="services-mini-panel">
                    <h3>{{ $isArabic ? 'نظرة سريعة' : 'Quick snapshot' }}</h3>

                    <div class="services-mini-list">
                        <div class="services-mini-item">
                            <span>{{ $isArabic ? 'عدد الخدمات' : 'Total services' }}</span>
                            <strong>{{ $services->total() }}</strong>
                        </div>

                        <div class="services-mini-item">
                            <span>{{ $isArabic ? 'المعروضة الآن' : 'Shown now' }}</span>
                            <strong>{{ $services->count() }}</strong>
                        </div>

                        <div class="services-mini-item">
                            <span>{{ $isArabic ? 'الصفحة الحالية' : 'Current page' }}</span>
                            <strong>{{ $services->currentPage() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="services-grid-card">
            <div class="services-topbar">
                <h2 class="panel-title">{{ $isArabic ? 'الخدمات' : 'Services' }}</h2>

                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    @can('create-services')
                        <a href="{{ route('services.create') }}" class="service-btn-primary">
                            {{ $isArabic ? 'إضافة خدمة' : 'Create service' }}
                        </a>
                    @endcan

                    <form method="POST" action="{{ route('display-currency.update') }}" class="display-currency-form">
                        @csrf
                        <select name="display_currency" onchange="this.form.submit()" class="display-currency-select">
                            <option value="SYP" @selected($displayCurrency === 'SYP')>
                                {{ $isArabic ? 'عرض بالليرة السورية' : 'Show in SYP' }}
                            </option>
                            <option value="USD" @selected($displayCurrency === 'USD')>
                                {{ $isArabic ? 'عرض بالدولار' : 'Show in USD' }}
                            </option>
                        </select>
                    </form>

                    <div class="services-count">
                        {{ $isArabic ? 'إجمالي النتائج:' : 'Total results:' }} {{ $services->total() }}
                    </div>
                </div>
            </div>

            @if ($services->count())
                <div class="services-grid">
                    @foreach ($services as $service)
                        @php
                            $title = $resolveServiceTitle($service);
                            $description = $resolveServiceDescription($service);
                            $providerName = $resolveProviderName($service);
                            $categoryLabel = $resolveCategoryLabel($service);
                            $image = $service->primaryImage()?->path;
                            $imageUrl = $image
                                ? asset('storage/' . ltrim($image, '/'))
                                : $fallbackImages[$loop->index % count($fallbackImages)];

                            $isOwner = auth()->check()
                                && $service->businessAccount
                                && (int) $service->businessAccount->user_id === (int) auth()->id();

                            $priceText = method_exists($service, 'formattedPriceFor')
                                ? $service->formattedPriceFor($displayCurrency)
                                : (method_exists($service, 'formattedPrice')
                                    ? $service->formattedPrice()
                                    : ($service->price ?? '—'));
                        @endphp

                        <article class="service-card">
                            <img src="{{ $imageUrl }}" alt="{{ $title }}">

                            <div class="service-body">
                                <div class="service-topline">
                                    <span class="service-pill">{{ $categoryLabel }}</span>

                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        @if ($isOwner)
                                            <span class="service-owner-badge">
                                                {{ $isArabic ? 'خدمتي' : 'My Service' }}
                                            </span>
                                        @endif

                                        <span class="service-id">#{{ $service->id }}</span>
                                    </div>
                                </div>

                                <h3 class="service-title">{{ $title }}</h3>

                                <p class="service-text">
                                    {{ \Illuminate\Support\Str::limit(
                                        $description ?: ($isArabic ? 'لا يوجد وصف متاح حاليًا لهذه الخدمة.' : 'No description is currently available for this service.'),
                                        150
                                    ) }}
                                </p>

                                <div class="service-meta">
                                    <div class="service-meta-box">
                                        <span>{{ $isArabic ? 'السعر المعروض' : 'Displayed price' }}</span>
                                        <strong>{{ $priceText }}</strong>
                                    </div>

                                    <div class="service-meta-box">
                                        <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                                        <strong class="service-status-badge {{ $statusClass($service->status) }}">
                                            {{ $statusLabel($service->status) }}
                                        </strong>
                                    </div>

                                    <div class="service-meta-box">
                                        <span>{{ $isArabic ? 'مزود الخدمة' : 'Provider' }}</span>
                                        <strong>{{ $providerName }}</strong>
                                    </div>

                                    <div class="service-meta-box">
                                        <span>{{ $isArabic ? 'التصنيف' : 'Category' }}</span>
                                        <strong>{{ $categoryLabel }}</strong>
                                    </div>
                                </div>

                                <div class="service-actions">
                                    @if ($isOwner)
                                        <a href="{{ route('services.show', $service) }}" class="service-btn-secondary">
                                            {{ $isArabic ? 'التفاصيل' : 'Details' }}
                                        </a>

                                        <a href="{{ route('services.edit', $service) }}" class="service-btn-secondary">
                                            {{ $isArabic ? 'تعديل' : 'Edit' }}
                                        </a>

                                        <form method="POST"
                                              action="{{ route('services.destroy', $service) }}"
                                              style="margin:0;"
                                              onsubmit="return confirm('{{ $isArabic ? 'هل أنت متأكد من حذف هذه الخدمة؟' : 'Are you sure you want to delete this service?' }}');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="service-btn-secondary"
                                                    style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;">
                                                {{ $isArabic ? 'حذف' : 'Delete' }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('services.show', $service) }}" class="service-btn-secondary">
                                            {{ $isArabic ? 'التفاصيل' : 'Details' }}
                                        </a>

                                        <a href="{{ route('services.show', $service) }}" class="service-btn-primary">
                                            {{ $isArabic ? 'إرسال طلب' : 'Send request' }}
                                        </a>

                                        @if (in_array($service->id, $favoriteIds))
                                            <form method="POST" action="{{ route('favorites.destroy', $service->id) }}" style="margin:0;">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="service-btn-secondary">
                                                    {{ $isArabic ? 'إزالة من المفضلة' : 'Remove favorite' }}
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('favorites.store', $service->id) }}" style="margin:0;">
                                                @csrf

                                                <button type="submit" class="service-btn-secondary">
                                                    {{ $isArabic ? 'إضافة للمفضلة' : 'Add to favorites' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $services->links() }}
                </div>
            @else
                <div class="empty-state">
                    {{ $isArabic ? 'لا توجد خدمات لعرضها حاليًا.' : 'There are no services to display right now.' }}
                </div>
            @endif
        </section>
    </div>
@endsection