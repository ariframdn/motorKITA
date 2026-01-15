<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MotorKita') - Sistem Manajemen Bengkel</title>
    
    @auth
    <script>
        window.userId = {{ auth()->id() }};
    </script>
    @endauth
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e40af 0%, #2563eb 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.15);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        
        .table {
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3 d-flex flex-column">
                <div class="mb-4">
                    <h4 class="text-white mb-0">
                        <i class="bi bi-bicycle"></i> MotorKita
                    </h4>
                    <small class="text-white-50">Bengkel Motor</small>
                </div>

                <nav class="nav flex-column">
                    @yield('sidebar')
                </nav>

                @auth
                <div class="mt-auto pt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link text-white w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Notification Bell -->
                @auth
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                        <small class="text-muted">Selamat datang, {{ auth()->user()->name }}</small>
                    </div>
                    <div class="position-relative">
                        <button class="btn btn-light position-relative" id="notificationBell" onclick="toggleNotifications()">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
                                0
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" id="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto; display: none;">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="mb-0">Notifikasi</h6>
                                <button class="btn btn-sm btn-link text-decoration-none" onclick="markAllAsRead()">Tandai semua dibaca</button>
                            </div>
                            <div id="notificationsList">
                                <div class="text-center p-3 text-muted">
                                    <i class="bi bi-hourglass-split"></i> Memuat...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Notification Scripts -->
    @auth
    <script>
        let notificationDropdownOpen = false;
        
        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            updateNotificationCount();
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('notificationDropdown');
                const bell = document.getElementById('notificationBell');
                if (!dropdown.contains(event.target) && !bell.contains(event.target)) {
                    dropdown.style.display = 'none';
                    notificationDropdownOpen = false;
                }
            });
        });
        
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (!notificationDropdownOpen) {
                dropdown.style.display = 'block';
                notificationDropdownOpen = true;
                loadNotifications();
            } else {
                dropdown.style.display = 'none';
                notificationDropdownOpen = false;
            }
        }
        
        function loadNotifications() {
            fetch('{{ route("notifications.index") }}')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('notificationsList');
                    if (data.data && data.data.length > 0) {
                        list.innerHTML = data.data.map(notif => `
                            <div class="p-3 border-bottom ${notif.is_read ? '' : 'bg-light'}" onclick="markAsRead(${notif.id})" style="cursor: pointer;">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1">
                                        <strong>${notif.title}</strong>
                                        <p class="mb-1 text-muted small">${notif.message}</p>
                                        <small class="text-muted">${new Date(notif.created_at).toLocaleString('id-ID')}</small>
                                    </div>
                                    ${notif.is_read ? '' : '<span class="badge bg-primary">Baru</span>'}
                                </div>
                            </div>
                        `).join('');
                    } else {
                        list.innerHTML = '<div class="text-center p-3 text-muted">Tidak ada notifikasi</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }
        
        function updateNotificationCount() {
            fetch('{{ route("notifications.count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error updating count:', error);
                });
        }
        
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(() => {
                updateNotificationCount();
                loadNotifications();
            });
        }
        
        function markAllAsRead() {
            fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(() => {
                updateNotificationCount();
                loadNotifications();
            });
        }
        
        // Global functions for Echo event handlers
        window.updateNotificationCount = updateNotificationCount;
        
        window.showNotificationToast = function(data) {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <strong>${data.title}</strong><br>
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        };
    </script>
    @endauth

    <!-- PAGE SPECIFIC SCRIPTS (INI KUNCI UTAMA) -->
    @yield('scripts')

</body>
</html>
