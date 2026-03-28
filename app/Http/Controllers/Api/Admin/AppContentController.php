<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppContent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppContentController extends Controller
{
    public function index()
    {
        return response()->json(AppContent::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', Rule::in(['privacy_policy', 'terms_of_use']), 'unique:app_contents,key'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $content = AppContent::create([
            'key' => $data['key'],
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'] ?? null,
            'content_ar' => $data['content_ar'],
            'content_en' => $data['content_en'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json($content, 201);
    }

    public function show(AppContent $appContent)
    {
        return response()->json($appContent);
    }

    public function update(Request $request, AppContent $appContent)
    {
        $data = $request->validate([
            'key' => ['sometimes', 'string', Rule::in(['privacy_policy', 'terms_of_use']), 'unique:app_contents,key,' . $appContent->id],
            'title_ar' => ['sometimes', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content_ar' => ['sometimes', 'string'],
            'content_en' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $appContent->update($data);

        return response()->json($appContent->fresh());
    }

    public function destroy(AppContent $appContent)
    {
        $appContent->delete();

        return response()->json([
            'message' => 'تم حذف المحتوى بنجاح'
        ]);
    }
}