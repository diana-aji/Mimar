@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $initialLat = old('latitude', $service->latitude ?? 33.5138);
        $initialLng = old('longitude', $service->longitude ?? 36.2765);
    @endphp

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />

    <style>
        .service-form-shell {
            display: grid;
            gap: 22px;
        }

        .service-form-hero {
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

        .service-form-hero h1 {
            margin: 0 0 10px;
            font-size: 40px;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .service-form-hero p {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
            line-height: 1.95;
        }

        .service-form-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            padding: 24px;
        }

        .service-form-title {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.08;
            font-weight: 800;
            color: #24304d;
        }

        .service-form-subtitle {
            margin: 0 0 20px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .service-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .service-form-group {
            display: grid;
            gap: 8px;
        }

        .service-form-group.full {
            grid-column: 1 / -1;
        }

        .service-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .service-input,
        .service-select,
        .service-textarea,
        .service-file {
            width: 100%;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .service-textarea {
            min-height: 140px;
            resize: vertical;
        }

        .service-input:focus,
        .service-select:focus,
        .service-textarea:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }

        .service-file {
            border-style: dashed;
        }

        .service-help {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
            margin-top: 4px;
        }

        .service-error {
            color: #dc2626;
            font-size: 12px;
        }

        .service-alert {
            background: rgba(239,68,68,0.08);
            color: #b91c1c;
            border: 1px solid rgba(239,68,68,0.12);
            border-radius: 18px;
            padding: 14px 16px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .service-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .service-btn-primary,
        .service-btn-secondary {
            height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            border: none;
            cursor: pointer;
        }

        .service-btn-primary {
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            box-shadow: 0 14px 26px rgba(36,56,115,0.16);
        }

        .service-btn-secondary {
            background: #f8fafc;
            color: #334155;
            border: 1px solid rgba(15,23,42,0.08);
        }

        .current-images {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .current-image-card {
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 16px;
            overflow: hidden;
        }

        .current-image-card img {
            width: 100%;
            height: 130px;
            object-fit: cover;
            display: block;
        }

        .current-image-meta {
            padding: 10px 12px;
            font-size: 12px;
            color: #475569;
        }

        .service-map-wrap {
            display: grid;
            gap: 10px;
        }

        .service-map-box {
            width: 100%;
            height: 360px;
            border-radius: 22px;
            border: 1px solid rgba(15,23,42,.08);
            overflow: hidden;
            background: #e2e8f0;
        }

        .service-coordinates-preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .service-coordinate-chip {
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.06);
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .service-open-map-link {
            color: #4458db;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .service-currency-note {
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(68,88,219,0.06);
            color: #334155;
            font-size: 12px;
            line-height: 1.8;
            border: 1px solid rgba(68,88,219,0.10);
        }

        @media (max-width: 767px) {
            .service-form-grid,
            .current-images,
            .service-coordinates-preview {
                grid-template-columns: 1fr;
            }

            .service-form-hero h1 {
                font-size: 30px;
            }

            .service-map-box {
                height: 300px;
            }
        }
    </style>

    <div class="service-form-shell">
        <section class="service-form-hero">
            <h1>{{ $isArabic ? 'تعديل الخدمة' : 'Edit service' }}</h1>
            <p>
                {{ $isArabic
                    ? 'حدّث بيانات الخدمة الحالية، وعدّل موقعها وعملتها وصورها عند الحاجة.'
                    : 'Update the current service details, adjust its location, currency, and images when needed.' }}
            </p>
        </section>

        <section class="service-form-card">
            <h2 class="service-form-title">{{ $isArabic ? 'تحديث بيانات الخدمة' : 'Update service details' }}</h2>
            <p class="service-form-subtitle">
                {{ $isArabic
                    ? 'يمكنك تعديل الحقول الحالية وإضافة صور جديدة. سيتم الحفاظ على الصور المرفوعة سابقًا.'
                    : 'You can update the current fields and add new images. Previously uploaded images will be preserved.' }}
            </p>

            @if ($errors->any())
                <div class="service-alert">
                    {{ $isArabic ? 'يرجى مراجعة الحقول وإصلاح الأخطاء الظاهرة أدناه.' : 'Please review the fields and fix the errors shown below.' }}
                </div>
            @endif

            <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="service-form-grid">
                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'حساب الأعمال' : 'Business account' }}</label>
                        <select class="service-select" name="business_account_id" required>
                            <option value="">{{ $isArabic ? 'اختر حساب الأعمال' : 'Select business account' }}</option>
                            @foreach ($businessAccounts as $businessAccount)
                                <option value="{{ $businessAccount->id }}" @selected(old('business_account_id', $service->business_account_id) == $businessAccount->id)>
                                    {{ $isArabic
                                        ? ($businessAccount->name_ar ?? $businessAccount->name_en ?? ('#' . $businessAccount->id))
                                        : ($businessAccount->name_en ?? $businessAccount->name_ar ?? ('#' . $businessAccount->id)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_account_id') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'التصنيف' : 'Category' }}</label>
                        <select class="service-select" name="category_id" required>
                            <option value="">{{ $isArabic ? 'اختر التصنيف' : 'Select category' }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $service->category_id) == $category->id)>
                                    {{ $isArabic
                                        ? ($category->name_ar ?? $category->name_en ?? ('#' . $category->id))
                                        : ($category->name_en ?? $category->name_ar ?? ('#' . $category->id)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'التصنيف الفرعي' : 'Subcategory' }}</label>
                        <select class="service-select" name="subcategory_id" required>
                            <option value="">{{ $isArabic ? 'اختر التصنيف الفرعي' : 'Select subcategory' }}</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $service->subcategory_id) == $subcategory->id)>
                                    {{ $isArabic
                                        ? ($subcategory->name_ar ?? $subcategory->name_en ?? ('#' . $subcategory->id))
                                        : ($subcategory->name_en ?? $subcategory->name_ar ?? ('#' . $subcategory->id)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcategory_id') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'السعر الأصلي' : 'Base price' }}</label>
                        <input class="service-input" type="number" step="0.01" name="price" value="{{ old('price', $service->price) }}" required>
                        @error('price') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'عملة التسعير' : 'Pricing currency' }}</label>
                        <select class="service-select" name="currency" required>
                            <option value="SYP" @selected(old('currency', $service->currency ?? 'SYP') === 'SYP')>
                                {{ $isArabic ? 'ليرة سورية (SYP)' : 'Syrian Pound (SYP)' }}
                            </option>
                            <option value="USD" @selected(old('currency', $service->currency) === 'USD')>
                                {{ $isArabic ? 'دولار أمريكي (USD)' : 'US Dollar (USD)' }}
                            </option>
                        </select>
                        @error('currency') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group full">
                        <div class="service-currency-note">
                            {{ $isArabic
                                ? 'هذا هو السعر الأصلي المخزن للخدمة. يمكن لاحقاً عرضه للمستخدم حسب عملة العرض المختارة وسعر الصرف.'
                                : 'This is the base stored service price. Later, it can be displayed to the user according to the chosen display currency and exchange rate.' }}
                        </div>
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'اسم الخدمة بالعربية' : 'Service name (Arabic)' }}</label>
                        <input class="service-input" type="text" name="name_ar" value="{{ old('name_ar', $service->name_ar) }}" required>
                        @error('name_ar') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'اسم الخدمة بالإنجليزية' : 'Service name (English)' }}</label>
                        <input class="service-input" type="text" name="name_en" value="{{ old('name_en', $service->name_en) }}" required>
                        @error('name_en') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group full">
                        <label class="service-label">{{ $isArabic ? 'الوصف' : 'Description' }}</label>
                        <textarea class="service-textarea" name="description">{{ old('description', $service->description) }}</textarea>
                        @error('description') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group full">
                        <label class="service-label">{{ $isArabic ? 'حدد موقع الخدمة على الخريطة' : 'Pick service location on map' }}</label>

                        <div class="service-map-wrap">
                            <div id="serviceMap" class="service-map-box"></div>

                            <div class="service-help">
                                {{ $isArabic
                                    ? 'اضغط على الخريطة لتحديد موقع الخدمة أو اسحب العلامة إلى المكان الصحيح.'
                                    : 'Click on the map to select the service location or drag the marker to the correct place.' }}
                            </div>
                        </div>
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'خط العرض' : 'Latitude' }}</label>
                        <input id="latitudeInput" class="service-input" type="text" name="latitude" value="{{ old('latitude', $service->latitude) }}" readonly>
                        @error('latitude') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group">
                        <label class="service-label">{{ $isArabic ? 'خط الطول' : 'Longitude' }}</label>
                        <input id="longitudeInput" class="service-input" type="text" name="longitude" value="{{ old('longitude', $service->longitude) }}" readonly>
                        @error('longitude') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="service-form-group full">
                        <div class="service-coordinates-preview">
                            <div class="service-coordinate-chip">
                                Latitude:
                                <span id="latitudePreview">{{ old('latitude', $service->latitude ?? '—') }}</span>
                            </div>

                            <div class="service-coordinate-chip">
                                Longitude:
                                <span id="longitudePreview">{{ old('longitude', $service->longitude ?? '—') }}</span>
                            </div>
                        </div>

                        @if ($service->latitude && $service->longitude)
                            <a
                                class="service-open-map-link"
                                href="https://www.google.com/maps?q={{ $service->latitude }},{{ $service->longitude }}"
                                target="_blank"
                            >
                                {{ $isArabic ? 'فتح الموقع الحالي على الخريطة' : 'Open current location on map' }}
                            </a>
                        @endif
                    </div>

                    <div class="service-form-group full">
                        <label class="service-label">{{ $isArabic ? 'إضافة صور جديدة' : 'Add new images' }}</label>
                        <input class="service-file" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
                        <div class="service-help">
                            {{ $isArabic ? 'يمكنك رفع صور إضافية جديدة، وسيتم الاحتفاظ بالصور الحالية.' : 'You can upload additional new images, and the existing images will be kept.' }}
                        </div>
                        @error('images') <div class="service-error">{{ $message }}</div> @enderror
                        @error('images.*') <div class="service-error">{{ $message }}</div> @enderror
                    </div>

                    @if ($service->images && $service->images->count())
                        <div class="service-form-group full">
                            <label class="service-label">{{ $isArabic ? 'الصور الحالية' : 'Current images' }}</label>

                            <div class="current-images">
                                @foreach ($service->images as $image)
                                    <div class="current-image-card">
                                        <img src="{{ asset('storage/' . ltrim($image->path, '/')) }}" alt="service image">
                                        <div class="current-image-meta">
                                            {{ $image->is_primary ? ($isArabic ? 'الصورة الرئيسية' : 'Primary image') : ($isArabic ? 'صورة إضافية' : 'Additional image') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="service-actions">
                    <button type="submit" class="service-btn-primary">
                        {{ $isArabic ? 'حفظ التعديلات' : 'Save changes' }}
                    </button>

                    <a href="{{ route('services.show', $service) }}" class="service-btn-secondary">
                        {{ $isArabic ? 'العودة للتفاصيل' : 'Back to details' }}
                    </a>
                </div>
            </form>
        </section>
    </div>

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('latitudeInput');
            const lngInput = document.getElementById('longitudeInput');
            const latPreview = document.getElementById('latitudePreview');
            const lngPreview = document.getElementById('longitudePreview');
            const mapElement = document.getElementById('serviceMap');

            if (!mapElement || !latInput || !lngInput) {
                return;
            }

            const fallbackLat = parseFloat(@json((float) $initialLat));
            const fallbackLng = parseFloat(@json((float) $initialLng));

            const oldLat = parseFloat(latInput.value);
            const oldLng = parseFloat(lngInput.value);

            const hasSavedLocation = !isNaN(oldLat) && !isNaN(oldLng);

            const startLat = hasSavedLocation ? oldLat : fallbackLat;
            const startLng = hasSavedLocation ? oldLng : fallbackLng;

            const map = L.map('serviceMap').setView([startLat, startLng], hasSavedLocation ? 13 : 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let marker = L.marker([startLat, startLng], {
                draggable: true
            }).addTo(map);

            function updateLocation(lat, lng) {
                const normalizedLat = parseFloat(lat).toFixed(7);
                const normalizedLng = parseFloat(lng).toFixed(7);

                latInput.value = normalizedLat;
                lngInput.value = normalizedLng;

                if (latPreview) latPreview.textContent = normalizedLat;
                if (lngPreview) lngPreview.textContent = normalizedLng;

                marker.setLatLng([parseFloat(normalizedLat), parseFloat(normalizedLng)]);
            }

            updateLocation(startLat, startLng);

            map.on('click', function (e) {
                updateLocation(e.latlng.lat, e.latlng.lng);
            });

            marker.on('dragend', function (e) {
                const position = e.target.getLatLng();
                updateLocation(position.lat, position.lng);
            });

            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });
    </script>
@endsection