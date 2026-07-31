<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?int   $productId      = null;
    public int    $subCategoryId  = 0;
    public string $name           = '';
    public string $sku            = '';
    public string $description    = '';
    public float  $mrp            = 0;
    public float  $unitPrice      = 0;
    public float  $distributorPrice = 0;
    public string $hsnCode        = '';
    public string $stockUnit      = 'unit';
    public bool   $isActive       = true;
    public        $productImage   = null;

    public function mount(?Product $product = null): void
    {
        if ($product && $product->exists) {
            $this->productId       = $product->id;
            $this->subCategoryId   = $product->sub_category_id;
            $this->name            = $product->name;
            $this->sku             = $product->sku;
            $this->description     = $product->description ?? '';
            $this->mrp             = (float) $product->mrp;
            $this->unitPrice       = (float) $product->unit_price;
            $this->distributorPrice= (float) $product->distributor_price;
            $this->hsnCode         = $product->hsn_code ?? '';
            $this->stockUnit       = $product->stock_unit ?? 'unit';
            $this->isActive        = $product->is_active;
        }
    }

    public function save(): void
    {
        $this->validate([
            'subCategoryId'   => 'required|exists:sub_categories,id',
            'name'            => 'required|string|max:200',
            'sku'             => 'nullable|string|max:50',
            'mrp'             => 'required|numeric|min:0',
            'unitPrice'       => 'required|numeric|min:0',
            'distributorPrice'=> 'nullable|numeric|min:0',
            'hsnCode'         => 'nullable|string|max:20',
            'productImage'    => 'nullable|image|max:3072',
        ]);

        $imagePath = $this->productImage?->store('product-images', 'public');

        $data = [
            'sub_category_id'   => $this->subCategoryId,
            'name'              => $this->name,
            'slug'              => Str::slug($this->name),
            'sku'               => $this->sku ?: null,
            'description'       => $this->description ?: null,
            'mrp'               => $this->mrp,
            'unit_price'        => $this->unitPrice,
            'distributor_price' => $this->distributorPrice,
            'hsn_code'          => $this->hsnCode ?: null,
            'stock_unit'        => $this->stockUnit,
            'is_active'         => $this->isActive,
        ];

        if ($imagePath) $data['image'] = $imagePath;

        $this->productId
            ? Product::findOrFail($this->productId)->update($data)
            : Product::create($data);

        session()->flash('success', $this->productId ? 'Product updated.' : 'Product created.');
        $this->redirect(route('products.index'), navigate: true);
    }

    public function render()
    {
        $categories = Category::with('subCategories:id,name,category_id')
            ->active()->orderBy('sort_order')->get();

        return view('livewire.products.product-form', compact('categories'))
            ->layout('layouts.app', ['title' => $this->productId ? 'Edit Product' : 'Add Product']);
    }
}
