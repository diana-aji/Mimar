<?php

namespace App\Services\Order;

use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Collection;

class OrderService
{
    public function listSentOrders(User $user): Collection
    {
        return Order::query()
            ->with(['service', 'user', 'senderBusinessAccount', 'receiverBusinessAccount', 'rating'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function listReceivedOrders(User $user): Collection
    {
        return Order::query()
            ->with(['service', 'user', 'senderBusinessAccount', 'receiverBusinessAccount', 'rating'])
            ->whereHas('receiverBusinessAccount', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
    }

    public function create(User $user, Service $service, array $data): Order
    {
        if ($service->status !== 'approved') {
            throw new DomainException(__('messages.service_not_available_for_order'));
        }

        if ((int) $service->businessAccount?->user_id === (int) $user->id) {
            throw new DomainException(__('messages.cannot_order_own_service'));
        }

        $order = Order::query()->create([
            'service_id' => $service->id,
            'user_id' => $user->id,
            'sender_business_account_id' => null,
            'receiver_business_account_id' => $service->business_account_id,
            'quantity' => $data['quantity'] ?? 1,
            'details' => $data['details'] ?? null,
            'needed_at' => $data['needed_at'] ?? null,
            'status' => OrderStatus::PENDING->value,
        ])->load(['service', 'user', 'senderBusinessAccount', 'receiverBusinessAccount']);

        $order->receiverBusinessAccount?->user?->notify(
            new OrderStatusChangedNotification($order)
        );

        return $order;
    }

    public function accept(User $user, Order $order): Order
    {
        $this->ensureReceiverOwnership($user, $order);
        $this->ensurePending($order);

        $order->update([
            'status' => OrderStatus::ACCEPTED->value,
            'accepted_at' => now(),
            'rejected_at' => null,
            'cancelled_at' => null,
        ]);

        $order = $order->refresh()->load(['service', 'user', 'senderBusinessAccount', 'receiverBusinessAccount']);

        $order->user?->notify(
            new OrderStatusChangedNotification($order)
        );

        return $order;
    }

    public function reject(User $user, Order $order): Order
    {
        $this->ensureReceiverOwnership($user, $order);
        $this->ensurePending($order);

        $order->update([
            'status' => OrderStatus::REJECTED->value,
            'accepted_at' => null,
            'rejected_at' => now(),
            'cancelled_at' => null,
        ]);

        $order = $order->refresh()->load(['service', 'user', 'senderBusinessAccount', 'receiverBusinessAccount']);

        $order->user?->notify(
            new OrderStatusChangedNotification($order)
        );

        return $order;
    }

    public function cancel(User $user, Order $order): Order
    {
        $this->ensureSenderOwnership($user, $order);

        if ($order->status !== OrderStatus::PENDING->value) {
            throw new DomainException(__('messages.order_cannot_be_cancelled'));
        }

        $order->update([
            'status' => OrderStatus::CANCELLED->value,
            'accepted_at' => null,
            'rejected_at' => null,
            'cancelled_at' => now(),
        ]);

        $order = $order->refresh()->load(['service', 'user', 'senderBusinessAccount', 'receiverBusinessAccount']);

        $order->receiverBusinessAccount?->user?->notify(
            new OrderStatusChangedNotification($order)
        );

        return $order;
    }

    protected function ensureReceiverOwnership(User $user, Order $order): void
    {
        if ((int) $order->receiverBusinessAccount?->user_id !== (int) $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensureSenderOwnership(User $user, Order $order): void
    {
        if ((int) $order->user_id !== (int) $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensurePending(Order $order): void
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            throw new DomainException(__('messages.order_is_not_pending'));
        }
    }
}