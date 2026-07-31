<?php

namespace App\Livewire\Dashboard;

use App\Models\Sale;
use App\Models\UserStock;
use Livewire\Component;

class FieldDashboard extends Component
{
    public function render()
    {
        $user    = auth()->user();
        $today   = today();
        $month   = now()->startOfMonth();

        $todaySales    = Sale::where('user_id', $user->id)->whereDate('sale_date', $today)->where('status','approved')->sum('net_amount');
        $monthSales    = Sale::where('user_id', $user->id)->where('status','approved')->whereBetween('sale_date', [$month, now()->endOfMonth()])->sum('net_amount');
        $pendingCount  = Sale::where('user_id', $user->id)->where('status','pending')->count();
        $monthTarget   = $user->targets()->where('month', now()->month)->where('year', now()->year)->sum('target_amount');
        $achievement   = $monthTarget > 0 ? round(($monthSales / $monthTarget) * 100, 1) : 0;

        $myStock       = UserStock::with('product')->where('user_id', $user->id)->where('quantity', '>', 0)->orderByDesc('quantity')->take(10)->get();
        $recentSales   = Sale::with('shop','items.product')->where('user_id', $user->id)->orderByDesc('sale_date')->take(5)->get();

        return view('livewire.dashboard.field-dashboard', compact(
            'todaySales', 'monthSales', 'pendingCount',
            'monthTarget', 'achievement', 'myStock', 'recentSales'
        ))->layout('layouts.app', ['title' => 'My Dashboard']);
    }
}
