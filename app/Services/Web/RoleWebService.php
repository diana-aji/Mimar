<?php

namespace App\Services\Web;

use Spatie\Permission\Models\Role;

class RoleWebService
{
    public function create(array $data): Role
    {
        $role = Role::query()->create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role->refresh()->load('permissions');
    }

    public function update(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role->refresh()->load('permissions');
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}