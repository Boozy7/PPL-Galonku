<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
    ];

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }
}
