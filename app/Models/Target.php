<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    protected $fillable = [
        'user_id', 'set_by_user_id', 'category_id',
        'month', 'year', 'target_amount', 'target_quantity', 'notes',
    ];

    protected function casts(): array
    {
        return ['target_amount' => 'decimal:2'];
    }

    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function setBy(): BelongsTo     { return $this->belongsTo(User::class, 'set_by_user_id'); }
    public function category(): BelongsTo  { return $this->belongsTo(Category::class); }

    /**
     * Calculate the achievement percentage against target.
     */
    public function getAchievementPercent(float $achieved): float
    {
        if ($this->target_amount <= 0) return 0;
        return round(($achieved / $this->target_amount) * 100, 1);
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('month', now()->month)->where('year', now()->year);
    }
}
