@extends('layouts.app')

@section('title', 'Data Import/Export - Admin')

@section('header')
    <h1 class="text-3xl font-bold text-white tracking-tight">Data Import/Export System</h1>
    <p class="mt-2 text-white">Manage bulk book and user data with Excel, CSV, and PDF support</p>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Books Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Book Import Section --}}
        <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center text-blue-400">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Book Import Module
            </h2>

            <div class="mb-6 p-4 bg-gray-900 rounded-lg border border-gray-700">
                <h3 class="text-sm font-bold text-blue-400 mb-2 uppercase tracking-wider">Instructions:</h3>
                <ul class="text-xs text-white space-y-1 list-disc ml-4">
                    <li>Required headers: ISBN, Title, Author, Price, Stock, Category.</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('admin.template') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 underline uppercase tracking-tighter">
                        Download Book CSV Template
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-white mb-2 uppercase tracking-tight">Select File</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-700 file:text-blue-400 hover:file:bg-gray-600" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-white mb-2 uppercase tracking-tight">Duplicate Detection</label>
                    <div class="flex space-x-6">
                        <label class="inline-flex items-center group cursor-pointer">
                            <input type="radio" name="duplicate_action" value="skip" checked class="text-blue-400 focus:ring-blue-400 bg-gray-700 border-gray-600">
                            <span class="ml-2 text-sm text-white group-hover:text-blue-400 transition">Skip</span>
                        </label>
                        <label class="inline-flex items-center group cursor-pointer">
                            <input type="radio" name="duplicate_action" value="update" class="text-blue-400 focus:ring-blue-400 bg-gray-700 border-gray-600">
                            <span class="ml-2 text-sm text-white group-hover:text-blue-400 transition">Update</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-400 text-white font-black py-3 px-4 rounded-lg hover:bg-blue-500 transition duration-200 uppercase tracking-widest shadow-lg shadow-blue-400/20">
                    Process Book Import
                </button>
            </form>
        </div>

        {{-- Book Export Section --}}
        <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center text-blue-400">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Book Export
            </h2>

            <form action="{{ route('admin.export') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-wider">Category</label>
                        <select name="category" class="mt-1 block w-full text-sm bg-gray-700 border-gray-600 text-white rounded-md focus:ring-blue-400">
                            <option value="">All</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-wider">Format</label>
                        <select name="format" class="mt-1 block w-full text-sm bg-gray-700 border-gray-600 text-white rounded-md focus:ring-blue-400">
                            <option value="xlsx">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-400 text-white font-black py-3 px-4 rounded-lg hover:bg-blue-500 transition duration-200 uppercase tracking-widest shadow-lg shadow-blue-400/20">
                    Generate Book Export
                </button>
            </form>
        </div>
    </div>

    {{-- Users Section (GDPR & Bulk Support) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- User Import Section --}}
        <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center text-purple-400">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Bulk User Import
            </h2>

            <div class="mb-6 p-4 bg-gray-900 rounded-lg border border-gray-700">
                <h3 class="text-sm font-bold text-purple-400 mb-2 uppercase tracking-wider">Corporate Account Setup:</h3>
                <ul class="text-xs text-white space-y-1 list-disc ml-4">
                    <li>Headers: Name, Email, Role, Password.</li>
                    <li>Roles: 'admin' or 'customer'.</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('admin.users.template') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 underline uppercase tracking-tighter">
                        Download User CSV Template
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-white mb-2 uppercase tracking-tight">Select User File</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-700 file:text-purple-400 hover:file:bg-gray-600" required>
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white font-black py-3 px-4 rounded-lg hover:bg-purple-700 transition duration-200 uppercase tracking-widest shadow-lg shadow-purple-500/20">
                    Process User Import
                </button>
            </form>
        </div>

        {{-- User Export Section (GDPR Compliance) --}}
        <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center text-purple-400">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                GDPR User Export
            </h2>

            <form action="{{ route('admin.users.export') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-wider">Role Filter</label>
                        <select name="role" class="mt-1 block w-full text-sm bg-gray-700 border-gray-600 text-white rounded-md focus:ring-purple-400">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white uppercase tracking-wider">Format</label>
                        <select name="format" class="mt-1 block w-full text-sm bg-gray-700 border-gray-600 text-white rounded-md focus:ring-purple-400">
                            <option value="xlsx">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </div>

                <div class="p-4 bg-gray-900 rounded-lg border border-gray-700">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="redact_pii" value="1" class="text-purple-500 focus:ring-purple-500 bg-gray-700 border-gray-600 rounded">
                        <span class="ml-2 text-sm text-white font-bold uppercase tracking-widest">Enable PII Redaction</span>
                    </label>
                    <p class="mt-2 text-[10px] text-gray-400 italic">Redacts Names and masks Email addresses for GDPR compliant analytics.</p>
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white font-black py-3 px-4 rounded-lg hover:bg-purple-700 transition duration-200 uppercase tracking-widest shadow-lg shadow-purple-500/20">
                    Generate GDPR Export
                </button>
            </form>
        </div>
    </div>

    {{-- Log History --}}
    <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700 bg-gray-900">
            <h2 class="text-lg font-bold text-white uppercase tracking-tight">Processing Logs & History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-900 text-white uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">File Name</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black tracking-tighter
                                    {{ in_array($log->type, ['import', 'export']) ? 'bg-blue-900/50 text-blue-400 border border-blue-400/30' : 'bg-purple-900/50 text-purple-400 border border-purple-400/30' }}">
                                    {{ strtoupper(str_replace('_', ' ', $log->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-white font-medium truncate max-w-[200px]">{{ $log->file_name }}</td>
                            <td class="px-6 py-4">
                                <span class="flex items-center text-xs">
                                    <span class="h-2 w-2 rounded-full mr-2 {{ $log->status == 'completed' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : ($log->status == 'failed' ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]' : 'bg-yellow-500 animate-pulse') }}"></span>
                                    <span class="{{ $log->status == 'completed' ? 'text-green-400' : ($log->status == 'failed' ? 'text-red-400' : 'text-yellow-400') }} font-bold">{{ ucfirst($log->status) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-white text-xs font-medium">{{ $log->created_at->format('M d, H:i') }}</td>
                            <td class="px-6 py-4 text-white text-xs italic">{{ $log->user->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.data-portability.show', $log) }}" class="text-blue-400 hover:text-blue-300 font-bold uppercase text-[10px] tracking-widest underline transition">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-white italic">No import/export activity found in recent history.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
