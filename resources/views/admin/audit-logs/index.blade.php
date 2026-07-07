@extends('layouts.app')

@section('title', 'Admin Audit Logs')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Audit Logs</h1>
        </div>
        <div class="flex flex-wrap gap-4">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn-secondary px-6 no-underline">
                Export Data (.CSV)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn-primary px-6 no-underline">
                Generate Report (.XLSX)
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-12">

    {{-- Filters --}}
    <div class="card p-10">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
            <div>
                <label class="block text-[10px] font-black text-[#1A1A1A] uppercase tracking-[0.2em] mb-3">Operator</label>
                <select name="user_id" class="w-full bg-[#FEF6F0] border-none rounded-xl px-8 py-3 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
                    <option value="">Matrix: All</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-[#1A1A1A] uppercase tracking-[0.2em] mb-3">Asset Class</label>
                <select name="auditable_type" class="w-full bg-[#FEF6F0] border-none rounded-xl px-8 py-3 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
                    <option value="">Matrix: All</option>
                    @foreach($modelTypes as $type => $label)
                        <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-[#1A1A1A] uppercase tracking-[0.2em] mb-3">Event Logic</label>
                <select name="event" class="w-full bg-[#FEF6F0] border-none rounded-xl px-8 py-3 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
                    <option value="">Matrix: All</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $event)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-[#1A1A1A] uppercase tracking-[0.2em] mb-3">Temporal Start</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-[#FEF6F0] border-none rounded-xl px-8 py-3 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
            </div>

            <div>
                <label class="block text-[10px] font-black text-[#1A1A1A] uppercase tracking-[0.2em] mb-3">Temporal End</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-[#FEF6F0] border-none rounded-xl px-8 py-3 font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
            </div>

            <div class="md:col-span-3 lg:col-span-5 flex justify-end items-center space-x-6 pt-4">
                <a href="{{ route('admin.audit-logs.index') }}" class="text-[10px] font-black text-[#1A1A1A]/40 uppercase tracking-widest no-underline hover:text-[#FF6B00] transition">
                    Reset Parameters
                </a>
                <button type="submit" class="btn-primary px-12">
                    Execute Scan
                </button>
            </div>
        </form>
    </div>

    {{-- Audit Logs Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#1A1A1A] text-white uppercase text-[10px] font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Timestamp</th>
                        <th class="px-6 py-6">Operator</th>
                        <th class="px-6 py-6">Logic Event</th>
                        <th class="px-6 py-6">Target Node</th>
                        <th class="px-6 py-6">Integrity</th>
                        <th class="px-10 py-6 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1A1A1A]/5">
                    @forelse($audits as $audit)
                    <tr class="group hover:bg-[#FEF6F0] transition-colors {{ $audit->isCriticalEvent() ? 'bg-red-500/5' : '' }}">
                        <td class="px-10 py-6 text-xs font-bold text-[#1A1A1A]/60">
                            {{ $audit->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-6">
                            <span class="text-sm font-black text-[#1A1A1A]">{{ $audit->user->name ?? 'SYSTEM' }}</span>
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-full border
                                @if($audit->event == 'deleted' || $audit->event == 'login_failed' || $audit->event == 'error_logged') border-red-500 text-red-600 bg-red-500/5
                                @elseif($audit->event == 'created' || $audit->event == 'login') border-green-500 text-green-600 bg-green-500/5
                                @else border-[#1A1A1A]/10 text-[#1A1A1A]/60
                                @endif">
                                {{ str_replace('_', ' ', $audit->event) }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <span class="text-xs font-bold text-[#1A1A1A]">{{ class_basename($audit->auditable_type) }}</span>
                            @if($audit->auditable_id) <span class="text-[10px] opacity-40">ID:{{ $audit->auditable_id }}</span> @endif
                        </td>
                        <td class="px-6 py-6">
                            @if($audit->isValid())
                                <span class="text-green-600 flex items-center text-[10px] font-black uppercase tracking-widest">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    Secure
                                </span>
                            @else
                                <span class="text-red-600 flex items-center text-[10px] font-black uppercase tracking-widest animate-pulse">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Compromised
                                </span>
                            @endif
                        </td>
                        <td class="px-10 py-6 text-right">
                            <a href="{{ route('admin.audit-logs.show', $audit) }}" class="text-[#1A1A1A] font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-[#1A1A1A] hover:text-[#FF6B00] hover:border-[#FF6B00] transition">
                                Inspect
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20">No matching logs identified in registry.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-10 py-6 border-t border-[#1A1A1A]/5">
            {{ $audits->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
