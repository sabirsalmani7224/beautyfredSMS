<?php

namespace App\Livewire\Dashboard;

use App\Models\Sale;
use App\Models\User;
use App\Services\HierarchyService;
use App\Traits\HasHierarchySalesFilter;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    use HasHierarchySalesFilter;

    public string $period = 'month';

    protected ?HierarchyService $hierarchy = null;

    public function boot(HierarchyService $hierarchy): void
    {
        $this->hierarchy = $hierarchy;
    }

    private function hierarchy(): HierarchyService
    {
        return $this->hierarchy ??= app(HierarchyService::class);
    }

    private function periodDateRange(): array
    {
        return match($this->period) {
            'today'   => [today()->startOfDay(), today()->endOfDay()],
            'week'    => [now()->startOfWeek(), now()->endOfWeek()],
            'month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'year'    => [now()->startOfYear(), now()->endOfYear()],
            default   => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    public function setPeriod(string $period): void
    {
        $this->period = in_array($period, ['today','week','month','quarter','year'])
            ? $period
            : 'month';
    }

    public function render()
    {
        $user             = auth()->user();
        $downlineIds      = $this->getFilteredUserIds();
        $hierarchyFilters = $this->getHierarchyFilterOptions();
        [$from, $to]      = $this->periodDateRange();

        // ── Sales Stats ───────────────────────────────────────────────────
        $baseQuery = Sale::whereIn('user_id', $downlineIds)
            ->where('status', 'approved')
            ->whereBetween('sale_date', [$from, $to]);

        $totalSales   = (clone $baseQuery)->sum('net_amount');
        $salesCount   = (clone $baseQuery)->count();
        $pendingSales = Sale::whereIn('user_id', $downlineIds)->where('status', 'pending')->count();
        $avgSale      = $salesCount > 0 ? round($totalSales / $salesCount, 2) : 0;

        // ── Target for this month ─────────────────────────────────────────
        $monthTarget = $user->targets()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->sum('target_amount');
        $achievement = $monthTarget > 0 ? round(($totalSales / $monthTarget) * 100, 1) : 0;

        // ── Sales by Category ──────────────────────────────────────────────
        $salesByCategory = DB::table('sale_items')
            ->join('sales',         'sale_items.sale_id',          '=', 'sales.id')
            ->join('products',      'sale_items.product_id',        '=', 'products.id')
            ->join('sub_categories','products.sub_category_id',     '=', 'sub_categories.id')
            ->join('categories',    'sub_categories.category_id',   '=', 'categories.id')
            ->whereIn('sales.user_id', $downlineIds)
            ->where('sales.status', 'approved')
            ->whereBetween('sales.sale_date', [$from->toDateString(), $to->toDateString()])
            ->whereNull('sales.deleted_at')
            ->select('categories.name', DB::raw('SUM(sale_items.total_price) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        // ── Top Performers ─────────────────────────────────────────────────
        $topPerformers = $this->hierarchy()->getDirectReportRankings($user, $this->period)->take(5);

        // ── Trend Query (Hours for today, Week days for week, Dates for month, Months for year) ──
        if ($this->period === 'today') {
            $monthlyTrend = Sale::whereIn('user_id', $downlineIds)
                ->where('status', 'approved')
                ->whereBetween('created_at', [$from, $to])
                ->selectRaw("TO_CHAR(created_at, 'HH24:00') as month, TO_CHAR(created_at, 'HH24') as ym, ROUND(SUM(net_amount)::numeric, 2) as total")
                ->groupByRaw("TO_CHAR(created_at, 'HH24:00'), TO_CHAR(created_at, 'HH24')")
                ->orderBy('ym')
                ->get();
        } else if ($this->period === 'week') {
            $monthlyTrend = Sale::whereIn('user_id', $downlineIds)
                ->where('status', 'approved')
                ->whereBetween('sale_date', [$from, $to])
                ->selectRaw("TO_CHAR(sale_date, 'Dy') as month, TO_CHAR(sale_date, 'YYYY-MM-DD') as ym, ROUND(SUM(net_amount)::numeric, 2) as total")
                ->groupByRaw("TO_CHAR(sale_date, 'Dy'), TO_CHAR(sale_date, 'YYYY-MM-DD')")
                ->orderBy('ym')
                ->get();
        } else if ($this->period === 'month') {
            $monthlyTrend = Sale::whereIn('user_id', $downlineIds)
                ->where('status', 'approved')
                ->whereBetween('sale_date', [$from, $to])
                ->selectRaw("TO_CHAR(sale_date, 'DD Mon') as month, TO_CHAR(sale_date, 'YYYY-MM-DD') as ym, ROUND(SUM(net_amount)::numeric, 2) as total")
                ->groupByRaw("TO_CHAR(sale_date, 'DD Mon'), TO_CHAR(sale_date, 'YYYY-MM-DD')")
                ->orderBy('ym')
                ->get();
        } else if ($this->period === 'quarter') {
            $monthlyTrend = Sale::whereIn('user_id', $downlineIds)
                ->where('status', 'approved')
                ->whereBetween('sale_date', [$from, $to])
                ->selectRaw("TO_CHAR(sale_date, 'Mon YY') as month, TO_CHAR(sale_date, 'YYYY-MM') as ym, ROUND(SUM(net_amount)::numeric, 2) as total")
                ->groupByRaw("TO_CHAR(sale_date, 'Mon YY'), TO_CHAR(sale_date, 'YYYY-MM')")
                ->orderBy('ym')
                ->get();
        } else { // year
            $monthlyTrend = Sale::whereIn('user_id', $downlineIds)
                ->where('status', 'approved')
                ->whereBetween('sale_date', [$from, $to])
                ->selectRaw("TO_CHAR(sale_date, 'Mon') as month, TO_CHAR(sale_date, 'YYYY-MM') as ym, ROUND(SUM(net_amount)::numeric, 2) as total")
                ->groupByRaw("TO_CHAR(sale_date, 'Mon'), TO_CHAR(sale_date, 'YYYY-MM')")
                ->orderBy('ym')
                ->get();
        }

        // ── Team Stats ─────────────────────────────────────────────────────
        $totalTeamSize = max(0, count($downlineIds) - 1);
        $activeToday   = Sale::whereIn('user_id', $downlineIds)
            ->whereDate('sale_date', today())
            ->distinct('user_id')
            ->count('user_id');

        return view('livewire.dashboard.admin-dashboard', compact(
            'totalSales', 'salesCount', 'pendingSales', 'avgSale',
            'monthTarget', 'achievement', 'salesByCategory',
            'topPerformers', 'monthlyTrend', 'totalTeamSize', 'activeToday',
            'hierarchyFilters'
        ))->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
