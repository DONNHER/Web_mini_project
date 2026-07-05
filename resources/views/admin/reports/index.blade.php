@extends('layouts.app')

@section('title', 'Report Center - Admin')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Report Center</h1>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-12">
    <!-- Quick Favorites -->
    @if($favorites->count() > 0)
    <section>
        <h2 class="text-xs font-black mb-6 text-black uppercase tracking-[0.3em] opacity-40">Favorite Configurations</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($favorites as $fav)
                <div class="bg-black text-brand rounded-2xl p-6 border border-black shadow-lg relative group">
                    <h3 class="font-black uppercase tracking-tight">{{ $fav->name }}</h3>
                    <p class="text-[10px] opacity-60 mt-1 uppercase">{{ str_replace('_', ' ', $fav->report_type) }}</p>
                    <form action="{{ route('reports.generate') }}" method="POST" class="mt-4">
                        @csrf
                        @foreach($fav->filters as $key => $val)
                            @if(is_array($val))
                                @foreach($val as $k => $v) <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}"> @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="report_type" value="{{ $fav->report_type }}">
                        <input type="hidden" name="format" value="{{ $fav->format }}">
                        <button type="submit" class="w-full bg-white text-black py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-80 transition">Generate Now</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Custom Report Builder -->
    <section class="bg-black/5 rounded-3xl p-10 border border-black/5">
        <h2 class="text-xl font-black uppercase tracking-widest mb-8 text-black">Advanced Report Builder</h2>

        <form action="{{ route('reports.generate') }}" method="POST" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Select Report Type -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">Report Engine</label>
                    <select name="report_type" id="report_type" class="w-full bg-white/20 border-black/10 rounded-xl text-black font-bold p-3">
                        <option value="user_activity">User Activity Report</option>
                        <option value="transaction_summary">Transaction Summary</option>
                        <option value="audit_trail">Full Audit Trail</option>
                        <option value="system_usage">System Usage Stats</option>
                    </select>
                </div>

                <!-- Select Output Format -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">Output Format</label>
                    <select name="format" class="w-full bg-white/20 border-black/10 rounded-xl text-black font-bold p-3">
                        <option value="pdf">Official PDF Document</option>
                        <option value="xlsx">Excel Spreadsheet (.xlsx)</option>
                        <option value="csv">CSV Data (.csv)</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">Time Period</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="date_from" class="bg-white/20 border-black/10 rounded-xl text-black font-bold p-3 text-xs">
                        <input type="date" name="date_to" class="bg-white/20 border-black/10 rounded-xl text-black font-bold p-3 text-xs">
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-black/5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="save_favorite" id="save_favorite" class="rounded border-black/20 text-black focus:ring-black">
                            <label for="save_favorite" class="ml-3 text-[10px] font-black uppercase tracking-widest opacity-60 cursor-pointer">Save to Favorites</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="email_me" id="email_me" class="rounded border-black/20 text-black focus:ring-black">
                            <label for="email_me" class="ml-3 text-[10px] font-black uppercase tracking-widest opacity-60 cursor-pointer">Email copy to me</label>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <button type="button" onclick="window.print()" class="bg-black/5 text-black px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-black hover:text-brand transition no-print">
                            Print View
                        </button>
                        <button type="submit" class="bg-black text-brand px-12 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:opacity-80 transition shadow-xl">
                            Execute Generation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; color: black !important; }
    }
</style>
@endsection
