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

            'ratings_avg' => $this->ratings_avg_rating !== null
                ? round((float) $this->ratings_avg_rating, 2)
                : 0,

            'ratings_count' => $this->ratings_count ?? 0,

            'is_favorite' => (bool) ($this->is_favorite ?? false),

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

            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($image) => [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                    'image_url' => $image->image_url ?? null,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                ]);
            }),

            'dynamic_fields' => $this->whenLoaded('dynamicFieldValues', function () {
                return $this->dynamicFieldValues->map(function ($item) {
                    return [
                        'field_id' => $item->dynamic_field_id,
                        'label_ar' => $item->dynamicField?->label_ar,
                        'label_en' => $item->dynamicField?->label_en,
                        'key' => $item->dynamicField?->key,
                        'type' => $item->dynamicField?->type,
                        'value' => $item->value,
                    ];
                });
            }),

            'latest_ratings' => $this->whenLoaded('ratings', function () {
                return $this->ratings
                    ->sortByDesc('created_at')
                    ->take(5)
                    ->values()
                    ->map(function ($rating) {
                        return [
                            'id' => $rating->id,
                            'rating' => $rating->rating,
                            'comment' => $rating->comment,
                            'created_at' => $rating->created_at,
                            'user' => [
                                'id' => $rating->user?->id,
                                'name' => $rating->user?->name,
                            ],
                        ];
                    });
            }),
        ];
    }
}