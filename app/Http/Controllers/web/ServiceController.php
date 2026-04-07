<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Service\StoreServiceWebRequest;
use App\Http\Requests\Web\Service\UpdateServiceWebRequest;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Service;
use App\Models\Subcategory;
use App\Services\Web\ServiceWebService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceWebService $serviceWebService
    ) {
    }

    public function index(Request $request): View
    {
        $displayCurrency = $request->query('display_currency');

        if (in_array($displayCurrency, ['SYP', 'USD'])) {
            session(['display_currency' => $displayCurrency]);
        }

        $services = Service::query()
            ->with(['businessAccount', 'category', 'subcategory', 'images'])
            ->where(function ($query) {
                $query->where('status', 'approved');

                if (Auth::check()) {
                    $query->orWhereHas('businessAccount', function ($businessQuery) {
                        $businessQuery->where('user_id', Auth::id());
                    });
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $favoriteIds = [];

        if (Auth::check()) {
            $favoriteIds = Favorite::query()
                ->where('user_id', Auth::id())
                ->pluck('service_id')
                ->toArray();
        }

        return view('public.services.index', compact('services', 'favoriteIds'));
    }

    public function show(Service $service): View
    {
        $service->load([
            'businessAccount',
            'category',
            'subcategory',
            'images',
            'ratings.user',
        ]);

        $isOwner = Auth::check()
            && $service->businessAccount
            && (int) $service->businessAccount->user_id === (int) Auth::id();

        if (! $isOwner && $service->status !== 'approved') {
            abort(404);
        }

        $businessAccounts = BusinessAccount::query()
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->latest()
            ->get();

        $isFavorited = false;

        if (Auth::check()) {
            $isFavorited = Favorite::query()
                ->where('user_id', Auth::id())
                ->where('service_id', $service->id)
                ->exists();
        }

        return view('public.services.show', compact('service', 'businessAccounts', 'isFavorited'));
    }

    public function create(): View
    {
        $businessAccounts = BusinessAccount::query()
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->latest()
            ->get();

        abort_if($businessAccounts->isEmpty(), 403);

        $categories = Category::query()->latest()->get();
        $subcategories = Subcategory::query()->latest()->get();

        return view('public.services.create', compact(
            'businessAccounts',
            'categories',
            'subcategories'
        ));
    }

    public function store(StoreServiceWebRequest $request): RedirectResponse
    {
        $businessAccount = BusinessAccount::query()
            ->where('id', $request->business_account_id)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        $validated = $request->validated();
        $validated['business_account_id'] = $businessAccount->id;

        $service = $this->serviceWebService->create(
            $validated,
            $request->file('images', [])
        );

        return redirect()
            ->route('services.show', $service)
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Service $service): View
    {
        $service->load(['images', 'businessAccount']);

        abort_unless(
            $service->businessAccount
            && (int) $service->businessAccount->user_id === (int) Auth::id(),
            403
        );

        $businessAccounts = BusinessAccount::query()
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->latest()
            ->get();

        $categories = Category::query()->latest()->get();
        $subcategories = Subcategory::query()->latest()->get();

        return view('public.services.edit', compact(
            'service',
            'businessAccounts',
            'categories',
            'subcategories'
        ));
    }

    public function update(UpdateServiceWebRequest $request, Service $service): RedirectResponse
    {
        $service->loadMissing('businessAccount');

        abort_unless(
            $service->businessAccount
            && (int) $service->businessAccount->user_id === (int) Auth::id(),
            403
        );

        $businessAccount = BusinessAccount::query()
            ->where('id', $request->business_account_id)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        $validated = $request->validated();
        $validated['business_account_id'] = $businessAccount->id;

        $service = $this->serviceWebService->update(
            $service,
            $validated,
            $request->file('images', [])
        );

        return redirect()
            ->route('services.show', $service)
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        if (! Auth::check()) {
            abort(403);
        }

        $service->loadMissing(['businessAccount', 'images']);

        if (! $service->businessAccount || (int) $service->businessAccount->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        foreach ($service->images as $image) {
            if (! empty($image->path) && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $service->images()->delete();
        $service->favorites()->delete();
        $service->reports()->delete();
        $service->dynamicFieldValues()->delete();
        $service->orders()->delete();
        $service->ratings()->delete();

        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}