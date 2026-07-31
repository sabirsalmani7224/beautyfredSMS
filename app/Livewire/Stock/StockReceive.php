<?php

namespace App\Livewire\Stock;

use App\Models\StockAllocation;
use App\Services\StockService;
use Livewire\Component;

class StockReceive extends Component
{
    public array $receivedQtys = [];

    protected StockService $stockService;

    public function boot(StockService $stockService): void
    {
        $this->stockService = $stockService;
    }

    public function confirmReceipt(int $allocationId): void
    {
        $allocation = StockAllocation::with('items')
            ->where('to_user_id', auth()->id())
            ->findOrFail($allocationId);

        $this->stockService->receiveAllocation($allocation, $this->receivedQtys[$allocationId] ?? []);
        session()->flash('success', 'Stock received and added to your inventory.');
    }

    public function render()
    {
        $pendingAllocations = StockAllocation::with(['fromUser', 'items.product'])
            ->where('to_user_id', auth()->id())
            ->whereIn('status', ['pending', 'dispatched'])
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.stock.stock-receive', compact('pendingAllocations'))
            ->layout('layouts.app', ['title' => 'Receive Stock']);
    }
}
