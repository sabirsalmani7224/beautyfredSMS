<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    protected $fillable = ['name', 'region_id', 'asm_id', 'city', 'state', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function region(): BelongsTo { return $this->belongsTo(Region::class); }
    public function asm(): BelongsTo    { return $this->belongsTo(User::class, 'asm_id'); }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
