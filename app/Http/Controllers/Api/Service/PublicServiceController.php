<?php

namespace App\Http\Controllers\Api\Service;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Service\ServiceResource;
use App\Services\Service\PublicServiceService;

class PublicServiceController extends ApiController
{
    public function __construct(
        protected PublicServiceService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $services = $this->service->browse($request->all(), $request->user());

        return $this->successResponse(
            ServiceResource::collection($services),
            __('messages.success')
        );
    }

    public function show(Request $request, int $serviceId): JsonResponse
    {
        $service = $this->service->showApproved($serviceId, $request->user());

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.success')
        );
    }
}