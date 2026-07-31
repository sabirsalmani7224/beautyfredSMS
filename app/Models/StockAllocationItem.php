<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAllocationItem extends Model
{
    protected $fillable = [
        'stock_allocation_id', 'product_id', 'quantity_sent',
        'quantity_received', 'quantity_damaged', 'notes',
    ];

    public function stockAllocation(): BelongsTo { return $this->belongsTo(StockAllocation::class); }
    public function product(): BelongsTo         { return $this->belongsTo(Product::class); }
}
