@if ($paginator->hasPages())
<nav class="flex items-center gap-2">
    @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 text-xs text-white/20 bg-white/5 border border-white/5 rounded-lg cursor-not-allowed">← Prev</span>
    @else
        <button wire:click="previousPage" class="px-3 py-1.5 text-xs text-white/60 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg transition-all">← Prev</button>
    @endif

    @if ($paginator->hasMorePages())
        <button wire:click="nextPage" class="px-3 py-1.5 text-xs text-white/60 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg transition-all">Next →</button>
    @else
        <span class="px-3 py-1.5 text-xs text-white/20 bg-white/5 border border-white/5 rounded-lg cursor-not-allowed">Next →</span>
    @endif
</nav>
@endif
