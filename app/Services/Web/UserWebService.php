<?php

namespace App\Services\Web;

use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserWebService
{
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return User::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => !empty($data['password']) ? bcrypt($data['password']) : null,
            'locale' => $data['locale'] ?? 'ar',
            'is_active' => (bool) $data['is_active'],
            'account_type' => $data['account_type'],
        ]);

        $user->syncRoles([$data['account_type']]);

        return $user->refresh();
    }

    public function update(User $user, array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'locale' => $data['locale'] ?? $user->locale,
            'is_active' => (bool) $data['is_active'],
            'account_type' => $data['account_type'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = bcrypt($data['password']);
        }

        $user->update($payload);
        $user->syncRoles([$data['account_type']]);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}