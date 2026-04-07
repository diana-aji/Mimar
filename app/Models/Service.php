<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_account_id',
        'category_id',
        'subcategory_id',
        'name_ar',
        'name_en',
        'description',
        'price',
        'currency',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'price' => 'decimal:2',
        ];
    }

    public function businessAccount(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ServiceReport::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): ?ServiceImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function dynamicFieldValues(): HasMany
    {
        return $this->hasMany(ServiceDynamicFieldValue::class);
    }

    public function formattedPrice(): string
    {
        return number_format((float) $this->price, 2) . ' ' . ($this->currency ?? 'SYP');
    }

    public function convertedPrice(string $displayCurrency = 'SYP'): float
    {
        $serviceCurrency = $this->currency ?? 'SYP';

        if ($serviceCurrency === $displayCurrency) {
            return (float) $this->price;
        }

        $rate = ExchangeRate::activeRate($serviceCurrency, $displayCurrency);

        if (! $rate) {
            return (float) $this->price;
        }

        return (float) $this->price * $rate;
    }

    public function formattedPriceFor(string $displayCurrency = 'SYP'): string
    {
        $converted = $this->convertedPrice($displayCurrency);

        return number_format($converted, 2) . ' ' . $displayCurrency;
    }
}