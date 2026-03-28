@extends('layouts.app')

@section('content')
<div style="max-width:520px;margin:40px auto;background:white;padding:24px;border-radius:24px;">
    <h1>{{ app()->getLocale() === 'ar' ? 'دخول المستخدم عبر OTP' : 'User OTP Login' }}</h1>

    @if(session('success'))
        <div style="margin:16px 0;padding:12px;border-radius:12px;background:#ecfdf5;color:#059669;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.send') }}">
        @csrf

        <div style="margin-bottom:16px;">
            <label>{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone' }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}" style="width:100%;padding:12px;border:1px solid #ddd;border-radius:12px;">
            @error('phone')
                <div style="color:red;font-size:13px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" style="padding:12px 18px;border:none;border-radius:999px;background:#2563eb;color:white;">
            {{ app()->getLocale() === 'ar' ? 'إرسال الرمز' : 'Send code' }}
        </button>
    </form>
</div>
@endsection