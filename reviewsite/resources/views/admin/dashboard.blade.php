@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_posts'] }}</div>
            <div class="stat-label">Total Posts</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['featured_posts'] }}</div>
            <div class="stat-label">Featured Posts</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div class="stat-card">
            <h3 style="color: #E53E3E; margin-bottom: 1rem; font-family: 'Share Tech Mono', monospace;">Quick Actions</h3>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="{{ route('admin.users') }}" class="btn btn-primary">Manage Users</a>
                <a href="{{ route('admin.posts') }}" class="btn btn-secondary">Manage Posts</a>
            </div>
        </div>

        <div class="stat-card">
            <h3 style="color: #E53E3E; margin-bottom: 1rem; font-family: 'Share Tech Mono', monospace;">Recent Activity</h3>
            <p style="color: #A0AEC0;">No recent activity to display.</p>
        </div>
    </div>
@endsection 
