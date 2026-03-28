<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],

            'dynamic_fields' => ['nullable', 'array'],
            'dynamic_fields.*' => ['nullable'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => __('messages.attributes.category'),
            'subcategory_id' => __('messages.attributes.subcategory'),
            'name_ar' => __('messages.attributes.name_ar'),
            'name_en' => __('messages.attributes.name_en'),
            'description' => __('messages.attributes.description'),
            'price' => __('messages.attributes.price'),
            'dynamic_fields' => 'الحقول الديناميكية',
        ];
    }
}