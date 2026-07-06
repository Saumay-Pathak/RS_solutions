@extends('layouts.app')

@section('title', 'Create Hero Slide - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Create New Hero Slide</h4>
        <p class="mb-0">Add a new slide to your hero slider</p>
      </div>
      <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Slides
      </a>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="icon-base ti tabler-alert-triangle me-2"></i>
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
          <!-- Basic Information -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-slideshow me-2"></i>Slide Content
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="title">Slide Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title') }}" 
                       placeholder="Enter main heading" required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="subtitle">Subtitle</label>
                <input type="text" class="form-control @error('subtitle') is-invalid @enderror" 
                       id="subtitle" name="subtitle" value="{{ old('subtitle') }}" 
                       placeholder="Optional subtitle or tagline">
                @error('subtitle')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="content">HTML Content</label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" name="content" rows="6" 
                          placeholder="Add custom HTML content here (optional)">{{ old('content') }}</textarea>
                <div class="form-text">You can add custom HTML for advanced layouts</div>
                @error('content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="content_file">Upload HTML File (optional)</label>
                <input type="file" class="form-control @error('content_file') is-invalid @enderror" 
                       id="content_file" name="content_file" accept=".html,.htm,.txt">
                <div class="form-text">If provided, the file’s HTML will be used and the textarea can be left empty.</div>
                @error('content_file')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <!-- Button Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-click me-2"></i>Call-to-Action Buttons
              </h5>
            </div>
            <div class="card-body">
              <h6 class="mb-3">Primary Button</h6>
              <div class="row mb-4">
                <div class="col-md-4">
                  <label class="form-label" for="button_text">Button Text</label>
                  <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                         id="button_text" name="button_text" value="{{ old('button_text') }}" 
                         placeholder="e.g., Get Started">
                  @error('button_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-5">
                  <label class="form-label" for="button_link">Button Link</label>
                  <input type="text" class="form-control @error('button_link') is-invalid @enderror" 
                         id="button_link" name="button_link" value="{{ old('button_link') }}" 
                         placeholder="/contact-us">
                  @error('button_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3">
                  <label class="form-label" for="button_style">Button Style</label>
                  <select class="form-select @error('button_style') is-invalid @enderror" 
                          id="button_style" name="button_style">
                    <option value="btn-primary">Primary</option>
                    <option value="btn-secondary">Secondary</option>
                    <option value="btn-success">Success</option>
                    <option value="btn-danger">Danger</option>
                    <option value="btn-warning">Warning</option>
                    <option value="btn-info">Info</option>
                    <option value="btn-outline-primary">Outline Primary</option>
                    <option value="btn-outline-light">Outline Light</option>
                  </select>
                  @error('button_style')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <hr class="my-4">

              <h6 class="mb-3">Secondary Button (Optional)</h6>
              <div class="row">
                <div class="col-md-4">
                  <label class="form-label" for="secondary_button_text">Button Text</label>
                  <input type="text" class="form-control @error('secondary_button_text') is-invalid @enderror" 
                         id="secondary_button_text" name="secondary_button_text" value="{{ old('secondary_button_text') }}" 
                         placeholder="e.g., Learn More">
                  @error('secondary_button_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-5">
                  <label class="form-label" for="secondary_button_link">Button Link</label>
                  <input type="text" class="form-control @error('secondary_button_link') is-invalid @enderror" 
                         id="secondary_button_link" name="secondary_button_link" value="{{ old('secondary_button_link') }}" 
                         placeholder="/about-us">
                  @error('secondary_button_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3">
                  <label class="form-label" for="secondary_button_style">Button Style</label>
                  <select class="form-select @error('secondary_button_style') is-invalid @enderror" 
                          id="secondary_button_style" name="secondary_button_style">
                    <option value="btn-secondary">Secondary</option>
                    <option value="btn-primary">Primary</option>
                    <option value="btn-success">Success</option>
                    <option value="btn-danger">Danger</option>
                    <option value="btn-warning">Warning</option>
                    <option value="btn-info">Info</option>
                    <option value="btn-outline-primary">Outline Primary</option>
                    <option value="btn-outline-light">Outline Light</option>
                  </select>
                  @error('secondary_button_style')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <!-- Design Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-palette me-2"></i>Design & Styling
              </h5>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-4">
                  <label class="form-label" for="content_position">Content Position</label>
                  <select class="form-select @error('content_position') is-invalid @enderror" 
                          id="content_position" name="content_position">
                    <option value="left">Left</option>
                    <option value="center" selected>Center</option>
                    <option value="right">Right</option>
                  </select>
                  @error('content_position')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="background_color">Background Color</label>
                  <input type="color" class="form-control @error('background_color') is-invalid @enderror" 
                         id="background_color" name="background_color" value="{{ old('background_color', '#000000') }}">
                  @error('background_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="text_color">Text Color</label>
                  <input type="color" class="form-control @error('text_color') is-invalid @enderror" 
                         id="text_color" name="text_color" value="{{ old('text_color', '#ffffff') }}">
                  @error('text_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label" for="overlay_opacity">Overlay Opacity (%)</label>
                  <input type="range" class="form-range @error('overlay_opacity') is-invalid @enderror" 
                         id="overlay_opacity" name="overlay_opacity" value="{{ old('overlay_opacity', 50) }}" 
                         min="0" max="100" step="5">
                  <div class="form-text">Current: <span id="opacity-value">50</span>%</div>
                  @error('overlay_opacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="animation_type">Animation Type</label>
                  <select class="form-select @error('animation_type') is-invalid @enderror" 
                          id="animation_type" name="animation_type">
                    <option value="fade">Fade</option>
                    <option value="slide">Slide</option>
                    <option value="zoom">Zoom</option>
                    <option value="none">None</option>
                  </select>
                  @error('animation_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
          <!-- Slide Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-settings me-2"></i>Slide Settings
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="order">Display Order <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                       id="order" name="order" value="{{ old('order', $nextOrder) }}" 
                       min="1" required>
                <div class="form-text">Lower numbers appear first</div>
                @error('order')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="auto_play_delay">Auto-Play Delay (ms)</label>
                <input type="number" class="form-control @error('auto_play_delay') is-invalid @enderror" 
                       id="auto_play_delay" name="auto_play_delay" value="{{ old('auto_play_delay', 5000) }}" 
                       min="1000" max="20000" step="1000">
                <div class="form-text">Time before next slide (5000 = 5 seconds)</div>
                @error('auto_play_delay')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-check form-switch mb-4">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                  <span class="fw-semibold">Active Status</span>
                  <div class="form-text">Make this slide visible</div>
                </label>
              </div>
            </div>
          </div>

          <!-- Slide Image -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-photo me-2"></i>Slide Image
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                <div class="form-text">Max 5MB. Recommended 1920x1080px</div>
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="mb-3">
                <label class="form-label" for="image_alt">Image Alt Text</label>
                <input type="text" class="form-control @error('image_alt') is-invalid @enderror" 
                       id="image_alt" name="image_alt" value="{{ old('image_alt') }}" 
                       placeholder="Describe the image for SEO">
                @error('image_alt')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Image Preview -->
              <div id="image-preview" class="text-center d-none">
                <img id="preview-img" src="" alt="Slide Image Preview" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Display Schedule -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-calendar me-2"></i>Display Schedule
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label" for="display_from">Start Date</label>
                <input type="datetime-local" class="form-control @error('display_from') is-invalid @enderror" 
                       id="display_from" name="display_from" value="{{ old('display_from') }}">
                <div class="form-text">Leave empty to display immediately</div>
                @error('display_from')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label class="form-label" for="display_to">End Date</label>
                <input type="datetime-local" class="form-control @error('display_to') is-invalid @enderror" 
                       id="display_to" name="display_to" value="{{ old('display_to') }}">
                <div class="form-text">Leave empty for no end date</div>
                @error('display_to')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="card">
            <div class="card-body">
              <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="icon-base ti tabler-check me-2"></i>Create Slide
              </button>
              <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-outline-secondary w-100">
                <i class="icon-base ti tabler-x me-2"></i>Cancel
              </a>
            </div>
          </div>
        </div>
      </div>
    </form>

  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeImageBtn = document.getElementById('remove-image');

    imageInput?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    removeImageBtn?.addEventListener('click', function() {
        imageInput.value = '';
        previewImg.src = '';
        imagePreview.classList.add('d-none');
    });

    // Overlay opacity display
    const opacityInput = document.getElementById('overlay_opacity');
    const opacityValue = document.getElementById('opacity-value');
    
    opacityInput?.addEventListener('input', function() {
        opacityValue.textContent = this.value;
    });
});
</script>
@endpush
