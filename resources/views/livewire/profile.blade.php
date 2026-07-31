<div class="max-w-xl mx-auto animate-fade-up">
    <h2 class="font-display text-2xl font-bold text-white mb-6">My Profile</h2>

    <div class="glass-card p-6 space-y-5">
        <div class="flex items-center gap-4">
            <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-2xl ring-2 ring-[#C9A84C]/40">
            <div>
                <p class="text-xl font-bold text-white">{{ auth()->user()->name }}</p>
                <p class="text-[#C9A84C] text-sm font-medium">{{ auth()->user()->role_label }}</p>
                @if(auth()->user()->employee_code)
                <p class="text-white/40 text-xs font-mono mt-0.5">{{ auth()->user()->employee_code }}</p>
                @endif
            </div>
        </div>

        <hr class="border-white/10">

        <div class="space-y-3 text-sm">
            @foreach([
                'Email'         => auth()->user()->email,
                'Phone'         => auth()->user()->phone ?? '—',
                'Zone'          => auth()->user()->zone_label,
                'State'         => auth()->user()->state ?? '—',
                'City'          => auth()->user()->city ?? '—',
                'Reports To'    => auth()->user()->parent?->name ?? '—',
                'Last Login'    => auth()->user()->last_login_at?->diffForHumans() ?? 'Just now',
            ] as $label => $value)
            <div class="flex justify-between items-center py-2 border-b border-white/5 last:border-0">
                <span class="text-white/40">{{ $label }}</span>
                <span class="text-white font-medium">{{ $value }}</span>
            </div>
            @endforeach
        </div>

        <hr class="border-white/10">

        <div>
            <a href="{{ route('dashboard.main') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-white/70 hover:text-white text-sm rounded-xl transition-colors">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>
