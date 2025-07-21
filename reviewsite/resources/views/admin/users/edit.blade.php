@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3 style="color: #E53E3E; font-family: 'Share Tech Mono', monospace;">Edit User: {{ $user->name }}</h3>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">Back to Users</a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <span style="color: #E53E3E; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span style="color: #E53E3E; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" class="form-input">
            @error('password')
                <span style="color: #E53E3E; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input">
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} style="width: auto;">
                <span>Admin User</span>
            </label>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection 
