<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

trait HasHierarchy
{
    /**
     * Get all descendant user IDs (cached for performance at scale).
     */
    public function getAllDownlineIds(bool $includeSelf = false): array
    {
        if ($this->role === 'super_admin') {
            return static::pluck('id')->toArray();
        }

        $cacheKey = "downline_ids_{$this->id}";

        $ids = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return $this->collectDownlineIds($this->id);
        });

        if ($includeSelf) {
            $ids[] = $this->id;
        }

        return array_unique($ids);
    }

    /**
     * Recursively collect all descendant IDs.
     */
    protected function collectDownlineIds(int $userId): array
    {
        $children = static::where('parent_id', $userId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $allIds = $children;
        foreach ($children as $childId) {
            $allIds = array_merge($allIds, $this->collectDownlineIds($childId));
        }

        return $allIds;
    }

    /**
     * Flush the cached downline for this user and all ancestors.
     */
    public function flushDownlineCache(): void
    {
        Cache::forget("downline_ids_{$this->id}");

        // Also flush parent caches up the chain
        $parent = $this->parent;
        while ($parent) {
            Cache::forget("downline_ids_{$parent->id}");
            $parent = $parent->parent;
        }
    }

    /**
     * Get the role hierarchy level (lower = higher in org).
     */
    public function getRoleLevel(): int
    {
        return match($this->role) {
            'super_admin'        => 0,
            'md'                 => 1,
            'ceo'                => 2,
            'nsm'                => 3,
            'ba_national_head'   => 4,
            'zsm'                => 4,
            'rsm'                => 5,
            'asm'                => 6,
            'ba_state_supervisor'=> 7,
            'tl'                 => 7,
            'psm'                => 8,
            'ba'                 => 8,
            default              => 99,
        };
    }

    /**
     * Check if this user is above another in the hierarchy.
     */
    public function isAbove(self $other): bool
    {
        return $this->getRoleLevel() < $other->getRoleLevel();
    }

    /**
     * Check if a given user ID is in this user's downline.
     */
    public function hasInDownline(int $userId): bool
    {
        return in_array($userId, $this->getAllDownlineIds(includeSelf: true));
    }

    /**
     * Get the display label for this user's role.
     */
    public function getRoleLabel(): string
    {
        return match($this->role) {
            'super_admin'         => 'Developer',
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
            default               => ucfirst($this->role),
        };
    }

    /**
     * Check role helpers.
     */
    public function isSuperAdmin(): bool        { return $this->role === 'super_admin'; }
    public function isMD(): bool                { return $this->role === 'md'; }
    public function isCEO(): bool               { return $this->role === 'ceo'; }
    public function isNSM(): bool               { return $this->role === 'nsm'; }
    public function isZSM(): bool               { return $this->role === 'zsm'; }
    public function isRSM(): bool               { return $this->role === 'rsm'; }
    public function isASM(): bool               { return $this->role === 'asm'; }
    public function isTL(): bool                { return $this->role === 'tl'; }
    public function isPSM(): bool               { return $this->role === 'psm'; }
    public function isBANationalHead(): bool     { return $this->role === 'ba_national_head'; }
    public function isBAStateSupervisor(): bool  { return $this->role === 'ba_state_supervisor'; }
    public function isBA(): bool                { return $this->role === 'ba'; }

    /**
     * Returns true if the user is in the BA track.
     */
    public function isInBATrack(): bool
    {
        return in_array($this->role, ['ba_national_head', 'ba_state_supervisor', 'ba']);
    }

    /**
     * Returns true if this user has access to everything (top-level management).
     */
    public function isTopManagement(): bool
    {
        return in_array($this->role, ['super_admin', 'md', 'ceo', 'nsm']);
    }
}
