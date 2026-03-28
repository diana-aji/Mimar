<?php

namespace App\Http\Controllers\Web;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with([
                'subcategories' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }
            ])
            ->withCount([
                'services as services_count' => function ($query) {
                    $query->where('status', 'approved');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        return view('public.categories.index', compact('categories'));
    }
}