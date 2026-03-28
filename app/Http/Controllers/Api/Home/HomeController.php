<?php

namespace App\Http\Controllers\Api\Home;

use App\Models\Slider;
use App\Models\Service;
use App\Models\BusinessAccount;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;

class HomeController extends ApiController
{
    public function index(): JsonResponse
    {
        $featuredServices = Service::query()
            ->with(['businessAccount', 'category', 'subcategory', 'images'])
            ->where('status', 'approved')
            ->latest()
            ->take(4)
            ->get();

        $sliders = Slider::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $businessAccount = null;

        if (Auth::check()) {
            $businessAccount = BusinessAccount::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->first();
        }

        return $this->successResponse([
            'sliders' => $sliders,
            'featured_services' => $featuredServices,
            'business_account' => $businessAccount,
        ], __('messages.success'));
    }
}