@extends('layouts.guest')

@section('content')
    <x-auth.card
        :title="app()->getLocale() === 'ar' ? 'الدخول إلى لوحة التحكم' : 'Access the admin dashboard'"
        :subtitle="app()->getLocale() === 'ar'
            ? 'سجل الدخول كمدير أو مدير رئيسي للوصول إلى إدارة المستخدمين، الخدمات، المدن، والبلاغات.'
            : 'Sign in as an admin or super admin to access users, services, cities, and reports management.'"
    >
        <div class="auth-switch">
            <a href="{{ route('login') }}">
                {{ app()->getLocale() === 'ar' ? 'دخول المستخدم' : 'User login' }}
            </a>
            <a href="{{ route('admin.login') }}" class="active">
                {{ app()->getLocale() === 'ar' ? 'دخول الإدارة' : 'Admin login' }}
            </a>
        </div>

        @if (session('status'))
            <div class="status-alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address'"
                name="email"
                type="email"
                :placeholder="app()->getLocale() === 'ar' ? 'name@example.com' : 'name@example.com'"
                icon="✉"
            />

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'"
                name="password"
                type="password"
                :placeholder="app()->getLocale() === 'ar' ? 'أدخل كلمة المرور' : 'Enter your password'"
                icon="•"
            />

            <button type="submit" class="btn-primary">
                {{ app()->getLocale() === 'ar' ? 'دخول لوحة التحكم' : 'Access dashboard' }}
            </button>
        </form>

        <div class="auth-micro">
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'مدير' : 'Admin' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'مدير رئيسي' : 'Super admin' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'إدارة المنصة' : 'Platform control' }}</span>
        </div>

        <div class="auth-divider">
            <div class="auth-footer">
                {{ app()->getLocale() === 'ar' ? 'مستخدم عادي؟' : 'Regular user?' }}
                <a href="{{ route('login') }}">
                    {{ app()->getLocale() === 'ar' ? 'العودة إلى دخول المستخدم' : 'Back to user login' }}
                </a>
            </div>
        </div>
    </x-auth.card>
@endsection