@extends('layouts.app')

@section('title', 'Log Details - Data Portability')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.data-portability') }}" class="text-white hover:text-blue-400 transition">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Log Details</h1>
            <p class="mt-2 text-white font-medium italic opacity-80">Reference #{{ $log->id }} - {{ $log->file_name }}</p>
        </div>
    </div>
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 overflow-hidden">
        {{-- Header Status Bar --}}
        <div class="px-8 py-6 bg-gray-900 border-b border-gray-700 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase border {{ in_array($log->type, ['import', 'export']) ? 'bg-blue-900/50 text-blue-400 border-blue-400/30' : 'bg-purple-900/50 text-purple-400 border-purple-400/30' }}">
                    {{ str_replace('_', ' ', $log->type) }}
                </span>
                <span class="flex items-center text-sm">
                    <span class="h-2 w-2 rounded-full mr-2 {{ $log->status == 'completed' ? 'bg-green-500' : ($log->status == 'failed' ? 'bg-red-500' : 'bg-yellow-500 animate-pulse') }}"></span>
                    <span class="{{ $log->status == 'completed' ? 'text-green-400' : ($log->status == 'failed' ? 'text-red-400' : 'text-yellow-400') }} font-bold uppercase tracking-widest text-xs">
                        {{ $log->status }}
                    </span>
                </span>
            </div>
            <div class="text-white text-xs font-medium opacity-60">
                Processed at: {{ $log->created_at->format('Y-m-d H:i:s') }}
            </div>
        </div>

        <div class="p-8 space-y-8">
            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Rows</p>
                    <p class="text-2xl font-black text-white">{{ number_format($log->total_rows) }}</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Processed Successfully</p>
                    <p class="text-2xl font-black text-green-400">{{ number_format($log->processed_rows) }}</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Failures</p>
                    <p class="text-2xl font-black {{ ($log->total_rows - $log->processed_rows) > 0 ? 'text-red-400' : 'text-gray-500' }}">
                        {{ number_format($log->total_rows - $log->processed_rows) }}
                    </p>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] mb-4">Execution Details</h3>
                    <dl class="space-y-4">
                        <div class="flex justify-between border-b border-gray-700 pb-2">
                            <dt class="text-xs text-gray-400 font-bold uppercase">Initiated By</dt>
                            <dd class="text-xs text-white font-black italic">{{ $log->user->name ?? 'System' }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-gray-700 pb-2">
                            <dt class="text-xs text-gray-400 font-bold uppercase">File Name</dt>
                            <dd class="text-xs text-white font-mono">{{ $log->file_name }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-gray-700 pb-2">
                            <dt class="text-xs text-gray-400 font-bold uppercase">Storage Path</dt>
                            <dd class="text-[10px] text-gray-500 font-mono">imports/{{ $log->file_name }}</dd>
                        </div>
                    </dl>
                </div>

                @if($log->errors && count($log->errors) > 0)
                <div>
                    <h3 class="text-xs font-black text-red-400 uppercase tracking-[0.2em] mb-4">Validation Errors</h3>
                    <div class="bg-red-900/10 border border-red-900/30 rounded-lg p-4 max-h-[200px] overflow-y-auto custom-scrollbar">
                        <ul class="space-y-2">
                            @foreach($log->errors as $error)
                                <li class="text-[11px] text-red-200 flex items-start">
                                    <svg class="h-3 w-3 mr-2 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @else
                <div class="flex flex-col items-center justify-center p-8 bg-gray-900/30 rounded-xl border border-dashed border-gray-700">
                    <svg class="h-12 w-12 text-green-500/20 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">No errors recorded</p>
                </div>
                @endif
            </div>
        </div>

        <div class="px-8 py-6 bg-gray-900/50 border-t border-gray-700 flex justify-end">
            <a href="{{ route('admin.data-portability') }}" class="bg-gray-700 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition font-black uppercase tracking-widest text-xs">
                Back to History
            </a>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4B5563; }
</style>
@endsection
