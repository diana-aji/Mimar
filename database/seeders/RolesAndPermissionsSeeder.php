<?php

namespace Database\Seeders;

use App\Enums\SystemRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // users / roles
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // business accounts
            'view-business-accounts',
            'create-business-accounts',
            'edit-business-accounts',
            'delete-business-accounts',
            'approve-business-accounts',
            'reject-business-accounts',

            // services
            'view-services',
            'create-services',
            'edit-services',
            'delete-services',
            'approve-services',
            'reject-services',

            // categories
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // subcategories
            'view-subcategories',
            'create-subcategories',
            'edit-subcategories',
            'delete-subcategories',

            // dynamic fields
            'view-dynamic-fields',
            'create-dynamic-fields',
            'edit-dynamic-fields',
            'delete-dynamic-fields',

            // orders
            'view-orders',
            'create-orders',
            'accept-orders',
            'reject-orders',
            'cancel-orders',
            'delete-orders',

            // ratings
            'view-ratings',
            'create-ratings',
            'delete-ratings',

            // favorites
            'create-favorites',
            'delete-favorites',

            // reports
            'view-reports',
            'create-reports',
            'resolve-reports',
            'delete-reports',

            // sliders
            'view-sliders',
            'create-sliders',
            'edit-sliders',
            'delete-sliders',

            // cities
            'view-cities',
            'create-cities',
            'edit-cities',
            'delete-cities',

            // notifications
            'view-notifications',
            'send-notifications',

            // estimation
            'view-estimation-types',
            'create-estimation-types',
            'edit-estimation-types',
            'delete-estimation-types',

            'view-material-types',
            'create-material-types',
            'edit-material-types',
            'delete-material-types',

            'view-city-material-prices',
            'create-city-material-prices',
            'edit-city-material-prices',
            'delete-city-material-prices',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => SystemRole::SUPER_ADMIN->value,
            'guard_name' => 'web',
        ]);

        $adminRole = Role::query()->firstOrCreate([
            'name' => SystemRole::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $userRole = Role::query()->firstOrCreate([
            'name' => SystemRole::USER->value,
            'guard_name' => 'web',
        ]);

        $superAdminRole->syncPermissions(
            Permission::query()->pluck('name')->toArray()
        );

        $adminRole->syncPermissions([
            'view-business-accounts',
            'approve-business-accounts',
            'reject-business-accounts',

            'view-services',
            'approve-services',
            'reject-services',

            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            'view-subcategories',
            'create-subcategories',
            'edit-subcategories',
            'delete-subcategories',

            'view-dynamic-fields',
            'create-dynamic-fields',
            'edit-dynamic-fields',
            'delete-dynamic-fields',

            'view-sliders',
            'create-sliders',
            'edit-sliders',
            'delete-sliders',

            'view-cities',
            'create-cities',
            'edit-cities',
            'delete-cities',

            'view-reports',
            'resolve-reports',
            'delete-reports',
        ]);

        $userRole->syncPermissions([
            'create-business-accounts',
            'edit-business-accounts',
            'view-business-accounts',

            'view-services',
            'create-services',
            'edit-services',
            'delete-services',

            'view-orders',
            'create-orders',
            'accept-orders',
            'reject-orders',
            'cancel-orders',

            'create-ratings',

            'create-favorites',
            'delete-favorites',

            'create-reports',
        ]);
    }
}