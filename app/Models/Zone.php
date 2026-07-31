<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name', 'slug', 'zsm_id', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function zsm(): BelongsTo   { return $this->belongsTo(User::class, 'zsm_id'); }
    public function regions(): HasMany { return $this->hasMany(Region::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
