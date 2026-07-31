<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['name', 'zone_id', 'rsm_id', 'states_covered', 'is_active'];

    protected function casts(): array
    {
        return [
            'states_covered' => 'array',
            'is_active'      => 'boolean',
        ];
    }

    public function zone(): BelongsTo  { return $this->belongsTo(Zone::class); }
    public function rsm(): BelongsTo   { return $this->belongsTo(User::class, 'rsm_id'); }
    public function areas(): HasMany   { return $this->hasMany(Area::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
