<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'user_id',
        'sender_business_account_id',
        'receiver_business_account_id',
        'quantity',
        'details',
        'needed_at',
        'status',
        'accepted_at',
        'rejected_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'needed_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function senderBusinessAccount(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class, 'sender_business_account_id');
    }

    public function receiverBusinessAccount(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class, 'receiver_business_account_id');
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }
}