<?php

namespace App\Models;

use App\Traits\HasHierarchy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasHierarchy, SoftDeletes;

    // ── Role Constants ─────────────────────────────────────────────────────
    const ROLES = [
        'super_admin'         => 'Super Admin',
        'md'                  => 'Managing Director',
        'ceo'                 => 'CEO',
        'nsm'                 => 'National Sales Manager',
        'zsm'                 => 'Zonal Sales Manager',
        'rsm'                 => 'Regional Sales Manager',
        'asm'                 => 'Area Sales Manager',
        'tl'                  => 'Team Leader',
        'psm'                 => 'Product Sales Manager',
        'ba_national_head'    => 'BA National Head',
        'ba_state_supervisor' => 'BA State Supervisor',
        'ba'                  => 'Beauty Advisor',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'parent_id', 'asm_id', 'zone', 'state', 'city',
        'employee_code', 'profile_photo', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /** For BA State Supervisors: the ASM they also report to */
    public function asmManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asm_id');
    }

    public function baStateSupervisors(): HasMany
    {
        return $this->hasMany(User::class, 'asm_id')->where('role', 'ba_state_supervisor');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockAllocationsReceived(): HasMany
    {
        return $this->hasMany(StockAllocation::class, 'to_user_id');
    }

    public function stockAllocationsSent(): HasMany
    {
        return $this->hasMany(StockAllocation::class, 'from_user_id');
    }

    public function userStock(): HasMany
    {
        return $this->hasMany(UserStock::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class, 'registered_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string|array $role)
    {
        return $query->whereIn('role', (array) $role);
    }

    public function scopeInDownlineOf($query, User $manager)
    {
        $ids = $manager->getAllDownlineIds(includeSelf: false);
        return $query->whereIn('id', $ids);
    }

    // ── Role Helpers ───────────────────────────────────────────────────────

    public function isSuperAdmin(): bool       { return $this->role === 'super_admin'; }
    public function isMD(): bool               { return $this->role === 'md'; }
    public function isCEO(): bool              { return $this->role === 'ceo'; }
    public function isNSM(): bool              { return $this->role === 'nsm'; }
    public function isZSM(): bool              { return $this->role === 'zsm'; }
    public function isRSM(): bool              { return $this->role === 'rsm'; }
    public function isASM(): bool              { return $this->role === 'asm'; }
    public function isTL(): bool               { return $this->role === 'tl'; }
    public function isPSM(): bool              { return $this->role === 'psm'; }
    public function isBANationalHead(): bool   { return $this->role === 'ba_national_head'; }
    public function isBAStateSupervisor(): bool{ return $this->role === 'ba_state_supervisor'; }
    public function isBA(): bool               { return $this->role === 'ba'; }

    public function isTopManagement(): bool
    {
        return in_array($this->role, ['super_admin', 'md', 'ceo', 'nsm']);
    }

    public function isFieldStaff(): bool
    {
        return in_array($this->role, ['psm', 'ba']);
    }

    /**
     * Check if a given user ID is within this user's visible downline.
     */
    public function hasInDownline(int $userId): bool
    {
        return in_array($userId, $this->getAllDownlineIds(includeSelf: true));
    }

    /**
     * Get the human-readable role label.
     */
    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role ?? ''));
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        $name = urlencode(substr($this->name ?? 'BF', 0, 2));
        return "https://ui-avatars.com/api/?name={$name}&background=4A0E2E&color=C9A84C&bold=true&format=svg";
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->getRoleLabel();
    }

    public function getZoneLabelAttribute(): string
    {
        return $this->zone ? ucfirst($this->zone) : '—';
    }
}
