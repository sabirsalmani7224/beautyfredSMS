<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search      = '';
    public int    $categoryId  = 0;
    public bool   $showInactive = false;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategoryId(): void { $this->resetPage(); }

    public function toggleActive(int $id): void
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isMD() && !auth()->user()->isCEO() && !auth()->user()->isNSM()) {
            abort(403);
        }
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
    }

    public function render()
    {
        $categories = Category::active()->get();

        $products = Product::with('subCategory.category')
            ->when($this->search, fn($q) =>
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('sku', 'ilike', "%{$this->search}%")
            )
            ->when($this->categoryId, fn($q) =>
                $q->whereHas('subCategory', fn($s) => $s->where('category_id', $this->categoryId))
            )
            ->when(!$this->showInactive, fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate(24);

        return view('livewire.products.product-catalog', compact('products', 'categories'))
            ->layout('layouts.app', ['title' => 'Product Catalog']);
    }
}
