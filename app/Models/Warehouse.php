<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'city', 'state', 'pincode',
        'manager_name', 'manager_phone', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function stockAllocations(): HasMany
    {
        return $this->hasMany(StockAllocation::class, 'from_warehouse_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
