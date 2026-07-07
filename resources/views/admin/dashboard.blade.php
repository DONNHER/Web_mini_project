@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <h1 class="text-2xl font-black text-black uppercase tracking-tighter leading-none">Command Center</h1>
        <div class="flex space-x-2">
            <button id="refresh-dashboard" class="btn-secondary px-4 py-2">Recalibrate</button>
            <form action="{{ route('admin.dashboard.backup') }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary px-4 py-2">Archive</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" id="dashboard-app">
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 border-none shadow-sm">
            <p class="text-[8px] font-black uppercase tracking-widest text-black/40">Total Nodes</p>
            <p class="text-2xl font-black text-black tracking-tighter mt-1" id="stat-totalUsers">{{ $totalUsers }}</p>
        </div>
        <div class="card p-4 border-none shadow-sm">
            <p class="text-[8px] font-black uppercase tracking-widest text-black/40">Disbursed</p>
            <p class="text-2xl font-black text-[#FF6B00] tracking-tighter mt-1" id="stat-totalDisbursed">₱{{ number_format($totalDisbursed) }}</p>
        </div>
        <div class="card p-4 border-none shadow-sm">
            <p class="text-[8px] font-black uppercase tracking-widest text-black/40">Registry Mass</p>
            <p class="text-2xl font-black text-black tracking-tighter mt-1" id="stat-dbSize">{{ $dbSize }}MB</p>
        </div>
        <div class="card p-4 border-none shadow-sm">
            <p class="text-[8px] font-black uppercase tracking-widest text-black/40">Integrity</p>
            <p class="text-lg font-black text-green-600 uppercase tracking-tighter mt-1">HEALTHY</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Charts --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 bg-white border-none shadow-sm">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-6 text-black/40">Temporal Node Growth</h3>
                <canvas id="registrationsChart" height="120"></canvas>
            </div>
        </div>

        {{-- Directives --}}
        <div class="space-y-6">
            <div class="card p-6 border-none shadow-sm">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 border-b border-black/5 pb-3">Directives</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-3 bg-[#FEF6F0] rounded-xl hover:bg-black hover:text-white transition-all no-underline group">
                        <span class="text-[8px] font-black uppercase tracking-widest">Registry</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </a>
                    <a href="{{ route('admin.loans.index') }}" class="flex items-center justify-between p-3 bg-[#FEF6F0] rounded-xl hover:bg-black hover:text-white transition-all no-underline group">
                        <span class="text-[8px] font-black uppercase tracking-widest">Loans</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center justify-between p-3 bg-[#FEF6F0] rounded-xl hover:bg-black hover:text-white transition-all no-underline group">
                        <span class="text-[8px] font-black uppercase tracking-widest">Audits</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <div class="card p-6 border-none shadow-sm overflow-hidden">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 border-b border-black/5 pb-3 text-black/40">Active Stream</h3>
                <div class="space-y-4" id="recent-activities">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-start justify-between border-b border-black/5 pb-3 last:border-none">
                            <div class="flex items-center">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#FF6B00] mr-3 mt-1"></span>
                                <div>
                                    <p class="text-[9px] font-black uppercase text-black leading-none">{{ $activity->user->name ?? 'System' }}</p>
                                    <p class="text-[7px] font-bold text-black/40 uppercase mt-1">{{ str_replace('_', ' ', $activity->event) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let regChart;
    function initCharts() {
        const regCtx = document.getElementById('registrationsChart').getContext('2d');
        regChart = new Chart(regCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($registrations)->pluck('date')) !!},
                datasets: [{
                    data: {!! json_encode(collect($registrations)->pluck('total')) !!},
                    borderColor: '#000000',
                    borderWidth: 3,
                    pointRadius: 0,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { ticks: { font: { size: 8, weight: 'bold' } }, grid: { display: false } }
                }
            }
        });
    }

    async function refreshData() {
        const btn = document.getElementById('refresh-dashboard');
        btn.innerText = 'WAIT...';
        try {
            const response = await fetch('{{ route("admin.dashboard.stats") }}');
            const data = await response.json();
            document.getElementById('stat-totalUsers').innerText = data.totalUsers;
            document.getElementById('stat-totalDisbursed').innerText = '₱' + new Intl.NumberFormat().format(data.totalDisbursed);
            document.getElementById('stat-dbSize').innerText = data.dbSize + 'MB';
            regChart.data.labels = data.registrations.map(r => r.date);
            regChart.data.datasets[0].data = data.registrations.map(r => r.total);
            regChart.update();
        } catch (error) { console.error(error); }
        finally { btn.innerText = 'RECALIBRATE'; }
    }
    document.getElementById('refresh-dashboard').addEventListener('click', refreshData);
    window.addEventListener('load', initCharts);
</script>
@endpush
@endsection
