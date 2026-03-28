<?php

namespace App\Http\Controllers\Api\DynamicField;

use App\Http\Controllers\Controller;
use App\Models\DynamicField;
use Illuminate\Http\Request;

class PublicDynamicFieldController extends Controller
{
    public function byCategory(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
        ]);

        $fields = DynamicField::query()
            ->where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->where('category_id', $request->category_id);

                if ($request->filled('subcategory_id')) {
                    $q->orWhere('subcategory_id', $request->subcategory_id);
                }
            })
            ->orderBy('sort_order')
            ->get();

        return response()->json($fields);
    }
}