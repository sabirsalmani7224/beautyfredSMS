<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntry extends Model
{
    protected $fillable = [
        'warehouse_id', 'product_id', 'quantity', 'batch_number',
        'manufacturing_date', 'expiry_date', 'entry_date', 'purchase_price', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'manufacturing_date' => 'date',
            'expiry_date'        => 'date',
            'entry_date'         => 'date',
            'purchase_price'     => 'decimal:2',
        ];
    }

    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function product(): BelongsTo   { return $this->belongsTo(Product::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
