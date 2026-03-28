<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDynamicFieldValue extends Model
{
    protected $fillable = [
        'service_id',
        'dynamic_field_id',
        'value',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function dynamicField(): BelongsTo
    {
        return $this->belongsTo(DynamicField::class);
    }
}