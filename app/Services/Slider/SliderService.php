<?php

namespace App\Services\Slider;

use App\Models\Slider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SliderService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Slider::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function active(): Collection
    {
        return Slider::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $data): Slider
    {
        return Slider::query()->create([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'subtitle_ar' => $data['subtitle_ar'] ?? null,
            'subtitle_en' => $data['subtitle_en'] ?? null,
            'image' => $data['image'],
            'button_text_ar' => $data['button_text_ar'] ?? null,
            'button_text_en' => $data['button_text_en'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Slider $slider, array $data): Slider
    {
        $slider->update([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'subtitle_ar' => $data['subtitle_ar'] ?? null,
            'subtitle_en' => $data['subtitle_en'] ?? null,
            'image' => $data['image'],
            'button_text_ar' => $data['button_text_ar'] ?? null,
            'button_text_en' => $data['button_text_en'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return $slider->refresh();
    }

    public function delete(Slider $slider): void
    {
        $slider->delete();
    }
}