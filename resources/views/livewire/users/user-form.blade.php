<div class="max-w-2xl mx-auto space-y-5 animate-fade-up">

    <div>
        <h2 class="font-display text-2xl font-bold text-white">{{ $userId ? 'Edit Team Member' : 'Add Team Member' }}</h2>
        <p class="text-white/40 text-sm mt-1">{{ $userId ? 'Update details for this user' : 'Create a new user in your hierarchy' }}</p>
    </div>

    <div class="glass-card p-6 space-y-5">

        {{-- Basic Info --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="text-xs font-medium text-white/50 block mb-1.5">Full Name *</label>
                <input wire:model="name" type="text" placeholder="John Doe"
                       class="w-full bg-white/5 border {{ $errors->has('name') ? 'border-rose-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Email *</label>
                <input wire:model="email" type="email" placeholder="user@beautyfed.in"
                       class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-rose-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Phone</label>
                <input wire:model="phone" type="tel" placeholder="9XXXXXXXXX"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            @if(!$userId)
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Password *</label>
                <input wire:model="password" type="password" placeholder="Min 8 chars"
                       class="w-full bg-white/5 border {{ $errors->has('password') ? 'border-rose-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
                @error('password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endif
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Employee Code</label>
                <input wire:model="employeeCode" type="text" placeholder="EMP-001"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>

        <hr class="border-white/10">

        {{-- Role & Hierarchy --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Role *</label>
                <select wire:model.live="role"
                        class="w-full bg-white/5 border {{ $errors->has('role') ? 'border-rose-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="">— Select Role —</option>
                    @foreach($manageableRoles as $r)
                    <option value="{{ $r }}" class="bg-[#1A0510]">{{ \App\Models\User::make(['role' => $r])->getRoleLabel() }}</option>
                    @endforeach
                </select>
                @error('role') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Reports To (Manager)</label>
                <select wire:model="parentId" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="">— None —</option>
                    @foreach($potentialManagers as $manager)
                    <option value="{{ $manager->id }}" class="bg-[#1A0510]">
                        {{ $manager->name }} ({{ $manager->getRoleLabel() }})
                    </option>
                    @endforeach
                </select>
            </div>

            @if($role === 'ba_state_supervisor')
            <div class="col-span-2">
                <label class="text-xs font-medium text-[#C9A84C] block mb-1.5">⚠️ Also reports to ASM (for BA track)</label>
                <select wire:model="asmId" class="w-full bg-white/5 border border-[#C9A84C]/30 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="">— Select ASM —</option>
                    @foreach($potentialManagers->where('role', 'asm') as $asm)
                    <option value="{{ $asm->id }}" class="bg-[#1A0510]">{{ $asm->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(in_array($role, ['zsm','rsm','asm','tl','psm']))
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Zone</label>
                <select wire:model="zone" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="">— Select Zone —</option>
                    <option value="north">North</option>
                    <option value="south">South</option>
                    <option value="east">East</option>
                    <option value="west">West</option>
                </select>
            </div>
            @endif

            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">State</label>
                <input wire:model="state" type="text" placeholder="Maharashtra"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">City</label>
                <input wire:model="city" type="text" placeholder="Mumbai"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>

        <hr class="border-white/10">

        {{-- Profile Photo & Status --}}
        <div class="flex items-center gap-6">
            <div class="flex-1">
                <label class="text-xs font-medium text-white/50 block mb-1.5">Profile Photo</label>
                <input wire:model="profilePhoto" type="file" accept="image/*"
                       class="block w-full text-sm text-white/50 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#B76E79]/20 file:text-[#B76E79] hover:file:bg-[#B76E79]/30">
            </div>
            <div>
                <label class="text-xs font-medium text-white/50 block mb-1.5">Status</label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox"
                           class="w-5 h-5 rounded border-white/20 bg-white/5 text-[#C9A84C] focus:ring-[#C9A84C]/30">
                    <span class="text-sm text-white/70">Active</span>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-2">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50 shadow-lg">
                <span wire:loading.remove>{{ $userId ? 'Save Changes' : 'Create Member' }}</span>
                <span wire:loading>Saving...</span>
            </button>
            <a href="{{ route('users.index') }}" wire:navigate
               class="px-6 py-2.5 bg-white/5 border border-white/10 text-white/70 hover:text-white text-sm font-medium rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>
