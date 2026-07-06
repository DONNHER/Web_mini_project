@extends('layouts.app')

@section('title', 'AI Risk Assessment Dashboard')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">Neural Defense</span>
            <h1 class="text-5xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Security <span class="text-[#FF6B00]">Matrix</span></h1>
        </div>
        <a href="{{ route('admin.ai-security.usage') }}" class="btn-secondary px-6 no-underline">
            AI Utility & Costs
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-12">
    <!-- AI Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="card p-8 border-red-500/20 bg-red-500/5 shadow-none">
            <p class="text-[10px] font-black uppercase tracking-widest text-red-600/40">Flagged Anomalies</p>
            <p class="text-4xl font-black text-red-600 tracking-tighter mt-2">{{ $flaggedLoans->count() }}</p>
            <p class="text-[8px] font-black uppercase tracking-widest text-red-600/20 mt-2 italic">Awaiting Credit Review</p>
        </div>
        <div class="card p-8">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest">Total Assessments</p>
            <p class="text-4xl font-black text-[#1A1A1A] tracking-tighter mt-2">{{ $logs->total() }}</p>
            <p class="text-[8px] text-[#1A1A1A]/20 font-black uppercase tracking-widest mt-2 italic">Last 30 Cycles</p>
        </div>
        <div class="card p-8">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest">Mean Risk Index</p>
            <p class="text-4xl font-black text-[#FF6B00] tracking-tighter mt-2">{{ number_format($logs->avg('risk_score'), 1) }}%</p>
            <p class="text-[8px] text-[#1A1A1A]/20 font-black uppercase tracking-widest mt-2 italic">Behavioral Probability</p>
        </div>
    </div>

    <!-- Flagged Loans Queue -->
    <div class="card overflow-hidden">
        <div class="p-8 border-b border-[#1A1A1A]/5 flex justify-between items-center bg-[#FEF6F0]/50">
            <div>
                <h3 class="text-xl font-black text-[#1A1A1A] uppercase tracking-tighter">Manual Review Queue</h3>
                <p class="text-[8px] font-black uppercase tracking-widest text-[#1A1A1A]/30 mt-1 italic">High risk index detected by neural logic</p>
            </div>
            <div class="flex items-center space-x-6">
                <form action="{{ route('admin.ai-security.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] font-black text-[#1A1A1A]/40 uppercase tracking-widest hover:text-[#FF6B00] transition no-underline">
                        Sync Matrix Status
                    </button>
                </form>
                <span class="px-4 py-2 bg-red-600 text-white text-[8px] font-black uppercase tracking-widest rounded-full animate-pulse shadow-lg shadow-red-600/40">
                    Action Required
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#1A1A1A] text-white uppercase text-[10px] font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Asset ID</th>
                        <th class="px-6 py-6">Identity</th>
                        <th class="px-6 py-6">Capital</th>
                        <th class="px-6 py-6">Neural Logic</th>
                        <th class="px-10 py-6 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1A1A1A]/5">
                    @forelse($flaggedLoans as $loan)
                        @php
                            $latestLog = \App\Models\AISecurityLog::where('resource_id', $loan->id)
                                ->where('resource_type', 'Loan')
                                ->latest()
                                ->first();
                        @endphp
                        <tr class="group hover:bg-[#FEF6F0] transition-colors">
                            <td class="px-10 py-6 text-xs font-black text-[#FF6B00]">#{{ $loan->id }}</td>
                            <td class="px-6 py-6">
                                <span class="text-sm font-black text-[#1A1A1A]">{{ $loan->user->name }}</span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="text-xs font-bold text-[#1A1A1A]">₱{{ number_format($loan->principal_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-6 max-w-sm">
                                <p class="text-[10px] font-bold text-[#1A1A1A]/60 italic leading-relaxed">
                                    "{{ $latestLog->reason ?? 'No AI reason provided.' }}"
                                </p>
                            </td>
                            <td class="px-10 py-6 text-right space-x-3">
                                <form action="{{ route('admin.ai-security.rescan', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-[10px] font-black text-[#1A1A1A]/40 uppercase tracking-widest hover:text-[#FF6B00] transition">Rescan</button>
                                </form>
                                <form action="{{ route('admin.ai-security.resolve', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="text-[10px] font-black text-green-600 uppercase tracking-widest hover:opacity-60 transition">Clear</button>
                                </form>
                                <form action="{{ route('admin.ai-security.resolve', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="text-[10px] font-black text-red-600 uppercase tracking-widest hover:opacity-60 transition">Purge</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-20 text-center">
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20 italic">No high-risk anomalies identified. Matrix is secure.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Scan History -->
    <div class="card overflow-hidden">
        <div class="p-8 border-b border-[#1A1A1A]/5">
            <h3 class="text-xl font-black text-[#1A1A1A] uppercase tracking-tighter">Assessment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#1A1A1A] text-white uppercase text-[10px] font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Timeline</th>
                        <th class="px-6 py-6">Provider</th>
                        <th class="px-6 py-6">Target Node</th>
                        <th class="px-6 py-6">Risk Index</th>
                        <th class="px-6 py-6">Latency</th>
                        <th class="px-10 py-6 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1A1A1A]/5">
                    @foreach($logs as $log)
                    <tr class="hover:bg-[#FEF6F0] transition-colors">
                        <td class="px-10 py-6 text-xs font-bold text-[#1A1A1A]/40 uppercase tracking-widest">
                            {{ $log->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-3 py-1 bg-[#1A1A1A] text-white text-[8px] font-black uppercase tracking-widest rounded-full">
                                {{ $log->provider }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-xs font-black text-[#1A1A1A]">
                            {{ $log->resource_type }} #{{ $log->resource_id }}
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex items-center">
                                <div class="w-24 bg-[#1A1A1A]/5 rounded-full h-1.5 mr-3 overflow-hidden">
                                    <div class="h-full @if($log->risk_score > 70) bg-red-600 @elseif($log->risk_score > 30) bg-[#FF6B00] @else bg-green-500 @endif transition-all duration-1000" style="width: {{ $log->risk_score }}%"></div>
                                </div>
                                <span class="text-[10px] font-black @if($log->risk_score > 70) text-red-600 @elseif($log->risk_score > 30) text-[#FF6B00] @else text-green-600 @endif">
                                    {{ $log->risk_score }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-[10px] font-bold text-[#1A1A1A]/30 uppercase tracking-widest">
                            {{ number_format($log->response_time_ms, 0) }}ms
                        </td>
                        <td class="px-10 py-6 text-right space-x-2">
                            <a href="{{ route('admin.ai-security.show-log', $log) }}" class="text-[#1A1A1A] font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-[#1A1A1A] hover:text-[#FF6B00] hover:border-[#FF6B00] transition">
                                Details
                            </a>
                            <form action="{{ route('admin.ai-security.destroy-log', $log) }}" method="POST" class="inline" onsubmit="return confirm('Delete this log?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[#1A1A1A]/20 hover:text-red-600 transition ml-2">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-10 py-6 border-t border-[#1A1A1A]/5">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- AI Generation Disclaimer -->
    <div class="flex items-center justify-center space-x-3 text-[#1A1A1A]/20 text-[8px] font-black uppercase tracking-[0.2em] py-8">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Neural decisions are augmented for speed. Identity review is recommended for high risk nodes.</span>
    </div>
</div>

<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button');
            if(btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="animate-spin inline-block mr-2">↻</span> LOGGING...';
            }
        });
    });
</script>
@endsection
@endsection
