@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';

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
    @endphp

    <style>
        .service-show-shell {
            display: grid;
            gap: 22px;
        }

        .service-show-card {
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

        .report-box h4 {
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

        @media (max-width: 900px) {
            .service-show-grid,
            .service-show-meta,
            .service-order-grid {
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

                    <div class="service-show-meta">
                        <div class="service-show-meta-box">
                            <span>{{ $isArabic ? 'السعر' : 'Price' }}</span>
                            <strong>{{ $service->price ?? '—' }}</strong>
                        </div>

                        <div class="service-show-meta-box">
                            <span>{{ $isArabic ? 'الحالة' : 'Status' }}</span>
                            <strong>{{ $service->status ?? '—' }}</strong>
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

                    <div class="service-show-actions">
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

                        <a href="{{ route('chat.index') }}" class="service-show-btn-secondary">
                            {{ $isArabic ? 'فتح محادثة' : 'Open chat' }}
                        </a>

                        <a href="{{ route('services.edit', $service) }}" class="service-show-btn-secondary">
                            {{ $isArabic ? 'تعديل الخدمة' : 'Edit service' }}
                        </a>
                    </div>

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
                </div>
            </div>
        </article>
    </div>
@endsection