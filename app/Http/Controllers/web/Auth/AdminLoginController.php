<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\SystemRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => __('messages.account_inactive'),
            ]);
        }

        if (! $user->hasAnyRole([
            SystemRole::SUPER_ADMIN->value,
            SystemRole::ADMIN->value,
        ])) {
            throw ValidationException::withMessages([
                'email' => __('messages.admin_login_only'),
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $authUser = Auth::user();
        if ($authUser instanceof User) {
            $authUser->update([
                'last_login_at' => now(),
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }
}