<?php

namespace App\Services\Web;

use App\Models\Slider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SliderWebService
{
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return Slider::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);
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
            'is_active' => (bool) $data['is_active'],
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
            'is_active' => (bool) $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return $slider->refresh();
    }

    public function delete(Slider $slider): void
    {
        $slider->delete();
    }
}