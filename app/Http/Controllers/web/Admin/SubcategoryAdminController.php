<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Subcategory\StoreSubcategoryWebRequest;
use App\Http\Requests\Web\Subcategory\UpdateSubcategoryWebRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\Web\SubcategoryWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubcategoryAdminController extends Controller
{
    public function __construct(
        protected SubcategoryWebService $service
    ) {
    }

    public function index(Request $request): View
    {
        $subcategories = $this->service->paginate((int) $request->query('per_page', 12))
            ->withQueryString();

        $subcategoryItems = collect($subcategories->items());

        $selectedSubcategory = null;

        if ($request->filled('selected')) {
            $selectedSubcategory = $subcategoryItems->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedSubcategory) {
            $selectedSubcategory = $subcategoryItems->first();
        }

        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();

        return view('admin.subcategories.index', compact('subcategories', 'selectedSubcategory', 'categories'));
    }

    public function store(StoreSubcategoryWebRequest $request): RedirectResponse
    {
        $subcategory = $this->service->create($request->validated());

        return redirect()
            ->route('admin.subcategories.index', ['selected' => $subcategory->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(UpdateSubcategoryWebRequest $request, Subcategory $subcategory): RedirectResponse
    {
        $subcategory = $this->service->update($subcategory, $request->validated());

        return redirect()
            ->route('admin.subcategories.index', ['selected' => $subcategory->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $this->service->delete($subcategory);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}