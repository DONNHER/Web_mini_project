@extends('layouts.app')

@section('title', 'Audit Log Details - Admin')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Audit Log Details</h1>
    <p class="mt-2">
        <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-400 hover:text-blue-300 font-bold uppercase text-xs tracking-widest transition">
            ← Back to Audit Logs
        </a>
    </p>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-700">
        <div class="px-8 py-6 bg-gray-900 border-b border-gray-700">
            <h2 class="text-xl font-black text-white uppercase tracking-tight">
                {{ ucfirst($audit->event) }} Event <span class="text-blue-500 mx-2">—</span> <span class="text-white opacity-80 font-medium text-lg">{{ $audit->created_at->format('F j, Y, g:i a') }}</span>
            </h2>
        </div>

        <div class="p-8 space-y-10">
            {{-- Basic Information --}}
            <section>
                <h3 class="text-xs font-black uppercase tracking-widest mb-4 text-blue-400">Event Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">Event Type</dt>
                        <dd class="mt-2">
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter rounded-full border
                                @if($audit->event == 'created') bg-green-900/40 text-green-400 border-green-500/30
                                @elseif($audit->event == 'updated') bg-blue-900/40 text-blue-400 border-blue-500/30
                                @elseif($audit->event == 'deleted') bg-red-900/40 text-red-400 border-red-500/30
                                @else bg-gray-900 text-white border-gray-700
                                @endif">
                                {{ ucfirst($audit->event) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">Date & Time</dt>
                        <dd class="mt-2 text-sm text-white font-medium">{{ $audit->created_at->format('Y-m-d H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">Model Type</dt>
                        <dd class="mt-2 text-sm text-blue-400 font-black">{{ class_basename($audit->auditable_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">Model ID</dt>
                        <dd class="mt-2 text-sm text-white font-mono">#{{ $audit->auditable_id }}</dd>
                    </div>
                </dl>
            </section>

            {{-- User Information --}}
            <section class="pt-8 border-t border-gray-700/50">
                <h3 class="text-xs font-black uppercase tracking-widest mb-4 text-blue-400">User Context</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">Initiator</dt>
                        <dd class="mt-2 text-sm text-white font-bold">
                            {{ $audit->user->name ?? 'System/Console' }}
                            @if($audit->user)
                                <span class="text-blue-400 ml-1 opacity-80 text-xs font-medium italic">({{ $audit->user->email }})</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">IP Address</dt>
                        <dd class="mt-2 text-sm text-white font-mono">{{ $audit->ip_address ?? 'N/A' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">User Agent</dt>
                        <dd class="mt-2 text-xs text-white opacity-80 font-medium leading-relaxed bg-gray-900/50 p-3 rounded border border-gray-700 break-all">
                            {{ $audit->user_agent ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs font-bold text-white opacity-60 uppercase tracking-tighter">Request URL</dt>
                        <dd class="mt-2 text-sm text-blue-300 font-medium break-all">{{ $audit->url ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Changed Values --}}
            @if($audit->event == 'updated')
            <section class="pt-8 border-t border-gray-700/50">
                <h3 class="text-xs font-black uppercase tracking-widest mb-4 text-blue-400">Data Deltas</h3>

                @php
                    $oldValues = $audit->old_values;
                    $newValues = $audit->new_values;
                    $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
                @endphp

                @if(count($changedFields) > 0)
                    <div class="overflow-hidden border border-gray-700 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[10px] font-black text-white opacity-60 uppercase tracking-widest">Field</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black text-white opacity-60 uppercase tracking-widest">Old Value</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black text-white opacity-60 uppercase tracking-widest">New Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-900/20">
                                @foreach($changedFields as $field)
                                <tr class="hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3 text-sm font-bold text-white">{{ $field }}</td>
                                    <td class="px-4 py-3 text-sm text-red-400 line-through opacity-70">
                                        @if(is_array($oldValues[$field] ?? null))
                                            <pre class="text-[10px] bg-black/30 p-2 rounded text-white">{{ json_encode($oldValues[$field], JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $oldValues[$field] ?? 'null' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-green-400 font-bold">
                                        @if(is_array($newValues[$field] ?? null))
                                            <pre class="text-[10px] bg-black/30 p-2 rounded text-white">{{ json_encode($newValues[$field], JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $newValues[$field] ?? 'null' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-white opacity-60 italic">No field-level changes detected or values were not captured.</p>
                @endif
            </section>
            @endif

            {{-- Created Values --}}
            @if($audit->event == 'created')
            <section class="pt-8 border-t border-gray-700/50">
                <h3 class="text-xs font-black uppercase tracking-widest mb-4 text-blue-400">Object Snapshot</h3>
                <div class="bg-gray-900 rounded-lg p-6 border border-gray-700 shadow-inner">
                    <pre class="text-xs text-white overflow-x-auto leading-relaxed">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </section>
            @endif

            {{-- Deleted Values --}}
            @if($audit->event == 'deleted')
            <section class="pt-8 border-t border-gray-700/50">
                <h3 class="text-xs font-black uppercase tracking-widest mb-4 text-red-400">Final Object State (Prior to deletion)</h3>
                <div class="bg-gray-900 rounded-lg p-6 border border-red-900/30 shadow-inner">
                    <pre class="text-xs text-white opacity-80 overflow-x-auto leading-relaxed">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </section>
            @endif
        </div>
    </div>
</div>
@endsection
