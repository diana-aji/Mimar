<?php

namespace App\Http\Resources\BusinessAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'business_activity_type_id' => $this->business_activity_type_id,
            'city_id' => $this->city_id,
            'license_number' => $this->license_number,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'activities' => $this->activities,
            'details' => $this->details,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,

            'city' => $this->whenLoaded('city', fn () => [
                'id' => $this->city?->id,
                'name_ar' => $this->city?->name_ar,
                'name_en' => $this->city?->name_en,
            ]),

            'activity_type' => $this->whenLoaded('activityType', fn () => [
                'id' => $this->activityType?->id,
                'name_ar' => $this->activityType?->name_ar,
                'name_en' => $this->activityType?->name_en,
            ]),

            'images' => BusinessAccountImageResource::collection(
                $this->whenLoaded('images')
            ),

            'documents' => BusinessAccountDocumentResource::collection(
                $this->whenLoaded('documents')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}