@extends('layouts.app')

@section('title', 'View Popup - Admin Panel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">View Popup</h4>
            <p class="mb-0">Popup details and configuration</p>
        </div>
        <div>
            <a href="{{ route('admin.popups.edit', $popup) }}" class="btn btn-primary me-2">
                <i class="icon-base ti tabler-edit"></i>Edit
            </a>
            <a href="{{ route('admin.popups.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Popup Information</h5>
                </div>
                <div class="card-body">
                    <!-- Title -->
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Title:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $popup->title }}
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Type:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-info">{{ $popup->getTypes()[$popup->type] ?? $popup->type }}</span>
                        </div>
                    </div>

                    <!-- Content -->
                    @if($popup->content)
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Content:</strong>
                        </div>
                        <div class="col-sm-9">
                            <div class="border-start border-primary ps-3">
                                {!! nl2br(e($popup->content)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Video URL -->
                    @if($popup->video_url)
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Video URL:</strong>
                        </div>
                        <div class="col-sm-9">
                            <a href="{{ $popup->video_url }}" target="_blank" class="text-decoration-none">
                                {{ $popup->video_url }}
                                <i class="icon-base ti tabler-external-link ms-1"></i>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Button -->
                    @if($popup->button_text || $popup->button_url)
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Button:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($popup->button_url)
                                <a href="{{ $popup->button_url }}" class="btn btn-primary btn-sm" target="_blank">
                                    {{ $popup->button_text ?: 'Click Here' }}
                                    <i class="icon-base ti tabler-external-link ms-1"></i>
                                </a>
                            @else
                                <button class="btn btn-primary btn-sm" disabled>{{ $popup->button_text }}</button>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Priority -->
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Priority:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ $popup->priority >= 50 ? 'danger' : ($popup->priority >= 25 ? 'warning' : 'secondary') }}">
                                {{ $popup->priority ?? 0 }}
                            </span>
                            <small class="text-muted ms-2">(Higher numbers show first)</small>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $popup->created_at ? $popup->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <strong>Updated:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $popup->updated_at ? $popup->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Display Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <strong>Position:</strong>
                            <p class="mb-0">{{ $popup->getPositions()[$popup->position] ?? $popup->position }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <strong>Size:</strong>
                            <p class="mb-0">{{ $popup->getSizes()[$popup->size] ?? $popup->size }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <strong>Show After:</strong>
                            <p class="mb-0">{{ $popup->show_after ?? 3 }} seconds</p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <strong>Show Frequency:</strong>
                            <p class="mb-0">{{ $popup->getFrequencies()[$popup->show_frequency] ?? $popup->show_frequency }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <strong>Target Users:</strong>
                            <p class="mb-0">{{ $popup->getTargetUsers()[$popup->target_users] ?? $popup->target_users }}</p>
                        </div>

                        @if($popup->show_on_pages && count($popup->show_on_pages) > 0)
                        <div class="col-md-6 mb-4">
                            <strong>Show on Pages:</strong>
                            <div class="mt-1">
                                @foreach($popup->show_on_pages as $page)
                                    <span class="badge bg-secondary me-1">{{ $page }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            @if($popup->start_date || $popup->end_date)
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Scheduling</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($popup->start_date)
                        <div class="col-md-6 mb-4">
                            <strong>Start Date:</strong>
                            <p class="mb-0">{{ $popup->start_date->format('M d, Y \\a\\t H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($popup->end_date)
                        <div class="col-md-6 mb-4">
                            <strong>End Date:</strong>
                            <p class="mb-0">{{ $popup->end_date->format('M d, Y \\a\\t H:i') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($popup->start_date || $popup->end_date)
                    <div class="alert alert-info">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <strong>Status:</strong>
                        @php
                            $now = now();
                            $shouldShow = true;
                            
                            if ($popup->start_date && $popup->start_date > $now) {
                                $shouldShow = false;
                                $message = 'Scheduled to start on ' . $popup->start_date->format('M d, Y \\a\\t H:i');
                            } elseif ($popup->end_date && $popup->end_date < $now) {
                                $shouldShow = false;
                                $message = 'Ended on ' . $popup->end_date->format('M d, Y \\a\\t H:i');
                            } else {
                                $message = 'Currently active';
                            }
                        @endphp
                        {{ $message }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Custom Styles -->
            @if($popup->styles && count($popup->styles) > 0)
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Custom Styles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($popup->styles as $property => $value)
                            <div class="col-md-6 mb-3">
                                <strong>{{ ucwords(str_replace(['-', '_'], ' ', $property)) }}:</strong>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $value }}</span>
                                    @if(in_array($property, ['background-color', 'color', 'border-color']))
                                        <div class="border rounded" style="width: 20px; height: 20px; background-color: {{ $value }};"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-3">
                        <strong>CSS Preview:</strong>
                        <code class="d-block mt-2 p-2 bg-light rounded">{{ $popup->css_styles }}</code>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <!-- Status & Options -->
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Status & Options</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <strong>Status:</strong>
                            <br>
                            <span class="badge bg-{{ $popup->is_active ? 'success' : 'danger' }} mt-1">
                                {{ $popup->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image -->
            @if($popup->image)
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Popup Image</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $popup->image_url }}" alt="Popup Image" 
                         class="img-fluid rounded border" 
                         style="max-height: 300px; cursor: pointer;"
                         onclick="openImageModal('{{ $popup->image_url }}')">
                </div>
            </div>
            @endif

            <!-- Preview -->
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-info" onclick="previewPopup()">
                            <i class="icon-base ti tabler-eye me-2"></i>Preview Popup
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">Preview how this popup will appear on your website</small>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.popups.edit', $popup) }}" class="btn btn-primary">
                            <i class="icon-base ti tabler-edit"></i>Edit Popup
                        </a>
                        
                        <form method="POST" action="{{ route('admin.popups.toggle-status', $popup) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $popup->is_active ? 'warning' : 'success' }} w-100">
                                <i class="icon-base ti tabler-toggle-{{ $popup->is_active ? 'left' : 'right' }} me-2"></i>
                                {{ $popup->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        <hr class="my-3">

                        <form method="POST" action="{{ route('admin.popups.destroy', $popup) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this popup? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="icon-base ti tabler-trash"></i>Delete Popup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
@if($popup->image)
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Popup Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Popup Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endif

<!-- Popup Preview Modal -->
<div class="modal fade" id="popupPreviewModal" tabindex="-1" aria-labelledby="popupPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $popup->size == 'large' ? 'modal-lg' : ($popup->size == 'extra-large' ? 'modal-xl' : '') }}">
        <div class="modal-content" style="{{ $popup->css_styles }}">
            <div class="modal-header">
                <h5 class="modal-title" id="popupPreviewModalLabel">{{ $popup->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($popup->image)
                    <div class="text-center mb-3">
                        <img src="{{ $popup->image_url }}" alt="Popup Image" class="img-fluid" style="max-height: 200px;">
                    </div>
                @endif
                
                @if($popup->video_url)
                    <div class="text-center mb-3">
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ $popup->video_url }}" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif
                
                @if($popup->content)
                    <div class="mb-3">
                        {!! nl2br(e($popup->content)) !!}
                    </div>
                @endif
                
                @if($popup->button_text && $popup->button_url)
                    <div class="text-center">
                        <a href="{{ $popup->button_url }}" class="btn btn-primary" target="_blank">
                            {{ $popup->button_text }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImageModal(imageSrc) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}

function previewPopup() {
    $('#popupPreviewModal').modal('show');
}
</script>
@endpush
@endsection