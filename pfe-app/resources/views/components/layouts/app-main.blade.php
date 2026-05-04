<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activio — {{ $title ?? 'Home' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    @stack('styles')
</head>

<body class="min-h-screen flex flex-col" style="background:#F7F5F0">

    <x-navbar />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-footer />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/helpers.js') }}"></script>
    @auth
        <script>
            async function loadNotifications() {
                try {
                    const res = await fetch('/notifications', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    const badge = document.getElementById('notif-badge');
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }

                    const list = document.getElementById('notif-list');
                    if (!data.notifications.length) {
                        list.innerHTML = '<div class="notif-empty">No notifications yet</div>';
                        return;
                    }

                    const icons = { success: '✓', warning: '✕', info: '!' };
                    list.innerHTML = data.notifications.map(n => `
                    <div class="notif-item ${!n.is_read ? 'unread' : ''}">
                        <div class="notif-icon ${n.type}" style="font-weight:700;color:${n.type === 'success' ? '#0F6E56' : n.type === 'warning' ? '#e05252' : '#D4A350'}">
                            ${icons[n.type] || '!'}
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="notif-title">${n.title}</div>
                            <div class="notif-msg">${n.message}</div>
                            <div class="notif-time">${timeAgo(n.created_at)}</div>
                        </div>
                        ${!n.is_read ? '<div class="notif-unread-dot"></div>' : ''}
                    </div>
                `).join('');
                } catch (e) { }
            }

            function toggleNotifDropdown() {
                const dd = document.getElementById('notif-dropdown');
                const isOpen = dd.classList.toggle('open');
                if (isOpen) loadNotifications();
            }

            async function markAllRead() {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                document.getElementById('notif-badge').style.display = 'none';
                document.querySelectorAll('.notif-item.unread').forEach(el => {
                    el.classList.remove('unread');
                    el.querySelector('.notif-unread-dot')?.remove();
                });
            }

            document.addEventListener('click', e => {
                if (!e.target.closest('#notif-wrapper')) {
                    document.getElementById('notif-dropdown')?.classList.remove('open');
                }
            });

            function timeAgo(d) {
                const diff = Math.floor((Date.now() - new Date(d)) / 1000);
                if (diff < 60) return 'Just now';
                if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
                if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
                return Math.floor(diff / 86400) + 'd ago';
            }

            loadNotifications();
            setInterval(loadNotifications, 30000);
        </script>
    @endauth
    @stack('scripts')
</body>

</html>
