<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        ExchangeRate::query()->updateOrCreate(
            [
                'from_currency' => 'USD',
                'to_currency' => 'SYP',
            ],
            [
                'rate' => 15000,
                'is_active' => true,
            ]
        );
    }
}