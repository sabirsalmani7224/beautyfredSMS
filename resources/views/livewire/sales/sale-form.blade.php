<div class="max-w-4xl mx-auto space-y-5 animate-fade-up">

    <div>
        <h2 class="font-display text-2xl font-bold text-white">Record New Sale</h2>
        <p class="text-white/40 text-sm mt-1">Add products to cart, capture GPS location, and upload photo proof</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Left: Sale Details + Product Picker ──────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Sale Type --}}
            <div class="glass-card p-5">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">Sale Type</p>
                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="$set('saleType','shop_sale')"
                            class="py-3 rounded-xl border text-sm font-medium transition-all
                                   {{ $saleType === 'shop_sale' ? 'border-[#C9A84C] bg-[#C9A84C]/10 text-[#C9A84C]' : 'border-white/10 text-white/50 hover:border-white/30' }}">
                        🏪 Shop Sale
                    </button>
                    <button wire:click="$set('saleType','event_sale')"
                            class="py-3 rounded-xl border text-sm font-medium transition-all
                                   {{ $saleType === 'event_sale' ? 'border-[#C9A84C] bg-[#C9A84C]/10 text-[#C9A84C]' : 'border-white/10 text-white/50 hover:border-white/30' }}">
                        🎪 Event / Stall
                    </button>
                </div>
            </div>

            {{-- Shop / Event --}}
            <div class="glass-card p-5 space-y-4">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider">
                    {{ $saleType === 'shop_sale' ? 'Shop Details' : 'Event Details' }}
                </p>

                @if($saleType === 'shop_sale')
                <div class="relative">
                    <input wire:model.live.debounce.300ms="shopSearch" wire:keyup="searchShops"
                           type="text" placeholder="Search shop by name..."
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                    @if(count($shopResults) > 0)
                    <div class="absolute top-full left-0 right-0 mt-1 glass-card border border-white/10 rounded-xl overflow-hidden z-10">
                        @foreach($shopResults as $shop)
                        <button wire:click="selectShop({{ $shop['id'] }}, '{{ addslashes($shop['name']) }}')"
                                class="w-full text-left px-4 py-2.5 text-sm text-white hover:bg-white/10 border-b border-white/5 last:border-0">
                            {{ $shop['name'] }} <span class="text-white/40 text-xs">· {{ $shop['city'] }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                    @error('shopId') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @else
                <input wire:model="eventName" type="text" placeholder="Event / Stall name..."
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @error('eventName') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-white/50 block mb-1">Sale Date</label>
                        <input wire:model="saleDate" type="date"
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    </div>
                    <div>
                        <label class="text-xs text-white/50 block mb-1">Payment Mode</label>
                        <select wire:model="paymentMode" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="credit">Credit</option>
                            <option value="cheque">Cheque</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                </div>

                @if(in_array($paymentMode, ['upi','cheque']))
                <input wire:model="paymentRef" type="text" placeholder="Payment reference / cheque number..."
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @endif
            </div>

            {{-- Product Picker --}}
            <div class="glass-card p-5 space-y-4">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider">Add Products</p>
                <div class="flex gap-3">
                    <select wire:model="selectedProduct" class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="0">— Select product —</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ ($myStock[$product->id] ?? 0) === 0 ? 'disabled' : '' }}>
                            {{ $product->name }} · ₹{{ number_format($product->unit_price, 0) }}
                            (Stock: {{ $myStock[$product->id] ?? 0 }})
                        </option>
                        @endforeach
                    </select>
                    <input wire:model="selectedQty" type="number" min="1" placeholder="Qty"
                           class="w-20 bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white text-center focus:outline-none focus:border-[#C9A84C]/50">
                    <button wire:click="addToCart"
                            class="px-4 py-2.5 bg-[#C9A84C]/20 hover:bg-[#C9A84C]/30 text-[#C9A84C] rounded-xl text-sm font-medium transition-colors">
                        Add
                    </button>
                </div>
                @error('selectedProduct') <p class="text-rose-400 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- GPS + Photo --}}
            <div class="glass-card p-5 space-y-4">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider">Location & Proof</p>
                <button onclick="captureLocation()" type="button"
                        class="flex items-center gap-2 px-4 py-2.5 bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/20 text-sky-400 text-sm rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span id="gpsBtn">📍 Capture GPS Location</span>
                </button>
                <div>
                    <label class="text-xs text-white/50 block mb-1">Photo Proof</label>
                    <input wire:model="photoProof" type="file" accept="image/*"
                           class="block w-full text-sm text-white/50 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#B76E79]/20 file:text-[#B76E79] hover:file:bg-[#B76E79]/30">
                    @if($photoProof)
                    <p class="text-xs text-emerald-400 mt-1">✓ Photo captured</p>
                    @endif
                </div>
                <textarea wire:model="notes" rows="2" placeholder="Optional notes..."
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50 resize-none"></textarea>
            </div>
        </div>

        {{-- ── Right: Cart Summary ───────────────────────────────────────── --}}
        <div class="space-y-4">
            <div class="glass-card p-5 sticky top-6">
                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-4">Cart</p>

                @if(empty($cartItems))
                <div class="text-center py-8 text-white/20">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-sm">No items added</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($cartItems as $i => $item)
                    <div class="flex items-start gap-2 pb-3 border-b border-white/5 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-white leading-tight">{{ $item['name'] }}</p>
                            <p class="text-xs text-white/40 mt-0.5">{{ $item['qty'] }} × ₹{{ number_format($item['unit_price'], 0) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-white">₹{{ number_format($item['total_price'], 0) }}</p>
                            <button wire:click="removeFromCart({{ $i }})" class="text-rose-400/50 hover:text-rose-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-white/10">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-white/60">Total</span>
                        <span class="text-xl font-bold text-white">₹{{ number_format($this->totalAmount, 0) }}</span>
                    </div>
                </div>
                @endif

                @error('cartItems') <p class="text-rose-400 text-xs mt-2">{{ $message }}</p> @enderror

                <button wire:click="submit" wire:loading.attr="disabled"
                        class="mt-5 w-full py-3 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-[#C9A84C]/20">
                    <span wire:loading.remove>Submit Sale</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function captureLocation() {
    const btn = document.getElementById('gpsBtn');
    btn.textContent = '⏳ Getting location...';
    if (!navigator.geolocation) {
        btn.textContent = '❌ Not supported';
        return;
    }
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            @this.set('gpsLat', pos.coords.latitude);
            @this.set('gpsLng', pos.coords.longitude);
            btn.textContent = `✅ ${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}`;
        },
        () => { btn.textContent = '❌ Location denied'; }
    );
}
</script>
