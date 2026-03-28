<?php

namespace App\Http\Requests\Web\Slider;

use Illuminate\Foundation\Http\FormRequest;

class StoreSliderWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'subtitle_ar' => ['nullable', 'string'],
            'subtitle_en' => ['nullable', 'string'],
            'image' => ['required', 'string', 'max:2048'],
            'button_text_ar' => ['nullable', 'string', 'max:255'],
            'button_text_en' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['required', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}