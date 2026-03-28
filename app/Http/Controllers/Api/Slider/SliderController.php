<?php

namespace App\Http\Controllers\Api\Slider;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Slider\SliderService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Slider\SliderResource;
use App\Http\Requests\Slider\StoreSliderRequest;
use App\Http\Requests\Slider\UpdateSliderRequest;

class SliderController extends ApiController
{
    public function __construct(
        protected SliderService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => SliderResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function active(): JsonResponse
    {
        $items = $this->service->active();

        return $this->successResponse(
            SliderResource::collection($items),
            __('messages.success')
        );
    }

    public function store(StoreSliderRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new SliderResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(Slider $slider): JsonResponse
    {
        return $this->successResponse(
            new SliderResource($slider)
        );
    }

    public function update(UpdateSliderRequest $request, Slider $slider): JsonResponse
    {
        $item = $this->service->update($slider, $request->validated());

        return $this->successResponse(
            new SliderResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(Slider $slider): JsonResponse
    {
        $this->service->delete($slider);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}