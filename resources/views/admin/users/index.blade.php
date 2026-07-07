@extends('layouts.app')

@section('title', 'Users')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-black uppercase tracking-tighter leading-none">Users Registry</h1>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.users.export', request()->all()) }}" class="btn-secondary px-4 no-underline">
                Export Data
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn-primary px-6 no-underline">
                Register Node
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ showColumns: { email: true, role: true, status: true, joined: true } }">

    <!-- Filters -->
    <div class="card p-6">
        <form action="{{ route('admin.users.index') }}" method="GET" id="filter-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">Omni Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Scan identities..."
                           class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 placeholder-black/20 text-xs">
                </div>

                <div>
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">Integrity</label>
                    <select name="status" class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-[10px]">
                        <option value="">Matrix: All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">Authority</label>
                    <select name="role_id" class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-[10px]">
                        <option value="">Matrix: All</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[8px] font-black uppercase tracking-[0.2em] text-black/40 mb-2">Density</label>
                    <select name="per_page" class="w-full bg-[#FEF6F0] border-none rounded-lg px-6 py-2 text-black font-bold focus:ring-2 focus:ring-[#FF6B00]/5 text-[10px]">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / Cycle</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / Cycle</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / Cycle</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full btn-primary py-2.5">Apply Logic</button>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-black/5">
                <div class="flex items-center space-x-4">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-black/40">Visible Nodes:</span>
                    <div class="flex space-x-1">
                        <template x-for="(visible, col) in showColumns">
                            <button type="button" @click="showColumns[col] = !showColumns[col]"
                                    :class="visible ? 'bg-black text-white' : 'bg-[#FEF6F0] text-black/40'"
                                    class="px-3 py-1.5 rounded-full text-[7px] font-black uppercase tracking-widest transition-all duration-300"
                                    x-text="col"></button>
                        </template>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-[8px] font-black uppercase tracking-widest text-black/40 hover:text-[#FF6B00] no-underline">Reset Defaults</a>
            </div>
        </form>
    </div>

    <!-- Registry Table -->
    <form action="{{ route('admin.users.index') }}" method="GET" id="bulk-form" class="hidden"></form>
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex justify-between items-center bg-[#FEF6F0]/30">
            <div class="flex items-center space-x-2">
                <select name="bulk_action" form="bulk-form" class="bg-white/50 border-black/10 rounded-lg px-4 py-1.5 text-[8px] font-black uppercase tracking-widest">
                    <option value="">Bulk Actions</option>
                    <option value="activate">Activate</option>
                    <option value="suspend">Suspend</option>
                    <option value="delete">Purge</option>
                </select>
                <button type="submit" form="bulk-form" class="bg-black text-brand px-4 py-1.5 rounded-lg text-[8px] font-black uppercase tracking-widest hover:opacity-80">Execute</button>
            </div>
            <div class="text-[8px] font-black uppercase tracking-widest text-black/40">
                Total records: {{ $users->total() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-black text-white uppercase text-[8px] font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4"><input type="checkbox" id="select-all" class="rounded border-white/20 bg-white/10 text-brand focus:ring-brand"></th>
                        <th class="px-4 py-4">Identity</th>
                        <th x-show="showColumns.email" class="px-4 py-4">Identifier</th>
                        <th x-show="showColumns.role" class="px-4 py-4">Authority</th>
                        <th x-show="showColumns.status" class="px-4 py-4">Integrity</th>
                        <th x-show="showColumns.joined" class="px-4 py-4">Registry Date</th>
                        <th class="px-6 py-4 text-right">Directives</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach($users as $user)
                    <tr class="group hover:bg-[#FEF6F0] transition-colors">
                        <td class="px-6 py-4"><input type="checkbox" name="selected_users[]" value="{{ $user->id }}" form="bulk-form" class="user-checkbox rounded border-black/10 text-black"></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-lg bg-brand text-white font-black flex items-center justify-center text-xs mr-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="text-xs font-black text-black uppercase">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td x-show="showColumns.email" class="px-4 py-4 text-[10px] font-bold text-black/60">{{ $user->email }}</td>
                        <td x-show="showColumns.role" class="px-4 py-4">
                            <span class="px-2 py-0.5 rounded-full text-[7px] font-black uppercase tracking-widest border border-black/10">
                                {{ $user->role->name ?? 'User' }}
                            </span>
                        </td>
                        <td x-show="showColumns.status" class="px-4 py-4">
                            <span class="flex items-center text-[8px] font-black uppercase">
                                <span class="h-1.5 w-1.5 rounded-full mr-1.5 {{ $user->status == 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $user->status }}
                            </span>
                        </td>
                        <td x-show="showColumns.joined" class="px-4 py-4 text-[10px] font-bold text-black/40">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-black font-black text-[8px] uppercase tracking-widest border-b border-black hover:text-[#FF6B00] hover:border-[#FF6B00]">View Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
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
                for (let i = 0; i < checkboxes.length; i++) checkboxes[i].checked = this.checked;
            });
        }
    });
</script>
@endpush
@endsection
