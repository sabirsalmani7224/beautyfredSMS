<?php

namespace App\Livewire\Stock;

use App\Models\UserStock;
use App\Models\StockAllocation;
use Livewire\Component;

class StockDashboard extends Component
{
    public string $view = 'my_stock'; // my_stock | pending_in | pending_out

    public function render()
    {
        $user = auth()->user();

        $myStock = UserStock::with('product.subCategory.category')
            ->where('user_id', $user->id)
            ->orderByDesc('quantity')
            ->get();

        $pendingIn = StockAllocation::with(['fromUser', 'items.product'])
            ->where('to_user_id', $user->id)
            ->whereIn('status', ['pending', 'dispatched'])
            ->orderByDesc('created_at')
            ->get();

        $pendingOut = StockAllocation::with(['toUser', 'items.product'])
            ->where('from_user_id', $user->id)
            ->whereIn('status', ['pending', 'dispatched'])
            ->orderByDesc('created_at')
            ->get();

        $totalStockValue = $myStock->sum(fn($s) => $s->quantity * ($s->product->unit_price ?? 0));
        $lowStockItems   = $myStock->filter(fn($s) => $s->quantity > 0 && $s->quantity <= 10)->count();
        $outOfStockItems = $myStock->filter(fn($s) => $s->quantity === 0)->count();

        return view('livewire.stock.stock-dashboard', compact(
            'myStock', 'pendingIn', 'pendingOut',
            'totalStockValue', 'lowStockItems', 'outOfStockItems'
        ))->layout('layouts.app', ['title' => 'Inventory & Stock']);
    }
}
