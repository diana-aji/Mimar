<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Service;
use App\Models\BusinessAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Service\ServiceService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\Service\ServiceResource;
use App\Models\DynamicField;
use App\Models\ServiceDynamicFieldValue;
use Illuminate\Support\Facades\DB;

class ServiceController extends ApiController
{
    public function __construct(
        protected ServiceService $service
    ) {
    }

    public function index(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $services = $this->service->listForUser($request->user(), $businessAccount);

        return $this->successResponse(
            ServiceResource::collection($services),
            __('messages.success')
        );
        $service->load([
    'dynamicFieldValues.dynamicField'
         ]);
    }

    public function store(
        StoreServiceRequest $request,
        BusinessAccount $businessAccount
    ): JsonResponse {
        $service = $this->service->create(
            $request->user(),
            $businessAccount,
            $request->validated()
        );

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.created_successfully'),
            201
        );
        DB::transaction(function () use ($request, &$service) {
    $service = Service::create([
        // حقول الخدمة الحالية عندك
    ]);

    $dynamicValues = $request->input('dynamic_fields', []);

    if (!empty($dynamicValues)) {
        $fieldIds = array_keys($dynamicValues);

        $fields = DynamicField::whereIn('id', $fieldIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($dynamicValues as $fieldId => $value) {
            $field = $fields->get((int) $fieldId);

            if (!$field) {
                continue;
            }

            if ($field->is_required && ($value === null || $value === '')) {
                abort(response()->json([
                    'message' => "الحقل {$field->label_ar} مطلوب."
                ], 422));
            }

            if ($field->type === 'select' && !empty($field->options)) {
                if (!in_array($value, $field->options, true)) {
                    abort(response()->json([
                        'message' => "القيمة المدخلة للحقل {$field->label_ar} غير صالحة."
                    ], 422));
                }
            }

            ServiceDynamicFieldValue::create([
                'service_id' => $service->id,
                'dynamic_field_id' => $field->id,
                'value' => is_array($value) ? json_encode($value) : (string) $value,
            ]);
        }
    }
});
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service = $this->service->update(
            $request->user(),
            $service,
            $request->validated()
        );

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.updated_successfully')
        );
        $dynamicValues = $request->input('dynamic_fields', []);

    if (!empty($dynamicValues)) {
        $fieldIds = array_keys($dynamicValues);

        $fields = DynamicField::whereIn('id', $fieldIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($dynamicValues as $fieldId => $value) {
            $field = $fields->get((int) $fieldId);

            if (!$field) {
                continue;
            }

            if ($field->is_required && ($value === null || $value === '')) {
                return response()->json([
                    'message' => "الحقل {$field->label_ar} مطلوب."
                ], 422);
            }

            if ($field->type === 'select' && !empty($field->options)) {
                if (!in_array($value, $field->options, true)) {
                    return response()->json([
                        'message' => "القيمة المدخلة للحقل {$field->label_ar} غير صالحة."
                    ], 422);
                }
            }

            \App\Models\ServiceDynamicFieldValue::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'dynamic_field_id' => $field->id,
                ],
                [
                    'value' => is_array($value) ? json_encode($value) : (string) $value,
                ]
        );
    }
}
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        $this->service->delete($request->user(), $service);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}