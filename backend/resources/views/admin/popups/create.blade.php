@extends('admin.layouts.admin')

@section('title', 'Create New Popup')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create New Popup</h3>
                    <a href="{{ route('admin.popups.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.popups.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Type -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('type') is-invalid @enderror" 
                                                            id="type" name="type" required>
                                                        <option value="">Select Type</option>
                                                        @foreach($types as $key => $label)
                                                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="priority" class="form-label">Priority</label>
                                                    <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                                           id="priority" name="priority" value="{{ old('priority', 0) }}" 
                                                           min="0" max="100">
                                                    <small class="form-text text-muted">Higher priority popups show first (0-100)</small>
                                                    @error('priority')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Content</label>
                                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                                      id="content" name="content" rows="4">{{ old('content') }}</textarea>
                                            <small class="form-text text-muted">HTML tags are allowed</small>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Image Upload -->
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                   id="image" name="image" accept="image/*">
                                            <small class="form-text text-muted">Max file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Video URL (for video popups) -->
                                        <div class="mb-3">
                                            <label for="video_url" class="form-label">Video URL</label>
                                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                                   id="video_url" name="video_url" value="{{ old('video_url') }}"
                                                   placeholder="https://www.youtube.com/watch?v=...">
                                            <small class="form-text text-muted">For video popups - YouTube, Vimeo, etc.</small>
                                            @error('video_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Button -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="button_text" class="form-label">Button Text</label>
                                                    <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                                           id="button_text" name="button_text" value="{{ old('button_text') }}"
                                                           placeholder="Learn More">
                                                    @error('button_text')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="button_url" class="form-label">Button URL</label>
                                                    <input type="url" class="form-control @error('button_url') is-invalid @enderror" 
                                                           id="button_url" name="button_url" value="{{ old('button_url') }}"
                                                           placeholder="https://example.com">
                                                    @error('button_url')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Styling -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Styling Options</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="background_color" class="form-label">Background Color</label>
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="background_color" name="background_color" 
                                                           value="{{ old('background_color', '#ffffff') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="text_color" class="form-label">Text Color</label>
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="text_color" name="text_color" 
                                                           value="{{ old('text_color', '#000000') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="border_color" class="form-label">Border Color</label>
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="border_color" name="border_color" 
                                                           value="{{ old('border_color', '#dddddd') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="border_radius" class="form-label">Border Radius (px)</label>
                                                    <input type="number" class="form-control" 
                                                           id="border_radius" name="border_radius" 
                                                           value="{{ old('border_radius', 10) }}" min="0" max="50">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings & Display -->
                            <div class="col-md-4">
                                <!-- Display Settings -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Display Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                            <select class="form-select @error('position') is-invalid @enderror" 
                                                    id="position" name="position" required>
                                                @foreach($positions as $key => $label)
                                                    <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('position')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="size" class="form-label">Size <span class="text-danger">*</span></label>
                                            <select class="form-select @error('size') is-invalid @enderror" 
                                                    id="size" name="size" required>
                                                @foreach($sizes as $key => $label)
                                                    <option value="{{ $key }}" {{ old('size') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="show_after" class="form-label">Show After (seconds)</label>
                                            <input type="number" class="form-control @error('show_after') is-invalid @enderror" 
                                                   id="show_after" name="show_after" value="{{ old('show_after', 3) }}" 
                                                   min="0" max="60">
                                            <small class="form-text text-muted">Delay before showing popup</small>
                                            @error('show_after')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="show_frequency" class="form-label">Show Frequency <span class="text-danger">*</span></label>
                                            <select class="form-select @error('show_frequency') is-invalid @enderror" 
                                                    id="show_frequency" name="show_frequency" required>
                                                @foreach($frequencies as $key => $label)
                                                    <option value="{{ $key }}" {{ old('show_frequency') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('show_frequency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="target_users" class="form-label">Target Users <span class="text-danger">*</span></label>
                                            <select class="form-select @error('target_users') is-invalid @enderror" 
                                                    id="target_users" name="target_users" required>
                                                @foreach($targetUsers as $key => $label)
                                                    <option value="{{ $key }}" {{ old('target_users') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('target_users')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" value="{{ old('start_date') }}">
                                            <small class="form-text text-muted">Leave empty to start immediately</small>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                                            <small class="form-text text-muted">Leave empty for no end date</small>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Inactive popups will not be displayed</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.popups.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Create Popup</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview image on upload
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add image preview here if needed
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    // Auto-generate button text based on type
    const typeSelect = document.getElementById('type');
    const buttonTextInput = document.getElementById('button_text');
    
    if (typeSelect && buttonTextInput) {
        typeSelect.addEventListener('change', function() {
            if (!buttonTextInput.value) {
                switch (this.value) {
                    case 'newsletter':
                        buttonTextInput.value = 'Subscribe';
                        break;
                    case 'promotion':
                        buttonTextInput.value = 'Get Offer';
                        break;
                    case 'video':
                        buttonTextInput.value = 'Watch Video';
                        break;
                    default:
                        buttonTextInput.value = 'Learn More';
                }
            }
        });
    }
});
</script>
@endsection