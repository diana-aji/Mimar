@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $selectedSlider = $selectedSlider ?? null;
    @endphp

    <style>
        .sliders-shell {
            display: grid;
            gap: 24px;
        }

        .sliders-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 20px;
            align-items: start;
        }

        .sliders-card,
        .sliders-form-card {
            background: #fff;
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(15,23,42,.05);
            padding: 24px;
        }

        .sliders-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .sliders-head h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .slider-list {
            display: grid;
            gap: 12px;
        }

        .slider-item {
            display: block;
            text-decoration: none;
            padding: 16px;
            border-radius: 20px;
            background: #fafbff;
            border: 1px solid rgba(15,23,42,.06);
            transition: .2s ease;
        }

        .slider-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15,23,42,.06);
        }

        .slider-item.active {
            border-color: rgba(59,130,246,.25);
            background: #eff6ff;
        }

        .slider-item strong {
            display: block;
            color: #111827;
            margin-bottom: 6px;
            font-size: 16px;
        }

        .slider-item span {
            display: block;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .slider-badge {
            margin-top: 8px;
            display: inline-flex;
            height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            align-items: center;
            font-size: 12px;
            font-weight: 800;
        }

        .slider-badge.active {
            background: rgba(5,150,105,.10);
            color: #059669;
        }

        .slider-badge.inactive {
            background: rgba(239,68,68,.10);
            color: #dc2626;
        }

        .slider-form {
            display: grid;
            gap: 14px;
        }

        .slider-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .slider-group {
            display: grid;
            gap: 8px;
        }

        .slider-group.full {
            grid-column: 1 / -1;
        }

        .slider-label {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
        }

        .slider-input,
        .slider-textarea,
        .slider-select {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 14px;
            outline: none;
        }

        .slider-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .slider-input:focus,
        .slider-textarea:focus,
        .slider-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,.10);
        }

        .slider-error {
            color: #dc2626;
            font-size: 12px;
        }

        .slider-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .slider-btn-primary,
        .slider-btn-secondary,
        .slider-btn-danger {
            height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .slider-btn-primary {
            background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
            color: #fff;
        }

        .slider-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,.08);
        }

        .slider-btn-danger {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.12);
        }

        .slider-preview {
            margin-top: 16px;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(15,23,42,.06);
            background: #f8fafc;
        }

        .slider-preview img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .slider-preview-body {
            padding: 16px;
        }

        .slider-preview-body strong {
            display: block;
            color: #111827;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .slider-empty {
            padding: 28px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.10);
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .sliders-grid,
            .slider-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sliders-shell">
        @if (session('success'))
            <div style="padding:14px 16px;border-radius:18px;background:rgba(5,150,105,.10);color:#059669;border:1px solid rgba(5,150,105,.12);font-size:14px;font-weight:700;">
                {{ session('success') }}
            </div>
        @endif

        <section class="sliders-grid">
            <div class="sliders-card">
                <div class="sliders-head">
                    <h2>{{ $isArabic ? 'السلايدر' : 'Sliders' }}</h2>
                    <span>{{ $sliders->total() }}</span>
                </div>

                @if ($sliders->count())
                    <div class="slider-list">
                        @foreach ($sliders as $slider)
                            <a href="{{ route('admin.sliders.index', ['selected' => $slider->id]) }}" class="slider-item {{ $selectedSlider && $selectedSlider->id === $slider->id ? 'active' : '' }}">
                                <strong>{{ $isArabic ? $slider->title_ar : $slider->title_en }}</strong>
                                <span>{{ $slider->button_url ?? '—' }}</span>
                                <span>{{ $isArabic ? 'الترتيب:' : 'Order:' }} {{ $slider->sort_order }}</span>

                                <span class="slider-badge {{ $slider->is_active ? 'active' : 'inactive' }}">
                                    {{ $slider->is_active ? ($isArabic ? 'مفعل' : 'Active') : ($isArabic ? 'غير مفعل' : 'Inactive') }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div style="margin-top:18px;">
                        {{ $sliders->withQueryString()->links() }}
                    </div>
                @else
                    <div class="slider-empty">{{ $isArabic ? 'لا توجد عناصر سلايدر حالياً.' : 'No sliders yet.' }}</div>
                @endif
            </div>

            <div class="sliders-form-card">
                <div class="sliders-head">
                    <h2>{{ $selectedSlider ? ($isArabic ? 'تعديل السلايدر' : 'Edit slider') : ($isArabic ? 'إضافة سلايدر' : 'Create slider') }}</h2>
                </div>

                <form method="POST" action="{{ $selectedSlider ? route('admin.sliders.update', $selectedSlider->id) : route('admin.sliders.store') }}" class="slider-form">
                    @csrf
                    @if ($selectedSlider)
                        @method('PUT')
                    @endif

                    <div class="slider-grid">
                        <div class="slider-group">
                            <label class="slider-label">{{ $isArabic ? 'العنوان بالعربية' : 'Arabic title' }}</label>
                            <input type="text" name="title_ar" class="slider-input" value="{{ old('title_ar', $selectedSlider->title_ar ?? '') }}">
                            @error('title_ar') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group">
                            <label class="slider-label">{{ $isArabic ? 'العنوان بالإنجليزية' : 'English title' }}</label>
                            <input type="text" name="title_en" class="slider-input" value="{{ old('title_en', $selectedSlider->title_en ?? '') }}">
                            @error('title_en') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group full">
                            <label class="slider-label">{{ $isArabic ? 'الوصف بالعربية' : 'Arabic subtitle' }}</label>
                            <textarea name="subtitle_ar" class="slider-textarea">{{ old('subtitle_ar', $selectedSlider->subtitle_ar ?? '') }}</textarea>
                            @error('subtitle_ar') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group full">
                            <label class="slider-label">{{ $isArabic ? 'الوصف بالإنجليزية' : 'English subtitle' }}</label>
                            <textarea name="subtitle_en" class="slider-textarea">{{ old('subtitle_en', $selectedSlider->subtitle_en ?? '') }}</textarea>
                            @error('subtitle_en') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group full">
                            <label class="slider-label">{{ $isArabic ? 'رابط الصورة' : 'Image URL / path' }}</label>
                            <input type="text" name="image" class="slider-input" value="{{ old('image', $selectedSlider->image ?? '') }}">
                            @error('image') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group">
                            <label class="slider-label">{{ $isArabic ? 'زر بالعربية' : 'Arabic button text' }}</label>
                            <input type="text" name="button_text_ar" class="slider-input" value="{{ old('button_text_ar', $selectedSlider->button_text_ar ?? '') }}">
                            @error('button_text_ar') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group">
                            <label class="slider-label">{{ $isArabic ? 'زر بالإنجليزية' : 'English button text' }}</label>
                            <input type="text" name="button_text_en" class="slider-input" value="{{ old('button_text_en', $selectedSlider->button_text_en ?? '') }}">
                            @error('button_text_en') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group full">
                            <label class="slider-label">{{ $isArabic ? 'رابط الزر' : 'Button URL' }}</label>
                            <input type="text" name="button_url" class="slider-input" value="{{ old('button_url', $selectedSlider->button_url ?? '') }}">
                            @error('button_url') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group">
                            <label class="slider-label">{{ $isArabic ? 'الترتيب' : 'Sort order' }}</label>
                            <input type="number" name="sort_order" class="slider-input" value="{{ old('sort_order', $selectedSlider->sort_order ?? 0) }}">
                            @error('sort_order') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="slider-group">
                            <label class="slider-label">{{ $isArabic ? 'الحالة' : 'Status' }}</label>
                            <select name="is_active" class="slider-select">
                                <option value="1" @selected(old('is_active', $selectedSlider->is_active ?? true) == 1)>{{ $isArabic ? 'مفعل' : 'Active' }}</option>
                                <option value="0" @selected(old('is_active', $selectedSlider->is_active ?? true) == 0)>{{ $isArabic ? 'غير مفعل' : 'Inactive' }}</option>
                            </select>
                            @error('is_active') <div class="slider-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="slider-actions">
                        <button type="submit" class="slider-btn-primary">
                            {{ $selectedSlider ? ($isArabic ? 'حفظ التعديلات' : 'Save changes') : ($isArabic ? 'إضافة السلايدر' : 'Create slider') }}
                        </button>

                        @if ($selectedSlider)
                            <a href="{{ route('admin.sliders.index') }}" class="slider-btn-secondary">
                                {{ $isArabic ? 'عنصر جديد' : 'New item' }}
                            </a>
                        @endif
                    </div>
                </form>

                @if ($selectedSlider)
                    <form method="POST" action="{{ route('admin.sliders.destroy', $selectedSlider->id) }}" style="margin-top:14px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="slider-btn-danger">
                            {{ $isArabic ? 'حذف السلايدر' : 'Delete slider' }}
                        </button>
                    </form>

                    <div class="slider-preview">
                        <img src="{{ \Illuminate\Support\Str::startsWith($selectedSlider->image, ['http://', 'https://']) ? $selectedSlider->image : asset('storage/' . ltrim($selectedSlider->image, '/')) }}" alt="slider image">
                        <div class="slider-preview-body">
                            <strong>{{ $isArabic ? $selectedSlider->title_ar : $selectedSlider->title_en }}</strong>
                            <div style="margin-top:8px;color:#64748b;font-size:14px;line-height:1.8;">
                                {{ $isArabic ? $selectedSlider->subtitle_ar : $selectedSlider->subtitle_en }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection