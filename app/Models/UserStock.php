<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStock extends Model
{
    protected $table = 'user_stock';

    protected $fillable = ['user_id', 'product_id', 'quantity', 'last_updated_at'];

    protected function casts(): array
    {
        return ['last_updated_at' => 'datetime'];
    }

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }

    public function scopeLowStock($query, int $threshold = 10)
    {
        return $query->where('quantity', '<=', $threshold)->where('quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }
}
