@extends('layouts.app')

@section('title', 'Manage Orders - Admin')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white tracking-tight">Manage Orders</h1>
        <div class="flex space-x-2">
            <button onclick="document.getElementById('export-modal').classList.remove('hidden')" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-bold uppercase tracking-wider transition shadow-lg shadow-blue-400/20">
                Export Orders
            </button>
            <a href="{{ route('admin.orders.financial') }}" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-bold uppercase tracking-wider transition shadow-lg shadow-blue-400/20">
                Financial Report
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-gray-800 p-4 rounded-lg shadow-md mb-6 border border-gray-700">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-white uppercase tracking-widest mb-1">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-blue-400 focus:ring-blue-400">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="md:col-start-4 flex items-end">
                <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-bold uppercase tracking-wider transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Order #</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-white">#{{ $order->id }}</td>
                    <td class="px-6 py-4 text-sm text-white">{{ $order->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-white font-medium">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm font-black text-blue-400">₱{{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="text-xs border-gray-600 bg-gray-900 text-white rounded-md shadow-sm focus:ring-blue-400 focus:border-blue-400 py-1">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit" class="px-3 py-1 bg-blue-600/20 border border-blue-400/30 text-blue-400 rounded-md hover:bg-blue-600 hover:text-white transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">Update</button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-sm flex space-x-3">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 font-bold uppercase text-xs tracking-wider">View</a>
                        <a href="{{ route('orders.invoice', $order) }}" class="text-green-400 hover:text-green-300 font-bold uppercase text-xs tracking-wider">Invoice</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 pagination-dark">
        {{ $orders->links() }}
    </div>

    <!-- Export Modal -->
    <div id="export-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-all duration-300">
        <div class="relative top-20 mx-auto p-6 border border-gray-700 w-96 shadow-2xl rounded-xl bg-gray-800">
            <div class="mt-3">
                <h3 class="text-xl font-black text-white uppercase tracking-tight border-b border-gray-700 pb-2 mb-4">Export Orders</h3>
                <form action="{{ route('admin.orders.export') }}" method="GET" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-widest mb-1">Status</label>
                        <select name="status" class="block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-blue-400 focus:ring-blue-400">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-widest mb-1">From Date</label>
                        <input type="date" name="date_from" class="block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-blue-400 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-widest mb-1">To Date</label>
                        <input type="date" name="date_to" class="block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-blue-400 focus:ring-blue-400">
                    </div>
                    <div class="flex justify-end space-x-3 mt-8">
                        <button type="button" onclick="document.getElementById('export-modal').classList.add('hidden')" class="bg-gray-700 text-white px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit" class="bg-blue-400 text-white px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-blue-500 transition shadow-lg shadow-blue-400/20">Download XLSX</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
