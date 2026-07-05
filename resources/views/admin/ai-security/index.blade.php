@extends('layouts.app')

@section('title', 'AI Risk Assessment Dashboard')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-blue-400 leading-tight">
            AI Loan Risk Assessment & Security Dashboard
        </h2>
        <a href="{{ route('admin.ai-security.usage') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            View AI Usage & Costs
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- AI Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase tracking-wider">Flagged Loans</h3>
            <p class="mt-2 text-3xl font-bold text-red-500">{{ $flaggedLoans->count() }}</p>
            <p class="mt-1 text-xs text-gray-500">Awaiting manual credit review</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase tracking-wider">Total Assessments</h3>
            <p class="mt-2 text-3xl font-bold text-blue-400">{{ $logs->total() }}</p>
            <p class="mt-1 text-xs text-gray-500">Last 30 days</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase tracking-wider">Avg. Risk Score</h3>
            <p class="mt-2 text-3xl font-bold text-yellow-500">
                {{ number_format($logs->avg('risk_score'), 1) }}
            </p>
            <p class="mt-1 text-xs text-gray-500">Behavioral risk assessment</p>
        </div>
    </div>

    <!-- Flagged Loans Queue -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white">Manual Risk Review Queue</h3>
                <p class="text-xs text-gray-500">Loans marked as 'flagged' due to high AI risk scores</p>
            </div>
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.ai-security.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs bg-gray-700 hover:bg-gray-600 text-gray-300 px-3 py-1 rounded border border-gray-600 transition">
                        Sync Status from Logs
                    </button>
                </form>
                <span class="px-3 py-1 bg-red-900 text-red-200 text-xs rounded-full font-bold animate-pulse">
                    ACTION REQUIRED
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Loan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">AI Reasoning</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800">
                    @forelse($flaggedLoans as $loan)
                        @php
                            $latestLog = \App\Models\AISecurityLog::where('resource_id', $loan->id)
                                ->where('resource_type', 'Loan')
                                ->latest()
                                ->first();
                        @endphp
                        <tr class="hover:bg-gray-750 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-400">
                                #{{ $loan->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $loan->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-mono">
                                ₱{{ number_format($loan->principal_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400 max-w-xs">
                                <div class="bg-gray-900 p-2 rounded border-l-2 border-red-500 italic">
                                    "{{ $latestLog->reason ?? 'No AI reason provided.' }}"
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                <a href="{{ route('loans.show', $loan) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition">
                                    View
                                </a>
                                <form action="{{ route('admin.ai-security.rescan', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded transition">
                                        Rescan
                                    </button>
                                </form>
                                <form action="{{ route('admin.ai-security.resolve', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded transition">
                                        Clear
                                    </button>
                                </form>
                                <form action="{{ route('admin.ai-security.resolve', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                No high-risk loan applications detected. System is secure.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Scan History -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-bold text-white">AI Assessment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Risk Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Latency</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-750 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $log->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-blue-900 text-blue-200 text-xs rounded border border-blue-700 uppercase font-bold">
                                {{ $log->provider }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $log->resource_type }} #{{ $log->resource_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-700 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full @if($log->risk_score > 70) bg-red-500 @elseif($log->risk_score > 30) bg-yellow-500 @else bg-green-500 @endif" style="width: {{ $log->risk_score }}%"></div>
                                </div>
                                <span class="text-sm font-mono @if($log->risk_score > 70) text-red-400 @elseif($log->risk_score > 30) text-yellow-400 @else text-green-400 @endif">
                                    {{ $log->risk_score }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                            {{ number_format($log->response_time_ms, 0) }}ms
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('admin.ai-security.show-log', $log) }}" class="text-blue-400 hover:text-blue-300 font-bold uppercase text-xs tracking-widest">
                                Details
                            </a>
                            <form action="{{ route('admin.ai-security.destroy-log', $log) }}" method="POST" class="inline" onsubmit="return confirm('Delete this log?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 ml-2">
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
        <div class="p-4 bg-gray-900 border-t border-gray-700">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- AI Generation Disclaimer -->
    <div class="flex items-center justify-center space-x-2 text-gray-500 text-xs py-4">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Decisions on this page are augmented by Artificial Intelligence. Human review is recommended for high risk scores.</span>
    </div>
</div>

<script>
    // Frontend requirement: loading states (simple CSS overlay)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            this.querySelector('button').disabled = true;
            this.querySelector('button').innerHTML = '<span class="animate-spin inline-block mr-1">↻</span> Processing...';
        });
    });
</script>
@endsection
