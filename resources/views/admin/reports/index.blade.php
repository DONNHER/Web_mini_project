@extends('layouts.app')

@section('title', 'Reports')

@section('header')
    <div>
        <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Reports</h1>
    </div>
@endsection

@section('content')
<div class="space-y-16">
    <!-- Quick Favorites -->
    @if($favorites->count() > 0)
    <section>
        <h2 class="text-[10px] font-black mb-8 text-[#1A1A1A] uppercase tracking-[0.4em] opacity-40">Preset Configurations</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($favorites as $fav)
                <div class="card p-8 bg-[#1A1A1A] text-white border-none relative group overflow-hidden">
                    <div class="absolute top-0 right-0 p-4">
                        <span class="text-[#FF6B00] text-[8px] font-black uppercase tracking-widest border border-[#FF6B00]/30 px-2 py-1 rounded">PRESET</span>
                    </div>
                    <h3 class="text-xl font-black uppercase tracking-tighter mt-4">{{ $fav->name }}</h3>
                    <p class="text-[8px] text-white/40 mt-1 font-black uppercase tracking-[0.2em]">{{ str_replace('_', ' ', $fav->report_type) }}</p>

                    <form action="{{ route('admin.reports.generate') }}" method="POST" class="mt-8">
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
                        <button type="submit" class="w-full bg-[#FF6B00] text-white py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-[#1A1A1A] transition-all duration-300">
                            Execute Preset
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Custom Report Builder -->
    <section class="card p-12 border-dashed border-2 border-[#1A1A1A]/10 bg-transparent shadow-none">
        <h2 class="text-2xl font-black uppercase tracking-tighter mb-10 text-[#1A1A1A]">Advanced Analytics Builder</h2>

        <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Select Report Type -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-4">Intelligence Engine</label>
                    <select name="report_type" id="report_type" class="w-full bg-[#FEF6F0] border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-sm">
                        <option value="user_activity">User Activity Matrix</option>
                        <option value="transaction_summary">Asset Transaction Ledger</option>
                        <option value="audit_trail">Full Security Audit Trail</option>
                        <option value="system_usage">Core Utility Statistics</option>
                    </select>
                </div>

                <!-- Select Output Format -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-4">Export Protocol</label>
                    <select name="format" class="w-full bg-[#FEF6F0] border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-sm">
                        <option value="pdf">Secured PDF Document</option>
                        <option value="xlsx">Excel Data Matrix (.XLSX)</option>
                        <option value="csv">Raw Data Stream (.CSV)</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-4">Temporal Window</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="date_from" class="bg-[#FEF6F0] border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-[10px]">
                        <input type="date" name="date_to" class="bg-[#FEF6F0] border-none rounded-2xl px-8 py-4 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-[10px]">
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t border-[#1A1A1A]/5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex items-center space-x-10">
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="save_favorite" id="save_favorite" class="rounded-lg border-2 border-[#1A1A1A]/10 text-[#FF6B00] focus:ring-[#FF6B00] w-5 h-5 transition">
                            <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-[#1A1A1A]/60 group-hover:text-[#1A1A1A]">Save Preset</span>
                        </label>
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="email_me" id="email_me" class="rounded-lg border-2 border-[#1A1A1A]/10 text-[#FF6B00] focus:ring-[#FF6B00] w-5 h-5 transition">
                            <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-[#1A1A1A]/60 group-hover:text-[#1A1A1A]">Transmit via Email</span>
                        </label>
                    </div>
                    <div class="flex space-x-4">
                        <button type="button" onclick="window.print()" class="btn-secondary px-8 no-print">
                            Print Local
                        </button>
                        <button type="submit" class="btn-primary px-12 shadow-2xl shadow-[#FF6B00]/20">
                            Generate Intelligence
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
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
    }
</style>
@endsection
