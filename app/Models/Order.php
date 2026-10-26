<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $guarded = ['id'];

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function waterProduct(): BelongsTo
    {
        return $this->belongsTo(WaterProduct::class);
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'DITERIMA' => 'Pesanan Diterima',
            'SEDANG_DIISI' => 'Sedang Disterilisasi & Diisi',
            'SIAP_DIAMBIL' => 'Siap Diambil di Depot',
            'SELESAI' => 'Selesai',
            'BATAL' => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Get Google Maps direction URL from user coordinates to depot
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps/dir/?api=1&destination={$this->depot->lat},{$this->depot->lng}&travelmode=driving";
    }
}