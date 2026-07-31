<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'shop_id', 'event_name', 'sale_type', 'sale_date',
        'total_amount', 'discount_amount', 'net_amount', 'payment_mode',
        'payment_reference', 'gps_latitude', 'gps_longitude', 'photo_proof',
        'notes', 'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'sale_date'      => 'date',
            'approved_at'    => 'datetime',
            'total_amount'   => 'decimal:2',
            'discount_amount'=> 'decimal:2',
            'net_amount'     => 'decimal:2',
            'gps_latitude'   => 'decimal:7',
            'gps_longitude'  => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_proof ? asset('storage/' . $this->photo_proof) : null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'  => '<span class="badge badge-warning">Pending</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
            default    => '<span class="badge badge-secondary">Unknown</span>',
        };
    }

    public function scopePending($query)   { return $query->where('status', 'pending'); }
    public function scopeApproved($query)  { return $query->where('status', 'approved'); }
    public function scopeRejected($query)  { return $query->where('status', 'rejected'); }

    public function scopeForPeriod($query, string $period)
    {
        return match($period) {
            'today'   => $query->whereDate('sale_date', today()),
            'week'    => $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]),
            'month'   => $query->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year),
            'quarter' => $query->whereBetween('sale_date', [now()->startOfQuarter(), now()->endOfQuarter()]),
            'year'    => $query->whereYear('sale_date', now()->year),
            default   => $query,
        };
    }
}
