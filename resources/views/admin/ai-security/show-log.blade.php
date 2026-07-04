@extends('layouts.app')

@section('title', 'AI Security Log Detail')

@section('header')
    <h2 class="font-semibold text-xl text-blue-400 leading-tight">
        AI Security Analysis Detail #{{ $log->id }}
    </h2>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.ai-security.index') }}" class="text-blue-400 hover:text-blue-300 flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>

        <form action="{{ route('admin.ai-security.destroy-log', $log) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this log entry?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">
                Delete Log Entry
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <!-- AI Reasoning -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-700 bg-gray-900">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        AI Reasoning & Assessment
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-gray-900 p-4 rounded-lg border border-gray-700 italic text-gray-300 leading-relaxed">
                        "{{ $log->reason }}"
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                            <span class="text-xs text-gray-500 uppercase font-bold">Risk Category</span>
                            <p class="text-xl font-bold {{ $log->risk_score > 70 ? 'text-red-500' : ($log->risk_score > 30 ? 'text-yellow-500' : 'text-green-500') }}">
                                {{ $log->risk_category ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                            <span class="text-xs text-gray-500 uppercase font-bold">Feature Scanned</span>
                            <p class="text-xl font-bold text-blue-400">
                                {{ ucfirst(str_replace('_', ' ', $log->feature)) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Context -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-lg font-bold text-white">Input Context Data</h3>
                </div>
                <div class="p-0">
                    <pre class="p-6 bg-gray-900 text-green-400 font-mono text-sm overflow-x-auto"><code>{{ json_encode($log->input_context, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Resource Info -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-700 bg-gray-900">
                    <h3 class="text-lg font-bold text-white">Target Resource</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold">Resource</span>
                        <p class="text-white">{{ $log->resource_type }} #{{ $log->resource_id }}</p>
                    </div>

                    @if($log->resource_type == 'Order')
                        <a href="{{ route('orders.show', $log->resource_id) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                            View Order Details
                        </a>
                    @endif

                    <hr class="border-gray-700">

                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold">Provider</span>
                        <p class="text-white uppercase">{{ $log->provider }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold">Response Time</span>
                        <p class="text-white">{{ number_format($log->response_time_ms, 2) }} ms</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold">Timestamp</span>
                        <p class="text-white">{{ $log->created_at->format('M d, Y H:i:s') }}</p>
                        <p class="text-xs text-gray-500">({{ $log->created_at->diffForHumans() }})</p>
                    </div>
                </div>
            </div>

            <!-- Performance Meter -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 shadow-xl p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Risk Assessment</h3>
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ $log->risk_score > 70 ? 'text-red-200 bg-red-900' : ($log->risk_score > 30 ? 'text-yellow-200 bg-yellow-900' : 'text-green-200 bg-green-900') }}">
                                {{ $log->risk_score }}% Confidence
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-gray-700">
                        <div style="width:{{ $log->risk_score }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $log->risk_score > 70 ? 'bg-red-500' : ($log->risk_score > 30 ? 'bg-yellow-500' : 'bg-green-500') }}"></div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 text-center">
                    Higher percentage indicates higher probability of anomalous behavior.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
