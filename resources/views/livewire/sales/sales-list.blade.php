<div class="space-y-5 animate-fade-up">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Sales Records</h2>
            <p class="text-white/40 text-sm mt-1">{{ number_format($stats['approved']) }} approved · {{ $stats['pending'] }} pending</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.sales.csv') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border border-white/10 text-white/80 hover:text-white text-sm font-semibold rounded-xl hover:bg-white/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            @if(auth()->user()->isPSM() || auth()->user()->isBA() || auth()->user()->isTL())
            <a href="{{ route('sales.create') }}" wire:navigate
               class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-[#C9A84C]/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Sale
            </a>
            @endif
        </div>
    </div>

    {{-- Hierarchy Level Filters --}}
    @php
        $anyFilterActive = $selectedZsmId || $selectedRsmId || $selectedAsmId || $selectedTlId || $selectedPsmId
                        || $selectedBaNationalHeadId || $selectedBaStateSupervisorId || $selectedBaId;
        $showSalesTrack  = $hierarchyFilters['showZsm'] || $hierarchyFilters['showRsm'] || $hierarchyFilters['showAsm'] || $hierarchyFilters['showTl'] || $hierarchyFilters['showPsm'];
        $showBaTrack     = $hierarchyFilters['showBaNH'] || $hierarchyFilters['showBaSS'] || $hierarchyFilters['showBa'];
    @endphp
    @if($showSalesTrack || $showBaTrack)
    <div class="glass-card p-4 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-[#C9A84C] uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Hierarchy Filters
            </span>
            @if($anyFilterActive)
            <button wire:click="resetHierarchyFilters" type="button" class="text-xs text-rose-400 hover:text-rose-300 font-medium cursor-pointer">
                Reset All Filters
            </button>
            @endif
        </div>

        {{-- ── Sales Track ───────────────────────────────────────────── --}}
        @if($showSalesTrack)
        <div>
            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-2">Sales Track (PSM / TL / ASM / RSM / ZSM)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">

                @if($hierarchyFilters['showZsm'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">ZSM (Zonal)</label>
                    <select wire:model.live="selectedZsmId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="">All ZSMs</option>
                        @foreach($hierarchyFilters['zsmOptions'] as $zsm)
                        <option value="{{ $zsm->id }}">{{ $zsm->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($hierarchyFilters['showRsm'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">RSM (Regional)</label>
                    <select wire:model.live="selectedRsmId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="">All RSMs</option>
                        @foreach($hierarchyFilters['rsmOptions'] as $rsm)
                        <option value="{{ $rsm->id }}">{{ $rsm->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($hierarchyFilters['showAsm'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">ASM (Area)</label>
                    <select wire:model.live="selectedAsmId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="">All ASMs</option>
                        @foreach($hierarchyFilters['asmOptions'] as $asm)
                        <option value="{{ $asm->id }}">{{ $asm->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($hierarchyFilters['showTl'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">Team Leader (TL)</label>
                    <select wire:model.live="selectedTlId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="">All TLs</option>
                        @foreach($hierarchyFilters['tlOptions'] as $tl)
                        <option value="{{ $tl->id }}">{{ $tl->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($hierarchyFilters['showPsm'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">PSM (Sales Rep)</label>
                    <select wire:model.live="selectedPsmId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#C9A84C]/50">
                        <option value="">All PSMs</option>
                        @foreach($hierarchyFilters['psmOptions'] as $psm)
                        <option value="{{ $psm->id }}">{{ $psm->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

            </div>
        </div>
        @endif

        {{-- ── BA Track ──────────────────────────────────────────────── --}}
        @if($showBaTrack)
        @if($showSalesTrack)
        <div class="border-t border-white/10"></div>
        @endif
        <div>
            <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-2">BA Track (BA National Head / BA State Supervisor / Beauty Advisor)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">

                {{-- BA National Head (NSM + top management) --}}
                @if($hierarchyFilters['showBaNH'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">BA National Head</label>
                    <select wire:model.live="selectedBaNationalHeadId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-rose-400/50">
                        <option value="">All BA National Heads</option>
                        @foreach($hierarchyFilters['baNHOptions'] as $baNH)
                        <option value="{{ $baNH->id }}">{{ $baNH->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- BA State Supervisor (ASM and above + BANH) --}}
                @if($hierarchyFilters['showBaSS'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">BA State Supervisor</label>
                    <select wire:model.live="selectedBaStateSupervisorId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-rose-400/50">
                        <option value="">All BA Supervisors</option>
                        @foreach($hierarchyFilters['baSSOptions'] as $baSS)
                        <option value="{{ $baSS->id }}">{{ $baSS->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Individual BA (BASS + ASM and above) --}}
                @if($hierarchyFilters['showBa'])
                <div>
                    <label class="block text-[10px] font-semibold text-white/50 uppercase mb-1">Beauty Advisor (BA)</label>
                    <select wire:model.live="selectedBaId" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-rose-400/50">
                        <option value="">All BAs</option>
                        @foreach($hierarchyFilters['baOptions'] as $ba)
                        <option value="{{ $ba->id }}">{{ $ba->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- Filters --}}
    <div class="glass-card p-4 flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search by rep name or shop..."
                       class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
            </div>
        </div>
        <select wire:model.live="period" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month" selected>This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
        </select>
        <select wire:model.live="status" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <select wire:model.live="saleType" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            <option value="">All Types</option>
            <option value="shop_sale">Shop Sale</option>
            <option value="event_sale">Event / Stall</option>
        </select>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="glass-card px-4 py-3 text-center">
            <p class="text-xs text-white/40">Total (Approved)</p>
            <p class="text-lg font-bold text-white">₹{{ number_format($stats['total'], 0) }}</p>
        </div>
        <div class="glass-card px-4 py-3 text-center">
            <p class="text-xs text-white/40">Approved Orders</p>
            <p class="text-lg font-bold text-emerald-400">{{ $stats['approved'] }}</p>
        </div>
        <div class="glass-card px-4 py-3 text-center">
            <p class="text-xs text-white/40">Pending Review</p>
            <p class="text-lg font-bold text-amber-400">{{ $stats['pending'] }}</p>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Rep</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Shop / Event</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Products</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Payment</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-white/3 transition-colors">
                        <td class="px-5 py-3.5 text-white/70">{{ $sale->sale_date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <img src="{{ $sale->user->avatar_url }}" class="w-6 h-6 rounded-md">
                                <span class="text-white font-medium text-xs">{{ $sale->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-white/70 text-xs">
                            {{ $sale->sale_type === 'shop_sale' ? ($sale->shop?->name ?? '—') : ('🎪 ' . $sale->event_name) }}
                        </td>
                        <td class="px-5 py-3.5 text-white/50 text-xs">{{ $sale->items->count() }} item(s)</td>
                        <td class="px-5 py-3.5 text-right font-semibold text-white">₹{{ number_format($sale->net_amount, 0) }}</td>
                        <td class="px-5 py-3.5">
                            <span class="badge badge-info uppercase">{{ $sale->payment_mode }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($sale->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($sale->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('sales.show', $sale) }}" wire:navigate class="text-white/40 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($sale->status === 'pending' && !auth()->user()->isPSM())
                                <button wire:click="approve({{ $sale->id }})" class="text-emerald-400/60 hover:text-emerald-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button wire:click="reject({{ $sale->id }})" class="text-rose-400/60 hover:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center text-white/30">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            No sales found for this period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="px-5 py-4 border-t border-white/10">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
