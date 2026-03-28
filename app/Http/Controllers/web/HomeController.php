<?php

namespace App\Http\Controllers\Web;

use App\Models\Slider;
use App\Models\Service;
use Illuminate\View\View;
use App\Models\BusinessAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(): View
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

        return view('public.home', compact(
            'featuredServices',
            'sliders',
            'businessAccount'
        ));
    }
}