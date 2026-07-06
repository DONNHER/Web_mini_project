<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PIL - Point of Sale and Lending System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#FEF6F0] text-[#1A1A1A]">
    <div class="min-h-screen">
        @include('partials.navigation')

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white/50 backdrop-blur-sm border-b border-[#FFEDD5]">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Real-time Notification Component -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] space-y-3"></div>

    @auth
    <script>
        async function fetchNotifications() {
            try {
                const response = await fetch('{{ route("notifications.unread") }}');
                const data = await response.json();
                const countBadge = document.getElementById('notification-count');
                const listContainer = document.getElementById('notification-list');

                if (data.unread_count > 0) {
                    countBadge.innerText = data.unread_count;
                    countBadge.classList.remove('hidden');
                } else {
                    countBadge.classList.add('hidden');
                }

                // If we have notification data, we could render it here
                if (data.notifications && data.notifications.length > 0) {
                    listContainer.innerHTML = data.notifications.map(n => `
                        <div class="p-4 border-b border-[#FFEDD5] hover:bg-[#FEF6F0] transition">
                            <p class="text-[10px] font-bold text-[#1A1A1A]">${n.message || 'Notification'}</p>
                            <p class="text-[8px] text-[#1A1A1A]/40 mt-1">${n.time || ''}</p>
                        </div>
                    `).join('');
                } else {
                    listContainer.innerHTML = '<p class="p-8 text-center text-[#1A1A1A]/30 text-[10px] font-bold uppercase tracking-widest italic">No new events</p>';
                }
            } catch (error) { console.error(error); }
        }

        async function markAllNotificationsRead() {
            try {
                const response = await fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                if (response.ok) {
                    document.getElementById('notification-count').classList.add('hidden');
                    document.getElementById('notification-list').innerHTML = '<p class="p-8 text-center text-[#1A1A1A]/30 text-[10px] font-bold uppercase tracking-widest italic">No new events</p>';
                }
            } catch (error) { console.error(error); }
        }

        setInterval(fetchNotifications, 30000);
        window.addEventListener('load', fetchNotifications);

        function showToast(title, message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `bg-white p-6 rounded-2xl shadow-2xl border-l-4 border-[#FF6B00] transform translate-y-10 opacity-0 transition-all duration-500 min-w-[320px]`;
            toast.innerHTML = `
                <div class="flex-1">
                    <p class="text-[#FF6B00] font-extrabold text-sm uppercase tracking-widest">${title}</p>
                    <p class="text-[#1A1A1A] text-xs mt-1 font-medium">${message}</p>
                </div>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 100);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }
    </script>
    @endauth
    @stack('scripts')
</body>
</html>
