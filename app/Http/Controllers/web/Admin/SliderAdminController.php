<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\Web\SliderWebService;
use App\Http\Requests\Web\Slider\StoreSliderWebRequest;
use App\Http\Requests\Web\Slider\UpdateSliderWebRequest;

class SliderAdminController extends Controller
{
    public function __construct(
        protected SliderWebService $service
    ) {
    }

    public function index(Request $request): View
    {
        $sliders = $this->service->paginate((int) $request->query('per_page', 12));
        $sliderItems = collect($sliders->items());

        $selectedSlider = null;

        if ($request->filled('selected')) {
            $selectedSlider = $sliderItems->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedSlider) {
            $selectedSlider = $sliderItems->first();
        }

        return view('admin.sliders.index', compact('sliders', 'selectedSlider'));
    }

    public function store(StoreSliderWebRequest $request): RedirectResponse
    {
        $slider = $this->service->create($request->validated());

        return redirect()
            ->route('admin.sliders.index', ['selected' => $slider->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(UpdateSliderWebRequest $request, Slider $slider): RedirectResponse
    {
        $slider = $this->service->update($slider, $request->validated());

        return redirect()
            ->route('admin.sliders.index', ['selected' => $slider->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        $this->service->delete($slider);

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}