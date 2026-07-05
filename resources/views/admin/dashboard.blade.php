@extends('layouts.app')

@section('title', 'Admin Dashboard - LendingSystem')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white tracking-tight">Admin Dashboard</h1>
        <div class="flex space-x-3">
            <button id="refresh-dashboard" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition font-bold text-xs uppercase tracking-widest border border-gray-600">
                Refresh Data
            </button>
            <form action="{{ route('admin.dashboard.backup') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-bold text-xs uppercase tracking-widest shadow-lg">
                    Run Backup
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-8" id="dashboard-app">
    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Users</p>
            <div class="flex items-end justify-between">
                <p class="text-3xl font-black text-white mt-1" id="stat-totalUsers">{{ $totalUsers }}</p>
                <p class="text-green-500 text-xs font-bold mb-1"><span id="stat-activeNow">{{ $activeNow }}</span> Active Now</p>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Disbursed</p>
            <p class="text-3xl font-black text-green-400 mt-1" id="stat-totalDisbursed">₱{{ number_format($totalDisbursed) }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Database Size</p>
            <p class="text-3xl font-black text-blue-400 mt-1" id="stat-dbSize">{{ $dbSize }} MB</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Backup Status</p>
            <div class="flex items-center mt-1">
                <span class="h-3 w-3 bg-green-500 rounded-full mr-2 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                <p class="text-xl font-black text-white">Healthy</p>
            </div>
            <p class="text-[8px] text-gray-500 font-bold uppercase mt-1">Integrity Verified</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6">User Registrations (Last 7 Days)</h3>
            <canvas id="registrationsChart" height="200"></canvas>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6">Loan Activity (Monthly)</h3>
            <canvas id="activityChart" height="200"></canvas>
        </div>
    </div>

    <!-- Middle Row: Quick Actions & Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6 border-b border-gray-700 pb-2">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.users.index') }}" class="bg-gray-900 border border-gray-700 p-4 rounded-lg text-center hover:border-blue-500 transition group">
                    <p class="text-white text-xs font-bold group-hover:text-blue-400">Manage Users</p>
                </a>
                <a href="{{ route('admin.loans.index') }}" class="bg-gray-900 border border-gray-700 p-4 rounded-lg text-center hover:border-blue-500 transition group">
                    <p class="text-white text-xs font-bold group-hover:text-blue-400">View Loans</p>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="bg-gray-900 border border-gray-700 p-4 rounded-lg text-center hover:border-blue-500 transition group">
                    <p class="text-white text-xs font-bold group-hover:text-blue-400">Audit Logs</p>
                </a>
                <a href="{{ route('admin.ai-security.index') }}" class="bg-gray-900 border border-gray-700 p-4 rounded-lg text-center hover:border-blue-500 transition group">
                    <p class="text-white text-xs font-bold group-hover:text-blue-400">AI Risk</p>
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
            <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6 border-b border-gray-700 pb-2">Recent Activities</h3>
            <div class="space-y-4" id="recent-activities">
                @foreach($recentActivities as $activity)
                    <div class="flex items-center justify-between text-sm border-b border-gray-700/50 pb-2">
                        <div>
                            <span class="text-blue-400 font-bold">{{ $activity->user->name ?? 'System' }}</span>
                            <span class="text-gray-400 ml-2">{{ str_replace('_', ' ', $activity->event) }}</span>
                            <span class="text-gray-600 text-xs ml-2">{{ class_basename($activity->auditable_type) }}</span>
                        </div>
                        <span class="text-gray-500 text-xs">{{ $activity->created_at->diffForHumans() }}</span>
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
                    label: 'Registrations',
                    data: {!! json_encode(collect($registrations)->pluck('total')) !!},
                    borderColor: '#3b82f6',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#374151' } }, x: { grid: { display: false } } }
            }
        });

        actChart = new Chart(actCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($monthlyActivity)->pluck('month')) !!},
                datasets: [{
                    label: 'Loans',
                    data: {!! json_encode(collect($monthlyActivity)->pluck('count')) !!},
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#374151' } }, x: { grid: { display: false } } }
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
            document.getElementById('stat-errorRate').innerText = data.errorRate + '%';

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
                <div class="flex items-center justify-between text-sm border-b border-gray-700/50 pb-2">
                    <div>
                        <span class="text-blue-400 font-bold">${a.user ? a.user.name : 'System'}</span>
                        <span class="text-gray-400 ml-2">${a.event.replace('_', ' ')}</span>
                        <span class="text-gray-600 text-xs ml-2">${a.auditable_type.split('\\').pop()}</span>
                    </div>
                    <span class="text-gray-500 text-xs">just now</span>
                </div>
            `).join('');

        } catch (error) {
            console.error('Error refreshing dashboard:', error);
        } finally {
            btn.disabled = false;
            btn.innerText = 'Refresh Data';
        }
    }

    document.getElementById('refresh-dashboard').addEventListener('click', refreshData);

    window.addEventListener('load', initCharts);
</script>
@endpush
@endsection
