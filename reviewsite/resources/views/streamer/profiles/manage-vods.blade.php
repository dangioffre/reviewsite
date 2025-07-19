@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Manage VODs</h1>
                <div>
                    <a href="{{ route('streamer.profile.show', $streamerProfile) }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Profile
                    </a>
                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#addVodModal">
                        <i class="fas fa-plus mr-1"></i>
                        Add Manual VOD
                    </button>
                    <form method="POST" action="{{ route('streamer.profile.import-vods', $streamerProfile) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success mr-2">
                            <i class="fas fa-download mr-1"></i>
                            Import from {{ ucfirst($streamerProfile->platform) }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('streamer.profile.check-vod-health', $streamerProfile) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-heartbeat mr-1"></i>
                            Check Health
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- VODs List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-video mr-2"></i>
                        Your VODs ({{ $streamerProfile->vods->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($streamerProfile->vods->count() > 0)
                        <div class="row">
                            @foreach($streamerProfile->vods as $vod)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        @if($vod->thumbnail_url)
                                            <img src="{{ $vod->thumbnail_url }}" class="card-img-top" alt="{{ $vod->title }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-video fa-3x text-white"></i>
                                            </div>
                                        @endif
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">{{ Str::limit($vod->title, 50) }}</h6>
                                            @if($vod->description)
                                                <p class="card-text small text-muted">{{ Str::limit($vod->description, 80) }}</p>
                                            @endif
                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        @if($vod->is_manual)
                                                            <span class="badge badge-info">Manual</span>
                                                        @else
                                                            <span class="badge badge-success">Imported</span>
                                                        @endif
                                                        @if($vod->health_status === 'healthy')
                                                            <span class="badge badge-success" title="VOD link is working">
                                                                <i class="fas fa-check-circle"></i> Healthy
                                                            </span>
                                                        @elseif($vod->health_status === 'unhealthy')
                                                            <span class="badge badge-danger" title="{{ $vod->health_check_error }}">
                                                                <i class="fas fa-exclamation-triangle"></i> Broken
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary" title="Health status not checked yet">
                                                                <i class="fas fa-question-circle"></i> Unchecked
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($vod->formatted_duration)
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $vod->formatted_duration }}
                                                        </small>
                                                    @endif
                                                </div>
                                                @if($vod->published_at)
                                                    <small class="text-muted d-block mb-2">
                                                        Published: {{ $vod->published_at->format('M j, Y') }}
                                                    </small>
                                                @endif
                                                <div class="btn-group w-100" role="group">
                                                    <a href="{{ $vod->vod_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt mr-1"></i>
                                                        View
                                                    </a>
                                                    <form method="POST" action="{{ route('streamer.profile.delete-vod', [$streamerProfile, $vod]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this VOD?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash mr-1"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-video fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No VODs Found</h4>
                            <p class="text-muted">Add your first VOD manually or import from {{ ucfirst($streamerProfile->platform) }}.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add VOD Modal -->
<div class="modal fade" id="addVodModal" tabindex="-1" role="dialog" aria-labelledby="addVodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('streamer.profile.add-vod', $streamerProfile) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addVodModalLabel">Add Manual VOD</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required maxlength="500">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="vod_url">VOD URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('vod_url') is-invalid @enderror" 
                               id="vod_url" name="vod_url" value="{{ old('vod_url') }}" required maxlength="500"
                               placeholder="https://...">
                        @error('vod_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="thumbnail_url">Thumbnail URL</label>
                        <input type="url" class="form-control @error('thumbnail_url') is-invalid @enderror" 
                               id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url') }}" maxlength="500"
                               placeholder="https://...">
                        @error('thumbnail_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add VOD</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-show modal if there are validation errors
    @if($errors->any())
        $('#addVodModal').modal('show');
    @endif
});
</script>
@endpush

@push('styles')
<style>
.card-img-top {
    transition: transform 0.2s;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.btn-group .btn {
    flex: 1;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-success {
    background-color: #28a745;
}
</style>
@endpush