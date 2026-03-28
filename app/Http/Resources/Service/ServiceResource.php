<?php

namespace App\Http\Resources\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_account_id' => $this->business_account_id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'description' => $this->description,
            'price' => $this->price,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'business_account' => $this->whenLoaded('businessAccount', fn () => [
                'id' => $this->businessAccount?->id,
                'name_ar' => $this->businessAccount?->name_ar,
                'name_en' => $this->businessAccount?->name_en,
            ]),

            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name_ar' => $this->category?->name_ar,
                'name_en' => $this->category?->name_en,
            ]),

            'subcategory' => $this->whenLoaded('subcategory', fn () => [
                'id' => $this->subcategory?->id,
                'name_ar' => $this->subcategory?->name_ar,
                'name_en' => $this->subcategory?->name_en,
            ]),
        ];
    }
}