@extends('layouts.app')

@section('title', 'Audit Logs - Admin')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Audit Logs</h1>
            <p class="mt-2 text-white font-medium">Track all changes made to system data</p>
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
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition font-bold uppercase tracking-widest text-xs flex items-center shadow-lg shadow-red-900/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Export PDF
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
                    <option value="" class="text-white bg-gray-800">All Users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }} class="text-white bg-gray-800">
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">Model Type</label>
                <select name="auditable_type" class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
                    <option value="" class="text-white bg-gray-800">All Models</option>
                    @foreach($modelTypes as $type => $label)
                        <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }} class="text-white bg-gray-800">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">Event Type</label>
                <select name="event" class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
                    <option value="" class="text-white bg-gray-800">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }} class="text-white bg-gray-800">
                            {{ ucfirst($event) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">From Date</label>
                <input type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-white uppercase tracking-widest mb-2">To Date</label>
                <input type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full bg-gray-700 border-gray-600 text-white rounded-md focus:ring-white text-sm">
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
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Model</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Integrity</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($audits as $audit)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 text-sm text-white font-medium whitespace-nowrap">
                            {{ $audit->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-white font-bold">
                            {{ $audit->user->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter rounded-full border border-white text-white">
                                {{ ucfirst($audit->event) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            <span class="font-bold text-white">{{ class_basename($audit->auditable_type) }}</span>
                            <span class="text-white text-xs opacity-60">#{{ $audit->auditable_id }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            @if($audit->isValid())
                                <span class="text-green-400 flex items-center text-[10px] font-black uppercase">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 4.946-3.078 9.174-7.412 10.78a.75.75 0 01-.576 0C5.659 16.174 2.581 11.946 2.581 7c0-.681.057-1.35.166-2.001zM13.47 8.53a.75.75 0 00-1.06-1.06L9 10.89 7.59 9.48a.75.75 0 10-1.06 1.06l2 2a.75.75 0 001.06 0l3.88-3.88z" clip-rule="evenodd"></path></svg>
                                    Valid
                                </span>
                            @else
                                <span class="text-red-400 flex items-center text-[10px] font-black uppercase">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path></svg>
                                    Tampered
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.audit-logs.show', $audit) }}"
                               class="text-white font-bold uppercase text-xs tracking-wider transition underline">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-white italic font-bold">
                            No audit logs found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-6 border-t border-gray-700 bg-gray-900/30 pagination-dark text-white text-xs">
            {{ $audits->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
