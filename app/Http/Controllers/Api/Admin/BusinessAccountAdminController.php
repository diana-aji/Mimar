<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessAccount;
use App\Http\Controllers\Api\ApiController;
use App\Services\BusinessAccount\BusinessAccountService;
use App\Http\Resources\BusinessAccount\BusinessAccountResource;
use App\Http\Requests\BusinessAccount\ApproveBusinessAccountRequest;
use App\Http\Requests\BusinessAccount\RejectBusinessAccountRequest;

class BusinessAccountAdminController extends ApiController
{
    public function __construct(
        protected BusinessAccountService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->listForAdmin((int) $request->get('per_page', 15));

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

    public function approve(
        ApproveBusinessAccountRequest $request,
        BusinessAccount $businessAccount
    ): JsonResponse {
        $businessAccount = $this->service->approve($request->user(), $businessAccount);

        return $this->successResponse(
            new BusinessAccountResource($businessAccount),
            __('messages.business_account_approved')
        );
    }

    public function reject(
        RejectBusinessAccountRequest $request,
        BusinessAccount $businessAccount
    ): JsonResponse {
        $businessAccount = $this->service->reject(
            $request->user(),
            $businessAccount,
            $request->validated()['rejection_reason']
        );

        return $this->successResponse(
            new BusinessAccountResource($businessAccount),
            __('messages.business_account_rejected')
        );
    }
}