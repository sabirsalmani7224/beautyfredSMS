<div class="max-w-3xl mx-auto space-y-5 animate-fade-up">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('sales.index') }}" class="text-white/40 hover:text-white text-sm inline-flex items-center gap-1 mb-2">
                ← Back to Sales
            </a>
            <h2 class="font-display text-2xl font-bold text-white">Sale #{{ $sale->id }}</h2>
            <p class="text-white/40 text-sm mt-1">{{ $sale->sale_date->format('l, d M Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($sale->status === 'pending' && !auth()->user()->isPSM())
            <button wire:click="reject" wire:confirm="Reject this sale?"
                    class="px-4 py-2 bg-rose-500/20 text-rose-400 text-sm font-medium rounded-xl hover:bg-rose-500/30 transition-colors">
                Reject
            </button>
            <button wire:click="approve"
                    class="px-4 py-2 bg-emerald-500/20 text-emerald-400 text-sm font-medium rounded-xl hover:bg-emerald-500/30 transition-colors">
                Approve
            </button>
            @endif
            @if($sale->status === 'approved')
                <span class="badge badge-success text-sm px-3 py-1.5">✓ Approved</span>
            @elseif($sale->status === 'rejected')
                <span class="badge badge-danger text-sm px-3 py-1.5">✗ Rejected</span>
            @else
                <span class="badge badge-warning text-sm px-3 py-1.5">⏳ Pending</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Sale Info --}}
        <div class="md:col-span-2 glass-card p-5 space-y-4">
            <p class="text-xs font-semibold text-white/40 uppercase tracking-wider">Sale Details</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-white/40">Recorded By</p>
                    <div class="flex items-center gap-2 mt-1">
                        <img src="{{ $sale->user->avatar_url }}" class="w-6 h-6 rounded-md">
                        <p class="text-sm font-medium text-white">{{ $sale->user->name }}</p>
                    </div>
                    <p class="text-xs text-white/30 mt-0.5">{{ $sale->user->role_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-white/40">Shop / Event</p>
                    <p class="text-sm font-medium text-white mt-1">
                        {{ $sale->sale_type === 'shop_sale' ? ($sale->shop?->name ?? '—') : ('🎪 ' . $sale->event_name) }}
                    </p>
                    @if($sale->shop?->city)
                    <p class="text-xs text-white/30 mt-0.5">{{ $sale->shop->city }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-white/40">Payment Mode</p>
                    <span class="badge badge-info mt-1">{{ strtoupper($sale->payment_mode) }}</span>
                    @if($sale->payment_reference)
                    <p class="text-xs text-white/30 mt-0.5 font-mono">Ref: {{ $sale->payment_reference }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-white/40">GPS Location</p>
                    @if($sale->gps_latitude)
                    <a href="https://maps.google.com/?q={{ $sale->gps_latitude }},{{ $sale->gps_longitude }}" target="_blank"
                       class="text-sky-400 text-xs mt-1 block hover:text-sky-300">
                        📍 View on Maps
                    </a>
                    @else
                    <p class="text-xs text-white/30 mt-1">Not captured</p>
                    @endif
                </div>
            </div>
            @if($sale->notes)
            <div class="pt-3 border-t border-white/10">
                <p class="text-xs text-white/40">Notes</p>
                <p class="text-sm text-white/70 mt-1">{{ $sale->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Photo Proof --}}
        <div class="glass-card p-5">
            <p class="text-xs font-semibold text-white/40 uppercase tracking-wider mb-3">Photo Proof</p>
            @if($sale->photo_url)
            <img src="{{ $sale->photo_url }}" alt="Sale proof" class="w-full rounded-xl object-cover aspect-square">
            <a href="{{ $sale->photo_url }}" target="_blank" class="text-xs text-[#C9A84C] block text-center mt-2 hover:text-[#E8C97A]">View Full Size</a>
            @else
            <div class="flex items-center justify-center h-32 rounded-xl bg-white/5 border border-dashed border-white/10">
                <p class="text-white/20 text-xs">No photo uploaded</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="glass-card overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10">
            <p class="text-sm font-semibold text-white/70">Products Sold ({{ $sale->items->count() }} items)</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left px-5 py-3 text-xs text-white/40 uppercase tracking-wider">Product</th>
                    <th class="text-right px-5 py-3 text-xs text-white/40 uppercase tracking-wider">Qty</th>
                    <th class="text-right px-5 py-3 text-xs text-white/40 uppercase tracking-wider">Unit Price</th>
                    <th class="text-right px-5 py-3 text-xs text-white/40 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($sale->items as $item)
                <tr>
                    <td class="px-5 py-3 text-white">
                        {{ $item->product->name }}
                        <span class="text-white/30 text-xs ml-2 font-mono">{{ $item->product->sku }}</span>
                    </td>
                    <td class="px-5 py-3 text-right text-white/70">{{ $item->quantity }}</td>
                    <td class="px-5 py-3 text-right text-white/70">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-white">₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-white/20">
                <tr>
                    <td colspan="3" class="px-5 py-4 text-right text-sm font-semibold text-white/60">Total Amount</td>
                    <td class="px-5 py-4 text-right text-lg font-bold text-white">₹{{ number_format($sale->net_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($sale->approved_at)
    <div class="glass-card px-5 py-3 flex items-center gap-2 text-xs text-white/40">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ ucfirst($sale->status) }} by {{ $sale->approvedBy?->name ?? 'System' }} on {{ $sale->approved_at->format('d M Y, h:i A') }}
    </div>
    @endif
</div>
