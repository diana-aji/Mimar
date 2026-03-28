<?php

namespace App\Models;

use App\Enums\BusinessAccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_activity_type_id',
        'city_id',
        'license_number',
        'name_ar',
        'name_en',
        'activities',
        'details',
        'latitude',
        'longitude',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(BusinessActivityType::class, 'business_activity_type_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessAccountImage::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BusinessAccountDocument::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isApproved(): bool
    {
        return $this->status === BusinessAccountStatus::APPROVED->value;
    }

    public function sentOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'sender_business_account_id');
    }

    public function receivedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'receiver_business_account_id');
    }
}