<?php

namespace App\Services\Web;

use App\Models\Subcategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubcategoryWebService
{
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return Subcategory::query()
            ->with('category')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Subcategory
    {
        return Subcategory::query()->create([
            'category_id' => $data['category_id'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $subcategory->update([
            'category_id' => $data['category_id'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => (bool) ($data['is_active'] ?? $subcategory->is_active),
            'sort_order' => $data['sort_order'] ?? $subcategory->sort_order,
        ]);

        return $subcategory->refresh()->load('category');
    }

    public function delete(Subcategory $subcategory): void
    {
        $subcategory->delete();
    }
}