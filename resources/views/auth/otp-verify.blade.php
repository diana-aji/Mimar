@extends('layouts.guest')

@section('content')
    <x-auth.card
        :title="app()->getLocale() === 'ar' ? 'التحقق من رمز الدخول' : 'Verify login code'"
        :subtitle="app()->getLocale() === 'ar'
            ? 'أدخل الرمز الذي تم إرساله إلى رقم الهاتف للمتابعة إلى حسابك.'
            : 'Enter the code sent to your phone number to continue to your account.'"
    >
        <div class="auth-switch">
            <a href="{{ route('login') }}">
                {{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone number' }}
            </a>
            <a href="{{ route('otp.verify.form', ['phone' => $phone]) }}" class="active">
                {{ app()->getLocale() === 'ar' ? 'رمز التحقق' : 'Verification code' }}
            </a>
        </div>

        @if (session('success'))
            <div class="status-alert">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf

            <input type="hidden" name="phone" value="{{ $phone }}">

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'رمز التحقق' : 'Verification code'"
                name="code"
                type="text"
                :placeholder="app()->getLocale() === 'ar' ? 'أدخل الرمز المكوّن من 6 أرقام' : 'Enter the 6-digit code'"
                icon="#"
            />

            <button type="submit" class="btn-primary">
                {{ app()->getLocale() === 'ar' ? 'تحقق وادخل' : 'Verify and login' }}
            </button>
        </form>

        <div class="auth-micro">
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'صالح لعدة دقائق' : 'Valid for a few minutes' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'دخول آمن' : 'Secure login' }}</span>
        </div>

        <div class="auth-divider">
            <div class="auth-footer">
                <a href="{{ route('login') }}">
                    {{ app()->getLocale() === 'ar' ? 'تعديل رقم الهاتف' : 'Change phone number' }}
                </a>
            </div>
        </div>
    </x-auth.card>
@endsection