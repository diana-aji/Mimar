<?php

namespace App\Http\Controllers\Web;

use App\Models\City;
use Illuminate\View\View;
use App\Models\BusinessAccount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\BusinessActivityType;
use App\Services\Web\BusinessAccountWebService;
use App\Http\Requests\Web\BusinessAccount\StoreBusinessAccountWebRequest;
use App\Http\Requests\Web\BusinessAccount\UpdateBusinessAccountWebRequest;

class BusinessAccountController extends Controller
{
    public function __construct(
        protected BusinessAccountWebService $service
    ) {
    }

    public function index(Request $request): View
    {
        $businessAccounts = BusinessAccount::query()
            ->with([
                'city',
                'activityType',
                'images',
                'documents',
            ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $latestBusinessAccount = $businessAccounts->first();

        $cities = City::query()
            ->latest()
            ->get();

        $activityTypes = BusinessActivityType::query()
            ->latest()
            ->get();

        return view('public.business-account.index', compact(
            'businessAccounts',
            'latestBusinessAccount',
            'cities',
            'activityTypes'
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('business-account.index', ['new' => 1]);
    }

    public function store(StoreBusinessAccountWebRequest $request): RedirectResponse
    {
        $this->service->create(
            $request->user(),
            $request->validated(),
            $request->file('images', []),
            $request->file('documents', [])
        );

        return redirect()
            ->route('business-account.index')
            ->with('success', __('messages.business_account_submitted'));
    }

    public function update(
        UpdateBusinessAccountWebRequest $request,
        BusinessAccount $businessAccount
    ): RedirectResponse {
        abort_unless(
            $businessAccount->user_id === $request->user()->id,
            403,
            __('messages.forbidden')
        );

        $this->service->update(
            $businessAccount,
            $request->validated(),
            $request->file('images', []),
            $request->file('documents', [])
        );

        return redirect()
            ->route('business-account.index')
            ->with('success', __('messages.business_account_updated'));
    }
}