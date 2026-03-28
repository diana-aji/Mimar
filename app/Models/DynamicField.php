<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicField extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'label_ar',
        'label_en',
        'key',
        'type',
        'is_required',
        'options',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ServiceDynamicFieldValue::class);
    }
}