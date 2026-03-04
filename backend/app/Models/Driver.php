<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    /** @use HasFactory<\Database\Factories\DriverFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'license_image',
        'insurance_document',
        'is_available',
        'is_approved',
        'is_on_delivery',
        'current_latitude',
        'current_longitude',
        'average_rating',
        'total_ratings',
        'total_deliveries',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'is_approved' => 'boolean',
            'is_on_delivery' => 'boolean',
            'average_rating' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
