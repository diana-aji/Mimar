<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExchangeRateAdminController extends Controller
{
    public function index(): View
    {
        $rates = ExchangeRate::query()->latest()->get();

        return view('admin.exchange-rates.index', compact('rates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'base_currency' => ['required', 'in:USD,SYP'],
            'target_currency' => ['required', 'in:USD,SYP'],
            'rate' => ['required', 'numeric', 'min:0.0001'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ExchangeRate::updateOrCreate(
            [
                'base_currency' => $validated['base_currency'],
                'target_currency' => $validated['target_currency'],
            ],
            [
                'rate' => $validated['rate'],
                'is_active' => (bool) ($validated['is_active'] ?? true),
            ]
        );

        return back()->with('success', 'Exchange rate saved successfully.');
    }

    public function update(Request $request, ExchangeRate $exchangeRate): RedirectResponse
    {
        $validated = $request->validate([
            'rate' => ['required', 'numeric', 'min:0.0001'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exchangeRate->update([
            'rate' => $validated['rate'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'Exchange rate updated successfully.');
    }

    public function destroy(ExchangeRate $exchangeRate): RedirectResponse
    {
        $exchangeRate->delete();

        return back()->with('success', 'Exchange rate deleted successfully.');
    }
}