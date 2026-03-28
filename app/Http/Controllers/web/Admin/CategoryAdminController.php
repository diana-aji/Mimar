<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Category\StoreCategoryWebRequest;
use App\Http\Requests\Web\Category\UpdateCategoryWebRequest;
use App\Models\Category;
use App\Services\Web\CategoryWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryAdminController extends Controller
{
    public function __construct(
        protected CategoryWebService $service
    ) {
    }

    public function index(Request $request): View
    {
        $categories = $this->service->paginate((int) $request->query('per_page', 12))
            ->withQueryString();

        $categoryItems = collect($categories->items());

        $selectedCategory = null;

        if ($request->filled('selected')) {
            $selectedCategory = $categoryItems->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedCategory) {
            $selectedCategory = $categoryItems->first();
        }

        return view('admin.categories.index', compact('categories', 'selectedCategory'));
    }

    public function store(StoreCategoryWebRequest $request): RedirectResponse
    {
        $category = $this->service->create($request->validated());

        return redirect()
            ->route('admin.categories.index', ['selected' => $category->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(UpdateCategoryWebRequest $request, Category $category): RedirectResponse
    {
        $category = $this->service->update($category, $request->validated());

        return redirect()
            ->route('admin.categories.index', ['selected' => $category->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->service->delete($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}