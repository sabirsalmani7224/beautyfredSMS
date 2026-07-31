<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    <meta name="description" content="BeautyFed Sales Management System — Nationwide sales tracking and analytics.">
    <link rel="icon" type="image/png" href="{{ asset('images/beautyfed-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/beautyfed-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --color-brand-deep:   #4A0E2E;
            --color-brand-mid:    #7B1D4C;
            --color-brand-light:  #B76E79;
            --color-gold:         #C9A84C;
            --color-gold-light:   #E8C97A;
            --color-cream:        #FFF8F0;
            --color-surface:      #1A1A2E;
            --color-surface-2:    #16213E;
            --color-surface-3:    #0F3460;
        }
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        /* Sidebar */
        .sidebar-link {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.5rem 0.875rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: rgba(255, 255, 255, 0.7) !important;
            white-space: nowrap !important;
            transition: all 0.2s ease-in-out !important;
            text-decoration: none !important;
        }
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }
        .sidebar-link svg {
            flex-shrink: 0 !important;
            width: 1.125rem !important;
            height: 1.125rem !important;
            display: inline-block !important;
        }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(201,168,76,0.25) 0%, rgba(183,110,121,0.12) 100%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-left: 3px solid #C9A84C !important;
        }

        /* Cards */
        .stat-card { @apply relative overflow-hidden rounded-2xl p-6 border border-white/10 backdrop-blur-sm; }
        .glass-card { @apply bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(185,110,121,0.4); border-radius: 10px; }

        /* Animations */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-up { animation: fadeInUp 0.4s ease forwards; }

        /* Badges */
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
        .badge-success { @apply bg-emerald-500/20 text-emerald-300; }
        .badge-warning { @apply bg-amber-500/20 text-amber-300; }
        .badge-danger  { @apply bg-rose-500/20 text-rose-300; }
        .badge-info    { @apply bg-sky-500/20 text-sky-300; }
        .badge-gold    { @apply bg-[#C9A84C]/20 text-[#E8C97A]; }
    </style>
</head>

<body class="h-full bg-[#0D0D1A] text-white antialiased">
<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ────────────────────────────────────────────────────── --}}
    <aside id="sidebar" class="w-64 flex-shrink-0 flex flex-col bg-gradient-to-b from-[#4A0E2E] via-[#3A0A22] to-[#1A0510] border-r border-white/10 transition-all duration-300 z-40">

        {{-- Logo --}}
        <div class="flex items-center justify-center px-4 py-4 border-b border-white/10">
            <img src="{{ asset('images/beautyfed-logo.png') }}" alt="BeautyFed" class="h-10 w-auto object-contain">
        </div>

        {{-- User Info --}}
        <div class="px-4 py-4 border-b border-white/10">
            <div class="flex items-center gap-3 bg-white/5 rounded-xl px-3 py-2.5">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                     class="w-8 h-8 rounded-lg object-cover ring-2 ring-[#C9A84C]/40">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-[#C9A84C] font-medium">{{ auth()->user()->role_label }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            {{-- Main --}}
            <a href="{{ route('dashboard.main') }}" class="sidebar-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            {{-- Sales --}}
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold text-[#C9A84C]/60 uppercase tracking-widest">Sales</p>
            </div>
            <a href="{{ route('sales.index') }}" class="sidebar-link {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>All Sales</span>
            </a>
            @if(auth()->user()->isPSM() || auth()->user()->isBA() || auth()->user()->isTL())
            <a href="{{ route('sales.create') }}" class="sidebar-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Record Sale</span>
            </a>
            @endif
            <a href="{{ route('shops.index') }}" class="sidebar-link {{ request()->routeIs('shops*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Shops</span>
            </a>

            {{-- Stock & Catalog --}}
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold text-[#C9A84C]/60 uppercase tracking-widest">Inventory</p>
            </div>
            <a href="{{ route('stock.index') }}" class="sidebar-link {{ request()->routeIs('stock*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Stock</span>
            </a>
            <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Products</span>
            </a>

            {{-- Performance --}}
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold text-[#C9A84C]/60 uppercase tracking-widest">Performance</p>
            </div>
            <a href="{{ route('targets.index') }}" class="sidebar-link {{ request()->routeIs('targets*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span>Targets</span>
            </a>

            {{-- Users Management --}}
            @if(!auth()->user()->isPSM() && !auth()->user()->isBA())
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold text-[#C9A84C]/60 uppercase tracking-widest">Management</p>
            </div>
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Team Members</span>
            </a>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full sidebar-link hover:bg-rose-500/20 hover:text-rose-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ──────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Bar --}}
        <header class="flex items-center justify-between px-6 py-4 bg-[#0D0D1A]/80 backdrop-blur-md border-b border-white/5">
            <div>
                <h1 class="text-lg font-semibold text-white">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-white/40">{{ now()->format('l, d M Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Zone badge for ZSM --}}
                @if(auth()->user()->zone)
                <span class="badge badge-gold">{{ auth()->user()->zone_label }} Zone</span>
                @endif
                {{-- Employee code --}}
                @if(auth()->user()->employee_code)
                <span class="text-xs text-white/40 font-mono">{{ auth()->user()->employee_code }}</span>
                @endif
                {{-- Avatar --}}
                <a href="{{ route('profile') }}">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Profile"
                         class="w-8 h-8 rounded-lg ring-2 ring-[#C9A84C]/40 hover:ring-[#C9A84C] transition-all">
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6 bg-[#0D0D1A]">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-rose-500/20 border border-rose-500/30 rounded-xl text-rose-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
<script>
    // Auto-hide flash messages after 4s
    setTimeout(() => {
        document.querySelectorAll('[class*="flash-msg"]').forEach(el => {
            el.style.opacity = '0';
            el.style.transition = 'opacity 0.5s';
        });
    }, 4000);
</script>
</body>
</html>
