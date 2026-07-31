<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sub_category_id', 'name', 'slug', 'sku', 'mrp', 'unit_price',
        'distributor_price', 'description', 'image', 'hsn_code', 'weight',
        'stock_unit', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mrp'               => 'decimal:2',
            'unit_price'        => 'decimal:2',
            'distributor_price' => 'decimal:2',
            'is_active'         => 'boolean',
        ];
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category()
    {
        return $this->subCategory->category ?? null;
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockAllocationItems(): HasMany
    {
        return $this->hasMany(StockAllocationItem::class);
    }

    public function userStocks(): HasMany
    {
        return $this->hasMany(UserStock::class);
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/product-placeholder.png');
    }
}
