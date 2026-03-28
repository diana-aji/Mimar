<?php

namespace App\Http\Requests\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        $businessAccount = $this->route('businessAccount');

        return $this->user() !== null
            && $businessAccount !== null
            && (int) $businessAccount->user_id === (int) $this->user()->id;
    }

    public function rules(): array
    {
        $businessAccount = $this->route('businessAccount');
        $businessAccountId = $businessAccount?->id;

        return [
            'business_activity_type_id' => ['required', 'exists:business_activity_types,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'license_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('business_accounts', 'license_number')->ignore($businessAccountId),
            ],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'activities' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:255'],

            'documents' => ['nullable', 'array'],
            'documents.*.file_name' => ['nullable', 'string', 'max:255'],
            'documents.*.file_path' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.document_type' => ['nullable', 'string', 'max:100'],
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
            'documents' => __('messages.attributes.documents'),
        ];
    }
}