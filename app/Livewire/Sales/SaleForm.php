<?php

namespace App\Livewire\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Services\StockService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class SaleForm extends Component
{
    use WithFileUploads;

    // Sale fields
    public string $saleType     = 'shop_sale';
    public string $saleDate     = '';
    public ?int   $shopId       = null;
    public string $eventName    = '';
    public string $paymentMode  = 'cash';
    public string $paymentRef   = '';
    public string $notes        = '';
    public float  $gpsLat       = 0;
    public float  $gpsLng       = 0;
    public        $photoProof   = null;

    // Cart
    public array  $cartItems    = []; // [{product_id, name, qty, unit_price, total}]
    public int    $selectedProduct = 0;
    public int    $selectedQty  = 1;

    // Shop search
    public string $shopSearch   = '';
    public array  $shopResults  = [];

    protected StockService $stockService;

    public function boot(StockService $stockService): void
    {
        $this->stockService = $stockService;
        $this->saleDate     = today()->toDateString();
    }

    public function addToCart(): void
    {
        if (!$this->selectedProduct || $this->selectedQty < 1) return;

        $product = Product::find($this->selectedProduct);
        if (!$product) return;

        // Check stock
        $available = $this->stockService->getProductStock(auth()->user(), $product->id);
        if ($available < $this->selectedQty) {
            $this->addError('selectedProduct', "Only {$available} units available in stock.");
            return;
        }

        // Check if already in cart
        $existing = collect($this->cartItems)->search(fn($i) => $i['product_id'] === $product->id);
        if ($existing !== false) {
            $this->cartItems[$existing]['qty']        += $this->selectedQty;
            $this->cartItems[$existing]['total_price'] = $this->cartItems[$existing]['qty'] * $this->cartItems[$existing]['unit_price'];
        } else {
            $this->cartItems[] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'sku'        => $product->sku,
                'qty'        => $this->selectedQty,
                'unit_price' => (float) $product->unit_price,
                'total_price'=> $this->selectedQty * (float) $product->unit_price,
            ];
        }

        $this->selectedProduct = 0;
        $this->selectedQty     = 1;
        $this->resetErrorBag('selectedProduct');
    }

    public function removeFromCart(int $index): void
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
    }

    public function getTotalAmountProperty(): float
    {
        return collect($this->cartItems)->sum('total_price');
    }

    public function searchShops(): void
    {
        $this->shopResults = Shop::where('name', 'ilike', "%{$this->shopSearch}%")
            ->limit(6)->get(['id', 'name', 'city', 'shop_type'])->toArray();
    }

    public function selectShop(int $id, string $name): void
    {
        $this->shopId     = $id;
        $this->shopSearch = $name;
        $this->shopResults = [];
    }

    public function submit(): void
    {
        $this->validate([
            'saleType'   => 'required|in:shop_sale,event_sale',
            'saleDate'   => 'required|date',
            'cartItems'  => 'required|min:1',
            'paymentMode'=> 'required',
            'photoProof' => 'nullable|image|max:5120',
        ]);

        if ($this->saleType === 'shop_sale' && !$this->shopId) {
            $this->addError('shopId', 'Please select a shop for this sale.');
            return;
        }
        if ($this->saleType === 'event_sale' && !$this->eventName) {
            $this->addError('eventName', 'Please enter the event/stall name.');
            return;
        }

        DB::transaction(function () {
            $photoPath = $this->photoProof?->store('sale-proofs', 'public');
            $totalAmt  = $this->totalAmount;

            $sale = Sale::create([
                'user_id'          => auth()->id(),
                'shop_id'          => $this->saleType === 'shop_sale' ? $this->shopId : null,
                'event_name'       => $this->saleType === 'event_sale' ? $this->eventName : null,
                'sale_type'        => $this->saleType,
                'sale_date'        => $this->saleDate,
                'total_amount'     => $totalAmt,
                'discount_amount'  => 0,
                'net_amount'       => $totalAmt,
                'payment_mode'     => $this->paymentMode,
                'payment_reference'=> $this->paymentRef ?: null,
                'gps_latitude'     => $this->gpsLat ?: null,
                'gps_longitude'    => $this->gpsLng ?: null,
                'photo_proof'      => $photoPath,
                'notes'            => $this->notes ?: null,
                'status'           => 'pending',
            ]);

            foreach ($this->cartItems as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount'   => 0,
                    'total_price'=> $item['total_price'],
                ]);
            }

            // Deduct from stock
            $this->stockService->deductSaleStock(auth()->user(), array_map(
                fn($i) => ['product_id' => $i['product_id'], 'quantity' => $i['qty']],
                $this->cartItems
            ));
        });

        session()->flash('success', 'Sale recorded successfully! Pending approval.');
        $this->redirect(route('sales.index'), navigate: true);
    }

    public function render()
    {
        $products = Product::with('subCategory.category')
            ->active()
            ->orderBy('name')
            ->get(['id','name','sku','unit_price','sub_category_id']);

        $myStock = \App\Models\UserStock::where('user_id', auth()->id())
            ->pluck('quantity', 'product_id');

        return view('livewire.sales.sale-form', compact('products', 'myStock'))
            ->layout('layouts.app', ['title' => 'Record Sale']);
    }
}
