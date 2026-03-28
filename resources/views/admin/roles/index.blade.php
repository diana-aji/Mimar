@extends('layouts.admin')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <style>
        .roles-shell { display:grid; gap:22px; }
        .roles-grid { display:grid; grid-template-columns: 1fr 1fr; gap:20px; }
        .roles-card {
            background:#fff;
            border:1px solid rgba(15,23,42,.06);
            border-radius:24px;
            padding:24px;
            box-shadow:0 12px 30px rgba(15,23,42,.05);
        }
        .roles-title {
            margin:0 0 16px;
            font-size:24px;
            font-weight:800;
            color:#111827;
        }
        .roles-list { display:grid; gap:12px; }
        .role-item {
            padding:16px;
            border-radius:18px;
            background:#f8fafc;
            border:1px solid rgba(15,23,42,.06);
        }
        .role-item strong {
            display:block;
            font-size:16px;
            color:#111827;
            margin-bottom:6px;
        }
        .perm-tags {
            display:flex;
            flex-wrap:wrap;
            gap:8px;
        }
        .perm-tag {
            padding:6px 10px;
            border-radius:999px;
            background:rgba(68,88,219,.08);
            color:#4458db;
            font-size:12px;
            font-weight:700;
        }
        .form-grid { display:grid; gap:14px; }
        .form-input {
            width:100%;
            border:1px solid rgba(15,23,42,.08);
            border-radius:14px;
            padding:12px 14px;
            font-size:14px;
        }
        .perm-grid {
            display:grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap:10px;
        }
        .perm-check {
            padding:10px 12px;
            border-radius:14px;
            background:#f8fafc;
            border:1px solid rgba(15,23,42,.06);
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
            .roles-grid { grid-template-columns:1fr; }
            .perm-grid { grid-template-columns:1fr; }
        }
    </style>

    <div class="roles-shell">
        @if (session('success'))
            <div style="padding:14px 16px;border-radius:16px;background:rgba(5,150,105,.10);color:#059669;">
                {{ session('success') }}
            </div>
        @endif

        <div class="roles-grid">
            <div class="roles-card">
                <h2 class="roles-title">{{ $isArabic ? 'الأدوار الحالية' : 'Existing Roles' }}</h2>

                <div class="roles-list">
                    @foreach ($roles as $role)
                        <div class="role-item">
                            <strong>{{ $role->name }}</strong>

                            <div class="perm-tags">
                                @foreach ($role->permissions as $permission)
                                    <span class="perm-tag">{{ $permission->name }}</span>
                                @endforeach
                            </div>

                            <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                                <a href="{{ route('admin.roles.index', ['selected' => $role->id]) }}" class="btn-main" style="display:inline-flex;align-items:center;text-decoration:none;">
                                    {{ $isArabic ? 'تعديل' : 'Edit' }}
                                </a>

                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}">
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

            <div class="roles-card">
                <h2 class="roles-title">
                    {{ $selectedRole ? ($isArabic ? 'تعديل الدور' : 'Edit Role') : ($isArabic ? 'إضافة دور' : 'Create Role') }}
                </h2>

                <form method="POST" action="{{ $selectedRole ? route('admin.roles.update', $selectedRole->id) : route('admin.roles.store') }}" class="form-grid">
                    @csrf
                    @if($selectedRole)
                        @method('PUT')
                    @endif

                    <input
                        type="text"
                        name="name"
                        class="form-input"
                        placeholder="{{ $isArabic ? 'اسم الدور' : 'Role name' }}"
                        value="{{ old('name', $selectedRole?->name) }}"
                        required
                    >

                    <div class="perm-grid">
                        @foreach ($permissions as $permission)
                            <label class="perm-check">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    @checked(
                                        in_array($permission->name, old('permissions', $selectedRole ? $selectedRole->permissions->pluck('name')->toArray() : []))
                                    )
                                >
                                {{ $permission->name }}
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn-main">
                        {{ $selectedRole ? ($isArabic ? 'حفظ التعديلات' : 'Save Changes') : ($isArabic ? 'إضافة الدور' : 'Create Role') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection