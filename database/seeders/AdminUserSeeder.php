<?php

namespace Database\Seeders;

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'admin@realestate.com')
            ->orWhere('phone', '09999999999')
            ->first();

        if (! $admin) {
            $admin = User::query()->create([
                'name' => 'Super Admin',
                'email' => 'admin@realestate.com',
                'phone' => '09999999999',
                'password' => bcrypt('12345678'),
                'locale' => 'ar',
                'is_active' => true,
                'account_type' => 'admin',
            ]);
        } else {
            $admin->update([
                'name' => 'Super Admin',
                'email' => 'admin@realestate.com',
                'phone' => '09999999999',
                'password' => bcrypt('12345678'),
                'locale' => 'ar',
                'is_active' => true,
                'account_type' => 'admin',
            ]);
        }

        $admin->syncRoles([SystemRole::SUPER_ADMIN->value]);
    }
}