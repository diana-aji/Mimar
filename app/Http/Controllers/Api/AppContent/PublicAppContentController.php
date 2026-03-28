<?php

namespace App\Http\Controllers\Api\AppContent;

use App\Http\Controllers\Controller;
use App\Models\AppContent;

class PublicAppContentController extends Controller
{
    public function privacyPolicy()
    {
        $content = AppContent::where('key', 'privacy_policy')
            ->where('is_active', true)
            ->first();

        if (!$content) {
            return response()->json([
                'message' => 'سياسة الخصوصية غير متوفرة حالياً'
            ], 404);
        }

        return response()->json($content);
    }

    public function termsOfUse()
    {
        $content = AppContent::where('key', 'terms_of_use')
            ->where('is_active', true)
            ->first();

        if (!$content) {
            return response()->json([
                'message' => 'شروط الاستخدام غير متوفرة حالياً'
            ], 404);
        }

        return response()->json($content);
    }
}