@extends('layouts.app')

@section('title', 'AI Cost Tracking')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-blue-400 leading-tight">
            AI Usage & Cost Monitoring
        </h2>
        <a href="{{ route('admin.ai-security.index') }}" class="text-sm text-gray-400 hover:text-white">
            &larr; Back to Security
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Cost Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase">Total Cost</h3>
            <p class="mt-2 text-3xl font-bold text-green-400">${{ number_format($stats['total_cost'], 4) }}</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase">Daily Cost</h3>
            <p class="mt-2 text-3xl font-bold text-blue-400">${{ number_format($stats['daily_cost'], 4) }}</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase">Total Tokens</h3>
            <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['total_tokens']) }}</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-xl">
            <h3 class="text-gray-400 text-sm font-medium uppercase">Avg Cost / Call</h3>
            <p class="mt-2 text-3xl font-bold text-yellow-500">
                ${{ $usageLogs->total() > 0 ? number_format($stats['total_cost'] / $usageLogs->total(), 5) : '0.00' }}
            </p>
        </div>
    </div>

    <!-- Usage History -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-bold text-white">Consumption History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Feature</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Tokens</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Est. Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800">
                    @foreach($usageLogs as $log)
                    <tr class="hover:bg-gray-750 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $log->created_at->format('M d, H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->provider == 'ollama' ? 'bg-orange-900 text-orange-200' : 'bg-blue-900 text-blue-200' }}">
                                {{ $log->provider }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ ucwords(str_replace('_', ' ', $log->feature)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-white">
                            {{ number_format($log->tokens_used) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-green-400">
                            ${{ number_format($log->cost_estimate, 5) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-gray-900">
            {{ $usageLogs->links() }}
        </div>
    </div>
</div>
@endsection
