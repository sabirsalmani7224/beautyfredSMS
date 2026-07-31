<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAllocation extends Model
{
    protected $fillable = [
        'from_user_id', 'to_user_id', 'from_warehouse_id', 'status',
        'dispatch_date', 'received_date', 'notes', 'rejection_reason', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'dispatch_date'  => 'date',
            'received_date'  => 'date',
        ];
    }

    public function fromUser(): BelongsTo      { return $this->belongsTo(User::class, 'from_user_id'); }
    public function toUser(): BelongsTo        { return $this->belongsTo(User::class, 'to_user_id'); }
    public function fromWarehouse(): BelongsTo { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function createdBy(): BelongsTo     { return $this->belongsTo(User::class, 'created_by'); }

    public function items(): HasMany
    {
        return $this->hasMany(StockAllocationItem::class);
    }

    public function scopePending($query)    { return $query->where('status', 'pending'); }
    public function scopeDispatched($query) { return $query->where('status', 'dispatched'); }
    public function scopeReceived($query)   { return $query->where('status', 'received'); }
}
