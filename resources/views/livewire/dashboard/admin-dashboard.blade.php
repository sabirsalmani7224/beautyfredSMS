<div class="space-y-6 animate-fade-up">

    {{-- ── Period Selector ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Sales Overview</h2>
            <p class="text-white/40 text-sm mt-1">{{ auth()->user()->role_label }} · {{ auth()->user()->zone_label !== '—' ? auth()->user()->zone_label . ' Zone' : 'All Zones' }}</p>
        </div>
        <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl p-1">
            @foreach(['today' => 'Today', 'week' => 'Week', 'month' => 'Month', 'quarter' => 'Quarter', 'year' => 'Year'] as $val => $label)
            <button wire:click="setPeriod('{{ $val }}')"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-200
                           {{ $period === $val ? 'bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white shadow' : 'text-white/50 hover:text-white' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ── Hierarchy Level Filters ─────────────────────────────────────────── --}}
    @if(isset($hierarchyFilters))
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
                Sales Hierarchy Filter
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
    @endif

    {{-- ── KPI Cards ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Sales --}}
        <div class="stat-card bg-gradient-to-br from-[#4A0E2E]/60 to-[#7B1D4C]/30">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-white/50 font-medium uppercase tracking-wider">Total Sales</p>
                    <p class="text-2xl font-bold text-white mt-1">₹{{ number_format($totalSales, 0) }}</p>
                    <p class="text-xs text-white/40 mt-1">{{ $salesCount }} orders</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-[#C9A84C]/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Target Achievement --}}
        <div class="stat-card bg-gradient-to-br from-emerald-900/40 to-emerald-700/20">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-white/50 font-medium uppercase tracking-wider">Achievement</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $achievement }}%</p>
                    <p class="text-xs text-white/40 mt-1">Target ₹{{ number_format($monthTarget, 0) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-700"
                         style="width: {{ min($achievement, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Avg Sale --}}
        <div class="stat-card bg-gradient-to-br from-sky-900/40 to-sky-700/20">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-white/50 font-medium uppercase tracking-wider">Avg Order</p>
                    <p class="text-2xl font-bold text-white mt-1">₹{{ number_format($avgSale, 0) }}</p>
                    <p class="text-xs text-white/40 mt-1">Per transaction</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>

        {{-- Pending Approvals --}}
        <div class="stat-card bg-gradient-to-br from-amber-900/40 to-amber-700/20">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-white/50 font-medium uppercase tracking-wider">Pending</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $pendingSales }}</p>
                    <p class="text-xs text-white/40 mt-1">Awaiting approval</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            @if($pendingSales > 0)
            <a href="{{ route('sales.index') }}" class="mt-3 block text-xs text-amber-400 hover:text-amber-300">Review now →</a>
            @endif
        </div>
    </div>

    {{-- ── Charts Row ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4"
         x-data="{
             renderCharts() {
                 const months = {{ json_encode($monthlyTrend->pluck('month')) }};
                 const totals = {{ json_encode($monthlyTrend->pluck('total')) }};
                 const catNames = {{ json_encode($salesByCategory->pluck('name')) }};
                 const catVals = {{ json_encode($salesByCategory->pluck('total')) }};

                 const existingTrend = Chart.getChart(this.$refs.trendCanvas);
                 if (existingTrend) existingTrend.destroy();

                 const existingCat = Chart.getChart(this.$refs.categoryCanvas);
                 if (existingCat) existingCat.destroy();

                 if (this.$refs.trendCanvas) {
                     new Chart(this.$refs.trendCanvas, {
                         type: 'line',
                         data: {
                             labels: months,
                             datasets: [{
                                 data: totals,
                                 borderColor: '#C9A84C',
                                 backgroundColor: 'rgba(201,168,76,0.1)',
                                 borderWidth: 2,
                                 pointBackgroundColor: '#C9A84C',
                                 pointRadius: 4,
                                 fill: true,
                                 tension: 0.4,
                             }]
                         },
                         options: {
                             responsive: true,
                             plugins: { legend: { display: false } },
                             scales: {
                                 x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)', font: { size: 11 } } },
                                 y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)', font: { size: 11 }, callback: v => '₹' + (v/1000).toFixed(0) + 'k' } },
                             }
                         }
                     });
                 }

                 if (this.$refs.categoryCanvas && catNames.length > 0) {
                     new Chart(this.$refs.categoryCanvas, {
                         type: 'doughnut',
                         data: {
                             labels: catNames,
                             datasets: [{
                                 data: catVals,
                                 backgroundColor: ['#C9A84C','#B76E79','#7B1D4C','#4A8FA8','#5B7E4A'],
                                 borderWidth: 0,
                             }]
                         },
                         options: {
                             responsive: true,
                             cutout: '65%',
                             plugins: {
                                 legend: { position: 'bottom', labels: { color: 'rgba(255,255,255,0.5)', font: { size: 11 }, boxWidth: 10, padding: 12 } }
                             }
                         }
                     });
                 }
             }
         }"
         x-init="renderCharts()"
         x-effect="renderCharts()">

        <div class="lg:col-span-2 glass-card p-5">
            <h3 class="text-sm font-semibold text-white/80 mb-4">
                Sales Trend — {{ match($period) {
                    'today'   => 'Today (Hourly)',
                    'week'    => 'This Week (Daily)',
                    'month'   => 'This Month (Daily)',
                    'quarter' => 'This Quarter (Monthly)',
                    'year'    => 'This Year (Monthly)',
                    default   => 'Overview'
                } }}
            </h3>
            <canvas x-ref="trendCanvas" height="180"></canvas>
        </div>

        {{-- Category Breakdown --}}
        <div class="glass-card p-5">
            <h3 class="text-sm font-semibold text-white/80 mb-4">By Category</h3>
            @if($salesByCategory->isEmpty())
                <p class="text-white/30 text-sm text-center py-8">No sales data</p>
            @else
                <canvas x-ref="categoryCanvas" height="180"></canvas>
            @endif
        </div>
    </div>

    {{-- ── Top Performers & Team Stats ─────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Top Performers Table --}}
        <div class="lg:col-span-2 glass-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white/80">Top Performers</h3>
                <a href="{{ route('users.index') }}" class="text-xs text-[#C9A84C] hover:text-[#E8C97A]">View all →</a>
            </div>
            <div class="space-y-3">
                @forelse($topPerformers as $i => $perf)
                <div class="flex items-center gap-3 py-2.5 border-b border-white/5 last:border-0">
                    <span class="text-xs font-bold text-white/30 w-5 text-center">{{ $i + 1 }}</span>
                    <img src="{{ $perf['user']->avatar_url }}" class="w-8 h-8 rounded-lg object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $perf['user']->name }}</p>
                        <p class="text-xs text-white/40">{{ $perf['user']->role_label }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-white">₹{{ number_format($perf['sales_amount'], 0) }}</p>
                        <p class="text-xs {{ $perf['achievement_pct'] >= 100 ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $perf['achievement_pct'] }}%
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-white/30 text-sm text-center py-8">No team data available</p>
                @endforelse
            </div>
        </div>

        {{-- Team Stats --}}
        <div class="space-y-4">
            <div class="glass-card p-5">
                <p class="text-xs text-white/50 uppercase tracking-wider">Team Size</p>
                <p class="text-3xl font-bold text-white mt-1">{{ number_format($totalTeamSize) }}</p>
                <p class="text-xs text-white/40 mt-1">Total members in downline</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-xs text-white/50 uppercase tracking-wider">Active Today</p>
                <p class="text-3xl font-bold text-emerald-400 mt-1">{{ $activeToday }}</p>
                <p class="text-xs text-white/40 mt-1">Members who recorded sales</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-xs text-white/50 uppercase tracking-wider">Quick Actions</p>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('sales.index') }}"
                       class="block px-3 py-2 rounded-lg bg-[#C9A84C]/10 hover:bg-[#C9A84C]/20 text-[#C9A84C] text-xs font-medium transition-colors">
                        📋 Review Sales
                    </a>
                    <a href="{{ route('users.create') }}"
                       class="block px-3 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white/70 text-xs font-medium transition-colors">
                        👤 Add Team Member
                    </a>
                    <a href="{{ route('stock.allocate') }}"
                       class="block px-3 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white/70 text-xs font-medium transition-colors">
                        📦 Allocate Stock
                    </a>
                </div>
            </div>
        </div>
</div>
