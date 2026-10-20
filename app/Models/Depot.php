<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Depot extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_open' => 'boolean',
        'is_certified' => 'boolean',
        'is_lab_passed' => 'boolean',
        'rating' => 'float',
        'lat' => 'float',
        'lng' => 'float',
        'facilities' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(WaterProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class)->latest();
    }

    /**
     * Get lowest refill price among products
     */
    public function getLowestPriceAttribute(): int
    {
        return $this->products->min('price') ?? 6000;
    }
}
