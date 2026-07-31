<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Traits\HasHierarchySalesFilter;
use Livewire\Component;
use Livewire\WithPagination;

class SalesList extends Component
{
    use WithPagination, HasHierarchySalesFilter;

    public string $search   = '';
    public string $period   = 'month';
    public string $status   = '';
    public string $saleType = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPeriod(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function approve(int $id): void
    {
        $sale = Sale::findOrFail($id);
        if (!auth()->user()->hasInDownline($sale->user_id) && !auth()->user()->isTopManagement()) {
            abort(403);
        }
        $sale->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        session()->flash('success', 'Sale approved successfully.');
    }

    public function reject(int $id): void
    {
        $sale = Sale::findOrFail($id);
        $sale->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        session()->flash('success', 'Sale rejected.');
    }

    public function render()
    {
        $filteredUserIds = $this->getFilteredUserIds();
        $hierarchyFilters = $this->getHierarchyFilterOptions();

        $sales = Sale::with(['user', 'shop', 'items.product'])
            ->whereIn('user_id', $filteredUserIds)
            ->when($this->search, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'ilike', "%{$this->search}%"))
                  ->orWhereHas('shop', fn($s) => $s->where('name', 'ilike', "%{$this->search}%"))
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->saleType, fn($q) => $q->where('sale_type', $this->saleType))
            ->forPeriod($this->period)
            ->orderByDesc('sale_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'total'    => Sale::whereIn('user_id', $filteredUserIds)->forPeriod($this->period)->where('status','approved')->sum('net_amount'),
            'pending'  => Sale::whereIn('user_id', $filteredUserIds)->pending()->count(),
            'approved' => Sale::whereIn('user_id', $filteredUserIds)->approved()->forPeriod($this->period)->count(),
        ];

        return view('livewire.sales.sales-list', compact('sales', 'stats', 'hierarchyFilters'))
            ->layout('layouts.app', ['title' => 'Sales']);
    }
}
