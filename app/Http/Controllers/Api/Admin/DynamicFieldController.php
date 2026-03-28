<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DynamicFieldController extends Controller
{
    public function index(Request $request)
    {
        $fields = DynamicField::query()
            ->with(['category:id,name_ar,name_en', 'subcategory:id,name_ar,name_en'])
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->subcategory_id, fn ($q) => $q->where('subcategory_id', $request->subcategory_id))
            ->orderBy('sort_order')
            ->latest()
            ->paginate(20);

        return response()->json($fields);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'label_ar' => ['required', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:dynamic_fields,key'],
            'type' => ['required', Rule::in(['text', 'textarea', 'number', 'select', 'boolean', 'date'])],
            'is_required' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($data['category_id']) && empty($data['subcategory_id'])) {
            return response()->json([
                'message' => 'يجب ربط الحقل بتصنيف رئيسي أو فرعي على الأقل.'
            ], 422);
        }

        $field = DynamicField::create([
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'label_ar' => $data['label_ar'],
            'label_en' => $data['label_en'] ?? null,
            'key' => $data['key'],
            'type' => $data['type'],
            'is_required' => $data['is_required'] ?? false,
            'options' => $data['options'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json($field, 201);
    }

    public function show(DynamicField $dynamicField)
    {
        return response()->json($dynamicField->load(['category', 'subcategory']));
    }

    public function update(Request $request, DynamicField $dynamicField)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'label_ar' => ['sometimes', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'key' => ['sometimes', 'string', 'max:255', 'unique:dynamic_fields,key,' . $dynamicField->id],
            'type' => ['sometimes', Rule::in(['text', 'textarea', 'number', 'select', 'boolean', 'date'])],
            'is_required' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $dynamicField->update($data);

        return response()->json($dynamicField->fresh());
    }

    public function destroy(DynamicField $dynamicField)
    {
        $dynamicField->delete();

        return response()->json([
            'message' => 'تم حذف الحقل الديناميكي بنجاح'
        ]);
    }
}