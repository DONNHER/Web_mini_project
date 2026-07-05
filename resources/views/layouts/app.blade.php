<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    @php
        $branding = \App\Models\Setting::get('branding', ['theme_color' => '#C06C3E']);
    @endphp
    <style>
        :root {
            --brand-color: {{ $branding['theme_color'] }};
        }
        .bg-brand { background-color: var(--brand-color) !important; }
        .text-brand { color: var(--brand-color) !important; }
        body { background-color: var(--brand-color) !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-brand text-black">
    @if(session('impersonator_id'))
        <div class="bg-black text-brand p-2 text-center text-[10px] font-black uppercase tracking-[0.5em] flex justify-center items-center">
            <span>Critical Mode: Impersonating {{ auth()->user()->name }}</span>
            <a href="{{ route('admin.users.stop-impersonating') }}" class="ml-4 bg-brand text-black px-4 py-1 rounded-full no-underline hover:opacity-80 transition">Exit Session</a>
        </div>
    @endif
    <div class="min-h-screen bg-brand">
        @include('partials.navigation')

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-black/10 shadow-sm border-b border-black/10">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Page Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Real-time Notification Component -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] space-y-3"></div>

    @auth
    <script>
        // Session Expiration Warning
        let sessionLifetime = {{ config('session.lifetime') }} * 60;
        let warningTime = 300;

        let countdown = sessionLifetime - warningTime;

        setTimeout(function() {
            showToast('Session Expiring', 'Your session will expire in 5 minutes.', 'warning');
        }, countdown * 1000);

        async function fetchNotifications() {
            try {
                const response = await fetch('{{ route("notifications.unread") }}');
                const data = await response.json();

                const countBadge = document.getElementById('notification-count');
                const list = document.getElementById('notification-list');

                if (data.unread_count > 0) {
                    countBadge.innerText = data.unread_count;
                    countBadge.classList.remove('hidden');

                    list.innerHTML = data.notifications.map(n => `
                        <div class="p-4 border-b border-black/10 hover:bg-black/5 transition">
                            <p class="text-black text-xs font-bold">${n.title}</p>
                            <p class="text-black/70 text-[10px] mt-1 line-clamp-2">${n.message}</p>
                            <span class="text-[8px] text-black/50 font-bold uppercase mt-1 block">${n.time}</span>
                        </div>
                    `).join('');
                } else {
                    countBadge.classList.add('hidden');
                    list.innerHTML = '<p class="p-4 text-center text-black/50 text-xs italic">No new notifications</p>';
                }
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        }

        function showToast(title, message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = 'bg-black';
            const textColor = 'text-brand';

            toast.className = `${bgColor} p-4 rounded-lg shadow-2xl flex items-start space-x-3 transform translate-y-10 opacity-0 transition-all duration-500 min-w-[300px]`;
            toast.innerHTML = `
                <div class="flex-1">
                    <p class="${textColor} font-bold text-sm uppercase tracking-wider">${title}</p>
                    <p class="${textColor} text-xs mt-1 opacity-80">${message}</p>
                </div>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 100);

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        async function markAllNotificationsRead() {
            try {
                await fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                fetchNotifications();
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        }

        setInterval(fetchNotifications, 30000);
        window.addEventListener('load', fetchNotifications);

        // --- 12. UX ENHANCEMENTS ---

        // 1. Loading Spinners on Submit (Requirement 12.2)
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    const spinner = btn.querySelector('.loading-spinner');
                    if (spinner) spinner.classList.remove('hidden');
                }
            });
        });

        // 2. Unsaved Changes Warning (Requirement 12.4)
        let isDirty = false;
        document.querySelectorAll('.dirty-check').forEach(form => {
            form.addEventListener('input', () => isDirty = true);
        });

        window.addEventListener('beforeunload', function (e) {
            if (isDirty) {
                const forms = document.querySelectorAll('.dirty-check');
                let formStillDirty = false;
                forms.forEach(f => { if(f.classList.contains('dirty-check')) formStillDirty = true; });

                if (formStillDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            }
        });

        // 3. Inline Validation on Blur (Requirement 12.1)
        document.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.classList.add('border-red-600', 'ring-2', 'ring-red-600');
                } else {
                    this.classList.remove('border-red-600', 'ring-2', 'ring-red-600');
                }
            });
        });
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
