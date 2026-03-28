<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Models\DynamicField;
use App\Exceptions\DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class PublicServiceService
{
    public function browse(array $filters = [], $user = null)
    {
        $query = Service::query()
            ->with([
                'businessAccount',
                'category',
                'subcategory',
                'images',
                'dynamicFieldValues.dynamicField',
                'ratings.user',
                'favorites',
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('status', 'approved');

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['subcategory_id'])) {
            $query->where('subcategory_id', $filters['subcategory_id']);
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (! empty($filters['name'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name_ar', 'like', '%' . $filters['name'] . '%')
                    ->orWhere('name_en', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (! empty($filters['dynamic_filters']) && is_array($filters['dynamic_filters'])) {
            $fieldIds = array_keys($filters['dynamic_filters']);

            $dynamicFields = DynamicField::query()
                ->whereIn('id', $fieldIds)
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            foreach ($filters['dynamic_filters'] as $fieldId => $filter) {
                if (! is_array($filter)) {
                    throw ValidationException::withMessages([
                        "dynamic_filters.$fieldId" => ['صيغة الفلتر غير صحيحة.'],
                    ]);
                }

                $field = $dynamicFields->get((int) $fieldId);

                if (! $field) {
                    throw ValidationException::withMessages([
                        "dynamic_filters.$fieldId" => ['الحقل الديناميكي غير موجود أو غير فعال.'],
                    ]);
                }

                $operator = $filter['operator'] ?? 'eq';

                if (! $this->isAllowedOperatorForFieldType($field->type, $operator)) {
                    throw ValidationException::withMessages([
                        "dynamic_filters.$fieldId.operator" => [
                            "المعامل {$operator} غير مسموح لهذا النوع من الحقول ({$field->type})."
                        ],
                    ]);
                }

                $normalizedFilter = $this->normalizeDynamicFilter($field->type, $operator, $filter);

                $this->validateDynamicFilterPayload($fieldId, $field->type, $operator, $normalizedFilter);

                $query->whereHas('dynamicFieldValues', function ($q) use ($fieldId, $field, $normalizedFilter, $operator) {
                    $q->where('dynamic_field_id', $fieldId);

                    $this->applyDynamicFilterOperator($q, $field->type, $operator, $normalizedFilter);
                });
            }
        }

        $services = $query->latest()->paginate(12);

        if ($user) {
            $favoriteServiceIds = $user->favorites()
                ->pluck('service_id')
                ->toArray();

            $services->getCollection()->transform(function ($service) use ($favoriteServiceIds) {
                $service->is_favorite = in_array($service->id, $favoriteServiceIds, true);
                return $service;
            });
        } else {
            $services->getCollection()->transform(function ($service) {
                $service->is_favorite = false;
                return $service;
            });
        }

        return $services;
    }

    public function showApproved(int $serviceId, $user = null): Service
    {
        $service = Service::query()
            ->with([
                'businessAccount',
                'category',
                'subcategory',
                'images',
                'dynamicFieldValues.dynamicField',
                'ratings.user',
                'favorites',
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('status', 'approved')
            ->find($serviceId);

        if (! $service) {
            throw new DomainException(__('messages.not_found'));
        }

        $service->is_favorite = $user
            ? $user->favorites()->where('service_id', $service->id)->exists()
            : false;

        return $service;
    }

    private function isAllowedOperatorForFieldType(string $fieldType, string $operator): bool
    {
        $allowedOperators = match ($fieldType) {
            'number' => ['eq', 'gt', 'gte', 'lt', 'lte', 'between'],
            'date' => ['eq', 'before', 'after', 'between_date'],
            'text', 'textarea', 'select', 'boolean' => ['eq'],
            default => ['eq'],
        };

        return in_array($operator, $allowedOperators, true);
    }

    private function normalizeDynamicFilter(string $fieldType, string $operator, array $filter): array
    {
        if ($fieldType !== 'boolean' || $operator !== 'eq' || ! array_key_exists('value', $filter)) {
            return $filter;
        }

        $normalized = $this->normalizeBooleanValue($filter['value']);

        if ($normalized !== null) {
            $filter['value'] = $normalized;
        }

        return $filter;
    }

    private function normalizeBooleanValue(mixed $value): ?string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '1', 'true', 'yes', 'on' => '1',
            '0', 'false', 'no', 'off' => '0',
            default => null,
        };
    }

    private function validateDynamicFilterPayload(int|string $fieldId, string $fieldType, string $operator, array $filter): void
    {
        $allowedOperators = ['eq', 'gt', 'gte', 'lt', 'lte', 'between', 'before', 'after', 'between_date'];

        if (! in_array($operator, $allowedOperators, true)) {
            throw ValidationException::withMessages([
                "dynamic_filters.$fieldId.operator" => ['المعامل المطلوب غير مدعوم.'],
            ]);
        }

        if ($operator === 'between') {
            $from = $filter['from'] ?? null;
            $to = $filter['to'] ?? null;

            if ($from === null || $from === '' || $to === null || $to === '') {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId" => ['يجب إرسال from و to عند استخدام between.'],
                ]);
            }

            if (! is_numeric($from) || ! is_numeric($to)) {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId" => ['قيم from و to يجب أن تكون رقمية.'],
                ]);
            }

            if ((float) $from > (float) $to) {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId" => ['قيمة from يجب أن تكون أصغر من أو تساوي to.'],
                ]);
            }

            return;
        }

        if ($operator === 'between_date') {
            $from = $filter['from'] ?? null;
            $to = $filter['to'] ?? null;

            if ($from === null || $from === '' || $to === null || $to === '') {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId" => ['يجب إرسال from و to عند استخدام between_date.'],
                ]);
            }

            if (! $this->isValidDate($from) || ! $this->isValidDate($to)) {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId" => ['قيم from و to يجب أن تكون بصيغة تاريخ صحيحة مثل 2026-03-28.'],
                ]);
            }

            if ($from > $to) {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId" => ['قيمة from يجب أن تكون أقدم من أو مساوية to.'],
                ]);
            }

            return;
        }

        $value = $filter['value'] ?? null;

        if ($value === null || $value === '') {
            throw ValidationException::withMessages([
                "dynamic_filters.$fieldId.value" => ['يجب إرسال value لهذا الفلتر.'],
            ]);
        }

        if (in_array($operator, ['gt', 'gte', 'lt', 'lte'], true) && ! is_numeric($value)) {
            throw ValidationException::withMessages([
                "dynamic_filters.$fieldId.value" => ['القيمة يجب أن تكون رقمية لهذا النوع من المعاملات.'],
            ]);
        }

        if (in_array($operator, ['before', 'after'], true) && ! $this->isValidDate($value)) {
            throw ValidationException::withMessages([
                "dynamic_filters.$fieldId.value" => ['القيمة يجب أن تكون تاريخًا صحيحًا مثل 2026-03-28.'],
            ]);
        }

        if ($fieldType === 'date' && $operator === 'eq' && ! $this->isValidDate($value)) {
            throw ValidationException::withMessages([
                "dynamic_filters.$fieldId.value" => ['القيمة يجب أن تكون تاريخًا صحيحًا مثل 2026-03-28.'],
            ]);
        }

        if ($fieldType === 'boolean' && $operator === 'eq') {
            if (! in_array((string) $value, ['1', '0'], true)) {
                throw ValidationException::withMessages([
                    "dynamic_filters.$fieldId.value" => ['قيمة الحقل المنطقي يجب أن تكون true أو false أو 1 أو 0 أو yes أو no.'],
                ]);
            }
        }
    }

    private function applyDynamicFilterOperator(Builder $query, string $fieldType, string $operator, array $filter): void
    {
        switch ($operator) {
            case 'eq':
                if ($fieldType === 'date') {
                    $query->whereRaw('DATE(value) = ?', [$filter['value']]);
                } else {
                    $query->where('value', (string) $filter['value']);
                }
                break;

            case 'gt':
                $query->whereRaw('CAST(value AS DECIMAL(12,2)) > ?', [(float) $filter['value']]);
                break;

            case 'gte':
                $query->whereRaw('CAST(value AS DECIMAL(12,2)) >= ?', [(float) $filter['value']]);
                break;

            case 'lt':
                $query->whereRaw('CAST(value AS DECIMAL(12,2)) < ?', [(float) $filter['value']]);
                break;

            case 'lte':
                $query->whereRaw('CAST(value AS DECIMAL(12,2)) <= ?', [(float) $filter['value']]);
                break;

            case 'between':
                $query->whereRaw(
                    'CAST(value AS DECIMAL(12,2)) BETWEEN ? AND ?',
                    [(float) $filter['from'], (float) $filter['to']]
                );
                break;

            case 'before':
                $query->whereRaw('DATE(value) < ?', [$filter['value']]);
                break;

            case 'after':
                $query->whereRaw('DATE(value) > ?', [$filter['value']]);
                break;

            case 'between_date':
                $query->whereRaw(
                    'DATE(value) BETWEEN ? AND ?',
                    [$filter['from'], $filter['to']]
                );
                break;
        }
    }

    private function isValidDate(string $value): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}