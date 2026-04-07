@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <style>
        .exchange-shell {
            display: grid;
            gap: 22px;
        }

        .exchange-card {
            background: #fff;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid rgba(15,23,42,.06);
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
        }

        .exchange-title {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 800;
            color: #24304d;
        }

        .exchange-sub {
            margin: 0 0 20px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
        }

        .exchange-alert-success,
        .exchange-alert-error {
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .exchange-alert-success {
            background: rgba(5,150,105,.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,.12);
        }

        .exchange-alert-error {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .exchange-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .exchange-group {
            display: grid;
            gap: 8px;
        }

        .exchange-label {
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .exchange-input,
        .exchange-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 14px;
            color: #0f172a;
            background: #fff;
            outline: none;
        }

        .exchange-input:focus,
        .exchange-select:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,.10);
        }

        .exchange-btn {
            height: 44px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        .exchange-table-wrap {
            overflow-x: auto;
        }

        .exchange-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
        }

        .exchange-table th,
        .exchange-table td {
            padding: 12px;
            border-bottom: 1px solid #eef2f7;
            text-align: start;
            vertical-align: middle;
        }

        .exchange-table th {
            color: #475569;
            font-size: 13px;
            font-weight: 800;
        }

        .exchange-table td {
            color: #24304d;
            font-size: 14px;
        }

        .badge-active,
        .badge-inactive {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .badge-active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .badge-inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .row-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .small-btn {
            height: 36px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,.08);
            background: #f8fafc;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .small-btn.danger {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .small-inline-input {
            width: 120px;
            border: 1px solid rgba(15,23,42,.08);
            border-radius: 12px;
            padding: 9px 10px;
            font-size: 13px;
            outline: none;
        }

        .small-inline-input:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 3px rgba(68,88,219,.10);
        }

        .empty-row {
            color: #64748b;
            text-align: center;
            padding: 18px 12px;
        }

        @media (max-width: 900px) {
            .exchange-form {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="exchange-shell">
        <div class="exchange-card">
            <h1 class="exchange-title">{{ $isArabic ? 'إدارة أسعار الصرف' : 'Manage Exchange Rates' }}</h1>

            <p class="exchange-sub">
                {{ $isArabic
                    ? 'من هنا يمكنك إدارة سعر التحويل بين الدولار والليرة السورية لعرض أسعار الخدمات حسب العملة المختارة من المستخدم.'
                    : 'From here you can manage conversion rates between USD and SYP for displaying service prices based on the user selected currency.' }}
            </p>

            @if (session('success'))
                <div class="exchange-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="exchange-alert-error">
                    {{ $isArabic ? 'يرجى مراجعة الحقول وتصحيح الأخطاء.' : 'Please review the fields and fix the errors.' }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.exchange-rates.store') }}" class="exchange-form">
                @csrf

                <div class="exchange-group">
                    <label class="exchange-label">{{ $isArabic ? 'من عملة' : 'Base currency' }}</label>
                    <select name="base_currency" class="exchange-select" required>
                        <option value="USD" @selected(old('base_currency') === 'USD')>USD</option>
                        <option value="SYP" @selected(old('base_currency', 'SYP') === 'SYP')>SYP</option>
                    </select>
                    @error('base_currency') <div style="color:#dc2626; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <div class="exchange-group">
                    <label class="exchange-label">{{ $isArabic ? 'إلى عملة' : 'Target currency' }}</label>
                    <select name="target_currency" class="exchange-select" required>
                        <option value="SYP" @selected(old('target_currency', 'SYP') === 'SYP')>SYP</option>
                        <option value="USD" @selected(old('target_currency') === 'USD')>USD</option>
                    </select>
                    @error('target_currency') <div style="color:#dc2626; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <div class="exchange-group">
                    <label class="exchange-label">{{ $isArabic ? 'سعر الصرف' : 'Exchange rate' }}</label>
                    <input type="number" step="0.0001" min="0.0001" name="rate" value="{{ old('rate') }}" class="exchange-input" required>
                    @error('rate') <div style="color:#dc2626; font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <div class="exchange-group" style="align-self:end;">
                    <button type="submit" class="exchange-btn">
                        {{ $isArabic ? 'حفظ السعر' : 'Save Rate' }}
                    </button>
                </div>
            </form>

            <div class="exchange-table-wrap">
                <table class="exchange-table">
                    <thead>
                        <tr>
                            <th>{{ $isArabic ? 'من' : 'From' }}</th>
                            <th>{{ $isArabic ? 'إلى' : 'To' }}</th>
                            <th>{{ $isArabic ? 'السعر الحالي' : 'Current rate' }}</th>
                            <th>{{ $isArabic ? 'الحالة' : 'Status' }}</th>
                            <th>{{ $isArabic ? 'تعديل السعر' : 'Update rate' }}</th>
                            <th>{{ $isArabic ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                            <tr>
                                <td>{{ $rate->base_currency }}</td>
                                <td>{{ $rate->target_currency }}</td>
                                <td>{{ number_format((float) $rate->rate, 4) }}</td>
                                <td>
                                    <span class="{{ $rate->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $rate->is_active ? ($isArabic ? 'فعال' : 'Active') : ($isArabic ? 'غير فعال' : 'Inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.exchange-rates.update', $rate) }}" class="row-actions">
                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="number"
                                            step="0.0001"
                                            min="0.0001"
                                            name="rate"
                                            value="{{ $rate->rate }}"
                                            class="small-inline-input"
                                            required
                                        >

                                        <input type="hidden" name="is_active" value="{{ $rate->is_active ? 1 : 0 }}">

                                        <button type="submit" class="small-btn">
                                            {{ $isArabic ? 'حفظ التعديل' : 'Save' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <form method="POST" action="{{ route('admin.exchange-rates.update', $rate) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="rate" value="{{ $rate->rate }}">
                                            <input type="hidden" name="is_active" value="{{ $rate->is_active ? 0 : 1 }}">
                                            <button type="submit" class="small-btn">
                                                {{ $rate->is_active
                                                    ? ($isArabic ? 'إلغاء التفعيل' : 'Deactivate')
                                                    : ($isArabic ? 'تفعيل' : 'Activate') }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.exchange-rates.destroy', $rate) }}" onsubmit="return confirm('{{ $isArabic ? 'هل أنت متأكد من حذف سعر الصرف؟' : 'Are you sure you want to delete this rate?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="small-btn danger">
                                                {{ $isArabic ? 'حذف' : 'Delete' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-row">
                                    {{ $isArabic ? 'لا يوجد أسعار صرف مضافة بعد.' : 'No exchange rates added yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection