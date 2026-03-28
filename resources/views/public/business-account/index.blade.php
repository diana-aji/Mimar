@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $latestBusinessAccount = $latestBusinessAccount ?? null;
        $businessAccounts = $businessAccounts ?? collect();
        $cities = $cities ?? collect();
        $activityTypes = $activityTypes ?? collect();

        $showForm = request('new') || ! $latestBusinessAccount;

        $statusLabel = function ($status) use ($isArabic) {
            return match ($status) {
                'approved' => $isArabic ? 'مقبول' : 'Approved',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                default => $isArabic ? 'قيد المراجعة' : 'Pending review',
            };
        };

        $statusClass = function ($status) {
            return match ($status) {
                'approved' => 'background:rgba(5,150,105,.10); color:#059669;',
                'rejected' => 'background:rgba(239,68,68,.10); color:#dc2626;',
                default => 'background:rgba(245,158,11,.12); color:#d97706;',
            };
        };

        $resolveBusinessName = function ($business) use ($isArabic) {
            return $isArabic
                ? ($business->name_ar ?? $business->name_en ?? '—')
                : ($business->name_en ?? $business->name_ar ?? '—');
        };

        $resolveActivityName = function ($business) use ($isArabic) {
            if (! $business->activityType) {
                return '—';
            }

            return $isArabic
                ? ($business->activityType->name_ar ?? $business->activityType->name_en ?? '—')
                : ($business->activityType->name_en ?? $business->activityType->name_ar ?? '—');
        };

        $resolveCityName = function ($business) use ($isArabic) {
            if (! $business->city) {
                return '—';
            }

            return $isArabic
                ? ($business->city->name_ar ?? $business->city->name_en ?? '—')
                : ($business->city->name_en ?? $business->city->name_ar ?? '—');
        };
    @endphp

    <style>
        .ba-shell {
            display: grid;
            gap: 22px;
        }

        .ba-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            background:
                linear-gradient(135deg, rgba(12,18,32,.92) 0%, rgba(20,32,61,.80) 45%, rgba(34,52,95,.72) 100%),
                radial-gradient(circle at top right, rgba(255,255,255,0.10), transparent 24%);
            color: white;
            padding: 30px;
            box-shadow: 0 26px 60px rgba(15,23,42,0.18);
        }

        .ba-hero-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 20px;
            align-items: start;
        }

        .ba-hero h1 {
            margin: 0 0 12px;
            font-size: 44px;
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .ba-hero p {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .ba-hero-side {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .ba-hero-side h3 {
            margin: 0 0 14px;
            font-size: 20px;
            font-weight: 800;
        }

        .ba-hero-side ul {
            margin: 0;
            padding: 0 18px;
            display: grid;
            gap: 10px;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
            line-height: 1.8;
        }

        .ba-card,
        .ba-form-card,
        .ba-empty-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }

        .ba-card,
        .ba-empty-card {
            padding: 24px;
        }

        .ba-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .ba-title {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            color: #24304d;
            line-height: 1.05;
        }

        .ba-btn {
            height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            background: linear-gradient(135deg,#4458db 0%,#243873 100%);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            border: none;
            cursor: pointer;
        }

        .ba-sub {
            margin: 0 0 20px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .ba-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .ba-main-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
        }

        .ba-summary {
            display: grid;
            gap: 16px;
        }

        .ba-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .ba-meta-box {
            padding: 16px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .ba-meta-box span {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .ba-meta-box strong {
            display: block;
            color: #24304d;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.6;
        }

        .ba-side-note {
            padding: 18px;
            border-radius: 22px;
            background: #fff9ee;
            border: 1px solid rgba(217,119,6,0.10);
        }

        .ba-side-note h3 {
            margin: 0 0 14px;
            font-size: 18px;
            font-weight: 800;
            color: #24304d;
        }

        .ba-side-note ul {
            margin: 0;
            padding: 0 18px;
            display: grid;
            gap: 10px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.85;
        }

        .ba-reject-box {
            padding: 16px 18px;
            border-radius: 20px;
            background: rgba(239,68,68,0.06);
            border: 1px solid rgba(239,68,68,0.10);
            color: #991b1b;
        }

        .ba-reject-box strong {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .ba-gallery-wrap {
            margin-top: 22px;
            display: grid;
            gap: 18px;
        }

        .ba-section-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #24304d;
        }

        .ba-gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .ba-gallery-card {
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
        }

        .ba-gallery-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
        }

        .ba-gallery-card .meta {
            padding: 10px 12px;
            color: #475569;
            font-size: 12px;
        }

        .ba-docs {
            display: grid;
            gap: 12px;
        }

        .ba-doc {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 18px;
            background: #fff;
        }

        .ba-doc strong {
            color: #24304d;
            font-size: 14px;
        }

        .ba-doc a {
            color: #4458db;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .ba-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px 18px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .ba-list-item-main {
            display: grid;
            gap: 4px;
        }

        .ba-list-item-main strong {
            color: #24304d;
            font-size: 16px;
        }

        .ba-list-item-main span {
            color: #64748b;
            font-size: 13px;
        }

        .ba-form-card {
            padding: 24px;
        }

        .ba-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .ba-group {
            display: grid;
            gap: 8px;
        }

        .ba-group.full {
            grid-column: 1 / -1;
        }

        .ba-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .ba-input,
        .ba-select,
        .ba-textarea,
        .ba-file {
            width: 100%;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .ba-textarea {
            min-height: 140px;
            resize: vertical;
        }

        .ba-file {
            border-style: dashed;
        }

        .ba-input:focus,
        .ba-select:focus,
        .ba-textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }

        .ba-help {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }

        .ba-error {
            color: #dc2626;
            font-size: 12px;
        }

        .ba-alert {
            background: rgba(239,68,68,0.08);
            color: #b91c1c;
            border: 1px solid rgba(239,68,68,0.12);
            border-radius: 18px;
            padding: 14px 16px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .ba-form-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ba-secondary-btn {
            height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid rgba(15,23,42,0.08);
        }

        .ba-empty-card {
            display: grid;
            gap: 18px;
        }

        .ba-empty-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .ba-empty-box {
            padding: 18px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }

        .ba-empty-box strong {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #24304d;
        }

        .ba-empty-box span {
            color: #64748b;
            font-size: 13px;
            line-height: 1.9;
        }

        @media (max-width: 1100px) {
            .ba-hero-grid,
            .ba-main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .ba-hero h1 {
                font-size: 30px;
            }

            .ba-meta-grid,
            .ba-form-grid,
            .ba-gallery,
            .ba-empty-grid {
                grid-template-columns: 1fr;
            }

            .ba-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="ba-shell">
        <section class="ba-hero">
            <div class="ba-hero-grid">
                <div>
                    <h1>
                        {{ $isArabic
                            ? 'قدّم طلب حساب أعمال وابدأ رحلتك المهنية داخل Mi\'mar'
                            : 'Apply for a business account and start your professional journey inside Mi\'mar' }}
                    </h1>
                    <p>
                        {{ $isArabic
                            ? 'الحساب العادي هو نقطة البداية. وعند رغبتك بنشر الخدمات واستقبال الطلبات بشكل احترافي، يمكنك إرسال طلب حساب أعمال من هذه الصفحة.'
                            : 'A regular user account is your starting point. When you are ready to publish services and receive requests professionally, you can submit a business account request from this page.' }}
                    </p>
                </div>

                <div class="ba-hero-side">
                    <h3>{{ $isArabic ? 'ماذا يفتح لك حساب الأعمال؟' : 'What does a business account unlock?' }}</h3>
                    <ul>
                        <li>{{ $isArabic ? 'نشر الخدمات داخل المنصة' : 'Publish services on the platform' }}</li>
                        <li>{{ $isArabic ? 'استقبال الطلبات من العملاء' : 'Receive customer requests' }}</li>
                        <li>{{ $isArabic ? 'بناء حضور مهني موثوق' : 'Build a trusted professional presence' }}</li>
                    </ul>
                </div>
            </div>
        </section>

        @if (! $latestBusinessAccount)
            <section class="ba-empty-card">
                <div class="ba-head" style="margin-bottom: 0;">
                    <h2 class="ba-title">{{ $isArabic ? 'لا يوجد طلب حساب أعمال حتى الآن' : 'No business account request yet' }}</h2>

                    <a href="{{ route('business-account.index', ['new' => 1]) }}" class="ba-btn">
                        {{ $isArabic ? 'قدّم طلب حساب أعمال' : 'Submit business account request' }}
                    </a>
                </div>

                <p class="ba-sub" style="margin:0;">
                    {{ $isArabic
                        ? 'أنت الآن تستخدم المنصة كمستخدم عادي. إذا أردت إضافة خدماتك واستقبال الطلبات، أرسل طلب حساب أعمال وسيتم مراجعته من قبل الإدارة.'
                        : 'You are currently using the platform as a regular user. If you want to add your services and receive requests, submit a business account request and it will be reviewed by the admin team.' }}
                </p>

                <div class="ba-empty-grid">
                    <div class="ba-empty-box">
                        <strong>{{ $isArabic ? 'الخطوة 1' : 'Step 1' }}</strong>
                        <span>{{ $isArabic ? 'أدخل بيانات نشاطك التجاري الأساسية.' : 'Enter your core business information.' }}</span>
                    </div>

                    <div class="ba-empty-box">
                        <strong>{{ $isArabic ? 'الخطوة 2' : 'Step 2' }}</strong>
                        <span>{{ $isArabic ? 'أرفق الصور والوثائق الداعمة.' : 'Attach supporting images and documents.' }}</span>
                    </div>

                    <div class="ba-empty-box">
                        <strong>{{ $isArabic ? 'الخطوة 3' : 'Step 3' }}</strong>
                        <span>{{ $isArabic ? 'انتظر مراجعة الإدارة ثم ابدأ بإضافة خدماتك.' : 'Wait for admin review, then start publishing your services.' }}</span>
                    </div>
                </div>
            </section>
        @else
            <section class="ba-card">
                <div class="ba-head">
                    <h2 class="ba-title">{{ $isArabic ? 'آخر طلب حساب أعمال' : 'Latest business account request' }}</h2>

                    <a href="{{ route('business-account.index', ['new' => 1]) }}" class="ba-btn">
                        {{ $isArabic ? 'إضافة طلب جديد' : 'Create new request' }}
                    </a>
                </div>

                <p class="ba-sub">
                    {{ $isArabic
                        ? 'هذا هو أحدث طلب حساب أعمال قمت بإرساله أو تحديثه داخل المنصة.'
                        : 'This is the most recent business account request you submitted or updated on the platform.' }}
                </p>

                <div class="ba-main-grid">
                    <div class="ba-summary">
                        <div class="ba-status" style="{{ $statusClass($latestBusinessAccount->status) }}">
                            {{ $statusLabel($latestBusinessAccount->status) }}
                        </div>

                        <div class="ba-meta-grid">
                            <div class="ba-meta-box">
                                <span>{{ $isArabic ? 'اسم النشاط' : 'Business name' }}</span>
                                <strong>{{ $resolveBusinessName($latestBusinessAccount) }}</strong>
                            </div>

                            <div class="ba-meta-box">
                                <span>{{ $isArabic ? 'نوع النشاط' : 'Business type' }}</span>
                                <strong>{{ $resolveActivityName($latestBusinessAccount) }}</strong>
                            </div>

                            <div class="ba-meta-box">
                                <span>{{ $isArabic ? 'المدينة' : 'City' }}</span>
                                <strong>{{ $resolveCityName($latestBusinessAccount) }}</strong>
                            </div>

                            <div class="ba-meta-box">
                                <span>{{ $isArabic ? 'رقم الرخصة' : 'License number' }}</span>
                                <strong>{{ $latestBusinessAccount->license_number ?? '—' }}</strong>
                            </div>

                            <div class="ba-meta-box">
                                <span>{{ $isArabic ? 'النشاطات' : 'Activities' }}</span>
                                <strong>{{ $latestBusinessAccount->activities ?? '—' }}</strong>
                            </div>

                            <div class="ba-meta-box">
                                <span>{{ $isArabic ? 'التفاصيل' : 'Details' }}</span>
                                <strong>{{ $latestBusinessAccount->details ?? '—' }}</strong>
                            </div>
                        </div>

                        @if ($latestBusinessAccount->status === 'rejected' && $latestBusinessAccount->rejection_reason)
                            <div class="ba-reject-box">
                                <strong>{{ $isArabic ? 'سبب الرفض' : 'Rejection reason' }}</strong>
                                <div>{{ $latestBusinessAccount->rejection_reason }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="ba-side-note">
                        <h3>{{ $isArabic ? 'ما الذي يحدث الآن؟' : 'What happens now?' }}</h3>
                        <ul>
                            @if ($latestBusinessAccount->status === 'approved')
                                <li>{{ $isArabic ? 'تم قبول الطلب ويمكنك الآن استخدام حساب الأعمال بشكل كامل.' : 'Your request has been approved and you can now fully use your business account.' }}</li>
                                <li>{{ $isArabic ? 'أصبح بإمكانك إضافة الخدمات واستقبال الطلبات.' : 'You can now publish services and receive requests.' }}</li>
                                <li>{{ $isArabic ? 'يمكنك تحديث بيانات الحساب عند الحاجة.' : 'You may update account information when needed.' }}</li>
                            @elseif ($latestBusinessAccount->status === 'rejected')
                                <li>{{ $isArabic ? 'تم رفض الطلب الحالي ويمكنك تعديل البيانات وإعادة التقديم.' : 'Your current request was rejected and you can review the details and submit again.' }}</li>
                                <li>{{ $isArabic ? 'احرص على إرفاق معلومات ووثائق أوضح عند الإعادة.' : 'Make sure to provide clearer information and supporting documents on resubmission.' }}</li>
                                <li>{{ $isArabic ? 'يمكنك إنشاء طلب جديد مباشرة من هذه الصفحة.' : 'You can create a new request directly from this page.' }}</li>
                            @else
                                <li>{{ $isArabic ? 'طلبك قيد المراجعة من قبل الإدارة.' : 'Your request is currently under admin review.' }}</li>
                                <li>{{ $isArabic ? 'سيتم إشعارك عند تحديث الحالة.' : 'You will be notified when the status changes.' }}</li>
                                <li>{{ $isArabic ? 'بعد القبول ستتمكن من إدارة خدماتك بشكل كامل.' : 'Once approved, you will be able to manage your services fully.' }}</li>
                            @endif
                        </ul>
                    </div>
                </div>

                @if ($latestBusinessAccount->images && $latestBusinessAccount->images->count())
                    <div class="ba-gallery-wrap">
                        <h3 class="ba-section-title">{{ $isArabic ? 'صور الحساب' : 'Account images' }}</h3>

                        <div class="ba-gallery">
                            @foreach ($latestBusinessAccount->images as $image)
                                <div class="ba-gallery-card">
                                    <img src="{{ asset('storage/' . ltrim($image->path, '/')) }}" alt="business image">
                                    <div class="meta">
                                        {{ $isArabic ? 'صورة مرفوعة' : 'Uploaded image' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($latestBusinessAccount->documents && $latestBusinessAccount->documents->count())
                    <div class="ba-gallery-wrap">
                        <h3 class="ba-section-title">{{ $isArabic ? 'الوثائق' : 'Documents' }}</h3>

                        <div class="ba-docs">
                            @foreach ($latestBusinessAccount->documents as $document)
                                <div class="ba-doc">
                                    <strong>{{ $document->file_name ?? ($isArabic ? 'وثيقة مرفوعة' : 'Uploaded document') }}</strong>
                                    <a href="{{ asset('storage/' . ltrim($document->file_path, '/')) }}" target="_blank">
                                        {{ $isArabic ? 'فتح' : 'Open' }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($businessAccounts->count() > 1)
                    <div class="ba-gallery-wrap">
                        <h3 class="ba-section-title">{{ $isArabic ? 'طلبات أخرى' : 'Other requests' }}</h3>

                        <div class="ba-list">
                            @foreach ($businessAccounts->skip(1) as $account)
                                <div class="ba-list-item">
                                    <div class="ba-list-item-main">
                                        <strong>{{ $resolveBusinessName($account) }}</strong>
                                        <span>{{ $resolveActivityName($account) }} — {{ $resolveCityName($account) }}</span>
                                    </div>

                                    <div class="ba-status" style="{{ $statusClass($account->status) }}">
                                        {{ $statusLabel($account->status) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif

        @if ($showForm)
            <section class="ba-form-card">
                <h2 class="ba-title">
                    {{ $latestBusinessAccount
                        ? ($isArabic ? 'إرسال طلب حساب أعمال جديد' : 'Submit a new business account request')
                        : ($isArabic ? 'إرسال طلب حساب أعمال' : 'Submit a business account request') }}
                </h2>

                <p class="ba-sub">
                    {{ $isArabic
                        ? 'املأ المعلومات الأساسية وأرفق الصور والوثائق المطلوبة لإرسال طلبك إلى الإدارة للمراجعة.'
                        : 'Fill in the core information and attach the required images and documents to submit your request for admin review.' }}
                </p>

                @if ($errors->any())
                    <div class="ba-alert">
                        {{ $isArabic ? 'يرجى مراجعة الحقول وإصلاح الأخطاء الظاهرة أدناه.' : 'Please review the fields and fix the errors shown below.' }}
                    </div>
                @endif

                <form method="POST" action="{{ route('business-account.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="ba-form-grid">
                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'اسم النشاط بالعربية' : 'Business name (Arabic)' }}</label>
                            <input class="ba-input" type="text" name="name_ar" value="{{ old('name_ar') }}">
                            @error('name_ar') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'اسم النشاط بالإنجليزية' : 'Business name (English)' }}</label>
                            <input class="ba-input" type="text" name="name_en" value="{{ old('name_en') }}">
                            @error('name_en') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'نوع النشاط' : 'Business activity type' }}</label>
                            <select class="ba-select" name="business_activity_type_id">
                                <option value="">{{ $isArabic ? 'اختر نوع النشاط' : 'Select activity type' }}</option>
                                @foreach ($activityTypes as $activityType)
                                    <option value="{{ $activityType->id }}" @selected(old('business_activity_type_id') == $activityType->id)>
                                        {{ $isArabic
                                            ? ($activityType->name_ar ?? $activityType->name_en ?? ('#' . $activityType->id))
                                            : ($activityType->name_en ?? $activityType->name_ar ?? ('#' . $activityType->id)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('business_activity_type_id') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'المدينة' : 'City' }}</label>
                            <select class="ba-select" name="city_id">
                                <option value="">{{ $isArabic ? 'اختر المدينة' : 'Select city' }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                        {{ $isArabic
                                            ? ($city->name_ar ?? $city->name_en ?? ('#' . $city->id))
                                            : ($city->name_en ?? $city->name_ar ?? ('#' . $city->id)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'رقم الرخصة' : 'License number' }}</label>
                            <input class="ba-input" type="text" name="license_number" value="{{ old('license_number') }}">
                            @error('license_number') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'خط العرض' : 'Latitude' }}</label>
                            <input class="ba-input" type="text" name="latitude" value="{{ old('latitude') }}">
                            @error('latitude') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group">
                            <label class="ba-label">{{ $isArabic ? 'خط الطول' : 'Longitude' }}</label>
                            <input class="ba-input" type="text" name="longitude" value="{{ old('longitude') }}">
                            @error('longitude') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group full">
                            <label class="ba-label">{{ $isArabic ? 'النشاطات' : 'Activities' }}</label>
                            <textarea class="ba-textarea" name="activities">{{ old('activities') }}</textarea>
                            @error('activities') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group full">
                            <label class="ba-label">{{ $isArabic ? 'تفاصيل النشاط' : 'Business details' }}</label>
                            <textarea class="ba-textarea" name="details">{{ old('details') }}</textarea>
                            @error('details') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group full">
                            <label class="ba-label">{{ $isArabic ? 'صور النشاط' : 'Business images' }}</label>
                            <input class="ba-file" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
                            <div class="ba-help">
                                {{ $isArabic ? 'يمكنك رفع أكثر من صورة للنشاط التجاري.' : 'You can upload multiple business images.' }}
                            </div>
                            @error('images') <div class="ba-error">{{ $message }}</div> @enderror
                            @error('images.*') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ba-group full">
                            <label class="ba-label">{{ $isArabic ? 'الوثائق' : 'Documents' }}</label>
                            <input class="ba-file" type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <div class="ba-help">
                                {{ $isArabic ? 'يمكنك رفع وثائق مثل الرخصة أو المستندات الداعمة.' : 'You can upload documents such as a license or supporting files.' }}
                            </div>
                            @error('documents') <div class="ba-error">{{ $message }}</div> @enderror
                            @error('documents.*') <div class="ba-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="ba-form-actions">
                        <button type="submit" class="ba-btn">
                            {{ $isArabic ? 'إرسال الطلب' : 'Submit request' }}
                        </button>

                        <a href="{{ route('business-account.index') }}" class="ba-secondary-btn">
                            {{ $isArabic ? 'إلغاء' : 'Cancel' }}
                        </a>
                    </div>
                </form>
            </section>
        @endif
    </div>
@endsection