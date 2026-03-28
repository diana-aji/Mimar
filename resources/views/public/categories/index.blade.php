@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $categories = $categories ?? collect();

        $resolveCategoryName = function ($category) use ($isArabic) {
            return $isArabic
                ? ($category->name_ar ?? $category->name_en ?? '—')
                : ($category->name_en ?? $category->name_ar ?? '—');
        };

        $resolveSubcategoryName = function ($subcategory) use ($isArabic) {
            return $isArabic
                ? ($subcategory->name_ar ?? $subcategory->name_en ?? '—')
                : ($subcategory->name_en ?? $subcategory->name_ar ?? '—');
        };

        $resolveCategoryImage = function ($category) {
            $name = strtolower($category->name_en ?? $category->name_ar ?? '');

            if (str_contains($name, 'interior') || str_contains($name, 'finish') || str_contains($name, 'تشطيب')) {
                return 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80';
            }

            if (str_contains($name, 'build') || str_contains($name, 'construction') || str_contains($name, 'بناء')) {
                return 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200&q=80';
            }

            if (str_contains($name, 'plumb') || str_contains($name, 'electric') || str_contains($name, 'تمديد')) {
                return 'https://images.unsplash.com/photo-1581092918484-8313b4a3c1d1?auto=format&fit=crop&w=1200&q=80';
            }

            if (str_contains($name, 'paint') || str_contains($name, 'دهان')) {
                return 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?auto=format&fit=crop&w=1200&q=80';
            }

            if (str_contains($name, 'ceramic') || str_contains($name, 'tile') || str_contains($name, 'سيراميك')) {
                return 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80';
            }

            return 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1200&q=80';
        };

        $activeCategories = $categories->where('is_active', true);
        $subcategoriesCount = $categories->sum(fn ($category) => $category->subcategories->count());
        $servicesCount = $categories->sum(fn ($category) => $category->services_count ?? 0);
    @endphp

    <style>
        .categories-shell {
            display: grid;
            gap: 26px;
        }

        .categories-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            min-height: 360px;
            background:
                linear-gradient(135deg, rgba(11,17,32,.94) 0%, rgba(26,45,93,.88) 52%, rgba(45,73,139,.82) 100%),
                url('https://images.unsplash.com/photo-1511818966892-d7d671e672a2?auto=format&fit=crop&w=1800&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            box-shadow: 0 28px 70px rgba(15,23,42,0.18);
        }

        .categories-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.25), rgba(0,0,0,.92));
        }

        .categories-hero-inner {
            position: relative;
            z-index: 1;
            padding: 32px;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 22px;
            align-items: center;
            min-height: 360px;
        }

        .categories-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .categories-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d4a95f;
        }

        .categories-title {
            margin: 0 0 12px;
            font-size: 46px;
            line-height: 1.04;
            font-weight: 800;
            letter-spacing: -0.05em;
        }

        .categories-copy {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.95;
            max-width: 760px;
        }

        .categories-search {
            margin-top: 20px;
            max-width: 540px;
        }

        .categories-search input {
            width: 100%;
            height: 54px;
            border: 1px solid rgba(255,255,255,0.14);
            background: rgba(255,255,255,0.10);
            color: white;
            border-radius: 999px;
            padding: 0 18px;
            outline: none;
            font-size: 14px;
            backdrop-filter: blur(8px);
        }

        .categories-search input::placeholder {
            color: rgba(255,255,255,0.66);
        }

        .categories-panel {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .categories-stat {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 20px;
            backdrop-filter: blur(10px);
            min-height: 118px;
            display: grid;
            align-content: center;
            gap: 8px;
        }

        .categories-stat-number {
            font-size: 38px;
            font-weight: 800;
            color: white;
            line-height: 1;
        }

        .categories-stat-label {
            color: rgba(255,255,255,0.82);
            font-size: 13px;
            line-height: 1.8;
            font-weight: 700;
        }

        .categories-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .categories-toolbar h2 {
            margin: 0;
            font-size: 26px;
            color: #24304d;
            font-weight: 800;
        }

        .categories-toolbar-text {
            color: #64748b;
            font-size: 14px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .category-card {
            background: rgba(255,255,255,0.98);
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 14px 34px rgba(15,23,42,0.06);
            transition: .24s ease;
            display: grid;
        }

        .category-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 42px rgba(15,23,42,0.12);
        }

        .category-card-cover {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .category-card-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .35s ease;
        }

        .category-card:hover .category-card-cover img {
            transform: scale(1.05);
        }

        .category-card-cover::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15,23,42,.50), transparent 58%);
        }

        .category-badge {
            position: absolute;
            top: 14px;
            inset-inline-start: 14px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.92);
            color: #243873;
            font-size: 12px;
            font-weight: 800;
        }

        .category-services-badge {
            position: absolute;
            bottom: 14px;
            inset-inline-end: 14px;
            z-index: 2;
            height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(17,24,39,0.68);
            backdrop-filter: blur(8px);
            color: white;
            font-size: 12px;
            font-weight: 800;
        }

        .category-body {
            padding: 20px;
            display: grid;
            gap: 12px;
        }

        .category-title-row {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
        }

        .category-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #24304d;
            line-height: 1.2;
        }

        .category-count {
            flex-shrink: 0;
            min-width: 46px;
            height: 46px;
            border-radius: 16px;
            background: rgba(68,88,219,0.08);
            color: #4458db;
            display: grid;
            place-items: center;
            font-size: 14px;
            font-weight: 800;
        }

        .category-text {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
            min-height: 54px;
        }

        .subcategory-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .subcategory-tag {
            padding: 7px 10px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
            color: #334155;
            font-size: 12px;
            font-weight: 700;
        }

        .category-footer {
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .category-status {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .category-link {
            height: 42px;
            padding: 0 15px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            font-size: 13px;
            font-weight: 800;
        }

        .empty-categories {
            padding: 40px;
            border-radius: 24px;
            background: #fff;
            border: 1px dashed rgba(15,23,42,0.10);
            color: #64748b;
            text-align: center;
            font-size: 14px;
            line-height: 1.9;
        }

        @media (max-width: 1100px) {
            .categories-hero-inner {
                grid-template-columns: 1fr;
            }

            .categories-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 767px) {
            .categories-title {
                font-size: 32px;
            }

            .categories-grid,
            .categories-panel {
                grid-template-columns: 1fr;
            }

            .categories-hero-inner {
                padding: 22px;
            }
        }
    </style>

    <div class="categories-shell">
        <section class="categories-hero">
            <div class="categories-hero-inner">
                <div>
                    <span class="categories-kicker">
                        {{ $isArabic ? 'تصفح ذكي' : 'Smart browsing' }}
                    </span>

                    <h1 class="categories-title">
                        {{ $isArabic ? 'اكتشف التصنيفات والخدمات بشكل أفخم وأوضح' : 'Discover categories and services in a richer way' }}
                    </h1>

                    <p class="categories-copy">
                        {{ $isArabic
                            ? 'استعرض التصنيفات الرئيسية والتصنيفات الفرعية والخدمات المرتبطة بها داخل منصة Mi\'mar، وانتقل بسرعة إلى المجال المناسب لمشروعك.'
                            : 'Browse main categories, subcategories, and related services inside Mi\'mar, and move faster to the field that fits your project.' }}
                    </p>

                    <div class="categories-search">
                        <input
                            type="text"
                            id="categorySearch"
                            placeholder="{{ $isArabic ? 'ابحث عن تصنيف أو تصنيف فرعي...' : 'Search for a category or subcategory...' }}"
                        >
                    </div>
                </div>

                <div class="categories-panel">
                    <div class="categories-stat">
                        <div class="categories-stat-number">{{ $categories->count() }}</div>
                        <div class="categories-stat-label">
                            {{ $isArabic ? 'التصنيفات الرئيسية' : 'Main categories' }}
                        </div>
                    </div>

                    <div class="categories-stat">
                        <div class="categories-stat-number">{{ $subcategoriesCount }}</div>
                        <div class="categories-stat-label">
                            {{ $isArabic ? 'التصنيفات الفرعية' : 'Subcategories' }}
                        </div>
                    </div>

                    <div class="categories-stat">
                        <div class="categories-stat-number">{{ $servicesCount }}</div>
                        <div class="categories-stat-label">
                            {{ $isArabic ? 'الخدمات المعتمدة المرتبطة' : 'Approved related services' }}
                        </div>
                    </div>

                    <div class="categories-stat">
                        <div class="categories-stat-number">{{ $activeCategories->count() }}</div>
                        <div class="categories-stat-label">
                            {{ $isArabic ? 'تصنيفات فعالة حالياً' : 'Currently active categories' }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="categories-toolbar">
            <div>
                <h2>{{ $isArabic ? 'جميع التصنيفات' : 'All Categories' }}</h2>
                <div class="categories-toolbar-text">
                    {{ $isArabic ? 'استعرض المجالات المتوفرة داخل المنصة مع نظرة سريعة على التصنيفات الفرعية والخدمات.' : 'Browse available fields with a quick look at subcategories and services.' }}
                </div>
            </div>
        </div>

        @if ($categories->count())
            <section class="categories-grid" id="categoriesGrid">
                @foreach ($categories as $category)
                    @php
                        $categoryName = $resolveCategoryName($category);
                        $categoryImage = $resolveCategoryImage($category);
                        $subs = $category->subcategories ?? collect();
                        $servicesInCategory = $category->services_count ?? 0;
                    @endphp

                    <article
                        class="category-card searchable-category"
                        data-search="{{ strtolower($categoryName . ' ' . $subs->pluck('name_ar')->implode(' ') . ' ' . $subs->pluck('name_en')->implode(' ')) }}"
                    >
                        <div class="category-card-cover">
                            <img src="{{ $categoryImage }}" alt="{{ $categoryName }}">

                            <span class="category-badge">
                                {{ $isArabic ? 'تصنيف رئيسي' : 'Main Category' }}
                            </span>

                            <span class="category-services-badge">
                                {{ $servicesInCategory }} {{ $isArabic ? 'خدمة' : 'services' }}
                            </span>
                        </div>

                        <div class="category-body">
                            <div class="category-title-row">
                                <h2 class="category-title">{{ $categoryName }}</h2>
                                <div class="category-count">{{ $subs->count() }}</div>
                            </div>

                            <p class="category-text">
                                {{ $isArabic
                                    ? 'استكشف الخدمات المتوفرة ضمن هذا المجال وتعرف على التفرعات المرتبطة به قبل الانتقال للخدمة المناسبة.'
                                    : 'Explore the services available in this field and discover its subcategories before moving to the right service.' }}
                            </p>

                            @if ($subs->count())
                                <div class="subcategory-tags">
                                    @foreach ($subs->take(5) as $subcategory)
                                        <span class="subcategory-tag">
                                            {{ $resolveSubcategoryName($subcategory) }}
                                        </span>
                                    @endforeach

                                    @if ($subs->count() > 5)
                                        <span class="subcategory-tag">+{{ $subs->count() - 5 }}</span>
                                    @endif
                                </div>
                            @else
                                <div class="subcategory-tags">
                                    <span class="subcategory-tag">
                                        {{ $isArabic ? 'لا توجد تصنيفات فرعية حالياً' : 'No subcategories yet' }}
                                    </span>
                                </div>
                            @endif

                            <div class="category-footer">
                                <div class="category-status">
                                    {{ $category->is_active
                                        ? ($isArabic ? 'فعال داخل المنصة' : 'Active on platform')
                                        : ($isArabic ? 'غير مفعل حالياً' : 'Currently inactive') }}
                                </div>

                                <a href="{{ route('services.index') }}" class="category-link">
                                    {{ $isArabic ? 'عرض الخدمات' : 'View services' }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <div class="empty-categories">
                {{ $isArabic
                    ? 'لا توجد تصنيفات متاحة حالياً. أضف التصنيفات من لوحة التحكم لتظهر هنا بشكل مميز.'
                    : 'No categories are available right now. Add categories from the admin panel to display them here beautifully.' }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('categorySearch');
            const cards = document.querySelectorAll('.searchable-category');

            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const value = this.value.toLowerCase().trim();

                cards.forEach((card) => {
                    const text = card.dataset.search || '';
                    card.style.display = text.includes(value) ? '' : 'none';
                });
            });
        });
    </script>
@endsection