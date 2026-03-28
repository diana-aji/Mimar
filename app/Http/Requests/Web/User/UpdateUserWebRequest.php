<?php

namespace App\Http\Requests\Web\User;

use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = Auth::user();

        return $authUser instanceof User
            && $authUser->hasRole(SystemRole::SUPER_ADMIN->value);
    }

    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user?->id)],
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