<?php

namespace App\Http\Requests\Web\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'business_account_id' => ['required', 'exists:business_accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],

            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:SYP,USD'],

            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'dynamic_fields' => ['nullable', 'array'],
            'dynamic_fields.*' => ['nullable'],
        ];
    }

    public function attributes(): array
    {
        return [
            'business_account_id' => __('messages.attributes.business_account'),
            'category_id' => __('messages.attributes.category'),
            'subcategory_id' => __('messages.attributes.subcategory'),
            'name_ar' => __('messages.attributes.name_ar'),
            'name_en' => __('messages.attributes.name_en'),
            'description' => __('messages.attributes.description'),
            'price' => __('messages.attributes.price'),
            'currency' => __('messages.attributes.currency'),
            'latitude' => __('messages.attributes.latitude'),
            'longitude' => __('messages.attributes.longitude'),
            'images' => __('messages.attributes.images'),
            'images.*' => __('messages.attributes.image'),
        ];
    }
}