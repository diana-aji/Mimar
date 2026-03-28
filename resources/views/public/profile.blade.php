@extends('layouts.app')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $user = $user ?? auth()->user();
    @endphp

    <style>
        .profile-shell {
            display: grid;
            gap: 22px;
        }

        .profile-card {
            background: rgba(255,255,255,0.98);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.05);
            overflow: hidden;
        }

        .profile-hero {
            padding: 28px;
            background: linear-gradient(135deg, #182848 0%, #243b73 100%);
            color: white;
        }

        .profile-title {
            margin: 0 0 10px;
            font-size: 34px;
            font-weight: 800;
            line-height: 1.1;
        }

        .profile-copy {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 14px;
            line-height: 1.9;
        }

        .profile-body {
            padding: 28px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .profile-group {
            display: grid;
            gap: 8px;
        }

        .profile-group.full {
            grid-column: 1 / -1;
        }

        .profile-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .profile-input {
            width: 100%;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .profile-input:focus {
            border-color: #4458db;
            box-shadow: 0 0 0 4px rgba(68,88,219,0.10);
        }

        .profile-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .profile-btn {
            height: 44px;
            padding: 0 18px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #4458db 0%, #243873 100%);
            color: white;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        .profile-note {
            margin-top: 8px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }

        .alert-success-profile {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(5,150,105,0.10);
            color: #059669;
            border: 1px solid rgba(5,150,105,0.12);
            font-size: 14px;
            font-weight: 700;
        }

        .profile-error {
            color: #dc2626;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-title {
                font-size: 28px;
            }

            .profile-hero,
            .profile-body {
                padding: 20px;
            }
        }
    </style>

    <div class="profile-shell">
        @if (session('success'))
            <div class="alert-success-profile">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-card">
            <div class="profile-hero">
                <h1 class="profile-title">
                    {{ $isArabic ? 'الملف الشخصي' : 'My Profile' }}
                </h1>

                <p class="profile-copy">
                    {{ $isArabic
                        ? 'يمكنك من هنا تعديل بيانات حسابك الشخصية وتحديث كلمة المرور واللغة.'
                        : 'From here you can update your personal account details, password, and preferred language.' }}
                </p>
            </div>

            <div class="profile-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="profile-grid">
                        <div class="profile-group">
                            <label class="profile-label">
                                {{ $isArabic ? 'الاسم' : 'Name' }}
                            </label>
                            <input
                                type="text"
                                name="name"
                                class="profile-input"
                                value="{{ old('name', $user?->name) }}"
                                required
                            >
                            @error('name')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="profile-group">
                            <label class="profile-label">
                                {{ $isArabic ? 'رقم الهاتف' : 'Phone' }}
                            </label>
                            <input
                                type="text"
                                name="phone"
                                class="profile-input"
                                value="{{ old('phone', $user?->phone) }}"
                                required
                            >
                            @error('phone')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="profile-group">
                            <label class="profile-label">
                                {{ $isArabic ? 'البريد الإلكتروني' : 'Email' }}
                            </label>
                            <input
                                type="email"
                                name="email"
                                class="profile-input"
                                value="{{ old('email', $user?->email) }}"
                            >
                            @error('email')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="profile-group">
                            <label class="profile-label">
                                {{ $isArabic ? 'اللغة' : 'Language' }}
                            </label>
                            <select name="locale" class="profile-input" required>
                                <option value="ar" @selected(old('locale', $user?->locale) === 'ar')>
                                    العربية
                                </option>
                                <option value="en" @selected(old('locale', $user?->locale) === 'en')>
                                    English
                                </option>
                            </select>
                            @error('locale')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="profile-group">
                            <label class="profile-label">
                                {{ $isArabic ? 'كلمة المرور الجديدة' : 'New Password' }}
                            </label>
                            <input
                                type="password"
                                name="password"
                                class="profile-input"
                            >
                            @error('password')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="profile-group">
                            <label class="profile-label">
                                {{ $isArabic ? 'تأكيد كلمة المرور' : 'Confirm Password' }}
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="profile-input"
                            >
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="profile-btn">
                            {{ $isArabic ? 'حفظ التعديلات' : 'Save Changes' }}
                        </button>
                    </div>

                    <div class="profile-note">
                        {{ $isArabic
                            ? 'اترك حقل كلمة المرور فارغًا إذا كنت لا تريد تغييرها.'
                            : 'Leave the password field empty if you do not want to change it.' }}
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection