<div class="space-y-5 animate-fade-up">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Inventory & Stock</h2>
            <p class="text-white/40 text-sm mt-1">Manage your product stock levels and allocations</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('reports.stock.csv') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border border-white/10 text-white/80 hover:text-white text-sm font-semibold rounded-xl hover:bg-white/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('stock.receive') }}" wire:navigate
               class="px-4 py-2.5 bg-white/5 border border-white/10 hover:border-[#C9A84C]/40 text-white text-sm font-medium rounded-xl transition-all">
                📥 Receive Stock
            </a>
            @if(!auth()->user()->isPSM() && !auth()->user()->isBA())
            <a href="{{ route('stock.allocate') }}" wire:navigate
               class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-[#C9A84C]/20">
                📤 Allocate Stock
            </a>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card bg-gradient-to-br from-[#4A0E2E]/60 to-[#7B1D4C]/30">
            <p class="text-xs text-white/50 uppercase tracking-wider">Stock Value</p>
            <p class="text-2xl font-bold text-white mt-1">₹{{ number_format($totalStockValue, 0) }}</p>
            <p class="text-xs text-white/40 mt-1">At unit price</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-amber-900/40 to-amber-700/20">
            <p class="text-xs text-white/50 uppercase tracking-wider">Low Stock</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ $lowStockItems }}</p>
            <p class="text-xs text-white/40 mt-1">≤ 10 units remaining</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-rose-900/40 to-rose-700/20">
            <p class="text-xs text-white/50 uppercase tracking-wider">Out of Stock</p>
            <p class="text-2xl font-bold text-rose-400 mt-1">{{ $outOfStockItems }}</p>
            <p class="text-xs text-white/40 mt-1">Zero units</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 bg-white/5 border border-white/10 rounded-xl p-1 w-fit">
        @foreach(['my_stock' => '📦 My Stock', 'pending_in' => '📥 Incoming (' . $pendingIn->count() . ')', 'pending_out' => '📤 Outgoing (' . $pendingOut->count() . ')'] as $val => $label)
        <button wire:click="$set('view', '{{ $val }}')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all
                       {{ $view === $val ? 'bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white shadow' : 'text-white/50 hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- My Stock Table --}}
    @if($view === 'my_stock')
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Product</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Category</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">SKU</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Quantity</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Stock Value</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($myStock as $stock)
                    <tr class="hover:bg-white/3 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-white">{{ $stock->product->name }}</td>
                        <td class="px-5 py-3.5 text-white/50 text-xs">{{ $stock->product->subCategory?->category?->name }}</td>
                        <td class="px-5 py-3.5 text-white/40 text-xs font-mono">{{ $stock->product->sku }}</td>
                        <td class="px-5 py-3.5 text-right font-bold
                            {{ $stock->quantity === 0 ? 'text-rose-400' : ($stock->quantity <= 10 ? 'text-amber-400' : 'text-white') }}">
                            {{ number_format($stock->quantity) }}
                        </td>
                        <td class="px-5 py-3.5 text-right text-white/70">₹{{ number_format($stock->quantity * $stock->product->unit_price, 0) }}</td>
                        <td class="px-5 py-3.5">
                            @if($stock->quantity === 0)
                                <span class="badge badge-danger">Out of Stock</span>
                            @elseif($stock->quantity <= 10)
                                <span class="badge badge-warning">Low Stock</span>
                            @else
                                <span class="badge badge-success">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-16 text-center text-white/30">No stock allocated yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Pending In --}}
    @if($view === 'pending_in')
    <div class="space-y-3">
        @forelse($pendingIn as $alloc)
        <div class="glass-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-semibold text-white">From: {{ $alloc->fromUser?->name ?? 'Warehouse' }}</p>
                    <p class="text-xs text-white/40 mt-0.5">Dispatched: {{ $alloc->dispatch_date?->format('d M Y') ?? '—' }}</p>
                </div>
                <a href="{{ route('stock.receive') }}" wire:navigate
                   class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-xs font-medium rounded-lg transition-colors">
                    Confirm Receipt
                </a>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($alloc->items as $item)
                <span class="text-xs bg-white/5 border border-white/10 px-2.5 py-1 rounded-full text-white/70">
                    {{ $item->product->name }} × {{ $item->quantity_sent }}
                </span>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-center text-white/30 py-12">No incoming stock</p>
        @endforelse
    </div>
    @endif

    {{-- Pending Out --}}
    @if($view === 'pending_out')
    <div class="space-y-3">
        @forelse($pendingOut as $alloc)
        <div class="glass-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-semibold text-white">To: {{ $alloc->toUser->name }}</p>
                    <p class="text-xs text-white/40 mt-0.5">{{ $alloc->toUser->role_label }} · {{ $alloc->dispatch_date?->format('d M Y') }}</p>
                </div>
                <span class="badge badge-warning">{{ ucfirst($alloc->status) }}</span>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($alloc->items as $item)
                <span class="text-xs bg-white/5 border border-white/10 px-2.5 py-1 rounded-full text-white/70">
                    {{ $item->product->name }} × {{ $item->quantity_sent }}
                </span>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-center text-white/30 py-12">No outgoing allocations</p>
        @endforelse
    </div>
    @endif
</div>
