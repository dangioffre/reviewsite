@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/manage-vods.css') }}">
@endpush

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.vod.manage-vods-header', ['streamerProfile' => $streamerProfile])

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
