<div class="max-w-3xl mx-auto space-y-5 animate-fade-up">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Allocate Stock</h2>
            <p class="text-white/40 text-sm mt-1">Send stock to your direct team members</p>
        </div>
        <a href="{{ route('stock.index') }}" wire:navigate class="text-white/40 hover:text-white text-sm">← Back to Stock</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-4">
            <div class="glass-card p-5 space-y-4">
                <p class="text-xs font-semibold text-white/40 uppercase tracking-wider">Send To</p>
                <select wire:model="toUserId" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="0">— Select Team Member —</option>
                    @foreach($directReports as $member)
                    <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->getRoleLabel() }})</option>
                    @endforeach
                </select>
                @error('toUserId')<p class="text-rose-400 text-xs">{{ $message }}</p>@enderror
            </div>

            <div class="glass-card p-5 space-y-4">
                <p class="text-xs font-semibold text-white/40 uppercase tracking-wider">Add Products</p>
                <div class="flex gap-3">
                    <select wire:model="productId" class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="0">— Select Product —</option>
                        @foreach($myStock as $s)
                        <option value="{{ $s->product_id }}">{{ $s->product->name }} ({{ $s->quantity }} avail.)</option>
                        @endforeach
                    </select>
                    <input wire:model="quantity" type="number" min="1" placeholder="Qty"
                           class="w-20 bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white text-center focus:outline-none focus:border-[#C9A84C]/50">
                    <button wire:click="addItem" class="px-4 py-2.5 bg-[#C9A84C]/20 hover:bg-[#C9A84C]/30 text-[#C9A84C] rounded-xl text-sm font-medium">Add</button>
                </div>
                @error('productId')<p class="text-rose-400 text-xs">{{ $message }}</p>@enderror

                <textarea wire:model="notes" rows="2" placeholder="Optional notes..."
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50 resize-none"></textarea>
            </div>
        </div>

        <div class="glass-card p-5 sticky top-6">
            <p class="text-xs font-semibold text-white/40 uppercase tracking-wider mb-4">Items to Send</p>
            @if(empty($items))
            <p class="text-white/20 text-sm text-center py-8">No items added</p>
            @else
            <div class="space-y-3">
                @foreach($items as $i => $item)
                <div class="flex items-center justify-between pb-2 border-b border-white/5 last:border-0">
                    <div>
                        <p class="text-xs font-medium text-white">{{ $item['name'] }}</p>
                        <p class="text-xs text-white/40">Qty: {{ $item['quantity'] }}</p>
                    </div>
                    <button wire:click="removeItem({{ $i }})" class="text-rose-400/50 hover:text-rose-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
            @endif
            @error('items')<p class="text-rose-400 text-xs mt-2">{{ $message }}</p>@enderror
            <button wire:click="send" wire:loading.attr="disabled"
                    class="mt-4 w-full py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 disabled:opacity-50 shadow-lg">
                <span wire:loading.remove>📤 Dispatch Stock</span>
                <span wire:loading>Sending...</span>
            </button>
        </div>
    </div>
</div>
