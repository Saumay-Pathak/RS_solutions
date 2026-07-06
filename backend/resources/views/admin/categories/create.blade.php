@extends('layouts.app')

@section('title', 'Create Category - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Create New Category</h4>
        <p class="mb-0">Organize your content with a new category</p>
      </div>
      <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Categories
      </a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
          <!-- Basic Information -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-folder me-2"></i>Category Information
              </h5>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="name">Category Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" 
                         id="name" name="name" value="{{ old('name') }}" 
                         placeholder="Enter category name" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="slug">URL Slug</label>
                  <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                         id="slug" name="slug" value="{{ old('slug') }}" 
                         placeholder="auto-generated">
                  <div class="form-text">Leave empty to auto-generate</div>
                  @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4" 
                          placeholder="Describe what this category is about">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label" for="parent_id">Parent Category</label>
                  <select class="form-select @error('parent_id') is-invalid @enderror" 
                          id="parent_id" name="parent_id">
                    <option value="">No Parent (Root Category)</option>
                    @foreach($parentCategories as $parent)
                      <option value="{{ $parent->_id }}" {{ old('parent_id') == $parent->_id ? 'selected' : '' }}>
                        {{ $parent->name }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">Select a parent to create a subcategory</div>
                  @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="sort_order">Display Order</label>
                  <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                         id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                         min="0" placeholder="0">
                  <div class="form-text">Lower numbers appear first</div>
                  @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <!-- SEO Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-search me-2"></i>SEO Settings
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="meta_title">Meta Title</label>
                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                       id="meta_title" name="meta_title" value="{{ old('meta_title') }}" 
                       placeholder="SEO optimized title" maxlength="255">
                <div class="form-text">
                  <span id="meta-title-count">0</span>/255 characters - Appears in search results
                </div>
                @error('meta_title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                          id="meta_description" name="meta_description" rows="3" 
                          placeholder="Brief description for search engines" maxlength="500">{{ old('meta_description') }}</textarea>
                <div class="form-text">
                  <span id="meta-desc-count">0</span>/500 characters - Description in search results
                </div>
                @error('meta_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
          <!-- Status Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-settings me-2"></i>Category Settings
              </h5>
            </div>
            <div class="card-body">
              <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  <span class="fw-semibold">Active Status</span>
                  <div class="form-text">Make this category visible to users</div>
                </label>
              </div>

              <!-- Category Stats Preview -->
              <div class="border rounded p-3">
                <h6 class="text-muted mb-3">
                  <i class="icon-base ti tabler-chart-bar me-2"></i>Quick Info
                </h6>
                <div class="row text-center">
                  <div class="col-6">
                    <div class="border-end">
                      <h6 class="mb-0 text-primary" id="char-count">0</h6>
                      <small class="text-muted">Characters</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="mb-0 text-info" id="word-count">0</h6>
                    <small class="text-muted">Words</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Category Image -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-photo me-2"></i>Category Image
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                <div class="form-text">Max 2MB. JPG, PNG, GIF supported</div>
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Image Preview -->
              <div id="image-preview" class="text-center d-none">
                <img id="preview-img" src="" alt="Category Image Preview" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove
                  </button>
                </div>
              </div>

              <!-- Default Preview -->
              <div id="default-preview" class="text-center p-4 bg-light rounded">
                <i class="icon-base ti tabler-photo display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">Upload an image for this category</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0">
                <i class="icon-base ti tabler-check me-2"></i>Actions
              </h6>
            </div>
            <div class="card-body">
              <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ti tabler-plus"></i>Create Category
                </button>
                <button type="submit" name="create_another" value="1" class="btn btn-outline-primary">
                  <i class="icon-base ti tabler-plus"></i>Create & Add Another
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                  <i class="icon-base ti tabler-x me-2"></i>Cancel
                </a>
              </div>
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
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Character counters
    function updateCounter(inputId, counterId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);
        
        function updateCount() {
            const length = input.value.length;
            counter.textContent = length;
            counter.parentElement.className = length > maxLength ? 'form-text text-danger' : 'form-text';
        }
        
        input.addEventListener('input', updateCount);
        updateCount();
    }
    
    updateCounter('meta_title', 'meta-title-count', 255);
    updateCounter('meta_description', 'meta-desc-count', 500);

    // Description stats
    const descriptionTextarea = document.getElementById('description');
    const charCountEl = document.getElementById('char-count');
    const wordCountEl = document.getElementById('word-count');
    
    function updateStats() {
        const text = descriptionTextarea.value.trim();
        const chars = text.length;
        const words = text ? text.split(/\s+/).length : 0;
        
        charCountEl.textContent = chars;
        wordCountEl.textContent = words;
        
        // Update colors based on content length
        charCountEl.className = chars > 500 ? 'mb-0 text-warning' : chars > 1000 ? 'mb-0 text-danger' : 'mb-0 text-primary';
        wordCountEl.className = words > 100 ? 'mb-0 text-warning' : words > 200 ? 'mb-0 text-danger' : 'mb-0 text-info';
    }
    
    descriptionTextarea.addEventListener('input', updateStats);
    updateStats();

    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeBtn = document.getElementById('remove-image');
    const defaultPreview = document.getElementById('default-preview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a valid image file (JPG, PNG, GIF)');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
                defaultPreview.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
    
    removeBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.classList.add('d-none');
        defaultPreview.classList.remove('d-none');
    });

    // Form validation enhancement
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const nameValue = nameInput.value.trim();
        if (!nameValue) {
            e.preventDefault();
            nameInput.focus();
            nameInput.classList.add('is-invalid');
            
            // Remove invalid class after user starts typing
            nameInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            }, { once: true });
        }
    });

    // Auto-populate meta fields if empty
    nameInput.addEventListener('blur', function() {
        const metaTitleInput = document.getElementById('meta_title');
        const metaDescInput = document.getElementById('meta_description');
        const descInput = document.getElementById('description');
        
        if (!metaTitleInput.value && this.value) {
            metaTitleInput.value = this.value;
            updateCounter('meta_title', 'meta-title-count', 255);
        }
        
        if (!metaDescInput.value && descInput.value) {
            metaDescInput.value = descInput.value.substring(0, 160);
            updateCounter('meta_description', 'meta-desc-count', 500);
        }
    });
});
</script>
@endpush