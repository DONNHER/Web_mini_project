@extends('layouts.app')

@section('title', 'My Notifications - LendingSystem')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white tracking-tight">Notifications</h1>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition font-bold text-xs uppercase tracking-widest border border-gray-600">
                Mark all as read
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    @forelse($notifications as $notification)
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg flex justify-between items-start
            {{ $notification->read_at ? 'opacity-60' : 'border-l-4 border-l-blue-500' }}">
            <div class="flex-1">
                <h3 class="text-white font-bold">{{ $notification->data['title'] ?? 'System Notification' }}</h3>
                <p class="text-gray-400 text-sm mt-1">{{ $notification->data['message'] ?? '' }}</p>
                <span class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mt-2 block">
                    {{ $notification->created_at->format('M d, Y H:i') }} ({{ $notification->created_at->diffForHumans() }})
                </span>
            </div>

            <div class="flex space-x-2">
                @if(!$notification->read_at)
                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-blue-400 hover:text-blue-300 text-xs font-bold uppercase tracking-widest">
                            Mark as read
                        </button>
                    </form>
                @endif
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Delete this notification?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-gray-800 rounded-xl p-10 text-center border border-dashed border-gray-700">
            <svg class="h-12 w-12 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-gray-500 italic">You have no notifications at the moment.</p>
        </div>
    @endforelse

    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
