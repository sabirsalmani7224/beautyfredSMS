<div class="space-y-5 animate-fade-up">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Target Management</h2>
            <p class="text-white/40 text-sm mt-1">Set monthly sales targets for your team</p>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="targetMonth" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
            <select wire:model.live="targetYear" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                @foreach([now()->year - 1, now()->year, now()->year + 1] as $y)
                <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <button wire:click="$toggle('showForm')"
                    class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-semibold rounded-xl hover:opacity-90 shadow-lg">
                {{ $showForm ? '✕ Cancel' : '+ Set Target' }}
            </button>
        </div>
    </div>

    {{-- Set Target Form --}}
    @if($showForm)
    <div class="glass-card p-5 border border-[#C9A84C]/20 space-y-4">
        <p class="text-xs font-semibold text-[#C9A84C] uppercase tracking-wider">New Target</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="text-xs text-white/50 block mb-1.5">Team Member *</label>
                <select wire:model="selectedUserId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="0">— Select —</option>
                    @foreach($directReports as $dr)
                    <option value="{{ $dr->id }}">{{ $dr->name }}</option>
                    @endforeach
                </select>
                @error('selectedUserId')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">Category (optional)</label>
                <select wire:model="selectedCatId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
                    <option value="0">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">Target Amount (₹) *</label>
                <input wire:model="targetAmount" type="number" min="0" step="1000"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            </div>
            <div>
                <label class="text-xs text-white/50 block mb-1.5">Target Qty (units)</label>
                <input wire:model="targetQty" type="number" min="0"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>
        <button wire:click="saveTarget" wire:loading.attr="disabled"
                class="px-6 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 shadow-lg disabled:opacity-50">
            <span wire:loading.remove>Save Target</span>
            <span wire:loading>Saving...</span>
        </button>
    </div>
    @endif

    {{-- Performance Table --}}
    <div class="glass-card overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10">
            <p class="text-sm font-semibold text-white/70">
                Team Performance —
                {{ \Carbon\Carbon::create()->month($targetMonth)->format('F') }} {{ $targetYear }}
            </p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Member</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Target (₹)</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Achieved (₹)</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">%</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($directRankings as $row)
                <tr class="hover:bg-white/3 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <img src="{{ $row['user']->avatar_url }}" class="w-7 h-7 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-white">{{ $row['user']->name }}</p>
                                <p class="text-xs text-white/40">{{ $row['user']->role_label }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-right text-white">₹{{ number_format($row['target_amount'], 0) }}</td>
                    <td class="px-5 py-3.5 text-right font-semibold text-white">₹{{ number_format($row['sales_amount'], 0) }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <span class="{{ $row['achievement_pct'] >= 100 ? 'text-emerald-400' : ($row['achievement_pct'] >= 75 ? 'text-amber-400' : 'text-rose-400') }} font-bold">
                            {{ $row['achievement_pct'] }}%
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="w-24 h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $row['achievement_pct'] >= 100 ? 'bg-emerald-500' : ($row['achievement_pct'] >= 75 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                 style="width:{{ min(100, $row['achievement_pct']) }}%"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-white/30">No direct reports</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
