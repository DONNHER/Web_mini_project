@extends('layouts.app')

@section('title', 'Admin Users')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <span class="text-[#FF6B00] font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">Identity Control</span>
            <h1 class="text-5xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">System <span class="text-[#FF6B00]">Registry</span></h1>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('admin.users.export', request()->all()) }}" class="btn-secondary px-6 no-underline">
                Export Registry
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn-primary px-8 no-underline shadow-xl">
                Register New Node
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-8" x-data="{ showColumns: { email: true, role: true, status: true, joined: true } }">

    <!-- Advanced Data Controls (Requirement 13) -->
    <div class="card p-10">
        <form action="{{ route('admin.users.index') }}" method="GET" id="filter-form" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-8">
                <!-- Global Search -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-3">Omni Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Scan identity matrix..."
                           class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-4 text-[#1A1A1A] font-bold focus:ring-4 focus:ring-[#FF6B00]/5 placeholder-[#1A1A1A]/20">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-3">Integrity</label>
                    <select name="status" class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-4 text-[#1A1A1A] font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
                        <option value="">Matrix: All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-3">Authority</label>
                    <select name="role_id" class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-4 text-[#1A1A1A] font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
                        <option value="">Matrix: All</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40 mb-3">Density</label>
                    <select name="per_page" class="w-full bg-[#FEF6F0] border-none rounded-xl px-6 py-4 text-[#1A1A1A] font-bold focus:ring-4 focus:ring-[#FF6B00]/5 text-xs">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / Cycle</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / Cycle</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / Cycle</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / Cycle</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end">
                    <button type="submit" class="w-full btn-primary py-4">Apply Logic</button>
                </div>
            </div>

            <div class="flex justify-between items-center pt-8 border-t border-[#1A1A1A]/5">
                <!-- Column Visibility (Requirement 13.6) -->
                <div class="flex items-center space-x-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]/40">Matrix Columns:</span>
                    <div class="flex space-x-2">
                        <template x-for="(visible, col) in showColumns">
                            <button type="button" @click="showColumns[col] = !showColumns[col]"
                                    :class="visible ? 'bg-[#1A1A1A] text-white' : 'bg-[#FEF6F0] text-[#1A1A1A]/40'"
                                    class="px-4 py-2 rounded-full text-[8px] font-black uppercase tracking-widest transition-all duration-300"
                                    x-text="col"></button>
                        </template>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black uppercase tracking-widest text-[#1A1A1A]/40 hover:text-[#FF6B00] no-underline transition">Reset Matrix Defaults</a>
            </div>
        </form>
    </div>

    <!-- Bulk Action Processing Form Container -->
    <form action="{{ route('admin.users.index') }}" method="GET" id="bulk-form" class="hidden"></form>

    <!-- Registry Table Container -->
    <div class="bg-black/5 rounded-3xl overflow-hidden border border-black/5">
        <div class="px-10 py-6 border-b border-black/5 flex justify-between items-center bg-black/5">
            <!-- Bulk Actions (Requirement 13.5) -->
            <div class="flex items-center space-x-4">
                <select name="bulk_action" form="bulk-form" class="bg-white/20 border-black/10 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest focus:ring-black">
                    <option value="">Bulk Operations</option>
                    <option value="activate">Activate Selected</option>
                    <option value="suspend">Suspend Selected</option>
                    <option value="delete">Purge Selected</option>
                </select>
                <button type="submit" form="bulk-form" class="bg-black text-brand px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition hover:opacity-80">Execute</button>
            </div>

            <div class="text-[10px] font-black uppercase tracking-widest text-black/40">
                Total Records: {{ $users->total() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#1A1A1A] text-white uppercase text-[10px] font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">
                            <input type="checkbox" id="select-all" class="rounded border-white/20 bg-white/10 text-[#FF6B00] focus:ring-[#FF6B00]">
                        </th>
                        <th class="px-6 py-6 cursor-pointer hover:text-[#FF6B00] transition">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="no-underline flex items-center text-white hover:text-[#FF6B00]">
                                Identity @if(request('sort') == 'name') <span class="ml-1 text-[#FF6B00]">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span> @endif
                            </a>
                        </th>
                        <th x-show="showColumns.email" class="px-6 py-6">Identifier</th>
                        <th x-show="showColumns.role" class="px-6 py-6">Authority</th>
                        <th x-show="showColumns.status" class="px-6 py-6">Integrity</th>
                        <th x-show="showColumns.joined" class="px-6 py-6">Registry Date</th>
                        <th class="px-10 py-6 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach($users as $user)
                    <tr class="group hover:bg-black/5 transition-colors">
                        <td class="px-10 py-6">
                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" form="bulk-form" class="user-checkbox rounded border-black/20 text-black">
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-brand text-black font-black flex items-center justify-center border border-black/10 mr-4">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-black text-black">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td x-show="showColumns.email" class="px-6 py-6 text-xs font-bold text-black/60">{{ $user->email }}</td>
                        <td x-show="showColumns.role" class="px-6 py-6">
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border border-black/10">
                                {{ $user->role->name ?? 'User' }}
                            </span>
                        </td>
                        <td x-show="showColumns.status" class="px-6 py-6">
                            <span class="flex items-center text-[10px] font-black uppercase">
                                <span class="h-1.5 w-1.5 rounded-full mr-2 {{ $user->status == 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $user->status }}
                            </span>
                        </td>
                        <td x-show="showColumns.joined" class="px-6 py-6 text-xs font-bold text-black/40">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-10 py-6 text-right space-x-4">
                            <!-- Impersonation Directive Safeguard (Requirement 14.3) -->
                            @if(Route::has('admin.users.impersonate'))
                                <a href="{{ route('admin.users.impersonate', $user) }}" title="Secure Impersonation" class="text-black/40 hover:text-black transition">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </a>
                            @elseif(Route::has('impersonate'))
                                <a href="{{ route('impersonate', $user->id) }}" title="Secure Impersonation" class="text-black/40 hover:text-black transition">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </a>
                            @endif

                            <a href="{{ route('admin.users.edit', $user) }}" class="text-black font-black text-[10px] uppercase tracking-widest no-underline border-b-2 border-black">Configure</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const checkboxes = document.getElementsByClassName('user-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                for (let i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            });
        }
    });
</script>
@endpush
@endsection

