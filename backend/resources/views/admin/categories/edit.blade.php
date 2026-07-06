@extends('layouts.app')

@section('title', 'Edit Category - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Category: {{ $category->name }}</h4>
        <p class="mb-0">Update your category information and settings</p>
      </div>
      <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Categories
      </a>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
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
                         id="name" name="name" value="{{ old('name', $category->name) }}" 
                         placeholder="Enter category name" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="slug">URL Slug</label>
                  <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                         id="slug" name="slug" value="{{ old('slug', $category->slug) }}" 
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
                          placeholder="Describe what this category is about">{{ old('description', $category->description) }}</textarea>
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
                      <option value="{{ $parent->_id }}" {{ old('parent_id', $category->parent_id) == $parent->_id ? 'selected' : '' }}>
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
                         id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
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
                       id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" 
                       placeholder="SEO optimized title" maxlength="255">
                <div class="form-text">
                  <span id="meta-title-count">{{ strlen($category->meta_title ?? '') }}</span>/255 characters - Appears in search results
                </div>
                @error('meta_title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                          id="meta_description" name="meta_description" rows="3" 
                          placeholder="Brief description for search engines" maxlength="500">{{ old('meta_description', $category->meta_description) }}</textarea>
                <div class="form-text">
                  <span id="meta-desc-count">{{ strlen($category->meta_description ?? '') }}</span>/500 characters - Description in search results
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
                       {{ old('status', $category->status) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  <span class="fw-semibold">Active Status</span>
                  <div class="form-text">Make this category visible to users</div>
                </label>
              </div>

              <!-- Category Stats -->
              <div class="border rounded p-3">
                <h6 class="text-muted mb-3">
                  <i class="icon-base ti tabler-chart-bar me-2"></i>Category Stats
                </h6>
                <div class="row text-center">
                  <div class="col-6">
                    <div class="border-end">
                      <h6 class="mb-0 text-primary" id="char-count">{{ strlen($category->description ?? '') }}</h6>
                      <small class="text-muted">Characters</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="mb-0 text-info" id="word-count">{{ str_word_count($category->description ?? '') }}</h6>
                    <small class="text-muted">Words</small>
                  </div>
                </div>
                <hr>
                <div class="row text-center">
                  <div class="col-6">
                    <div class="border-end">
                      <h6 class="mb-0 text-success">{{ $category->created_at->format('M j, Y') }}</h6>
                      <small class="text-muted">Created</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="mb-0 text-warning">{{ $category->updated_at->format('M j, Y') }}</h6>
                    <small class="text-muted">Updated</small>
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
              <!-- Current Image -->
              @if($category->image)
                <div class="mb-3 text-center">
                  <img src="{{ Storage::url($category->image) }}" alt="Current Category Image" 
                       class="img-fluid rounded" style="max-height: 200px;">
                  <div class="mt-2">
                    <small class="text-muted">Current category image</small>
                  </div>
                </div>
              @endif
              
              <div class="mb-3">
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                <div class="form-text">Max 2MB. JPG, PNG, GIF supported. Leave empty to keep current image.</div>
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
            </div>
          </div>

          @if($category->children && $category->children->count() > 0)
          <!-- Child Categories -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-folder-tree me-2"></i>Child Categories
              </h5>
            </div>
            <div class="card-body">
              @foreach($category->children as $child)
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center">
                    <i class="icon-base ti tabler-folder me-2 text-muted"></i>
                    <span>{{ $child->name }}</span>
                    @if(!$child->status)
                      <span class="badge bg-secondary ms-2">Inactive</span>
                    @endif
                  </div>
                  <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline-primary">
                    <i class="icon-base ti tabler-edit"></i>
                  </a>
                </div>
              @endforeach
            </div>
          </div>
          @endif
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">
                    <i class="icon-base ti tabler-trash me-2"></i>Delete Category
                  </button>
                </div>
                <div class="d-flex gap-3">
                  <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-x me-2"></i>Cancel
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-check me-2"></i>Update Category
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <i class="icon-base ti tabler-alert-triangle text-warning mb-3" style="font-size: 3rem;"></i>
          <h6>Are you sure you want to delete this category?</h6>
          <p class="text-muted">This action cannot be undone. The category and its image will be permanently deleted.</p>
          @if($category->children && $category->children->count() > 0)
            <div class="alert alert-warning">
              <strong>Warning:</strong> This category has {{ $category->children->count() }} child categories that will also be affected.
            </div>
          @endif
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="icon-base ti tabler-trash me-2"></i>Delete Category
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    let originalSlug = slugInput.value;
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.value === '' || slugInput.dataset.manual !== 'true') {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', function() {
        if (this.value !== originalSlug) {
            this.dataset.manual = 'true';
        }
    });

    // Character counters
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescInput = document.getElementById('meta_description');
    const descInput = document.getElementById('description');
    const metaTitleCount = document.getElementById('meta-title-count');
    const metaDescCount = document.getElementById('meta-desc-count');
    const charCount = document.getElementById('char-count');
    const wordCount = document.getElementById('word-count');
    
    metaTitleInput.addEventListener('input', function() {
        metaTitleCount.textContent = this.value.length;
        if (this.value.length > 255) {
            metaTitleCount.parentElement.classList.add('text-danger');
        } else {
            metaTitleCount.parentElement.classList.remove('text-danger');
        }
    });
    
    metaDescInput.addEventListener('input', function() {
        metaDescCount.textContent = this.value.length;
        if (this.value.length > 500) {
            metaDescCount.parentElement.classList.add('text-danger');
        } else {
            metaDescCount.parentElement.classList.remove('text-danger');
        }
    });
    
    descInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        wordCount.textContent = this.value.trim() ? this.value.trim().split(/\s+/).length : 0;
    });

    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeImageBtn = document.getElementById('remove-image');
    
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('d-none');
        }
    });
    
    removeImageBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove the current category image?')) {
            imageInput.value = '';
            imagePreview.classList.add('d-none');
            
            // Add hidden field to indicate image removal
            if (!document.querySelector('input[name="remove_image"]')) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_image';
                input.value = '1';
                document.querySelector('form').appendChild(input);
            }
        }
    });
});
</script>
@endpush