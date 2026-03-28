<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'locale' => ['nullable', 'in:ar,en'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('messages.attributes.name'),
            'email' => __('messages.attributes.email'),
            'phone' => __('messages.attributes.phone'),
            'password' => __('messages.attributes.password'),
            'password_confirmation' => __('messages.attributes.password_confirmation'),
            'locale' => __('messages.attributes.locale'),
        ];
    }
}