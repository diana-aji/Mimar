<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            CitySeeder::class,
            BusinessActivityTypeSeeder::class,
            CategorySeeder::class,
            SubcategorySeeder::class,
            EstimationTypeSeeder::class,
            MaterialTypeSeeder::class,
            CityMaterialPriceSeeder::class,
            
        ]);
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
);

        $cities = [
            ['name_ar' => 'دمشق', 'name_en' => 'Damascus', 'sort_order' => 1],
            ['name_ar' => 'ريف دمشق', 'name_en' => 'Rif Dimashq', 'sort_order' => 2],
            ['name_ar' => 'حلب', 'name_en' => 'Aleppo', 'sort_order' => 3],
            ['name_ar' => 'حمص', 'name_en' => 'Homs', 'sort_order' => 4],
            ['name_ar' => 'حماة', 'name_en' => 'Hama', 'sort_order' => 5],
            ['name_ar' => 'اللاذقية', 'name_en' => 'Latakia', 'sort_order' => 6],
            ['name_ar' => 'طرطوس', 'name_en' => 'Tartus', 'sort_order' => 7],
            ['name_ar' => 'إدلب', 'name_en' => 'Idlib', 'sort_order' => 8],
            ['name_ar' => 'الرقة', 'name_en' => 'Raqqa', 'sort_order' => 9],
            ['name_ar' => 'الحسكة', 'name_en' => 'Al-Hasakah', 'sort_order' => 10],
            ['name_ar' => 'دير الزور', 'name_en' => 'Deir ez-Zor', 'sort_order' => 11],
            ['name_ar' => 'درعا', 'name_en' => 'Daraa', 'sort_order' => 12],
            ['name_ar' => 'السويداء', 'name_en' => 'Suwayda', 'sort_order' => 13],
            ['name_ar' => 'القنيطرة', 'name_en' => 'Quneitra', 'sort_order' => 14],
        ];

        foreach ($cities as $city) {
            City::query()->updateOrCreate(
                ['name_ar' => $city['name_ar']],
                [
                    'name_en' => $city['name_en'],
                    'is_active' => true,
                    'sort_order' => $city['sort_order'],
                ]
            );
    }
}
}