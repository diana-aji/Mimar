<?php

namespace App\Services\BusinessAccount;

use App\Enums\BusinessAccountStatus;
use App\Exceptions\DomainException;
use App\Models\BusinessAccount;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Notifications\BusinessAccountStatusChangedNotification;

class BusinessAccountService
{
    public function listForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return BusinessAccount::query()
            ->with(['city', 'activityType', 'images', 'documents'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function listForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return BusinessAccount::query()
            ->with(['user', 'city', 'activityType', 'images', 'documents'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(User $user, array $data): BusinessAccount
    {
        return DB::transaction(function () use ($user, $data) {
            $businessAccount = BusinessAccount::query()->create([
                'user_id' => $user->id,
                'business_activity_type_id' => $data['business_activity_type_id'],
                'city_id' => $data['city_id'],
                'license_number' => $data['license_number'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'activities' => $data['activities'] ?? null,
                'details' => $data['details'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'status' => BusinessAccountStatus::PENDING->value,
                'rejection_reason' => null,
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            $this->syncImages($businessAccount, $data['images'] ?? []);
            $this->syncDocuments($businessAccount, $data['documents'] ?? []);

            return $businessAccount->load(['city', 'activityType', 'images', 'documents']);
        });
    }

    public function update(User $user, BusinessAccount $businessAccount, array $data): BusinessAccount
    {
        $this->ensureOwnership($user, $businessAccount);

        return DB::transaction(function () use ($businessAccount, $data) {
            $newStatus = $businessAccount->status === BusinessAccountStatus::APPROVED->value
                ? BusinessAccountStatus::PENDING->value
                : $businessAccount->status;

            $businessAccount->update([
                'business_activity_type_id' => $data['business_activity_type_id'],
                'city_id' => $data['city_id'],
                'license_number' => $data['license_number'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'activities' => $data['activities'] ?? null,
                'details' => $data['details'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'status' => $newStatus,
                'rejection_reason' => null,
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            $businessAccount->images()->delete();
            $businessAccount->documents()->delete();

            $this->syncImages($businessAccount, $data['images'] ?? []);
            $this->syncDocuments($businessAccount, $data['documents'] ?? []);

            return $businessAccount->refresh()->load(['city', 'activityType', 'images', 'documents']);
        });
    }

    public function delete(User $user, BusinessAccount $businessAccount): void
    {
        $this->ensureOwnership($user, $businessAccount);

        $businessAccount->delete();
    }

    public function approve(User $admin, BusinessAccount $businessAccount): BusinessAccount
    {
        $businessAccount->update([
            'status' => BusinessAccountStatus::APPROVED->value,
            'rejection_reason' => null,
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        $businessAccount->user?->notify(
            new BusinessAccountStatusChangedNotification($businessAccount)
        );

        return $businessAccount->refresh()->load(['city', 'activityType', 'images', 'documents']);
    }

    public function reject(User $admin, BusinessAccount $businessAccount, string $reason): BusinessAccount
    {
        $businessAccount->update([
            'status' => BusinessAccountStatus::REJECTED->value,
            'rejection_reason' => $reason,
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
        ]);

        $businessAccount->user?->notify(
            new BusinessAccountStatusChangedNotification($businessAccount)
        );

        return $businessAccount->refresh()->load(['city', 'activityType', 'images', 'documents']);
    }

    protected function ensureOwnership(User $user, BusinessAccount $businessAccount): void
    {
        if ((int) $businessAccount->user_id !== (int) $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function syncImages(BusinessAccount $businessAccount, array $images): void
    {
        foreach ($images as $index => $path) {
            $businessAccount->images()->create([
                'path' => $path,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }

    protected function syncDocuments(BusinessAccount $businessAccount, array $documents): void
    {
        foreach ($documents as $document) {
            $businessAccount->documents()->create([
                'file_name' => $document['file_name'] ?? null,
                'file_path' => $document['file_path'],
                'document_type' => $document['document_type'] ?? null,
            ]);
        }
    }
}