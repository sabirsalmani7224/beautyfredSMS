<div class="max-w-2xl mx-auto space-y-5 animate-fade-up">
    <div>
        <h2 class="font-display text-2xl font-bold text-white">{{ $productId ? 'Edit Product' : 'Add Product' }}</h2>
        <p class="text-white/40 text-sm mt-1">{{ $productId ? 'Update product details and pricing' : 'Add a new product to the catalog' }}</p>
    </div>

    <div class="glass-card p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="text-xs font-medium text-white/50 block mb-1.5">Category / Sub-Category *</label>
                <select wire:model="subCategoryId" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="0">— Select Sub-Category —</option>
                    @foreach($categories as $cat)
                    <optgroup label="{{ $cat->name }}" class="bg-[#1A0510]">
                        @foreach($cat->subCategories as $sub)
                        <option value="{{ $sub->id }}" class="bg-[#1A0510]">{{ $sub->name }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                @error('subCategoryId') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2">
                <label class="text-xs font-medium text-white/50 block mb-1.5">Product Name *</label>
                <input wire:model="name" type="text" placeholder="Aloe Vera Face Gel"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">SKU Code</label>
                <input wire:model="sku" type="text" placeholder="BF-SK-0001"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white font-mono placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">HSN Code</label>
                <input wire:model="hsnCode" type="text" placeholder="330499"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white font-mono placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>

        <hr class="border-white/10">

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">MRP (₹) *</label>
                <input wire:model="mrp" type="number" step="0.01" min="0"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                @error('mrp') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Unit Price (₹) *</label>
                <input wire:model="unitPrice" type="number" step="0.01" min="0"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Distributor Price (₹)</label>
                <input wire:model="distributorPrice" type="number" step="0.01" min="0"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>

        <div>
            <label class="text-xs font-medium text-white/50 block mb-1.5">Description</label>
            <textarea wire:model="description" rows="3" placeholder="Product description..."
                      class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50 resize-none"></textarea>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex-1">
                <label class="text-xs font-medium text-white/50 block mb-1.5">Product Image</label>
                <input wire:model="productImage" type="file" accept="image/*"
                       class="block w-full text-sm text-white/50 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#B76E79]/20 file:text-[#B76E79] hover:file:bg-[#B76E79]/30">
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Active</label>
                <input wire:model="isActive" type="checkbox" class="w-5 h-5 rounded border-white/20 bg-white/5 text-[#C9A84C]">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 disabled:opacity-50 shadow-lg">
                <span wire:loading.remove>{{ $productId ? 'Save Changes' : 'Add Product' }}</span>
                <span wire:loading>Saving...</span>
            </button>
            <a href="{{ route('products.index') }}" wire:navigate
               class="px-6 py-2.5 bg-white/5 border border-white/10 text-white/70 hover:text-white text-sm font-medium rounded-xl transition-colors">Cancel</a>
        </div>
    </div>
</div>
