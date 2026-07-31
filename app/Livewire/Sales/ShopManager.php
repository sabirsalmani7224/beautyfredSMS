<?php

namespace App\Livewire\Sales;

use App\Models\Shop;
use Livewire\Component;
use Livewire\WithPagination;

class ShopManager extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $city      = '';
    public bool   $showForm  = false;

    // Form fields
    public ?int   $shopId    = null;
    public string $name      = '';
    public string $ownerName = '';
    public string $phone     = '';
    public string $address   = '';
    public string $shopCity  = '';
    public string $state     = '';
    public float  $lat       = 0;
    public float  $lng       = 0;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCity(): void   { $this->resetPage(); }

    public function editShop(int $id): void
    {
        $shop = Shop::findOrFail($id);
        abort_unless(auth()->user()->hasInDownline($shop->registered_by), 403);
        $this->shopId    = $shop->id;
        $this->name      = $shop->name;
        $this->ownerName = $shop->owner_name ?? '';
        $this->phone     = $shop->phone ?? '';
        $this->address   = $shop->address ?? '';
        $this->shopCity  = $shop->city ?? '';
        $this->state     = $shop->state ?? '';
        $this->lat       = (float) ($shop->gps_latitude ?? 0);
        $this->lng       = (float) ($shop->gps_longitude ?? 0);
        $this->showForm  = true;
    }

    public function newShop(): void
    {
        $this->reset(['shopId', 'name', 'ownerName', 'phone', 'address', 'shopCity', 'state', 'lat', 'lng']);
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'      => 'required|string|max:200',
            'phone'     => 'nullable|string|max:15',
            'shopCity'  => 'nullable|string|max:100',
            'state'     => 'nullable|string|max:100',
        ]);

        $data = [
            'name'          => $this->name,
            'owner_name'    => $this->ownerName ?: null,
            'phone'         => $this->phone ?: null,
            'address'       => $this->address ?: null,
            'city'          => $this->shopCity ?: null,
            'state'         => $this->state ?: null,
            'gps_latitude'  => $this->lat ?: null,
            'gps_longitude' => $this->lng ?: null,
            'is_active'     => true,
        ];

        if ($this->shopId) {
            Shop::findOrFail($this->shopId)->update($data);
        } else {
            $data['registered_by'] = auth()->id();
            Shop::create($data);
        }

        $this->showForm = false;
        $this->reset(['shopId', 'name', 'ownerName', 'phone', 'address', 'shopCity', 'state', 'lat', 'lng']);
        session()->flash('success', 'Shop saved.');
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
    }

    public function render()
    {
        $user        = auth()->user();
        $downlineIds = $user->getAllDownlineIds(includeSelf: true);

        $shops = Shop::whereIn('registered_by', $downlineIds)
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('owner_name', 'ilike', "%{$this->search}%")
                  ->orWhere('city', 'ilike', "%{$this->search}%");
            }))
            ->when($this->city, fn($q) => $q->where('city', 'ilike', "%{$this->city}%"))
            ->with('registeredBy:id,name')
            ->withCount('sales')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.sales.shop-manager', compact('shops'))
            ->layout('layouts.app', ['title' => 'Shops']);
    }
}
