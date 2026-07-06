@extends('layouts.app')

@section('title', 'Edit Solution - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Solution</h4>
        <p class="mb-0">Update solution information</p>
      </div>
      <div>
        <a href="{{ route('admin.solutions.show', $solution) }}" class="btn btn-info me-2">
          <i class="icon-base ti tabler-eye me-1"></i>View Solution
        </a>
        <a href="{{ route('admin.solutions.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Solutions
        </a>
      </div>
    </div>

    <form action="{{ route('admin.solutions.update', $solution) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
      <div class="row">
        <!-- Main Form -->
        <div class="col-12 col-lg-8">
          <!-- Basic Info -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
              <!-- Title -->
              <div class="mb-4">
                <label class="form-label" for="title">Solution Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title', $solution->title) }}" 
                       placeholder="Enter solution title" required>
                <div id="title-counter" class="form-text">
                  <span id="title-count">{{ strlen($solution->title ?? '') }}</span>/255 characters
                </div>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Slug -->
              <div class="mb-4">
                <label class="form-label" for="slug">URL Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                       id="slug" name="slug" value="{{ old('slug', $solution->slug) }}" 
                       placeholder="auto-generated-from-title">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Leave empty to auto-generate from title</div>
              </div>

              <!-- Category -->
              <div class="mb-4">
                <label class="form-label" for="category">Category</label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                  <option value="">Select Category</option>
                  @foreach($categories as $category)
                    <option value="{{ $category }}" {{ old('category', $solution->category) === $category ? 'selected' : '' }}>
                      {{ $category }}
                    </option>
                  @endforeach
                </select>
                @error('category')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Short Description -->
              <div class="mb-4">
                <label class="form-label" for="short_description">Short Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('short_description') is-invalid @enderror" 
                          id="short_description" name="short_description" rows="3" 
                          placeholder="Brief description for cards and previews..." required>{{ old('short_description', $solution->short_description) }}</textarea>
                <div id="short-desc-counter" class="form-text">
                  <span id="short-desc-count">{{ strlen($solution->short_description ?? '') }}</span>/500 characters
                </div>
                @error('short_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Keep it concise - used in solution cards and previews</div>
              </div>

              <!-- Full Description -->
              <div class="mb-4">
                <label class="form-label" for="description">Full Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="8" 
                          placeholder="Detailed description of the solution..." required>{{ old('description', $solution->description) }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Complete solution details and information</div>
              </div>
            </div>
          </div>

          <!-- Features & Benefits -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Features & Benefits</h5>
            </div>
            <div class="card-body">
              <!-- Features -->
              <div class="mb-4">
                <label class="form-label">Key Features</label>
                <div id="features-container">
                  @if(old('features', $solution->features) && count(old('features', $solution->features)) > 0)
                    @foreach(old('features', $solution->features) as $index => $feature)
                      <div class="input-group mb-2 feature-input">
                        <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Enter feature">
                        <button type="button" class="btn btn-outline-danger remove-feature">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    @endforeach
                  @else
                    <div class="input-group mb-2 feature-input">
                      <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                      <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-feature">
                  <i class="icon-base ti tabler-plus"></i>Add Feature
                </button>
              </div>

              <!-- Benefits -->
              <div class="mb-4">
                <label class="form-label">Benefits</label>
                <div id="benefits-container">
                  @if(old('benefits', $solution->benefits) && count(old('benefits', $solution->benefits)) > 0)
                    @foreach(old('benefits', $solution->benefits) as $index => $benefit)
                      <div class="input-group mb-2 benefit-input">
                        <input type="text" class="form-control" name="benefits[]" value="{{ $benefit }}" placeholder="Enter benefit">
                        <button type="button" class="btn btn-outline-danger remove-benefit">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    @endforeach
                  @else
                    <div class="input-group mb-2 benefit-input">
                      <input type="text" class="form-control" name="benefits[]" placeholder="Enter benefit">
                      <button type="button" class="btn btn-outline-danger remove-benefit">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-success" id="add-benefit">
                  <i class="icon-base ti tabler-plus"></i>Add Benefit
                </button>
              </div>

              <!-- Technologies -->
              <div class="mb-4">
                <label class="form-label">Technologies Used</label>
                <div id="technologies-container">
                  @if(old('technologies', $solution->technologies) && count(old('technologies', $solution->technologies)) > 0)
                    @foreach(old('technologies', $solution->technologies) as $index => $technology)
                      <div class="input-group mb-2 technology-input">
                        <input type="text" class="form-control" name="technologies[]" value="{{ $technology }}" placeholder="Enter technology">
                        <button type="button" class="btn btn-outline-danger remove-technology">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    @endforeach
                  @else
                    <div class="input-group mb-2 technology-input">
                      <input type="text" class="form-control" name="technologies[]" placeholder="Enter technology">
                      <button type="button" class="btn btn-outline-danger remove-technology">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-info" id="add-technology">
                  <i class="icon-base ti tabler-plus"></i>Add Technology
                </button>
              </div>
            </div>
          </div>

          <!-- Pricing & Delivery -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Pricing & Delivery</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="price_range">Price Range</label>
                    <input type="text" class="form-control @error('price_range') is-invalid @enderror" 
                           id="price_range" name="price_range" value="{{ old('price_range', $solution->price_range) }}" 
                           placeholder="e.g., $5,000 - $15,000">
                    @error('price_range')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="delivery_time">Delivery Time</label>
                    <input type="text" class="form-control @error('delivery_time') is-invalid @enderror" 
                           id="delivery_time" name="delivery_time" value="{{ old('delivery_time', $solution->delivery_time) }}" 
                           placeholder="e.g., 4-6 weeks">
                    @error('delivery_time')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SEO Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">SEO Settings</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="meta_title">Meta Title</label>
                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                       id="meta_title" name="meta_title" value="{{ old('meta_title', $solution->meta_title) }}" 
                       placeholder="SEO title for search engines" maxlength="255">
                <div id="meta-title-counter" class="form-text">
                  <span id="meta-title-count">{{ strlen($solution->meta_title ?? '') }}</span>/255 characters
                </div>
                @error('meta_title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                          id="meta_description" name="meta_description" rows="3" 
                          placeholder="SEO description for search engines" maxlength="500">{{ old('meta_description', $solution->meta_description) }}</textarea>
                <div id="meta-desc-counter" class="form-text">
                  <span id="meta-desc-count">{{ strlen($solution->meta_description ?? '') }}</span>/500 characters
                </div>
                @error('meta_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_keywords">Meta Keywords</label>
                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $solution->meta_keywords) }}" 
                       placeholder="keyword1, keyword2, keyword3" maxlength="255">
                <div class="form-text">Separate keywords with commas</div>
                @error('meta_keywords')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
          <!-- Current Image -->
          @if($solution->image)
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Current Image</h5>
            </div>
            <div class="card-body text-center">
              <img src="{{ $solution->image_url }}" alt="{{ $solution->title }}" 
                   class="img-fluid rounded mb-3" style="max-height: 200px;">
              <div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrentImage()">
                  <i class="icon-base ti tabler-trash me-1"></i>Remove Current
                </button>
              </div>
            </div>
          </div>
          @endif

          <!-- Image Upload -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">{{ $solution->image ? 'Update' : 'Upload' }} Image</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="image">{{ $solution->image ? 'New' : 'Upload' }} Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Recommended: 800x600px or similar ratio. Max size: 2MB</div>
              </div>

              <!-- Image Preview -->
              <div id="imagePreview" class="text-center" style="display: none;">
                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                <div class="mt-2">
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove Preview
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Options -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Options</h5>
            </div>
            <div class="card-body">
              <!-- Status -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="status" id="status" 
                         value="1" {{ old('status', $solution->status) ? 'checked' : '' }}>
                  <label class="form-check-label" for="status">
                    Active Status
                  </label>
                </div>
                <div class="form-text">Enable to make solution visible on website</div>
              </div>

              <!-- Featured -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="featured" id="featured" 
                         value="1" {{ old('featured', $solution->featured) ? 'checked' : '' }}>
                  <label class="form-check-label" for="featured">
                    Featured Solution
                  </label>
                </div>
                <div class="form-text">Featured solutions appear prominently</div>
              </div>

              <!-- Sort Order -->
              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" name="sort_order" value="{{ old('sort_order', $solution->sort_order ?? 0) }}" 
                       min="0" placeholder="0">
                @error('sort_order')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Lower numbers appear first</div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card">
            <div class="card-body">
              <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="icon-base ti tabler-check me-2"></i>Update Solution
              </button>
              <div class="d-flex gap-2">
                <a href="{{ route('admin.solutions.show', $solution) }}" class="btn btn-outline-info flex-fill">
                  <i class="icon-base ti tabler-eye me-1"></i>View
                </a>
                <a href="{{ route('admin.solutions.index') }}" class="btn btn-outline-secondary flex-fill">
                  <i class="icon-base ti tabler-arrow-left me-1"></i>Cancel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

    <!-- Solution Information -->
    <div class="row mt-6">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Solution Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <strong>Solution ID:</strong>
                <p class="mb-0">{{ $solution->_id }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Created:</strong>
                <p class="mb-0">{{ $solution->created_at ? $solution->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Last Updated:</strong>
                <p class="mb-0">{{ $solution->updated_at ? $solution->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Current Slug:</strong>
                <p class="mb-0"><code>{{ $solution->slug }}</code></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    function initCharacterCounters() {
        const fieldMapping = {
            'title': 'title-count',
            'short_description': 'short-desc-count',
            'meta_title': 'meta-title-count',
            'meta_description': 'meta-desc-count'
        };
        
        Object.keys(fieldMapping).forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            const counterId = fieldMapping[fieldId];
            const counter = document.getElementById(counterId);
            
            if (field && counter) {
                field.addEventListener('input', function() {
                    counter.textContent = this.value.length;
                });
            }
        });
    }

    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    titleInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', function() {
        slugInput.dataset.manual = 'true';
    });

    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Dynamic array inputs
    function setupDynamicInputs(containerId, fieldName, inputClass, addButtonId, removeButtonClass, placeholder) {
        const container = document.getElementById(containerId);
        const addButton = document.getElementById(addButtonId);
        
        addButton.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = `input-group mb-2 ${inputClass}`;
            div.innerHTML = `
                <input type="text" class="form-control" name="${fieldName}[]" placeholder="${placeholder}">
                <button type="button" class="btn btn-outline-danger ${removeButtonClass}">
                    <i class="icon-base ti tabler-trash"></i>
                </button>
            `;
            container.appendChild(div);
        });
        
        container.addEventListener('click', function(e) {
            const removeButton = e.target.closest(`.${removeButtonClass}`);
            if (removeButton) {
                e.preventDefault();
                e.stopPropagation();
                const inputGroup = removeButton.closest('.input-group');
                if (inputGroup && container.children.length > 1) {
                    inputGroup.remove();
                } else if (inputGroup && container.children.length === 1) {
                    // If it's the last one, just clear the value instead of removing
                    const input = inputGroup.querySelector('input');
                    if (input) {
                        input.value = '';
                    }
                }
            }
        });
    }
    
    setupDynamicInputs('features-container', 'features', 'feature-input', 'add-feature', 'remove-feature', 'Enter feature');
    setupDynamicInputs('benefits-container', 'benefits', 'benefit-input', 'add-benefit', 'remove-benefit', 'Enter benefit');
    setupDynamicInputs('technologies-container', 'technologies', 'technology-input', 'add-technology', 'remove-technology', 'Enter technology');
    
    // Initialize character counters
    initCharacterCounters();
});

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image?')) {
        // Add hidden field to indicate image removal
        const form = document.querySelector('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_current_image';
        input.value = '1';
        form.appendChild(input);
        
        // Hide the current image card
        document.querySelector('.card:has(img[alt*="{{ $solution->title }}"])').style.display = 'none';
    }
}
</script>
@endpush