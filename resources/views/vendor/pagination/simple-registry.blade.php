@if ($paginator->hasPages())
    <nav class="flex items-center space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black/20 cursor-not-allowed">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black hover:bg-black hover:text-white transition no-underline">Previous</a>
        @endif

        {{-- Pagination Elements --}}
        <span class="px-3 py-1.5 text-[7px] font-black uppercase text-black/40">
            Node {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black hover:bg-black hover:text-white transition no-underline">Next</a>
        @else
            <span class="px-3 py-1.5 bg-[#FEF6F0] rounded-lg text-[7px] font-black uppercase text-black/20 cursor-not-allowed">Next</span>
        @endif
    </nav>
@endif
