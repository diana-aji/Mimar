<?php

namespace App\Http\Requests\Web\User;

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->hasRole(SystemRole::SUPER_ADMIN->value);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['nullable', 'string', 'min:8'],
            'locale' => ['required', Rule::in(['ar', 'en'])],
            'is_active' => ['required', 'in:0,1'],
            'account_type' => ['required', Rule::in([
                SystemRole::ADMIN->value,
                SystemRole::USER->value,
            ])],
        ];
    }
}