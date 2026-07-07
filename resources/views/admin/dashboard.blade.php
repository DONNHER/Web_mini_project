@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-[#1A1A1A] uppercase tracking-tighter leading-none">Dashboard</h1>
        </div>
        <div class="flex space-x-3">
            <button id="refresh-dashboard" class="btn-secondary px-6">
                Recalibrate Data
            </button>
            <form action="{{ route('admin.dashboard.backup') }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary px-6">
                    Archive Registry
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-12" id="dashboard-app">
    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="card p-8">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest">Total Nodes</p>
            <div class="flex items-end justify-between mt-2">
                <p class="text-4xl font-black text-[#1A1A1A] tracking-tighter" id="stat-totalUsers">{{ $totalUsers }}</p>
                <p class="text-green-600 text-[10px] font-black uppercase tracking-widest mb-1"><span id="stat-activeNow">{{ $activeNow }}</span> Active</p>
            </div>
        </div>
        <div class="card p-8">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest">Disbursed Capital</p>
            <p class="text-4xl font-black text-[#FF6B00] tracking-tighter mt-2" id="stat-totalDisbursed">₱{{ number_format($totalDisbursed) }}</p>
        </div>
        <div class="card p-8">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest">Registry Mass</p>
            <p class="text-4xl font-black text-[#1A1A1A] tracking-tighter mt-2" id="stat-dbSize">{{ $dbSize }} MB</p>
        </div>
        <div class="card p-8">
            <p class="text-[#1A1A1A]/40 text-[10px] font-black uppercase tracking-widest">System Integrity</p>
            <div class="flex items-center mt-2">
                <span class="h-3 w-3 bg-green-500 rounded-full mr-2 shadow-lg shadow-green-500/40"></span>
                <p class="text-2xl font-black text-[#1A1A1A] uppercase tracking-tighter">Healthy</p>
            </div>
            <p class="text-[8px] text-[#1A1A1A]/20 font-black uppercase tracking-widest mt-2">Verified Integrity</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="card p-10">
            <h3 class="text-[#1A1A1A] font-black uppercase text-xs tracking-[0.3em] mb-8">Node Growth (Last 7 Cycles)</h3>
            <canvas id="registrationsChart" height="200"></canvas>
        </div>
        <div class="card p-10">
            <h3 class="text-[#1A1A1A] font-black uppercase text-xs tracking-[0.3em] mb-8">Asset Velocity (Monthly)</h3>
            <canvas id="activityChart" height="200"></canvas>
        </div>
    </div>

    <!-- Middle Row: Quick Actions & Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="card p-10">
            <h3 class="text-[#1A1A1A] font-black uppercase text-xs tracking-[0.3em] mb-8 border-b border-[#1A1A1A]/5 pb-4">Directives</h3>
            <div class="grid grid-cols-1 gap-4">
                <a href="{{ route('admin.users.index') }}" class="group flex items-center justify-between p-4 bg-[#FEF6F0] rounded-2xl hover:bg-[#FF6B00] transition-all duration-300 no-underline">
                    <span class="text-[#1A1A1A] text-[10px] font-black uppercase tracking-widest group-hover:text-white transition">Manage Registry</span>
                    <svg class="w-4 h-4 text-[#FF6B00] group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a href="{{ route('admin.loans.index') }}" class="group flex items-center justify-between p-4 bg-[#FEF6F0] rounded-2xl hover:bg-[#FF6B00] transition-all duration-300 no-underline">
                    <span class="text-[#1A1A1A] text-[10px] font-black uppercase tracking-widest group-hover:text-white transition">Loan Management</span>
                    <svg class="w-4 h-4 text-[#FF6B00] group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="group flex items-center justify-between p-4 bg-[#FEF6F0] rounded-2xl hover:bg-[#FF6B00] transition-all duration-300 no-underline">
                    <span class="text-[#1A1A1A] text-[10px] font-black uppercase tracking-widest group-hover:text-white transition">Full Audit Trail</span>
                    <svg class="w-4 h-4 text-[#FF6B00] group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a href="{{ route('admin.ai-security.index') }}" class="group flex items-center justify-between p-4 bg-[#FEF6F0] rounded-2xl hover:bg-[#FF6B00] transition-all duration-300 no-underline">
                    <span class="text-[#1A1A1A] text-[10px] font-black uppercase tracking-widest group-hover:text-white transition">Neural Defense</span>
                    <svg class="w-4 h-4 text-[#FF6B00] group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 card p-10">
            <h3 class="text-[#1A1A1A] font-black uppercase text-xs tracking-[0.3em] mb-8 border-b border-[#1A1A1A]/5 pb-4">Stream Logs</h3>
            <div class="space-y-6" id="recent-activities">
                @foreach($recentActivities as $activity)
                    <div class="flex items-center justify-between text-[10px] border-b border-[#1A1A1A]/5 pb-4">
                        <div class="flex items-center space-x-4">
                            <span class="h-2 w-2 rounded-full bg-[#FF6B00]"></span>
                            <div>
                                <span class="text-[#1A1A1A] font-black uppercase tracking-widest">{{ $activity->user->name ?? 'System' }}</span>
                                <span class="text-[#1A1A1A]/40 ml-2 font-bold uppercase">{{ str_replace('_', ' ', $activity->event) }}</span>
                                <span class="text-[#FF6B00] text-[8px] ml-2 font-black uppercase tracking-[0.2em]">{{ class_basename($activity->auditable_type) }}</span>
                            </div>
                        </div>
                        <span class="text-[#1A1A1A]/20 font-black uppercase tracking-widest">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let regChart, actChart;

    function initCharts() {
        const regCtx = document.getElementById('registrationsChart').getContext('2d');
        const actCtx = document.getElementById('activityChart').getContext('2d');

        regChart = new Chart(regCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($registrations)->pluck('date')) !!},
                datasets: [{
                    label: 'Nodes',
                    data: {!! json_encode(collect($registrations)->pluck('total')) !!},
                    borderColor: '#FF6B00',
                    borderWidth: 4,
                    pointBackgroundColor: '#1A1A1A',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(255, 107, 0, 0.05)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(26, 26, 26, 0.05)' },
                        ticks: { font: { weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                }
            }
        });

        actChart = new Chart(actCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($monthlyActivity)->pluck('month')) !!},
                datasets: [{
                    label: 'Assets',
                    data: {!! json_encode(collect($monthlyActivity)->pluck('count')) !!},
                    backgroundColor: '#1A1A1A',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(26, 26, 26, 0.05)' },
                        ticks: { font: { weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                }
            }
        });
    }

    async function refreshData() {
        const btn = document.getElementById('refresh-dashboard');
        btn.disabled = true;
        btn.innerText = 'Refreshing...';

        try {
            const response = await fetch('{{ route("admin.dashboard.stats") }}');
            const data = await response.json();

            // Update simple stats
            document.getElementById('stat-totalUsers').innerText = data.totalUsers;
            document.getElementById('stat-activeNow').innerText = data.activeNow;
            document.getElementById('stat-totalDisbursed').innerText = '₱' + new Intl.NumberFormat().format(data.totalDisbursed);
            document.getElementById('stat-dbSize').innerText = data.dbSize + ' MB';

            // Update charts
            regChart.data.labels = data.registrations.map(r => r.date);
            regChart.data.datasets[0].data = data.registrations.map(r => r.total);
            regChart.update();

            actChart.data.labels = data.monthlyActivity.map(a => a.month);
            actChart.data.datasets[0].data = data.monthlyActivity.map(a => a.count);
            actChart.update();

            // Update activities
            const activityContainer = document.getElementById('recent-activities');
            activityContainer.innerHTML = data.recentActivities.map(a => `
                <div class="flex items-center justify-between text-[10px] border-b border-[#1A1A1A]/5 pb-4">
                    <div class="flex items-center space-x-4">
                        <span class="h-2 w-2 rounded-full bg-[#FF6B00]"></span>
                        <div>
                            <span class="text-[#1A1A1A] font-black uppercase tracking-widest">${a.user ? a.user.name : 'System'}</span>
                            <span class="text-[#1A1A1A]/40 ml-2 font-bold uppercase">${a.event.replace('_', ' ')}</span>
                            <span class="text-[#FF6B00] text-[8px] ml-2 font-black uppercase tracking-[0.2em]">${a.auditable_type.split('\\').pop()}</span>
                        </div>
                    </div>
                    <span class="text-[#1A1A1A]/20 font-black uppercase tracking-widest">just now</span>
                </div>
            `).join('');

        } catch (error) {
            console.error('Error refreshing dashboard:', error);
        } finally {
            btn.disabled = false;
            btn.innerText = 'Recalibrate Data';
        }
    }

    document.getElementById('refresh-dashboard').addEventListener('click', refreshData);

    window.addEventListener('load', initCharts);
</script>
@endpush
@endsection
