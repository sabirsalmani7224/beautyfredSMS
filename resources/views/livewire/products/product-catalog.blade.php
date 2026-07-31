<div class="space-y-5 animate-fade-up">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Product Catalog</h2>
            <p class="text-white/40 text-sm mt-1">{{ $products->total() }} products across {{ $categories->count() }} categories</p>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isMD() || auth()->user()->isCEO() || auth()->user()->isNSM())
        <a href="{{ route('products.create') }}" wire:navigate
           class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="glass-card p-4 flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px] relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search products..."
                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
        </div>
        <div class="flex gap-2 flex-wrap">
            <button wire:click="$set('categoryId', 0)"
                    class="px-4 py-2 text-xs font-medium rounded-xl transition-all {{ $categoryId === 0 ? 'bg-[#C9A84C]/20 text-[#C9A84C] border border-[#C9A84C]/30' : 'bg-white/5 text-white/50 border border-white/10 hover:border-white/30' }}">
                All
            </button>
            @foreach($categories as $cat)
            <button wire:click="$set('categoryId', {{ $cat->id }})"
                    class="px-4 py-2 text-xs font-medium rounded-xl transition-all {{ $categoryId === $cat->id ? 'bg-[#C9A84C]/20 text-[#C9A84C] border border-[#C9A84C]/30' : 'bg-white/5 text-white/50 border border-white/10 hover:border-white/30' }}">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        @forelse($products as $product)
        <div class="glass-card p-4 flex flex-col hover:border-[#C9A84C]/30 border border-transparent transition-all duration-200 hover:-translate-y-0.5 group">
            <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-[#4A0E2E]/40 to-[#7B1D4C]/20 flex items-center justify-center mb-3 overflow-hidden">
                @if($product->image)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-xl">
                @else
                    <div class="text-center">
                        <span class="text-2xl">🧴</span>
                    </div>
                @endif
            </div>
            <p class="text-xs font-semibold text-white leading-snug flex-1 group-hover:text-[#C9A84C] transition-colors">{{ $product->name }}</p>
            <p class="text-[10px] text-white/30 mt-1">{{ $product->subCategory?->name }}</p>
            <div class="flex items-center justify-between mt-2.5">
                <span class="text-xs font-bold text-[#C9A84C]">
                    {{ $product->mrp > 0 ? '₹' . number_format($product->mrp, 0) : 'MRP TBD' }}
                </span>
                <span class="text-[10px] text-white/30 font-mono">{{ $product->sku }}</span>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isMD() || auth()->user()->isNSM())
            <div class="mt-2 flex gap-1">
                <a href="{{ route('products.edit', $product) }}" wire:navigate
                   class="flex-1 text-center py-1 text-[10px] bg-white/5 hover:bg-white/10 text-white/50 rounded-lg transition-colors">Edit</a>
                <button wire:click="toggleActive({{ $product->id }})"
                        class="flex-1 py-1 text-[10px] {{ $product->is_active ? 'bg-rose-500/10 text-rose-400' : 'bg-emerald-500/10 text-emerald-400' }} rounded-lg transition-colors">
                    {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full py-16 text-center text-white/30">
            <span class="text-4xl">🧴</span>
            <p class="text-sm mt-3">No products found</p>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="flex justify-center">{{ $products->links() }}</div>
    @endif
</div>
