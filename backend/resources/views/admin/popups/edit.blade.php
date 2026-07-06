@extends('layouts.app')

@section('title', 'Edit Popup - Admin Panel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Edit Popup</h4>
            <p class="mb-0">Update popup content and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.popups.show', $popup) }}" class="btn btn-info me-2">
                <i class="icon-base ti tabler-eye me-1"></i>View Popup
            </a>
            <a href="{{ route('admin.popups.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Popups
            </a>
        </div>
    </div>

    <form action="{{ route('admin.popups.update', $popup) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-12 col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Popup Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="title" class="form-label">Popup Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $popup->title) }}" required>
                                <div id="title-counter" class="form-text">
                                    <span id="title-count">{{ strlen($popup->title ?? '') }}</span>/255 characters
                                </div>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label">Popup Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $key => $value)
                                        <option value="{{ $key }}" {{ old('type', $popup->type) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">Popup Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="6" 
                                      placeholder="Enter the popup content/message">{{ old('content', $popup->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="button_text" class="form-label">Button Text</label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                       id="button_text" name="button_text" 
                                       value="{{ old('button_text', $popup->button_text) }}" 
                                       placeholder="e.g., Learn More">
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="button_url" class="form-label">Button URL</label>
                                <input type="url" class="form-control @error('button_url') is-invalid @enderror" 
                                       id="button_url" name="button_url" 
                                       value="{{ old('button_url', $popup->button_url) }}" 
                                       placeholder="https://example.com">
                                @error('button_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="video_url" class="form-label">Video URL</label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" name="video_url" 
                                   value="{{ old('video_url', $popup->video_url) }}" 
                                   placeholder="https://youtube.com/watch?v=...">
                            <div class="form-text">For video popups, embed URL (YouTube, Vimeo, etc.)</div>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <div class="col-md-4 mb-4">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <select class="form-select @error('position') is-invalid @enderror" 
                                        id="position" name="position" required>
                                    @foreach($positions as $key => $value)
                                        <option value="{{ $key }}" {{ old('position', $popup->position) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="size" class="form-label">Size <span class="text-danger">*</span></label>
                                <select class="form-select @error('size') is-invalid @enderror" 
                                        id="size" name="size" required>
                                    @foreach($sizes as $key => $value)
                                        <option value="{{ $key }}" {{ old('size', $popup->size) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="show_after" class="form-label">Show After (seconds)</label>
                                <input type="number" class="form-control @error('show_after') is-invalid @enderror" 
                                       id="show_after" name="show_after" min="0" 
                                       value="{{ old('show_after', $popup->show_after ?? 3) }}">
                                <div class="form-text">Delay before showing popup</div>
                                @error('show_after')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="show_frequency" class="form-label">Show Frequency <span class="text-danger">*</span></label>
                                <select class="form-select @error('show_frequency') is-invalid @enderror" 
                                        id="show_frequency" name="show_frequency" required>
                                    @foreach($frequencies as $key => $value)
                                        <option value="{{ $key }}" {{ old('show_frequency', $popup->show_frequency) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('show_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="target_users" class="form-label">Target Users <span class="text-danger">*</span></label>
                                <select class="form-select @error('target_users') is-invalid @enderror" 
                                        id="target_users" name="target_users" required>
                                    @foreach($targetUsers as $key => $value)
                                        <option value="{{ $key }}" {{ old('target_users', $popup->target_users) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="show_on_pages" class="form-label">Show on Pages</label>
                            <div id="pages-container">
                                @if($popup->show_on_pages && count($popup->show_on_pages) > 0)
                                    @foreach($popup->show_on_pages as $index => $page)
                                        <div class="input-group mb-2 page-item">
                                            <select name="show_on_pages[]" class="form-select">
                                                <option value="">Select Page</option>
                                                <option value="all" {{ $page == 'all' ? 'selected' : '' }}>All Pages</option>
                                                <option value="home" {{ $page == 'home' ? 'selected' : '' }}>Home Page</option>
                                                <option value="about" {{ $page == 'about' ? 'selected' : '' }}>About Page</option>
                                                <option value="contact" {{ $page == 'contact' ? 'selected' : '' }}>Contact Page</option>
                                                <option value="blog" {{ $page == 'blog' ? 'selected' : '' }}>Blog Pages</option>
                                                <option value="products" {{ $page == 'products' ? 'selected' : '' }}>Product Pages</option>
                                                <option value="solutions" {{ $page == 'solutions' ? 'selected' : '' }}>Solutions Pages</option>
                                                <option value="software" {{ $page == 'software' ? 'selected' : '' }}>Software Pages</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-danger remove-page">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 page-item">
                                        <select name="show_on_pages[]" class="form-select">
                                            <option value="">Select Page</option>
                                            <option value="all">All Pages</option>
                                            <option value="home">Home Page</option>
                                            <option value="about">About Page</option>
                                            <option value="contact">Contact Page</option>
                                            <option value="blog">Blog Pages</option>
                                            <option value="products">Product Pages</option>
                                            <option value="solutions">Solutions Pages</option>
                                            <option value="software">Software Pages</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger remove-page">
                                            <i class="icon-base ti tabler-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-page">
                                <i class="icon-base ti tabler-plus me-1"></i>Add Page
                            </button>
                            <div class="form-text">Select specific pages where this popup should appear</div>
                            @error('show_on_pages')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Scheduling -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Scheduling</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $popup->start_date ? $popup->start_date->format('Y-m-d\TH:i') : '') }}">
                                <div class="form-text">Leave blank to start immediately</div>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $popup->end_date ? $popup->end_date->format('Y-m-d\TH:i') : '') }}">
                                <div class="form-text">Leave blank for no end date</div>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Styles -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Custom Styles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="background_color" class="form-label">Background Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('background_color') is-invalid @enderror" 
                                           id="background_color" name="background_color" 
                                           value="{{ old('background_color', $popup->styles['background-color'] ?? '#ffffff') }}">
                                    <input type="text" class="form-control" 
                                           value="{{ old('background_color', $popup->styles['background-color'] ?? '#ffffff') }}" 
                                           readonly id="background_color_text">
                                </div>
                                @error('background_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="text_color" class="form-label">Text Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('text_color') is-invalid @enderror" 
                                           id="text_color" name="text_color" 
                                           value="{{ old('text_color', $popup->styles['color'] ?? '#333333') }}">
                                    <input type="text" class="form-control" 
                                           value="{{ old('text_color', $popup->styles['color'] ?? '#333333') }}" 
                                           readonly id="text_color_text">
                                </div>
                                @error('text_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="border_color" class="form-label">Border Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('border_color') is-invalid @enderror" 
                                           id="border_color" name="border_color" 
                                           value="{{ old('border_color', $popup->styles['border-color'] ?? '#dddddd') }}">
                                    <input type="text" class="form-control" 
                                           value="{{ old('border_color', $popup->styles['border-color'] ?? '#dddddd') }}" 
                                           readonly id="border_color_text">
                                </div>
                                @error('border_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="border_radius" class="form-label">Border Radius</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('border_radius') is-invalid @enderror" 
                                           id="border_radius" name="border_radius" min="0" max="50" 
                                           value="{{ old('border_radius', isset($popup->styles['border-radius']) ? (int)str_replace('px', '', $popup->styles['border-radius']) : 8) }}">
                                    <span class="input-group-text">px</span>
                                </div>
                                @error('border_radius')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-12 col-lg-4">
                <!-- Publishing Options -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $popup->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Active Status</strong>
                                <div class="form-text">Make this popup visible to visitors</div>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label for="priority" class="form-label">Priority</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                   id="priority" name="priority" min="0" max="100" 
                                   value="{{ old('priority', $popup->priority ?? 0) }}">
                            <div class="form-text">Higher numbers show first (0-100)</div>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Popup Image -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Popup Image</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($popup->image)
                                <img id="image-preview" src="{{ $popup->image_url }}" alt="Popup Image" class="img-fluid rounded border" style="max-height: 200px;">
                            @else
                                <div id="image-placeholder" class="bg-light rounded d-flex align-items-center justify-content-center border" style="height: 200px;">
                                    <div class="text-center">
                                        <i class="icon-base ti tabler-photo icon-lg text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No image selected</p>
                                    </div>
                                </div>
                                <img id="image-preview" src="" alt="Popup Image" class="img-fluid rounded border d-none" style="max-height: 200px;">
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            @error('image')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($popup->image)
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
                                <i class="icon-base ti tabler-trash me-1"></i>Remove Current
                            </button>
                        </div>
                        @endif
                        <small class="text-muted">Recommended size: 400x300 pixels</small>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ti tabler-check me-1"></i>Update Popup
                            </button>
                            <a href="{{ route('admin.popups.show', $popup) }}" class="btn btn-outline-info">
                                <i class="icon-base ti tabler-eye me-1"></i>Preview Popup
                            </a>
                            <hr class="my-3">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePopupModal">
                                <i class="icon-base ti tabler-trash me-1"></i>Delete Popup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Popup Modal -->
<div class="modal fade" id="deletePopupModal" tabindex="-1" aria-labelledby="deletePopupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deletePopupModalLabel">Delete Popup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the popup <strong>{{ $popup->title }}</strong>?</p>
        <p class="text-danger">This action cannot be undone and will permanently delete:</p>
        <ul class="text-danger">
          <li>Popup content and configuration</li>
          <li>Display and targeting settings</li>
          <li>Custom styles and images</li>
          <li>All scheduling information</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.popups.destroy', $popup) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete Popup</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Character counters
    function initCharacterCounters() {
        $('#title').on('input', function() {
            const count = $(this).val().length;
            $('#title-count').text(count);
        });
    }

    // Initialize character counters
    initCharacterCounters();

    // Image preview
    $('#image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('d-none');
                $('#image-placeholder').addClass('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    // Color picker syncing
    $('#background_color').on('input', function() {
        $('#background_color_text').val($(this).val());
    });

    $('#text_color').on('input', function() {
        $('#text_color_text').val($(this).val());
    });

    $('#border_color').on('input', function() {
        $('#border_color_text').val($(this).val());
    });

    // Add page
    $('#add-page').on('click', function() {
        const pageHtml = `
            <div class="input-group mb-2 page-item">
                <select name="show_on_pages[]" class="form-select">
                    <option value="">Select Page</option>
                    <option value="all">All Pages</option>
                    <option value="home">Home Page</option>
                    <option value="about">About Page</option>
                    <option value="contact">Contact Page</option>
                    <option value="blog">Blog Pages</option>
                    <option value="products">Product Pages</option>
                    <option value="solutions">Solutions Pages</option>
                    <option value="software">Software Pages</option>
                </select>
                <button type="button" class="btn btn-outline-danger remove-page">
                    <i class="icon-base ti tabler-x"></i>
                </button>
            </div>
        `;
        $('#pages-container').append(pageHtml);
    });

    // Remove page
    $(document).on('click', '.remove-page', function() {
        $(this).closest('.page-item').remove();
    });
});

function removeImage() {
    if (confirm('Are you sure you want to remove the current popup image?')) {
        $('#image-preview').addClass('d-none').attr('src', '');
        $('#image-placeholder').removeClass('d-none');
        
        // Add hidden field to indicate image removal
        $('<input>').attr({
            type: 'hidden',
            name: 'remove_image',
            value: '1'
        }).appendTo('form');
        
        // Hide the remove button
        $(event.target).closest('.mb-3').hide();
    }
}
</script>
@endpush
@endsection