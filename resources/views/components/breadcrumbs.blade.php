@props(['links' => []])

<nav class="flex mb-6 overflow-x-auto no-scrollbar" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-black/40 hover:text-black no-underline">
                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                System
            </a>
        </li>
        @foreach($links as $label => $url)
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-black/20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    @if($loop->last)
                        <span class="ml-1 text-[10px] font-black uppercase tracking-widest text-black cursor-default">{{ $label }}</span>
                    @else
                        <a href="{{ $url }}" class="ml-1 text-[10px] font-black uppercase tracking-widest text-black/40 hover:text-black no-underline transition">{{ $label }}</a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
