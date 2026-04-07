<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency',
        'target_currency',
        'rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public static function activeRate(string $from, string $to): ?float
    {
        if ($from === $to) {
            return 1.0;
        }

        $direct = self::query()
            ->where('base_currency', $from)
            ->where('target_currency', $to)
            ->where('is_active', true)
            ->first();

        if ($direct) {
            return (float) $direct->rate;
        }

        $reverse = self::query()
            ->where('base_currency', $to)
            ->where('target_currency', $from)
            ->where('is_active', true)
            ->first();

        if ($reverse && (float) $reverse->rate > 0) {
            return 1 / (float) $reverse->rate;
        }

        return null;
    }
}