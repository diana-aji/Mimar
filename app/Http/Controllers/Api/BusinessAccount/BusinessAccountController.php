<?php

namespace App\Http\Controllers\Api\BusinessAccount;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessAccount;
use App\Http\Controllers\Api\ApiController;
use App\Services\BusinessAccount\BusinessAccountService;
use App\Http\Resources\BusinessAccount\BusinessAccountResource;
use App\Http\Requests\BusinessAccount\StoreBusinessAccountRequest;
use App\Http\Requests\BusinessAccount\UpdateBusinessAccountRequest;

class BusinessAccountController extends ApiController
{
    public function __construct(
        protected BusinessAccountService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->listForUser(
            $request->user(),
            (int) $request->get('per_page', 15)
        );

        return $this->successResponse([
            'items' => BusinessAccountResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], __('messages.success'));
    }

    public function store(StoreBusinessAccountRequest $request): JsonResponse
    {
        $businessAccount = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            new BusinessAccountResource($businessAccount),
            __('messages.business_account_submitted'),
            201
        );
    }

    public function show(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        abort_unless(
            (int) $businessAccount->user_id === (int) $request->user()->id,
            403,
            __('messages.forbidden')
        );

        return $this->successResponse(
            new BusinessAccountResource(
                $businessAccount->load(['city', 'activityType', 'images', 'documents'])
            ),
            __('messages.success')
        );
    }

    public function update(UpdateBusinessAccountRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $businessAccount = $this->service->update(
            $request->user(),
            $businessAccount,
            $request->validated()
        );

        return $this->successResponse(
            new BusinessAccountResource($businessAccount),
            __('messages.business_account_updated')
        );
    }

    public function destroy(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->service->delete($request->user(), $businessAccount);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}