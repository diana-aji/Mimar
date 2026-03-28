@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $sentOrders = $sentOrders ?? collect();
        $receivedOrders = $receivedOrders ?? collect();
        $activeTab = $activeTab ?? 'sent';

        $orders = $activeTab === 'received' ? $receivedOrders : $sentOrders;

        $resolveBusinessName = function ($business) use ($isArabic) {
            if (! $business) {
                return $isArabic ? 'غير معروف' : 'Unknown';
            }

            return $isArabic
                ? ($business->name_ar ?? $business->name_en ?? '—')
                : ($business->name_en ?? $business->name_ar ?? '—');
        };

        $resolveUserName = function ($user) use ($isArabic) {
            if (! $user) {
                return $isArabic ? 'مستخدم غير معروف' : 'Unknown user';
            }

            return $user->name ?? ($isArabic ? 'مستخدم' : 'User');
        };

        $resolveServiceName = function ($service) use ($isArabic) {
            if (! $service) {
                return $isArabic ? 'خدمة غير معروفة' : 'Unknown service';
            }

            return $isArabic
                ? ($service->name_ar ?? $service->name_en ?? '—')
                : ($service->name_en ?? $service->name_ar ?? '—');
        };

        $formatStatus = function ($status) use ($isArabic) {
            return match ($status) {
                'accepted' => $isArabic ? 'مقبول' : 'Accepted',
                'rejected' => $isArabic ? 'مرفوض' : 'Rejected',
                'cancelled' => $isArabic ? 'ملغي' : 'Cancelled',
                default => $isArabic ? 'قيد المراجعة' : 'Pending',
            };
        };

        $statusClass = function ($status) {
            return match ($status) {
                'accepted' => 'status-complete',
                'rejected' => 'status-rejected',
                'cancelled' => 'status-cancelled',
                default => 'status-progress',
            };
        };

        $openCount = $sentOrders->where('status', 'pending')->count() + $receivedOrders->where('status', 'pending')->count();
        $progressCount = $sentOrders->where('status', 'accepted')->count() + $receivedOrders->where('status', 'accepted')->count();
        $completedCount = $sentOrders->where('status', 'accepted')->count() + $receivedOrders->where('status', 'accepted')->count();
    @endphp

    <style>
        .orders-shell { display:grid; gap:22px; }
        .orders-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.10), transparent 24%),
                linear-gradient(135deg, #111827 0%, #1f2937 50%, #243b73 100%);
            color: white;
            padding: 28px;
            box-shadow: 0 24px 54px rgba(17,24,39,0.20);
        }
        .orders-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.35), rgba(0,0,0,.9));
            pointer-events: none;
        }
        .orders-hero-inner {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 20px;
            align-items: end;
        }
        .orders-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.10);
            margin-bottom: 18px;
            font-size: 12px;
            font-weight: 700;
        }
        .orders-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d1a763;
        }
        .orders-title {
            margin: 0 0 10px;
            font-size: 42px;
            line-height: 1.08;
            letter-spacing: -0.04em;
            font-weight: 800;
        }
        .orders-copy {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.9;
            max-width: 720px;
        }
        .orders-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .orders-btn-primary,
        .orders-btn-secondary {
            height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
        }
        .orders-btn-primary {
            background: white;
            color: #1f2f4d;
        }
        .orders-btn-secondary {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.16);
            color: white;
        }
        .orders-side {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 24px;
            padding: 18px;
            backdrop-filter: blur(10px);
        }
        .orders-side h3 {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 800;
        }
        .orders-side-list { display: grid; gap: 10px; }
        .orders-side-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
        }
        .orders-side-item strong {
            color: white;
            font-size: 20px;
        }
        .orders-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 20px;
            align-items: start;
        }
        .panel-card,
        .summary-card,
        .order-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 26px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
        }
        .panel-card,
        .summary-card { padding: 22px; }
        .panel-header {
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
        .tabs-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .tab-pill {
            height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }
        .tab-pill.active {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            box-shadow: 0 14px 26px rgba(36,56,115,0.16);
        }
        .orders-list { display: grid; gap: 16px; }
        .order-card { padding: 20px; }
        .order-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .order-title {
            margin: 0 0 6px;
            font-size: 20px;
            font-weight: 800;
            color: #24304d;
        }
        .order-sub {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }
        .status-pill-v2 {
            height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .status-progress {
            background: rgba(245,158,11,0.12);
            color: #d97706;
        }
        .status-complete {
            background: rgba(5,150,105,0.10);
            color: #059669;
        }
        .status-rejected {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
        }
        .status-cancelled {
            background: rgba(100,116,139,0.12);
            color: #475569;
        }
        .order-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }
        .order-meta-box {
            padding: 14px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }
        .order-meta-box span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }
        .order-meta-box strong {
            color: #24304d;
            font-size: 15px;
            font-weight: 800;
        }
        .order-timeline {
            margin-top: 16px;
            display: grid;
            gap: 10px;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #475569;
            font-size: 13px;
            line-height: 1.8;
        }
        .timeline-item::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #4458db;
            flex-shrink: 0;
        }
        .order-actions {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .mini-btn,
        .mini-btn-ghost,
        .mini-btn-danger {
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
        .mini-btn {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            box-shadow: 0 14px 26px rgba(36,56,115,0.16);
        }
        .mini-btn-ghost {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }
        .mini-btn-danger {
            background: rgba(239,68,68,0.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.12);
        }
        .summary-list { display: grid; gap: 14px; }
        .summary-item {
            padding: 16px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
        }
        .summary-item strong {
            display: block;
            margin-bottom: 6px;
            font-size: 18px;
            color: #24304d;
        }
        .summary-item span {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }
        .summary-cta {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .summary-cta a {
            height: 42px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }
        .summary-cta .primary {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
        }
        .summary-cta .secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }
        .empty-orders {
            padding: 28px;
            border-radius: 22px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,0.10);
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
            text-align: center;
        }
        .alert-success-order {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,0.12);
            font-size: 14px;
            font-weight: 700;
        }
        .rating-box {
            margin-top: 16px;
            padding: 16px;
            border-radius: 18px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,0.06);
            display: grid;
            gap: 10px;
        }
        .rating-box h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #111827;
        }
        .rating-form { display: grid; gap: 10px; }
        .rating-form-row {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 10px;
            align-items: start;
        }
        .rating-form label {
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            padding-top: 10px;
        }
        .rating-form select,
        .rating-form textarea {
            width: 100%;
            border: 1px solid rgba(15,23,42,0.08);
            background: white;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 14px;
            color: #111827;
            outline: none;
        }
        .rating-form textarea {
            min-height: 100px;
            resize: vertical;
        }
        .rating-form select:focus,
        .rating-form textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }
        .rating-note {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }
        .rating-display {
            margin-top: 16px;
            padding: 16px;
            border-radius: 18px;
            background: rgba(5,150,105,0.06);
            border: 1px solid rgba(5,150,105,0.10);
            display: grid;
            gap: 8px;
        }
        .rating-score {
            font-size: 16px;
            font-weight: 800;
            color: #059669;
        }
        .alert-error-order {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(239,68,68,0.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        @media (max-width: 1100px) {
            .orders-hero-inner,
            .orders-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .orders-title {
                font-size: 30px;
            }

            .order-meta-grid,
            .rating-form-row {
                grid-template-columns: 1fr;
            }

            .panel-title {
                font-size: 22px;
            }
        }
    </style>

    <div class="orders-shell">
        <section class="orders-hero">
            <div class="orders-hero-inner">
                <div>
                    <span class="orders-kicker">{{ $isArabic ? 'مساحة الطلبات' : 'Orders workspace' }}</span>
                    <h1 class="orders-title">{{ $isArabic ? 'إدارة الطلبات' : 'Manage Orders' }}</h1>
                    <p class="orders-copy">
                        {{ $isArabic
                            ? 'تابع الطلبات المرسلة والمستلمة، وأدر حالة الطلبات بسهولة ضمن تجربة واضحة ومرتبة.'
                            : 'Track sent and received orders, and manage request statuses through a clean and organized experience.' }}
                    </p>

                    <div class="orders-actions">
                        <a href="{{ route('services.index') }}" class="orders-btn-primary">
                            {{ $isArabic ? 'استعراض الخدمات' : 'Explore services' }}
                        </a>
                        <a href="{{ route('chat.index') }}" class="orders-btn-secondary">
                            {{ $isArabic ? 'فتح المحادثات' : 'Open conversations' }}
                        </a>
                    </div>
                </div>

                <div class="orders-side">
                    <h3>{{ $isArabic ? 'ملخص سريع' : 'Orders snapshot' }}</h3>

                    <div class="orders-side-list">
                        <div class="orders-side-item">
                            <span>{{ $isArabic ? 'الطلبات المفتوحة' : 'Open requests' }}</span>
                            <strong>{{ $openCount }}</strong>
                        </div>

                        <div class="orders-side-item">
                            <span>{{ $isArabic ? 'الطلبات المقبولة' : 'Accepted' }}</span>
                            <strong>{{ $progressCount }}</strong>
                        </div>

                        <div class="orders-side-item">
                            <span>{{ $isArabic ? 'إجمالي الطلبات' : 'Total orders' }}</span>
                            <strong>{{ $sentOrders->count() + $receivedOrders->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="alert-success-order">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-error-order">
                {{ session('error') }}
            </div>
        @endif

        <section class="orders-grid">
            <div class="panel-card">
                <div class="panel-header">
                    <h2 class="panel-title">
                        {{ $activeTab === 'received'
                            ? ($isArabic ? 'الطلبات المستلمة' : 'Received Orders')
                            : ($isArabic ? 'الطلبات المرسلة' : 'Sent Orders') }}
                    </h2>
                </div>

                <div class="tabs-row">
                    <a href="{{ route('orders.index', ['tab' => 'sent']) }}" class="tab-pill {{ $activeTab === 'sent' ? 'active' : '' }}">
                        {{ $isArabic ? 'الطلبات المرسلة' : 'Sent Orders' }}
                    </a>

                    <a href="{{ route('orders.index', ['tab' => 'received']) }}" class="tab-pill {{ $activeTab === 'received' ? 'active' : '' }}">
                        {{ $isArabic ? 'الطلبات المستلمة' : 'Received Orders' }}
                    </a>
                </div>

                @if ($orders->count())
                    <div class="orders-list">
                        @foreach ($orders as $order)
                            @php
                                $serviceName = $resolveServiceName($order->service);
                                $senderName = $resolveUserName($order->user);
                                $receiverName = $resolveBusinessName($order->receiverBusinessAccount);
                            @endphp

                            <article class="order-card">
                                <div class="order-head">
                                    <div>
                                        <h3 class="order-title">{{ $serviceName }}</h3>
                                        <div class="order-sub">
                                            {{ $isArabic ? 'الخدمة' : 'Service' }}: {{ $serviceName }} •
                                            {{ $activeTab === 'received'
                                                ? ($isArabic ? 'المرسل' : 'Sender')
                                                : ($isArabic ? 'المستقبل' : 'Receiver') }}:
                                            {{ $activeTab === 'received' ? $senderName : $receiverName }}
                                        </div>
                                    </div>

                                    <span class="status-pill-v2 {{ $statusClass($order->status) }}">
                                        {{ $formatStatus($order->status) }}
                                    </span>
                                </div>

                                <div class="order-meta-grid">
                                    <div class="order-meta-box">
                                        <span>{{ $isArabic ? 'تاريخ الطلب' : 'Order date' }}</span>
                                        <strong>{{ optional($order->created_at)->format('Y-m-d') ?? '—' }}</strong>
                                    </div>

                                    <div class="order-meta-box">
                                        <span>{{ $isArabic ? 'الكمية' : 'Quantity' }}</span>
                                        <strong>{{ $order->quantity }}</strong>
                                    </div>

                                    <div class="order-meta-box">
                                        <span>{{ $isArabic ? 'السعر التقريبي' : 'Estimated budget' }}</span>
                                        <strong>{{ $order->service?->price ?? '—' }}</strong>
                                    </div>
                                </div>

                                <div class="order-timeline">
                                    <div class="timeline-item">
                                        {{ $isArabic ? 'تم إنشاء الطلب' : 'Order created' }}
                                    </div>

                                    @if ($order->status === 'pending')
                                        <div class="timeline-item">
                                            {{ $isArabic ? 'الطلب قيد المراجعة' : 'Order is under review' }}
                                        </div>
                                    @endif

                                    @if ($order->status === 'accepted')
                                        <div class="timeline-item">
                                            {{ $isArabic ? 'تم قبول الطلب' : 'Order accepted' }}
                                        </div>
                                    @endif

                                    @if ($order->status === 'rejected')
                                        <div class="timeline-item">
                                            {{ $isArabic ? 'تم رفض الطلب' : 'Order rejected' }}
                                        </div>
                                    @endif

                                    @if ($order->status === 'cancelled')
                                        <div class="timeline-item">
                                            {{ $isArabic ? 'تم إلغاء الطلب' : 'Order cancelled' }}
                                        </div>
                                    @endif
                                </div>

                                @if ($order->details)
                                    <div style="margin-top:14px;color:#64748b;font-size:13px;line-height:1.9;">
                                        <strong style="color:#24304d;">{{ $isArabic ? 'التفاصيل:' : 'Details:' }}</strong>
                                        {{ $order->details }}
                                    </div>
                                @endif

                                <div class="order-actions">
                                    <a href="{{ route('chat.index') }}" class="mini-btn">
                                        {{ $isArabic ? 'فتح محادثة' : 'Open chat' }}
                                    </a>

                                    @if ($order->service)
                                        <a href="{{ route('services.show', $order->service->id) }}" class="mini-btn-ghost">
                                            {{ $isArabic ? 'التفاصيل' : 'Details' }}
                                        </a>
                                    @endif

                                    @if ($activeTab === 'received' && $order->status === 'pending')
                                        <form method="POST" action="{{ route('orders.accept', $order->id) }}">
                                            @csrf
                                            <button type="submit" class="mini-btn">
                                                {{ $isArabic ? 'قبول' : 'Accept' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('orders.reject', $order->id) }}">
                                            @csrf
                                            <button type="submit" class="mini-btn-danger">
                                                {{ $isArabic ? 'رفض' : 'Reject' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if ($activeTab === 'sent' && $order->status === 'pending')
                                        <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                                            @csrf
                                            <button type="submit" class="mini-btn-ghost">
                                                {{ $isArabic ? 'إلغاء الطلب' : 'Cancel order' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                @if ($activeTab === 'sent' && $order->status === 'accepted')
                                    @if ($order->rating)
                                        <div class="rating-display">
                                            <strong>{{ $isArabic ? 'تم إرسال التقييم' : 'Rating submitted' }}</strong>
                                            <div class="rating-score">
                                                {{ $isArabic ? 'التقييم:' : 'Score:' }} {{ $order->rating->score }}/5
                                            </div>

                                            @if ($order->rating->comment)
                                                <div>
                                                    <strong>{{ $isArabic ? 'التعليق:' : 'Comment:' }}</strong>
                                                    {{ $order->rating->comment }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="rating-box">
                                            <h4>{{ $isArabic ? 'إضافة تقييم للخدمة' : 'Add a service rating' }}</h4>

                                            <form method="POST" action="{{ route('ratings.store', $order->id) }}" class="rating-form">
                                                @csrf

                                                <div class="rating-form-row">
                                                    <label>{{ $isArabic ? 'التقييم' : 'Score' }}</label>
                                                    <select name="score" required>
                                                        <option value="">{{ $isArabic ? 'اختر التقييم' : 'Select score' }}</option>
                                                        <option value="5">5 - {{ $isArabic ? 'ممتاز' : 'Excellent' }}</option>
                                                        <option value="4">4 - {{ $isArabic ? 'جيد جدًا' : 'Very Good' }}</option>
                                                        <option value="3">3 - {{ $isArabic ? 'جيد' : 'Good' }}</option>
                                                        <option value="2">2 - {{ $isArabic ? 'مقبول' : 'Fair' }}</option>
                                                        <option value="1">1 - {{ $isArabic ? 'ضعيف' : 'Poor' }}</option>
                                                    </select>
                                                </div>

                                                <div class="rating-form-row">
                                                    <label>{{ $isArabic ? 'تعليق' : 'Comment' }}</label>
                                                    <textarea
                                                        name="comment"
                                                        placeholder="{{ $isArabic ? 'اكتب رأيك بالخدمة...' : 'Write your feedback about the service...' }}"
                                                    >{{ old('comment') }}</textarea>
                                                </div>

                                                <div class="order-actions" style="margin-top:0;">
                                                    <button type="submit" class="mini-btn">
                                                        {{ $isArabic ? 'إرسال التقييم' : 'Submit rating' }}
                                                    </button>
                                                </div>

                                                <div class="rating-note">
                                                    {{ $isArabic
                                                        ? 'يظهر التقييم فقط للطلبات المقبولة، ويمكن إرسال تقييم واحد فقط لكل طلب.'
                                                        : 'Ratings are available only for accepted orders, and only one rating can be submitted per order.' }}
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-orders">
                        {{ $activeTab === 'received'
                            ? ($isArabic ? 'لا توجد طلبات مستلمة حاليًا.' : 'There are no received orders right now.')
                            : ($isArabic ? 'لا توجد طلبات مرسلة حاليًا.' : 'There are no sent orders right now.') }}
                    </div>
                @endif
            </div>

            <div class="summary-card">
                <div class="panel-header">
                    <h2 class="panel-title">{{ $isArabic ? 'ملخص الطلبات' : 'Orders Summary' }}</h2>
                </div>

                <div class="summary-list">
                    <div class="summary-item">
                        <strong>{{ $isArabic ? 'متابعة أسرع' : 'Fast tracking' }}</strong>
                        <span>{{ $isArabic ? 'شاهد جميع الطلبات وحالتها الحالية ضمن واجهة واحدة.' : 'View all orders and their current statuses from one place.' }}</span>
                    </div>

                    <div class="summary-item">
                        <strong>{{ $isArabic ? 'تنسيق مع المزود' : 'Provider coordination' }}</strong>
                        <span>{{ $isArabic ? 'انتقل مباشرة إلى المحادثات عند الحاجة للتفاصيل أو المتابعة.' : 'Jump directly to conversations when you need details or follow-up.' }}</span>
                    </div>

                    <div class="summary-item">
                        <strong>{{ $isArabic ? 'ربط مع الخدمات' : 'Connected to services' }}</strong>
                        <span>{{ $isArabic ? 'كل طلب مرتبط بالخدمة الأصلية لتسهيل المراجعة واتخاذ القرار.' : 'Each order is connected to its original service for easier review and decisions.' }}</span>
                    </div>
                </div>

                <div class="summary-cta">
                    <a href="{{ route('estimations.create') }}" class="primary">
                        {{ $isArabic ? 'ابدأ التقدير الذكي' : 'Start estimation' }}
                    </a>
                    <a href="{{ route('services.index') }}" class="secondary">
                        {{ $isArabic ? 'استكشاف الخدمات' : 'Explore services' }}
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection