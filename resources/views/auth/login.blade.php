@extends('layouts.guest')

@section('content')
    <x-auth.card
        :title="app()->getLocale() === 'ar' ? 'الدخول إلى مساحة المستخدم' : 'Access your user space'"
        :subtitle="app()->getLocale() === 'ar'
            ? 'أدخل رقم الهاتف وسنرسل لك رمز تحقق للدخول إلى الخدمات، الطلبات، المحادثات، والتقدير الذكي.'
            : 'Enter your phone number and we will send you a verification code to access services, requests, conversations, and smart estimation.'"
    >
        <div class="auth-switch">
            <a href="{{ route('login') }}" class="active">
                {{ app()->getLocale() === 'ar' ? 'دخول المستخدم' : 'User login' }}
            </a>
            <a href="{{ route('register') }}">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </div>

        @if (session('success'))
            <div class="status-alert">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.send') }}">
            @csrf

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone number'"
                name="phone"
                type="text"
                :placeholder="app()->getLocale() === 'ar' ? '09xxxxxxxx' : '09xxxxxxxx'"
                icon="📱"
            />

            <button type="submit" class="btn-primary">
                {{ app()->getLocale() === 'ar' ? 'إرسال رمز التحقق' : 'Send verification code' }}
            </button>
        </form>

        <div class="auth-micro">
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'دخول سريع' : 'Fast access' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'OTP آمن' : 'Secure OTP' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'ثنائي اللغة' : 'Bilingual' }}</span>
        </div>

        <div class="auth-divider">
            <div class="auth-footer">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
                <a href="{{ route('register') }}">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create new account' }}
                </a>
            </div>

        
        </div>
    </x-auth.card>
@endsection