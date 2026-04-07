@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
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

        $title = $isArabic
            ? ($service->name_ar ?? $service->name_en ?? '—')
            : ($service->name_en ?? $service->name_ar ?? '—');

        $description = $service->description ?? '';

        $providerName = $service->businessAccount
            ? ($isArabic
                ? ($service->businessAccount->name_ar ?? $service->businessAccount->name_en ?? 'مزود خدمة')
                : ($service->businessAccount->name_en ?? $service->businessAccount->name_ar ?? 'Service provider'))
            : ($isArabic ? 'مزود خدمة' : 'Service provider');

        $categoryLabel = null;
        if ($service->subcategory) {
            $categoryLabel = $isArabic
                ? ($service->subcategory->name_ar ?? $service->subcategory->name_en ?? 'خدمة')
                : ($service->subcategory->name_en ?? $service->subcategory->name_ar ?? 'Service');
        } elseif ($service->category) {
            $categoryLabel = $isArabic
                ? ($service->category->name_ar ?? $service->category->name_en ?? 'خدمة')
                : ($service->category->name_en ?? $service->category->name_ar ?? 'Service');
        } else {
            $categoryLabel = $isArabic ? 'خدمة' : 'Service';
        }

        $image = $service->primaryImage()?->path;
        $imageUrl = $image
            ? asset('storage/' . ltrim($image, '/'))
            : 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=80';

        $isFavorited = $isFavorited ?? false;

        $ratings = $service->ratings()->with('user')->latest()->get();
        $ratingsCount = $ratings->count();
        $averageRating = $ratingsCount ? round($ratings->avg('score'), 1) : null;

        $isOwner = auth()->check()
            && $service->businessAccount
            && (int) $service->businessAccount->user_id === (int) auth()->id();

        $hasLocation = ! is_null($service->latitude) && ! is_null($service->longitude);

        $priceText = method_exists($service, 'formattedPriceFor')
            ? $service->formattedPriceFor($displayCurrency)
            : (method_exists($service, 'formattedPrice')
                ? $service->formattedPrice()
                : ($service->price ?? '—'));
    @endphp

    @if ($hasLocation)
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    @endif

    <style>
        .service-show-shell {
            display: grid;
            gap: 22px;
        }

        .service-show-card,
        .service-rating-card,
        .service-location-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }

        .service-show-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .service-show-image img {
            width: 100%;
            height: 100%;
            min-height: 460px;
            object-fit: cover;
            display: block;
        }

        .service-show-body {
            padding: 28px;
            display: grid;
            gap: 16px;
        }

        .service-show-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(68,88,219,0.08);
            color: #4458db;
            font-size: 12px;
            font-weight: 700;
        }

        .service-show-title {
            margin: 0;
            font-size: 36px;
            line-height: 1.08;
            font-weight: 800;
            color: #24304d;
        }

        .service-show-text {
            margin: 0;
            color: #64748b;
            font-size: 15px;
            line-height: 1.95;
        }

        .service-show-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .service-show-meta-box {
            padding: 14px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .service-show-meta-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .service-show-meta-box strong {
            color: #24304d;
            font-size: 15px;
            font-weight: 800;
        }

        .service-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
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

        .service-show-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .service-show-btn-primary,
        .service-show-btn-secondary {
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

        .service-show-btn-primary {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
        }

        .service-show-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }

        .display-currency-form {
            margin: 0;
        }

        .display-currency-select {
            height: 44px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: #f8fafc;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            outline: none;
        }

        .service-show-gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 6px;
        }

        .service-show-gallery img {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.06);
            display: block;
        }

        .service-display-currency-note {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(68,88,219,0.06);
            color: #334155;
            border: 1px solid rgba(68,88,219,0.10);
            font-size: 12px;
            line-height: 1.8;
        }

        .service-order-form {
            display: grid;
            gap: 12px;
            margin-top: 8px;
            padding: 18px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .service-order-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .service-order-group {
            display: grid;
            gap: 6px;
        }

        .service-order-group.full {
            grid-column: 1 / -1;
        }

        .service-order-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .service-order-input,
        .service-order-textarea {
            width: 100%;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .service-order-textarea {
            min-height: 110px;
            resize: vertical;
        }

        .service-order-input:focus,
        .service-order-textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }

        .service-order-note {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }

        .service-order-error {
            color: #dc2626;
            font-size: 12px;
        }

        .service-success {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        .report-box {
            margin-top: 16px;
            padding: 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
            display: grid;
            gap: 10px;
        }

        .report-box h4,
        .rating-head h3,
        .service-location-head h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #111827;
        }

        .report-form {
            display: grid;
            gap: 10px;
        }

        .report-form textarea {
            width: 100%;
            min-height: 110px;
            border: 1px solid rgba(15,23,42,0.08);
            background: white;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 14px;
            color: #111827;
            resize: vertical;
            outline: none;
        }

        .report-form textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }

        .report-note {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }

        .alert-error-service {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(239,68,68,0.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        .service-rating-card,
        .service-location-card {
            padding: 24px;
        }

        .rating-head,
        .service-location-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .rating-summary {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .rating-score-box,
        .rating-list-box,
        .service-location-meta {
            padding: 18px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .rating-score-big {
            font-size: 42px;
            font-weight: 900;
            color: #24304d;
            line-height: 1;
            margin-bottom: 8px;
        }

        .rating-score-note {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .rating-list {
            display: grid;
            gap: 12px;
        }

        .rating-item {
            padding: 16px;
            border-radius: 18px;
            background: white;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .rating-item-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .rating-user {
            font-size: 15px;
            font-weight: 800;
            color: #24304d;
        }

        .rating-stars {
            font-size: 14px;
            font-weight: 800;
            color: #d97706;
        }

        .rating-comment {
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .rating-empty {
            padding: 18px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,0.10);
            color: #64748b;
            text-align: center;
        }

        .service-location-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 18px;
            align-items: start;
        }

        .service-map-box {
            width: 100%;
            height: 360px;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(15,23,42,0.08);
            background: #e2e8f0;
        }

        .service-location-info {
            display: grid;
            gap: 12px;
        }

        .service-location-chip {
            padding: 14px;
            border-radius: 16px;
            background: white;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .service-location-chip span {
            display: block;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .service-location-chip strong {
            color: #24304d;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.7;
        }

        @media (max-width: 900px) {
            .service-show-grid,
            .service-show-meta,
            .service-order-grid,
            .rating-summary,
            .service-location-grid {
                grid-template-columns: 1fr;
            }

            .service-show-title {
                font-size: 30px;
            }

            .service-show-image img {
                min-height: 280px;
            }

            .service-show-gallery {
                grid-template-columns: 1fr 1fr;
            }

            .service-map-box {
                height: 300px;
            }
        }

        @media (max-width: 600px) {
            .service-show-gallery {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="service-show-shell">
        <article class="service-show-card">
            <div class="service-show-grid">
                <div class="service-show-image">
                    <img src="{{ $imageUrl }}" alt="{{ $title }}">
                </div>

                <div class="service-show-body">
                    <span class="service-show-pill">{{ $categoryLabel }}</span>

                    <h1 class="service-show-title">{{ $title }}</h1>

                    <p class="service-show-text">
                        {{ $description ?: ($isArabic ? 'لا يوجد وصف متاح حاليًا لهذه الخدمة.' : 'No description is currently available for this service.') }}
                    </p>

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

                    <div class="service-show-meta">
                        <div class="service-show-meta-box">
                            <span>{{ $isArabic ? 'السعر المعروض' : 'Displayed price' }}</span>
                            <strong>{{ $priceText }}</strong>
                        </div>

                        <div class="service-show-meta-box">
                            <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                            <strong class="service-status-badge {{ $statusClass($service->status) }}">
                                {{ $statusLabel($service->status) }}
                            </strong>
                        </div>

                        <div class="service-show-meta-box">
                            <span>{{ $isArabic ? 'مزود الخدمة' : 'Provider' }}</span>
                            <strong>{{ $providerName }}</strong>
                        </div>

                        <div class="service-show-meta-box">
                            <span>{{ $isArabic ? 'التصنيف' : 'Category' }}</span>
                            <strong>{{ $categoryLabel }}</strong>
                        </div>
                    </div>

                    <div class="service-display-currency-note">
                        {{ $isArabic
                            ? 'السعر الظاهر حالياً معروض حسب عملة العرض المختارة للمستخدم: '
                            : 'The currently shown price is displayed according to the user selected display currency: ' }}
                        <strong>{{ $displayCurrency }}</strong>
                    </div>

                    @if ($averageRating)
                        <div class="service-show-meta">
                            <div class="service-show-meta-box">
                                <span>{{ $isArabic ? 'متوسط التقييم' : 'Average rating' }}</span>
                                <strong>{{ $averageRating }} / 5</strong>
                            </div>

                            <div class="service-show-meta-box">
                                <span>{{ $isArabic ? 'عدد التقييمات' : 'Ratings count' }}</span>
                                <strong>{{ $ratingsCount }}</strong>
                            </div>
                        </div>
                    @endif

                    @if ($service->images && $service->images->count() > 1)
                        <div class="service-show-gallery">
                            @foreach ($service->images->take(3) as $galleryImage)
                                <img src="{{ asset('storage/' . ltrim($galleryImage->path, '/')) }}" alt="{{ $title }}">
                            @endforeach
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="service-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert-error-service">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (! $isOwner)
                        <form method="POST" action="{{ route('orders.store', $service->id) }}" class="service-order-form">
                            @csrf

                            <div class="service-order-grid">
                                <div class="service-order-group">
                                    <label class="service-order-label">
                                        {{ $isArabic ? 'الكمية' : 'Quantity' }}
                                    </label>

                                    <input
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        value="{{ old('quantity', 1) }}"
                                        class="service-order-input"
                                        required
                                    >

                                    @error('quantity')
                                        <div class="service-order-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="service-order-group">
                                    <label class="service-order-label">
                                        {{ $isArabic ? 'التاريخ المطلوب' : 'Needed at' }}
                                    </label>

                                    <input
                                        type="datetime-local"
                                        name="needed_at"
                                        value="{{ old('needed_at') }}"
                                        class="service-order-input"
                                    >

                                    @error('needed_at')
                                        <div class="service-order-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="service-order-group full">
                                    <label class="service-order-label">
                                        {{ $isArabic ? 'تفاصيل الطلب' : 'Order details' }}
                                    </label>

                                    <textarea
                                        name="details"
                                        class="service-order-textarea"
                                        placeholder="{{ $isArabic ? 'اكتب تفاصيل إضافية للطلب...' : 'Write additional order details...' }}"
                                    >{{ old('details') }}</textarea>

                                    @error('details')
                                        <div class="service-order-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="service-show-actions">
                                <button type="submit" class="service-show-btn-primary" style="border:none; cursor:pointer;">
                                    {{ $isArabic ? 'إرسال طلب' : 'Send request' }}
                                </button>
                            </div>

                            <div class="service-order-note">
                                {{ $isArabic
                                    ? 'يمكنك إرسال طلب مباشرة من حسابك كمستخدم عادي.'
                                    : 'You can send a request directly from your regular user account.' }}
                            </div>
                        </form>
                    @endif

                    <div class="service-show-actions">
                        @if (! $isOwner)
                            @if ($isFavorited)
                                <form method="POST" action="{{ route('favorites.destroy', $service->id) }}" style="margin:0;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="service-show-btn-secondary" style="cursor:pointer;">
                                        {{ $isArabic ? 'إزالة من المفضلة' : 'Remove from favorites' }}
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('favorites.store', $service->id) }}" style="margin:0;">
                                    @csrf

                                    <button type="submit" class="service-show-btn-secondary" style="cursor:pointer;">
                                        {{ $isArabic ? 'إضافة إلى المفضلة' : 'Add to favorites' }}
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('chat.start-from-service', $service->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="service-show-btn-secondary" style="cursor:pointer;">
                                    {{ $isArabic ? 'فتح محادثة' : 'Open chat' }}
                                </button>
                            </form>
                        @endif

                        @if ($isOwner)
                            <a href="{{ route('services.edit', $service) }}" class="service-show-btn-secondary">
                                {{ $isArabic ? 'تعديل الخدمة' : 'Edit service' }}
                            </a>

                            <form method="POST"
                                  action="{{ route('services.destroy', $service) }}"
                                  style="margin:0;"
                                  onsubmit="return confirm('{{ $isArabic ? 'هل أنت متأكد من حذف هذه الخدمة؟' : 'Are you sure you want to delete this service?' }}');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="service-show-btn-secondary"
                                        style="cursor:pointer; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;">
                                    {{ $isArabic ? 'حذف الخدمة' : 'Delete service' }}
                                </button>
                            </form>
                        @endif
                    </div>

                    @if (! $isOwner)
                        <div class="report-box">
                            <h4>{{ $isArabic ? 'الإبلاغ عن هذه الخدمة' : 'Report this service' }}</h4>

                            <form method="POST" action="{{ route('reports.store', $service->id) }}" class="report-form">
                                @csrf

                                <textarea
                                    name="reason"
                                    placeholder="{{ $isArabic ? 'اكتب سبب البلاغ...' : 'Write the reason for this report...' }}"
                                    required
                                >{{ old('reason') }}</textarea>

                                <div class="service-show-actions" style="margin-top:0;">
                                    <button type="submit" class="service-show-btn-secondary" style="cursor:pointer;">
                                        {{ $isArabic ? 'إرسال البلاغ' : 'Submit report' }}
                                    </button>
                                </div>

                                <div class="report-note">
                                    {{ $isArabic
                                        ? 'استخدم البلاغ عند وجود محتوى غير مناسب أو معلومات مضللة أو مشكلة حقيقية بالخدمة.'
                                        : 'Use reports for inappropriate content, misleading information, or real issues with the service.' }}
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </article>

        @if ($hasLocation)
            <section class="service-location-card">
                <div class="service-location-head">
                    <h3>{{ $isArabic ? 'موقع الخدمة' : 'Service location' }}</h3>

                    <a
                        href="https://www.google.com/maps?q={{ $service->latitude }},{{ $service->longitude }}"
                        target="_blank"
                        class="service-show-btn-secondary"
                    >
                        {{ $isArabic ? 'فتح على Google Maps' : 'Open in Google Maps' }}
                    </a>
                </div>

                <div class="service-location-grid">
                    <div id="serviceLocationMap" class="service-map-box"></div>

                    <div class="service-location-info">
                        <div class="service-location-meta">
                            <div class="service-location-chip">
                                <span>{{ $isArabic ? 'خط العرض' : 'Latitude' }}</span>
                                <strong>{{ $service->latitude }}</strong>
                            </div>

                            <div class="service-location-chip">
                                <span>{{ $isArabic ? 'خط الطول' : 'Longitude' }}</span>
                                <strong>{{ $service->longitude }}</strong>
                            </div>

                            <div class="service-location-chip">
                                <span>{{ $isArabic ? 'نوع الموقع' : 'Location type' }}</span>
                                <strong>{{ $isArabic ? 'موقع تقريبي للخدمة أو مزودها' : 'Approximate location for the service or provider' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="service-rating-card">
            <div class="rating-head">
                <h3>{{ $isArabic ? 'تقييمات الخدمة' : 'Service ratings' }}</h3>
            </div>

            <div class="rating-summary">
                <div class="rating-score-box">
                    <div class="rating-score-big">{{ $averageRating ?? '—' }}</div>
                    <div class="rating-score-note">
                        {{ $averageRating
                            ? ($isArabic ? 'متوسط تقييم العملاء لهذه الخدمة' : 'Average customer rating for this service')
                            : ($isArabic ? 'لا توجد تقييمات بعد' : 'No ratings yet') }}
                    </div>
                </div>

                <div class="rating-list-box">
                    @if ($ratingsCount)
                        <div class="rating-list">
                            @foreach ($ratings->take(5) as $rating)
                                <div class="rating-item">
                                    <div class="rating-item-top">
                                        <div class="rating-user">{{ $rating->user->name ?? ($isArabic ? 'مستخدم' : 'User') }}</div>
                                        <div class="rating-stars">{{ $rating->score }}/5</div>
                                    </div>

                                    <div class="rating-comment">
                                        {{ $rating->comment ?: ($isArabic ? 'لا يوجد تعليق مرفق.' : 'No comment attached.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rating-empty">
                            {{ $isArabic ? 'لا توجد تقييمات على هذه الخدمة حتى الآن.' : 'There are no ratings for this service yet.' }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    @if ($hasLocation)
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
        ></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapElement = document.getElementById('serviceLocationMap');

                if (!mapElement) {
                    return;
                }

                const lat = parseFloat(@json((float) $service->latitude));
                const lng = parseFloat(@json((float) $service->longitude));

                const map = L.map('serviceLocationMap', {
                    scrollWheelZoom: false
                }).setView([lat, lng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map)
                    .bindPopup(@json($title))
                    .openPopup();

                setTimeout(() => {
                    map.invalidateSize();
                }, 300);
            });
        </script>
    @endif
@endsection