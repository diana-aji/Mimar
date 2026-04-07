<?php

namespace App\Services\Web;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ServiceWebService
{
    public function create(array $data, array $images = []): Service
    {
        return DB::transaction(function () use ($data, $images) {
            $service = Service::create([
                'business_account_id' => $data['business_account_id'],
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'SYP',
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'status' => 'pending',
            ]);

            $this->storeImages($service, $images);

            return $service->load([
                'businessAccount',
                'category',
                'subcategory',
                'images',
            ]);
        });
    }

    public function update(Service $service, array $data, array $images = []): Service
    {
        return DB::transaction(function () use ($service, $data, $images) {
            $service->update([
                'business_account_id' => $data['business_account_id'],
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'currency' => $data['currency'] ?? $service->currency ?? 'SYP',
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);

            $this->storeImages($service, $images);

            return $service->load([
                'businessAccount',
                'category',
                'subcategory',
                'images',
            ]);
        });
    }

    protected function storeImages(Service $service, array $images = []): void
    {
        $existingImagesCount = $service->images()->count();

        foreach ($images as $index => $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $path = $image->store('services/images', 'public');

            ServiceImage::create([
                'service_id' => $service->id,
                'path' => $path,
                'is_primary' => $existingImagesCount === 0 && $index === 0,
                'sort_order' => $existingImagesCount + $index + 1,
            ]);
        }
    }
}