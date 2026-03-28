<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Models\BusinessAccount;
use App\Models\User;
use App\Exceptions\DomainException;
use Illuminate\Support\Facades\DB;
use App\Notifications\ServiceStatusChangedNotification;

class ServiceService
{
    public function listForUser(User $user, BusinessAccount $businessAccount)
    {
        $this->ensureBusinessAccountOwnership($user, $businessAccount);

        return Service::query()
            ->with(['businessAccount', 'category', 'subcategory', 'images'])
            ->where('business_account_id', $businessAccount->id)
            ->latest()
            ->get();
    }

    public function create(User $user, BusinessAccount $businessAccount, array $data): Service
    {
        $this->ensureBusinessAccountOwnership($user, $businessAccount);

        if (! $businessAccount->isApproved()) {
            throw new DomainException(__('messages.business_account_not_approved'));
        }

        return DB::transaction(function () use ($businessAccount, $data) {
            $service = Service::query()->create([
                'business_account_id' => $businessAccount->id,
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'status' => 'pending',
                'rejection_reason' => null,
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            return $service->load(['businessAccount', 'category', 'subcategory', 'images']);
        });
    }

    public function update(User $user, Service $service, array $data): Service
    {
        $this->ensureServiceOwnership($user, $service);

        $newStatus = $service->status === 'approved' ? 'pending' : $service->status;

        $service->update([
            'category_id' => $data['category_id'],
            'subcategory_id' => $data['subcategory_id'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'status' => $newStatus,
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        return $service->refresh()->load(['businessAccount', 'category', 'subcategory', 'images']);
    }

    public function delete(User $user, Service $service): void
    {
        $this->ensureServiceOwnership($user, $service);

        $service->delete();
    }

    public function approve(User $admin, Service $service): Service
    {
        $service->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        $service->businessAccount?->user?->notify(
            new ServiceStatusChangedNotification($service)
        );

        return $service->refresh()->load(['businessAccount', 'category', 'subcategory', 'images']);
    }

    public function reject(User $admin, Service $service, string $reason): Service
    {
        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
        ]);

        $service->businessAccount?->user?->notify(
            new ServiceStatusChangedNotification($service)
        );

        return $service->refresh()->load(['businessAccount', 'category', 'subcategory', 'images']);
    }

    protected function ensureBusinessAccountOwnership(User $user, BusinessAccount $businessAccount): void
    {
        if ((int) $businessAccount->user_id !== (int) $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensureServiceOwnership(User $user, Service $service): void
    {
        $service->loadMissing('businessAccount');

        if (! $service->businessAccount || (int) $service->businessAccount->user_id !== (int) $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }
}