@extends('layouts.app')

@section('title', 'Audit Logs - Admin')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Audit Logs</h1>
            <p class="mt-2 text-white font-medium">Track all changes and access events in the system</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition font-bold uppercase tracking-widest text-xs flex items-center border border-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition font-bold uppercase tracking-widest text-xs flex items-center shadow-lg shadow-green-900/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Filters --}}
    <div class="bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-700">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">User</label>
                <select name="user_id" class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
                    <option value="">All Users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">Target Type</label>
                <select name="auditable_type" class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
                    <option value="">All Types</option>
                    @foreach($modelTypes as $type => $label)
                        <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">Event</label>
                <select name="event" class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $event)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-gray-700 border-gray-600 text-white rounded-md text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-gray-700 border-gray-600 text-white rounded-md text-sm">
            </div>

            <div class="md:col-span-3 lg:col-span-5 flex justify-end items-center space-x-4">
                <a href="{{ route('admin.audit-logs.index') }}" class="text-white font-bold text-xs uppercase tracking-tighter transition hover:underline">
                    Clear Filters
                </a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-md hover:bg-blue-700 transition font-black uppercase tracking-widest shadow-lg">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Audit Logs Table --}}
    <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Date/Time</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">User</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Event</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Target</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Integrity</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($audits as $audit)
                    <tr class="hover:bg-gray-750 transition-colors {{ $audit->isCriticalEvent() ? 'bg-red-900/10' : '' }}">
                        <td class="px-6 py-4 text-sm text-gray-300 font-medium whitespace-nowrap">
                            {{ $audit->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-white font-bold">
                            {{ $audit->user->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter rounded-full border
                                @if($audit->event == 'deleted' || $audit->event == 'login_failed' || $audit->event == 'error_logged') border-red-500 text-red-500 bg-red-500/10
                                @elseif($audit->event == 'created' || $audit->event == 'login') border-green-500 text-green-500 bg-green-500/10
                                @elseif($audit->event == 'accessed') border-gray-500 text-gray-500
                                @else border-blue-500 text-blue-500 bg-blue-500/10
                                @endif">
                                {{ str_replace('_', ' ', $audit->event) }}
                            </span>
                            @if($audit->isCriticalEvent())
                                <span class="ml-2 inline-flex items-center text-[8px] font-black text-red-500 uppercase tracking-widest animate-pulse">
                                    ● SUSPICIOUS
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            <span class="font-bold text-white">{{ class_basename($audit->auditable_type) }}</span>
                            @if($audit->auditable_id) <span class="text-xs opacity-60">#{{ $audit->auditable_id }}</span> @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($audit->isValid())
                                <span class="text-green-500 flex items-center text-[10px] font-black uppercase" title="Checksum Verified">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    Valid
                                </span>
                            @else
                                <span class="text-red-500 flex items-center text-[10px] font-black uppercase" title="Log Tampered!">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Tampered
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.audit-logs.show', $audit) }}" class="text-blue-400 hover:text-blue-300 font-bold uppercase text-[10px] tracking-widest transition">
                                Details →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                            No audit logs found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/50 pagination-dark">
            {{ $audits->withQueryString()->links() }}
        </div>
    </div>

    <div class="flex items-center justify-center space-x-2 text-gray-500 text-[10px] uppercase tracking-widest py-4">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>Logs older than 90 days are automatically archived.</span>
    </div>
</div>
@endsection
