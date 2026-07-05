@extends('layouts.app')

@section('title', 'Data Engine - Admin')

@section('header')
    <h1 class="text-3xl font-black text-black uppercase tracking-tighter">Data Import / Export</h1>
    <p class="mt-2 text-black/60 font-bold uppercase text-xs tracking-widest">Enterprise bulk operations for Loan Assets & Users</p>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-12">

    {{-- Assets Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {{-- Import --}}
        <div class="bg-black text-brand rounded-3xl p-10 border border-black shadow-xl">
            <h2 class="text-xl font-black mb-8 flex items-center uppercase tracking-widest">
                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Asset Bulk Import
            </h2>

            <div class="mb-8 p-6 bg-brand/5 rounded-2xl border border-brand/10">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-3 opacity-60">Template Engine</h3>
                <a href="{{ route('admin.template') }}" class="inline-block bg-black text-brand px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest no-underline hover:opacity-80 transition">
                    Download Loan Template (CSV)
                </a>
            </div>

            <form id="asset-import-form" action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Select Source File (.xlsx, .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-xs text-white file:mr-4 file:py-2 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-brand/20 file:text-white hover:file:bg-brand/30 cursor-pointer" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-3 opacity-40">Collision Strategy</label>
                    <div class="flex space-x-8">
                        <label class="inline-flex items-center group cursor-pointer">
                            <input type="radio" name="duplicate_action" value="skip" checked class="text-black focus:ring-black bg-brand/20 border-brand/20">
                            <span class="ml-3 text-xs font-black uppercase opacity-60 group-hover:opacity-100 transition">Skip Existing</span>
                        </label>
                        <label class="inline-flex items-center group cursor-pointer">
                            <input type="radio" name="duplicate_action" value="update" class="text-black focus:ring-black bg-brand/20 border-brand/20">
                            <span class="ml-3 text-xs font-black uppercase opacity-60 group-hover:opacity-100 transition">Merge/Update</span>
                        </label>
                    </div>
                </div>

                <button type="button"
                        onclick="confirmBulkOperation('asset-import-form', 'Initialize bulk injection of loan assets? This will validate all rows against the current schema.')"
                        class="w-full bg-white text-black font-black py-4 rounded-2xl hover:opacity-90 transition uppercase tracking-widest text-xs shadow-lg">
                    Execute Asset Injection
                </button>
            </form>
        </div>

        {{-- Export --}}
        <div class="bg-black/5 rounded-3xl p-10 border border-black/5">
            <h2 class="text-xl font-black mb-8 flex items-center uppercase tracking-widest text-black">
                <svg class="h-6 w-6 mr-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Asset Data Export
            </h2>

            <form action="{{ route('admin.export') }}" method="GET" class="space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">Asset Category</label>
                        <select name="category" class="w-full bg-white/20 border-black/10 rounded-xl text-black font-bold p-3">
                            <option value="">All Assets</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">Format Output</label>
                        <select name="format" class="w-full bg-white/20 border-black/10 rounded-xl text-black font-bold p-3">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="json">JSON (.json)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-black text-brand font-black py-4 rounded-2xl hover:opacity-90 transition uppercase tracking-widest text-xs shadow-xl">
                    Generate Export Stream
                </button>
            </form>
        </div>
    </div>

    {{-- Log History --}}
    <div class="bg-black/5 rounded-3xl overflow-hidden border border-black/5">
        <div class="px-10 py-6 border-b border-black/5 bg-black/5 flex justify-between items-center">
            <h2 class="text-xs font-black text-black uppercase tracking-[0.3em] opacity-40">Operation Intelligence Logs</h2>
            <div class="flex items-center space-x-2">
                <span class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-black opacity-40 uppercase">Real-time status tracking active</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-black/5 text-black uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-10 py-6">Type</th>
                        <th class="px-10 py-6">Reference File</th>
                        <th class="px-10 py-6">Integrity Status</th>
                        <th class="px-10 py-6">Execution Date</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($logs as $log)
                        <tr class="group hover:bg-black/5 transition-colors">
                            <td class="px-10 py-8">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-tighter uppercase border border-black/20 text-black">
                                    {{ str_replace('_', ' ', $log->type) }}
                                </span>
                            </td>
                            <td class="px-10 py-8 text-black font-black text-sm truncate max-w-[200px]">{{ $log->file_name }}</td>
                            <td class="px-10 py-8">
                                <span class="flex items-center text-[10px] font-black uppercase tracking-widest">
                                    <span class="h-2 w-2 rounded-full mr-3 {{ $log->status == 'completed' ? 'bg-green-500' : ($log->status == 'failed' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                                    <span class="{{ $log->status == 'completed' ? 'text-black' : ($log->status == 'failed' ? 'text-red-600' : 'text-yellow-600') }}">{{ $log->status }}</span>
                                </span>
                            </td>
                            <td class="px-10 py-8 text-black/40 text-xs font-bold">{{ $log->created_at->format('M d, H:i') }}</td>
                            <td class="px-10 py-8 text-right">
                                <a href="{{ route('admin.data-portability.show', $log) }}" class="text-black hover:opacity-60 transition font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-black">
                                    Analyze Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-20 text-center text-black/20 font-black uppercase tracking-widest">No operation history found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
