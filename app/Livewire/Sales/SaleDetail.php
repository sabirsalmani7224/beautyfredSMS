<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;

class SaleDetail extends Component
{
    public Sale $sale;

    public function mount(Sale $sale): void
    {
        $user = auth()->user();
        $downlineIds = $user->getAllDownlineIds(includeSelf: true);
        if (!in_array($sale->user_id, $downlineIds)) {
            abort(403);
        }
        $this->sale = $sale->load(['user', 'shop', 'items.product.subCategory.category', 'approvedBy']);
    }

    public function approve(): void
    {
        if (auth()->user()->isPSM()) abort(403);
        $this->sale->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        $this->sale->refresh();
        session()->flash('success', 'Sale approved.');
    }

    public function reject(): void
    {
        if (auth()->user()->isPSM()) abort(403);
        $this->sale->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        $this->sale->refresh();
        session()->flash('success', 'Sale rejected.');
    }

    public function render()
    {
        return view('livewire.sales.sale-detail')
            ->layout('layouts.app', ['title' => 'Sale #' . $this->sale->id]);
    }
}
