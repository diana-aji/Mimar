<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $sentOrders = $this->service->listSentOrders($user);
        $receivedOrders = $this->service->listReceivedOrders($user);

        $tab = $request->get('tab');

        if (! in_array($tab, ['sent', 'received'])) {
            $tab = $sentOrders->isNotEmpty() ? 'sent' : 'received';
        }

        return view('public.orders.index', [
            'sentOrders' => $sentOrders,
            'receivedOrders' => $receivedOrders,
            'activeTab' => $tab,
            'orderStatuses' => [
                'pending' => OrderStatus::PENDING->value,
                'accepted' => OrderStatus::ACCEPTED->value,
                'rejected' => OrderStatus::REJECTED->value,
                'cancelled' => OrderStatus::CANCELLED->value,
            ],
        ]);
    }

    public function store(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'details' => ['nullable', 'string'],
            'needed_at' => ['nullable', 'date'],
        ]);

        $this->service->create($request->user(), $service, [
            'quantity' => $validated['quantity'] ?? 1,
            'details' => $validated['details'] ?? null,
            'needed_at' => $validated['needed_at'] ?? null,
        ]);

        return redirect()
            ->route('orders.index', ['tab' => 'sent'])
            ->with('success', __('messages.created_successfully'));
    }

    public function accept(Request $request, Order $order): RedirectResponse
    {
        $this->service->accept($request->user(), $order);

        return redirect()
            ->route('orders.index', ['tab' => 'received'])
            ->with('success', __('messages.order_accepted'));
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $this->service->reject($request->user(), $order);

        return redirect()
            ->route('orders.index', ['tab' => 'received'])
            ->with('success', __('messages.order_rejected'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->service->cancel($request->user(), $order);

        return redirect()
            ->route('orders.index', ['tab' => 'sent'])
            ->with('success', __('messages.order_cancelled'));
    }
}