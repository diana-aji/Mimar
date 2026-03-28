@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <style>
        .crud-shell { display:grid; gap:22px; }
        .crud-grid { display:grid; grid-template-columns: 1fr 1fr; gap:20px; }
        .crud-card {
            background:#fff;
            border:1px solid rgba(15,23,42,.06);
            border-radius:24px;
            padding:24px;
            box-shadow:0 12px 30px rgba(15,23,42,.05);
        }
        .crud-title {
            margin:0 0 16px;
            font-size:24px;
            font-weight:800;
            color:#111827;
        }
        .crud-list { display:grid; gap:12px; }
        .crud-item {
            padding:16px;
            border-radius:18px;
            background:#f8fafc;
            border:1px solid rgba(15,23,42,.06);
        }
        .crud-item strong {
            display:block;
            font-size:16px;
            color:#111827;
            margin-bottom:6px;
        }
        .crud-meta {
            color:#64748b;
            font-size:13px;
            line-height:1.8;
        }
        .form-grid { display:grid; gap:14px; }
        .form-input, .form-select {
            width:100%;
            border:1px solid rgba(15,23,42,.08);
            border-radius:14px;
            padding:12px 14px;
            font-size:14px;
        }
        .btn-main, .btn-danger {
            height:42px;
            padding:0 16px;
            border:none;
            border-radius:999px;
            cursor:pointer;
            font-weight:800;
        }
        .btn-main {
            background:linear-gradient(135deg,#4458db 0%,#243873 100%);
            color:#fff;
        }
        .btn-danger {
            background:#fff1f2;
            color:#dc2626;
        }
        @media (max-width: 900px) {
            .crud-grid { grid-template-columns:1fr; }
        }
    </style>

    <div class="crud-shell">
        @if (session('success'))
            <div style="padding:14px 16px;border-radius:16px;background:rgba(5,150,105,.10);color:#059669;">
                {{ session('success') }}
            </div>
        @endif

        <div class="crud-grid">
            <div class="crud-card">
                <h2 class="crud-title">{{ $isArabic ? 'التصنيفات الحالية' : 'Existing Categories' }}</h2>

                <div class="crud-list">
                    @foreach ($categories as $category)
                        <div class="crud-item">
                            <strong>{{ $isArabic ? $category->name_ar : $category->name_en }}</strong>
                            <div class="crud-meta">
                                ID: {{ $category->id }} <br>
                                {{ $isArabic ? 'الترتيب' : 'Sort order' }}: {{ $category->sort_order }} <br>
                                {{ $isArabic ? 'الحالة' : 'Status' }}:
                                {{ $category->is_active ? ($isArabic ? 'مفعل' : 'Active') : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                            </div>

                            <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                                <a href="{{ route('admin.categories.index', ['selected' => $category->id]) }}" class="btn-main" style="display:inline-flex;align-items:center;text-decoration:none;">
                                    {{ $isArabic ? 'تعديل' : 'Edit' }}
                                </a>

                                <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">
                                        {{ $isArabic ? 'حذف' : 'Delete' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="crud-card">
                <h2 class="crud-title">
                    {{ $selectedCategory ? ($isArabic ? 'تعديل التصنيف' : 'Edit Category') : ($isArabic ? 'إضافة تصنيف' : 'Create Category') }}
                </h2>

                <form method="POST" action="{{ $selectedCategory ? route('admin.categories.update', $selectedCategory->id) : route('admin.categories.store') }}" class="form-grid">
                    @csrf
                    @if($selectedCategory)
                        @method('PUT')
                    @endif

                    <input type="text" name="name_ar" class="form-input" placeholder="الاسم بالعربية" value="{{ old('name_ar', $selectedCategory?->name_ar) }}" required>
                    <input type="text" name="name_en" class="form-input" placeholder="Name in English" value="{{ old('name_en', $selectedCategory?->name_en) }}" required>
                    <input type="text" name="icon" class="form-input" placeholder="Icon" value="{{ old('icon', $selectedCategory?->icon) }}">
                    <input type="number" name="sort_order" class="form-input" placeholder="Sort order" value="{{ old('sort_order', $selectedCategory?->sort_order ?? 0) }}">

                    <select name="is_active" class="form-select" required>
                        <option value="1" @selected(old('is_active', $selectedCategory?->is_active ?? 1) == 1)>
                            {{ $isArabic ? 'مفعل' : 'Active' }}
                        </option>
                        <option value="0" @selected(old('is_active', $selectedCategory?->is_active ?? 1) == 0)>
                            {{ $isArabic ? 'غير مفعل' : 'Inactive' }}
                        </option>
                    </select>

                    <button type="submit" class="btn-main">
                        {{ $selectedCategory ? ($isArabic ? 'حفظ التعديلات' : 'Save Changes') : ($isArabic ? 'إضافة التصنيف' : 'Create Category') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection