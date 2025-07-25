@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/manage-vods.css') }}">
<style>
    .manage-vods-container {
        background: linear-gradient(135deg, #151515 0%, #1A1A1B 50%, #27272A 100%);
        min-height: 100vh;
    }
    
    .glass-card {
        background: rgba(39, 39, 42, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(63, 63, 70, 0.3);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        border-color: rgba(229, 62, 62, 0.3);
        transform: translateY(-2px);
    }
    
    .section-header {
        background: linear-gradient(135deg, rgba(229, 62, 62, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        border-left: 4px solid #E53E3E;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .action-button {
        background: linear-gradient(135deg, #E53E3E 0%, #DC2626 100%);
        border: 1px solid rgba(229, 62, 62, 0.3);
        transition: all 0.3s ease;
    }
    
    .action-button:hover {
        box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
        border-color: rgba(229, 62, 62, 0.5);
        transform: translateY(-1px);
    }
    
    .action-button.secondary {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        border: 1px solid rgba(37, 99, 235, 0.3);
    }
    
    .action-button.secondary:hover {
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        border-color: rgba(37, 99, 235, 0.5);
    }
    
    .vod-card {
        background: rgba(26, 26, 27, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(63, 63, 70, 0.3);
        transition: all 0.3s ease;
    }
    
    .vod-card:hover {
        border-color: rgba(229, 62, 62, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .vod-thumbnail {
        position: relative;
        overflow: hidden;
    }
    
    .vod-thumbnail img {
        transition: transform 0.3s ease;
    }
    
    .vod-card:hover .vod-thumbnail img {
        transform: scale(1.05);
    }
    
    .management-nav {
        background: rgba(39, 39, 42, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(63, 63, 70, 0.3);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .nav-link {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .nav-link.active {
        background: linear-gradient(135deg, #E53E3E 0%, #DC2626 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
    }
    
    .nav-link:not(.active) {
        background: rgba(63, 63, 70, 0.5);
        color: #A1A1AA;
    }
    
    .nav-link:not(.active):hover {
        background: rgba(82, 82, 91, 0.8);
        color: white;
    }
    
    .nav-link.view-profile {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: white;
    }
    
    .nav-link.view-profile:hover {
        background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
    }
</style>
@endpush

@section('content')
<div class="manage-vods-container py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.streamer.management-nav', ['streamerProfile' => $streamerProfile])

        @include('components.vod.manage-vods-notifications')

        @include('components.vod.manage-vods-grid', ['streamerProfile' => $streamerProfile])
    </div>
</div>

@include('components.vod.add-vod-modal', ['streamerProfile' => $streamerProfile])

@include('components.vod.twitch-embed-modal')

@include('components.vod.kick-embed-modal')

@include('components.vod.delete-vod-modal')
@endsection

@push('scripts')
<script src="{{ asset('js/manage-vods.js') }}"></script>
@if($errors->any())
<script>
// Auto-show modal if there are validation errors
document.addEventListener('DOMContentLoaded', function() {
    if (typeof showModalOnErrors === 'function') {
        showModalOnErrors();
    }
});
</script>
@endif
@endpush
