<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HierarchyService
{
    /**
     * Get all user IDs visible to the given user (their downline + self).
     */
    public function getVisibleUserIds(User $user, bool $includeSelf = true): array
    {
        return $user->getAllDownlineIds(includeSelf: $includeSelf);
    }

    /**
     * Get direct reports of a user.
     */
    public function getDirectReports(User $user): Collection
    {
        return User::where('parent_id', $user->id)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the full management chain above a user (breadcrumb-style).
     */
    public function getAncestors(User $user): Collection
    {
        $ancestors = collect();
        $current = $user->parent;

        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    /**
     * Build a nested tree structure for the org chart.
     */
    public function buildOrgTree(User $root, int $depth = 3): array
    {
        if ($depth === 0) return [];

        $children = User::where('parent_id', $root->id)
            ->active()
            ->orderBy('name')
            ->get();

        $tree = [
            'id'         => $root->id,
            'name'       => $root->name,
            'role'       => $root->role,
            'role_label' => $root->role_label,
            'zone'       => $root->zone,
            'children'   => [],
        ];

        foreach ($children as $child) {
            $tree['children'][] = $this->buildOrgTree($child, $depth - 1);
        }

        return $tree;
    }

    /**
     * Get aggregated sales totals for a user's entire downline.
     */
    public function getDownlineSalesTotals(User $user, string $period = 'month'): array
    {
        $ids = $user->getAllDownlineIds(includeSelf: true);

        $query = Sale::whereIn('user_id', $ids)
            ->where('status', 'approved')
            ->forPeriod($period);

        return [
            'total_amount'  => $query->sum('net_amount'),
            'total_sales'   => $query->count(),
            'avg_sale'      => $query->avg('net_amount') ?? 0,
        ];
    }

    /**
     * Get performance ranking for direct reports of a user.
     */
    public function getDirectReportRankings(User $user, string $period = 'month'): Collection
    {
        $children = $this->getDirectReports($user);

        return $children->map(function (User $child) use ($period) {
            $ids    = $child->getAllDownlineIds(includeSelf: true);
            $sales  = Sale::whereIn('user_id', $ids)
                          ->where('status', 'approved')
                          ->forPeriod($period)
                          ->sum('net_amount');

            $target = $child->targets()->currentMonth()->sum('target_amount');

            return [
                'user'             => $child,
                'sales_amount'     => $sales,
                'target_amount'    => $target,
                'achievement_pct'  => $target > 0 ? round(($sales / $target) * 100, 1) : 0,
            ];
        })->sortByDesc('sales_amount')->values();
    }

    /**
     * Determine what roles a given user can manage/create.
     */
    public function getManageableRoles(User $user): array
    {
        return match($user->role) {
            'super_admin', 'md'  => ['ceo', 'nsm', 'zsm', 'rsm', 'asm', 'tl', 'psm', 'ba_national_head', 'ba_state_supervisor', 'ba'],
            'ceo'                => ['nsm', 'zsm', 'rsm', 'asm', 'tl', 'psm', 'ba_national_head', 'ba_state_supervisor', 'ba'],
            'nsm'                => ['zsm', 'rsm', 'asm', 'tl', 'psm', 'ba_national_head', 'ba_state_supervisor', 'ba'],
            'ba_national_head'   => ['ba_state_supervisor', 'ba'],
            'zsm'                => ['rsm', 'asm', 'tl', 'psm'],
            'rsm'                => ['asm', 'tl', 'psm'],
            'asm'                => ['tl', 'psm', 'ba_state_supervisor', 'ba'],
            'tl'                 => ['psm'],
            'ba_state_supervisor'=> ['ba'],
            default              => [],
        };
    }
}
