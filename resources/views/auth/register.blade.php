@extends('layouts.guest')

@section('content')
    <x-auth.card
        :title="app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create your account'"
        :subtitle="app()->getLocale() === 'ar'
            ? 'أنشئ حسابك كمستخدم عادي للوصول إلى الخدمات، الطلبات، المحادثات، والتقدير الذكي.'
            : 'Create your account as a regular user to access services, requests, conversations, and smart estimation.'"
    >
        <div class="auth-switch">
            <a href="{{ route('login') }}">
                {{ app()->getLocale() === 'ar' ? 'دخول المستخدم' : 'User login' }}
            </a>
            <a href="{{ route('register') }}" class="active">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </div>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full name'"
                name="name"
                :placeholder="app()->getLocale() === 'ar' ? 'أدخل الاسم الكامل' : 'Enter your full name'"
                icon="👤"
            />

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address'"
                name="email"
                type="email"
                :placeholder="app()->getLocale() === 'ar' ? 'name@example.com' : 'name@example.com'"
                icon="✉"
            />

            <x-auth.input
                :label="app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone number'"
                name="phone"
                type="text"
                :placeholder="app()->getLocale() === 'ar' ? '09xxxxxxxx' : '09xxxxxxxx'"
                icon="📱"
            />

            <div class="auth-grid-2">
                <x-auth.input
                    :label="app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'"
                    name="password"
                    type="password"
                    :placeholder="app()->getLocale() === 'ar' ? 'أدخل كلمة المرور' : 'Enter your password'"
                    icon="•"
                />

                <x-auth.input
                    :label="app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm password'"
                    name="password_confirmation"
                    type="password"
                    :placeholder="app()->getLocale() === 'ar' ? 'أعد إدخال كلمة المرور' : 'Re-enter your password'"
                    icon="•"
                />
            </div>

            <button type="submit" class="btn-primary">
                {{ app()->getLocale() === 'ar' ? 'إنشاء الحساب' : 'Create account' }}
            </button>
        </form>

        <div class="auth-micro">
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'مستخدم عادي' : 'Regular user' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'خدمات وطلبات' : 'Services & requests' }}</span>
            <span class="auth-chip">{{ app()->getLocale() === 'ar' ? 'تقديم أعمال لاحقًا' : 'Business request later' }}</span>
        </div>

        <div class="auth-divider">
            <div class="auth-footer">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}">
                    {{ app()->getLocale() === 'ar' ? 'دخول المستخدم' : 'User login' }}
                </a>
            </div>
        </div>
    </x-auth.card>
@endsection