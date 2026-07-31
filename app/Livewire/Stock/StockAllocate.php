<?php

namespace App\Livewire\Stock;

use App\Models\StockAllocation;
use App\Models\StockAllocationItem;
use App\Models\Product;
use App\Models\User;
use App\Services\StockService;
use Livewire\Component;

class StockAllocate extends Component
{
    public int    $toUserId   = 0;
    public array  $items      = []; // [{product_id, quantity}]
    public int    $productId  = 0;
    public int    $quantity   = 1;
    public string $notes      = '';

    protected StockService $stockService;

    public function boot(StockService $stockService): void
    {
        $this->stockService = $stockService;
    }

    public function addItem(): void
    {
        if (!$this->productId || $this->quantity < 1) return;

        $available = $this->stockService->getProductStock(auth()->user(), $this->productId);
        if ($available < $this->quantity) {
            $this->addError('productId', "Only {$available} units available.");
            return;
        }

        $existing = collect($this->items)->search(fn($i) => $i['product_id'] === $this->productId);
        if ($existing !== false) {
            $this->items[$existing]['quantity'] += $this->quantity;
        } else {
            $product = Product::find($this->productId);
            $this->items[] = [
                'product_id' => $this->productId,
                'name'       => $product->name,
                'quantity'   => $this->quantity,
            ];
        }
        $this->productId = 0;
        $this->quantity  = 1;
        $this->resetErrorBag('productId');
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function send(): void
    {
        $this->validate([
            'toUserId' => 'required|exists:users,id',
            'items'    => 'required|min:1',
        ]);

        $toUser = User::findOrFail($this->toUserId);
        $this->stockService->allocateStock(
            auth()->user(), $toUser,
            array_map(fn($i) => ['product_id' => $i['product_id'], 'quantity' => $i['quantity']], $this->items),
            null, $this->notes ?: null
        );

        session()->flash('success', "Stock dispatched to {$toUser->name}.");
        $this->redirect(route('stock.index'), navigate: true);
    }

    public function render()
    {
        $user         = auth()->user();
        $myStock      = \App\Models\UserStock::with('product')->where('user_id', $user->id)->where('quantity', '>', 0)->get();
        $directReports = User::where('parent_id', $user->id)->active()->orderBy('name')->get(['id','name','role']);

        return view('livewire.stock.stock-allocate', compact('myStock', 'directReports'))
            ->layout('layouts.app', ['title' => 'Allocate Stock']);
    }
}
