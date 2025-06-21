<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - ReviewSite Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0F0F0F;
            color: #FFFFFF;
            line-height: 1.6;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #1A1A1A;
            border-right: 2px solid #E53E3E;
            padding: 2rem 0;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #333;
            margin-bottom: 2rem;
        }

        .sidebar-header h1 {
            font-family: 'Share Tech Mono', monospace;
            color: #E53E3E;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #FFFFFF;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #2A2A2A;
            border-left-color: #E53E3E;
            color: #E53E3E;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background: #0F0F0F;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
        }

        .header h2 {
            font-family: 'Share Tech Mono', monospace;
            color: #E53E3E;
            font-size: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: #FFFFFF;
        }

        .logout-btn {
            background: #E53E3E;
            color: #FFFFFF;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Share Tech Mono', monospace;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #C53030;
        }

        .content {
            background: #1A1A1A;
            border-radius: 8px;
            padding: 2rem;
            border: 1px solid #333;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-family: 'Share Tech Mono', monospace;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #E53E3E;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #C53030;
        }

        .btn-secondary {
            background: #4A5568;
            color: #FFFFFF;
        }

        .btn-secondary:hover {
            background: #2D3748;
        }

        .btn-danger {
            background: #E53E3E;
            color: #FFFFFF;
        }

        .btn-danger:hover {
            background: #C53030;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .table th {
            background: #2A2A2A;
            font-family: 'Share Tech Mono', monospace;
            color: #E53E3E;
        }

        .table tr:hover {
            background: #2A2A2A;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #FFFFFF;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 4px;
            background: #2A2A2A;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #E53E3E;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #22543D;
            color: #9AE6B4;
            border: 1px solid #38A169;
        }

        .alert-error {
            background: #742A2A;
            color: #FEB2B2;
            border: 1px solid #E53E3E;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #2A2A2A;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #333;
        }

        .stat-number {
            font-family: 'Share Tech Mono', monospace;
            font-size: 2rem;
            color: #E53E3E;
            font-weight: 700;
        }

        .stat-label {
            color: #A0AEC0;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>ADMIN PANEL</h1>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.posts') }}" class="nav-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                            Post Management
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h2>@yield('title', 'Dashboard')</h2>
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <a href="{{ route('logout') }}" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html> 