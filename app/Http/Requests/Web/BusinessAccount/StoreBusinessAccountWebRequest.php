<?php

namespace App\Http\Requests\Web\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessAccountWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'business_activity_type_id' => ['required', 'exists:business_activity_types,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'license_number' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'activities' => ['required', 'string'],
            'details' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return [
            'business_activity_type_id' => __('messages.attributes.business_activity_type'),
            'city_id' => __('messages.attributes.city'),
            'license_number' => __('messages.attributes.license_number'),
            'name_ar' => __('messages.attributes.name_ar'),
            'name_en' => __('messages.attributes.name_en'),
            'activities' => __('messages.attributes.activities'),
            'details' => __('messages.attributes.details'),
            'latitude' => __('messages.attributes.latitude'),
            'longitude' => __('messages.attributes.longitude'),
            'images' => __('messages.attributes.images'),
            'images.*' => __('messages.attributes.image'),
            'documents' => __('messages.attributes.documents'),
            'documents.*' => __('messages.attributes.document'),
        ];
    }
}