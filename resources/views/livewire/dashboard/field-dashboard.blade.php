<div class="space-y-5 animate-fade-up">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card bg-gradient-to-br from-[#4A0E2E]/60 to-[#7B1D4C]/30">
            <p class="text-xs text-white/50 uppercase tracking-wider">Today's Sales</p>
            <p class="text-2xl font-bold text-white mt-1">₹{{ number_format($todaySales, 0) }}</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-emerald-900/40 to-emerald-700/20">
            <p class="text-xs text-white/50 uppercase tracking-wider">This Month</p>
            <p class="text-2xl font-bold text-white mt-1">₹{{ number_format($monthSales, 0) }}</p>
            <div class="mt-2 h-1.5 bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width:{{ min($achievement,100) }}%"></div>
            </div>
            <p class="text-xs text-white/40 mt-1">{{ $achievement }}% of ₹{{ number_format($monthTarget, 0) }} target</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-amber-900/40 to-amber-700/20">
            <p class="text-xs text-white/50 uppercase tracking-wider">Pending</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ $pendingCount }}</p>
            <p class="text-xs text-white/40 mt-1">Awaiting approval</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-sky-900/40 to-sky-700/20">
            <p class="text-xs text-white/50 uppercase tracking-wider">Stock Items</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $myStock->count() }}</p>
            <p class="text-xs text-white/40 mt-1">Products in hand</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Quick Actions --}}
        <div class="glass-card p-5">
            <p class="text-sm font-semibold text-white/70 mb-4">Quick Actions</p>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('sales.create') }}" wire:navigate
                   class="flex flex-col items-center gap-2 py-5 bg-gradient-to-br from-[#C9A84C]/20 to-[#B76E79]/10 border border-[#C9A84C]/20 rounded-xl hover:border-[#C9A84C]/40 transition-all">
                    <span class="text-2xl">💰</span>
                    <span class="text-xs font-semibold text-white">Record Sale</span>
                </a>
                <a href="{{ route('stock.index') }}" wire:navigate
                   class="flex flex-col items-center gap-2 py-5 bg-white/5 border border-white/10 rounded-xl hover:border-white/30 transition-all">
                    <span class="text-2xl">📦</span>
                    <span class="text-xs font-semibold text-white/70">My Stock</span>
                </a>
                <a href="{{ route('sales.index') }}" wire:navigate
                   class="flex flex-col items-center gap-2 py-5 bg-white/5 border border-white/10 rounded-xl hover:border-white/30 transition-all">
                    <span class="text-2xl">📋</span>
                    <span class="text-xs font-semibold text-white/70">Sales History</span>
                </a>
                <a href="{{ route('products.index') }}" wire:navigate
                   class="flex flex-col items-center gap-2 py-5 bg-white/5 border border-white/10 rounded-xl hover:border-white/30 transition-all">
                    <span class="text-2xl">🧴</span>
                    <span class="text-xs font-semibold text-white/70">Products</span>
                </a>
            </div>
        </div>

        {{-- My Stock --}}
        <div class="glass-card p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-white/70">My Stock</p>
                <a href="{{ route('stock.index') }}" class="text-xs text-[#C9A84C]">View all →</a>
            </div>
            <div class="space-y-2.5">
                @forelse($myStock as $stock)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-white/70 flex-1 truncate">{{ $stock->product->name }}</span>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-20 h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $stock->quantity <= 10 ? 'bg-amber-500' : 'bg-emerald-500' }}"
                                 style="width: {{ min(100, ($stock->quantity / 100) * 100) }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-white w-8 text-right">{{ $stock->quantity }}</span>
                    </div>
                </div>
                @empty
                <p class="text-white/30 text-sm text-center py-4">No stock in hand</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Sales --}}
    <div class="glass-card p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-white/70">Recent Sales</p>
            <a href="{{ route('sales.index') }}" class="text-xs text-[#C9A84C]">View all →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentSales as $sale)
            <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                <div>
                    <p class="text-sm text-white font-medium">
                        {{ $sale->sale_type === 'shop_sale' ? ($sale->shop?->name ?? 'Shop') : ('🎪 ' . $sale->event_name) }}
                    </p>
                    <p class="text-xs text-white/40">{{ $sale->sale_date->format('d M Y') }} · {{ $sale->items->count() }} items</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-white">₹{{ number_format($sale->net_amount, 0) }}</span>
                    @if($sale->status === 'approved')
                        <span class="badge badge-success">✓</span>
                    @elseif($sale->status === 'rejected')
                        <span class="badge badge-danger">✗</span>
                    @else
                        <span class="badge badge-warning">…</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-white/30 text-sm text-center py-6">No sales recorded yet</p>
            @endforelse
        </div>
    </div>
</div>
