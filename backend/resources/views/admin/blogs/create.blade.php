@extends('layouts.app')

@section('title', 'Create Blog Post - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Create New Blog Post</h4>
        <p class="mb-0">Write and publish engaging content for your blog</p>
      </div>
      <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Blogs
      </a>
    </div>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
          <!-- Basic Information -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-article me-2"></i>Blog Content
              </h5>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-8">
                  <label class="form-label" for="title">Blog Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('title') is-invalid @enderror" 
                         id="title" name="title" value="{{ old('title') }}" 
                         placeholder="Enter an engaging blog title">
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
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
                <label class="form-label" for="excerpt">Excerpt</label>
                <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                          id="excerpt" name="excerpt" rows="3" 
                          placeholder="Brief summary that appears in blog listings">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="content">Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" name="content" rows="15" 
                          placeholder="Write your blog post content here..." required>{{ old('content') }}</textarea>
                @error('content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label" for="category">Category</label>
                  <input type="text" class="form-control @error('category') is-invalid @enderror" 
                         id="category" name="category" value="{{ old('category') }}" 
                         placeholder="e.g., Technology, Business, Lifestyle">
                  @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="tags">Tags</label>
                  <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                         id="tags" name="tags" value="{{ old('tags') }}" 
                         placeholder="tag1, tag2, tag3">
                  <div class="form-text">Separate multiple tags with commas</div>
                  @error('tags')
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
          <!-- Publish Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-calendar me-2"></i>Publishing
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="author_id">Author <span class="text-danger">*</span></label>
                <select class="form-select @error('author_id') is-invalid @enderror" 
                        id="author_id" name="author_id" required>
                  <option value="">Select Author</option>
                  @foreach($authors as $author)
                    <option value="{{ $author->_id }}" {{ old('author_id', auth()->id()) == $author->_id ? 'selected' : '' }}>
                      {{ $author->name }}
                    </option>
                  @endforeach
                </select>
                @error('author_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="published_at">Publish Date</label>
                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                       id="published_at" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                @error('published_at')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  <span class="fw-semibold">Publish immediately</span>
                  <div class="form-text">Make this post visible to readers</div>
                </label>
              </div>
            </div>
          </div>

          <!-- Featured Image -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-photo me-2"></i>Featured Image
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                       id="featured_image" name="featured_image" accept="image/*">
                <div class="form-text">Max 2MB. JPG, PNG, GIF supported</div>
                @error('featured_image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Image Preview -->
              <div id="image-preview" class="text-center d-none">
                <img id="preview-img" src="" alt="Featured Image Preview" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="card mb-6">
            <div class="card-header">
              <h6 class="mb-0">
                <i class="icon-base ti tabler-info-circle me-2"></i>Post Stats
              </h6>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-6">
                  <div class="border-end">
                    <h6 class="mb-0" id="word-count">0</h6>
                    <small class="text-muted">Words</small>
                  </div>
                </div>
                <div class="col-6">
                  <h6 class="mb-0" id="read-time">0 min</h6>
                  <small class="text-muted">Read Time</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card">
            <div class="card-body">
              <div class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ti tabler-check me-2"></i>Create Blog Post
                </button>
                <button type="submit" name="save_draft" value="1" class="btn btn-outline-secondary">
                  <i class="icon-base ti tabler-file me-2"></i>Save as Draft
                </button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
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
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    titleInput.addEventListener('input', function() {
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

    // Word count and reading time
    const contentTextarea = document.getElementById('content');
    const wordCountEl = document.getElementById('word-count');
    const readTimeEl = document.getElementById('read-time');
    
    function updateStats() {
        const text = contentTextarea.value.trim();
        const words = text ? text.split(/\s+/).length : 0;
        const readTime = Math.ceil(words / 200); // 200 words per minute
        
        wordCountEl.textContent = words;
        readTimeEl.textContent = readTime + ' min';
    }
    
    contentTextarea.addEventListener('input', updateStats);
    updateStats();

    // Image preview
    const imageInput = document.getElementById('featured_image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeBtn = document.getElementById('remove-image');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
    
    removeBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.classList.add('d-none');
    });

    // Auto-save functionality (optional)
    let autoSaveTimer;
    const formElements = document.querySelectorAll('input, textarea, select');
    
    formElements.forEach(element => {
        element.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Auto-save logic could be implemented here
                console.log('Auto-saving draft...');
            }, 5000);
        });
    });
});
</script>
@endpush