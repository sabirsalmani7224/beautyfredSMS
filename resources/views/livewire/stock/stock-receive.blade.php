<div class="space-y-5 animate-fade-up">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Receive Stock</h2>
            <p class="text-white/40 text-sm mt-1">Confirm receipt of incoming stock allocations</p>
        </div>
        <a href="{{ route('stock.index') }}" wire:navigate class="text-white/40 hover:text-white text-sm">← Back to Stock</a>
    </div>

    @forelse($pendingAllocations as $alloc)
    <div class="glass-card p-5">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="font-semibold text-white">From: {{ $alloc->fromUser?->name ?? 'Warehouse' }}</p>
                <p class="text-xs text-white/40">{{ $alloc->fromUser?->role_label }} · Dispatched {{ $alloc->dispatch_date?->format('d M Y') }}</p>
            </div>
            <span class="badge badge-warning">{{ ucfirst($alloc->status) }}</span>
        </div>
        <div class="space-y-2 mb-4">
            @foreach($alloc->items as $item)
            <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                <span class="text-sm text-white">{{ $item->product->name }}</span>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-white/40">Sent: {{ $item->quantity_sent }}</span>
                    <input type="number" wire:model="receivedQtys.{{ $alloc->id }}.{{ $item->product_id }}"
                           placeholder="{{ $item->quantity_sent }}" min="0" max="{{ $item->quantity_sent }}"
                           class="w-20 bg-white/5 border border-white/10 rounded-lg px-2 py-1 text-sm text-white text-center focus:outline-none focus:border-[#C9A84C]/50">
                </div>
            </div>
            @endforeach
        </div>
        <button wire:click="confirmReceipt({{ $alloc->id }})"
                class="px-4 py-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-sm font-medium rounded-xl transition-colors">
            ✓ Confirm Receipt
        </button>
    </div>
    @empty
    <div class="text-center py-16 glass-card">
        <p class="text-4xl mb-3">📭</p>
        <p class="text-white/30 text-sm">No pending stock allocations</p>
    </div>
    @endforelse
</div>
