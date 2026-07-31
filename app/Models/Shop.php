<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'owner_name', 'phone', 'email', 'address',
        'city', 'state', 'pincode', 'gps_latitude', 'gps_longitude',
        'shop_type', 'registered_by', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gps_latitude'  => 'decimal:7',
            'gps_longitude' => 'decimal:7',
            'is_active'     => 'boolean',
        ];
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getShopTypeLabelAttribute(): string
    {
        return match($this->shop_type) {
            'retail'      => 'Retail Store',
            'wholesale'   => 'Wholesale',
            'distributor' => 'Distributor',
            'salon'       => 'Salon/Parlour',
            'pharmacy'    => 'Pharmacy',
            default       => 'Other',
        };
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->gps_latitude && $this->gps_longitude) {
            return "https://maps.google.com/?q={$this->gps_latitude},{$this->gps_longitude}";
        }
        return null;
    }
}
