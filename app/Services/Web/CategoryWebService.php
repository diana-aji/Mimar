<?php

namespace App\Services\Web;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryWebService
{
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return Category::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Category
    {
        return Category::query()->create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'icon' => $data['icon'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'icon' => $data['icon'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? $category->is_active),
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}