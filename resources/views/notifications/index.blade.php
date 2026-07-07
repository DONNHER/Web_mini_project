@extends('layouts.app')

@section('title', 'Notifications')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Notifications</h1>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn-secondary px-6">
                Clear Matrix
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @forelse($notifications as $notification)
        <div class="card p-8 flex justify-between items-start transition-all hover:border-[#FF6B00]/30
            {{ $notification->read_at ? 'opacity-60 bg-[#1A1A1A]/5' : 'border-l-4 border-l-[#FF6B00]' }}">
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-[8px] font-black text-[#FF6B00] uppercase tracking-[0.2em]">System Event</span>
                    @if(!$notification->read_at)
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B00] animate-pulse"></span>
                    @endif
                </div>
                <h3 class="text-[#1A1A1A] font-black uppercase text-lg tracking-tight">{{ $notification->data['title'] ?? 'System Notification' }}</h3>
                <p class="text-[#1A1A1A]/60 font-bold text-sm mt-2 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                <span class="text-[10px] text-[#1A1A1A]/40 font-black uppercase tracking-widest mt-4 block">
                    {{ $notification->created_at->format('M d, Y H:i') }} — {{ $notification->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="flex items-center space-x-6 ml-8">
                @if(!$notification->read_at)
                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[#FF6B00] hover:text-[#EA580C] text-[10px] font-black uppercase tracking-widest border-b-2 border-[#FF6B00] pb-0.5 transition">
                            Archive
                        </button>
                    </form>
                @endif
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Purge this record from registry?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-[#1A1A1A]/20 hover:text-red-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="card p-32 text-center border-dashed border-2">
            <div class="w-24 h-24 bg-[#FEF6F0] rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-[#1A1A1A]/5">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-2xl font-black uppercase tracking-tight text-[#1A1A1A]/30 italic">Registry Empty</h3>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1A1A1A]/20 mt-4 max-w-sm mx-auto leading-relaxed">No new system events detected. Check back later for neural updates.</p>
        </div>
    @endforelse

    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
