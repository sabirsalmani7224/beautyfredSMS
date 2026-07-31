<div class="space-y-5 animate-fade-up">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Shop Network</h2>
            <p class="text-white/40 text-sm mt-1">Manage shops in your territory</p>
        </div>
        <button wire:click="newShop"
                class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Shop
        </button>
    </div>

    {{-- Inline Form --}}
    @if($showForm)
    <div class="glass-card p-5 border border-[#C9A84C]/20 space-y-4">
        <p class="text-xs font-semibold text-[#C9A84C] uppercase tracking-wider">{{ $shopId ? 'Edit Shop' : 'Register New Shop' }}</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="text-xs text-white/50 block mb-1.5">Shop Name *</label>
                <input wire:model="name" type="text" placeholder="Beauty Palace"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">Owner Name</label>
                <input wire:model="ownerName" type="text" placeholder="Rajesh Kumar"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">Phone</label>
                <input wire:model="phone" type="tel" placeholder="9XXXXXXXXX"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">City</label>
                <input wire:model="shopCity" type="text" placeholder="Mumbai"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">State</label>
                <input wire:model="state" type="text" placeholder="Maharashtra"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div class="md:col-span-3">
                <label class="text-xs text-white/50 block mb-1.5">Address</label>
                <input wire:model="address" type="text" placeholder="Shop No 5, Main Market Road"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>
        <div class="flex gap-3">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="px-5 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 disabled:opacity-50 shadow-lg">
                <span wire:loading.remove>{{ $shopId ? 'Update Shop' : 'Register Shop' }}</span>
                <span wire:loading>Saving...</span>
            </button>
            <button wire:click="cancelForm" class="px-5 py-2.5 bg-white/5 border border-white/10 text-white/70 text-sm rounded-xl hover:text-white transition-colors">Cancel</button>
        </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="glass-card p-4 flex gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search shops, owners..."
                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
        </div>
        <input wire:model.live.debounce.400ms="city" type="text" placeholder="Filter by city..."
               class="w-48 bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
    </div>

    {{-- Shops Table --}}
    <div class="glass-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Shop</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Owner</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Location</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Registered By</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Sales</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($shops as $shop)
                <tr class="hover:bg-white/3 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#4A0E2E] to-[#7B1D4C] flex items-center justify-center text-sm">🏪</div>
                            <div>
                                <p class="font-medium text-white">{{ $shop->name }}</p>
                                @if($shop->phone)<p class="text-xs text-white/40">{{ $shop->phone }}</p>@endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-white/60 text-sm">{{ $shop->owner_name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-white/60 text-sm">
                        {{ collect([$shop->city, $shop->state])->filter()->implode(', ') ?: '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-white/50 text-xs">{{ $shop->registeredBy?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <span class="text-sm font-semibold text-white">{{ $shop->sales_count }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <button wire:click="editShop({{ $shop->id }})" class="text-white/40 hover:text-[#C9A84C] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-14 text-center text-white/30">No shops found in your territory</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($shops->hasPages())
        <div class="px-5 py-4 border-t border-white/10">{{ $shops->links() }}</div>
        @endif
    </div>
</div>
