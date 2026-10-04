<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'from_country',
        'to_country',
        'flight_date',
        'airline',
        'total_kg',
        'remaining_kg',
        'price_per_kg',
        'currency',
        'drop_off_deadline',
        'status',
    ];

    protected $casts = [
        'flight_date' => 'date',
        'drop_off_deadline' => 'date',
        'total_kg' => 'integer',
        'remaining_kg' => 'integer',
        'price_per_kg' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($flight) {
            if ($flight->remaining_kg > $flight->total_kg) {
                $flight->remaining_kg = $flight->total_kg;
            }
            if ($flight->remaining_kg === 0 && $flight->status !== 'cancelled') {
                $flight->status = 'full';
            }
        });
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('flight_date', '>=', now()->startOfDay());
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
