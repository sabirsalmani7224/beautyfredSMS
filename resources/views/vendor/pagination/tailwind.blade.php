@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
    <div class="text-xs text-white/30">
        Showing <span class="font-semibold text-white/60">{{ $paginator->firstItem() }}</span>
        to <span class="font-semibold text-white/60">{{ $paginator->lastItem() }}</span>
        of <span class="font-semibold text-white/60">{{ $paginator->total() }}</span> results
    </div>

    <div class="flex items-center gap-1">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-xs text-white/20 bg-white/5 border border-white/5 rounded-lg cursor-not-allowed">← Prev</span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="px-3 py-1.5 text-xs text-white/60 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/30 rounded-lg transition-all">
                ← Prev
            </button>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1.5 text-xs text-white/30">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-[#C9A84C] to-[#B76E79] rounded-lg shadow-sm shadow-[#C9A84C]/20">
                            {{ $page }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-xs text-white/60 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/30 rounded-lg transition-all">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="px-3 py-1.5 text-xs text-white/60 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/30 rounded-lg transition-all">
                Next →
            </button>
        @else
            <span class="px-3 py-1.5 text-xs text-white/20 bg-white/5 border border-white/5 rounded-lg cursor-not-allowed">Next →</span>
        @endif

    </div>
</nav>
@endif
