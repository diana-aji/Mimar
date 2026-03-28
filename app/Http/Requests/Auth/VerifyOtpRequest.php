<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => __('messages.attributes.phone'),
            'code' => __('messages.attributes.code'),
        ];
    }
}