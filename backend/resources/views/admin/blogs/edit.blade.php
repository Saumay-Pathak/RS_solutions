@extends('layouts.app')

@section('title', 'Edit Blog Post - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Blog Post: {{ $blog->title }}</h4>
        <p class="mb-0">Update your blog content and settings</p>
      </div>
      <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Blogs
      </a>
    </div>

    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
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
                         id="title" name="title" value="{{ old('title', $blog->title) }}" 
                         placeholder="Enter an engaging blog title">
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="slug">URL Slug</label>
                  <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                         id="slug" name="slug" value="{{ old('slug', $blog->slug) }}" 
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
                          placeholder="Brief summary that appears in blog listings">{{ old('excerpt', $blog->excerpt) }}</textarea>
                @error('excerpt')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="content">Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" name="content" rows="15" 
                          placeholder="Write your blog post content here..." required>{{ old('content', $blog->content) }}</textarea>
                @error('content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label" for="category">Category</label>
                  <input type="text" class="form-control @error('category') is-invalid @enderror" 
                         id="category" name="category" value="{{ old('category', $blog->category) }}" 
                         placeholder="e.g., Technology, Business, Lifestyle">
                  @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="tags">Tags</label>
                  <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                         id="tags" name="tags" value="{{ old('tags', is_array($blog->tags) ? implode(', ', $blog->tags) : $blog->tags) }}" 
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
                       id="meta_title" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" 
                       placeholder="SEO optimized title" maxlength="255">
                <div class="form-text">
                  <span id="meta-title-count">{{ strlen($blog->meta_title ?? '') }}</span>/255 characters - Appears in search results
                </div>
                @error('meta_title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                          id="meta_description" name="meta_description" rows="3" 
                          placeholder="Brief description for search engines" maxlength="500">{{ old('meta_description', $blog->meta_description) }}</textarea>
                <div class="form-text">
                  <span id="meta-desc-count">{{ strlen($blog->meta_description ?? '') }}</span>/500 characters - Description in search results
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
                    <option value="{{ $author->_id }}" {{ old('author_id', $blog->author_id) == $author->_id ? 'selected' : '' }}>
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
                       id="published_at" name="published_at" value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
                @error('published_at')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status', $blog->status) ? 'checked' : '' }}>
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
              <!-- Current Image -->
              @if($blog->featured_image)
                <div class="mb-3 text-center">
                  <img src="{{ Storage::url($blog->featured_image) }}" alt="Current Featured Image" 
                       class="img-fluid rounded" style="max-height: 200px;">
                  <div class="mt-2">
                    <small class="text-muted">Current featured image</small>
                  </div>
                </div>
              @endif
              
              <div class="mb-3">
                <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                       id="featured_image" name="featured_image" accept="image/*">
                <div class="form-text">Max 2MB. JPG, PNG, GIF supported. Leave empty to keep current image.</div>
                @error('featured_image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Image Preview -->
              <div id="image-preview" class="text-center d-none">
                <img id="preview-img" src="" alt="Image Preview" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Blog Stats -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-chart-bar me-2"></i>Blog Stats
              </h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-6">
                  <div class="border-end">
                    <h6 class="mb-0 text-primary">{{ $blog->reading_time ?? 0 }}</h6>
                    <small class="text-muted">Min Read</small>
                  </div>
                </div>
                <div class="col-6">
                  <h6 class="mb-0 text-info">{{ str_word_count(strip_tags($blog->content ?? '')) }}</h6>
                  <small class="text-muted">Words</small>
                </div>
              </div>
              <hr>
              <div class="row text-center">
                <div class="col-6">
                  <div class="border-end">
                    <h6 class="mb-0 text-success">{{ $blog->created_at->format('M j, Y') }}</h6>
                    <small class="text-muted">Created</small>
                  </div>
                </div>
                <div class="col-6">
                  <h6 class="mb-0 text-warning">{{ $blog->updated_at->format('M j, Y') }}</h6>
                  <small class="text-muted">Updated</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteBlogModal">
                    <i class="icon-base ti tabler-trash me-2"></i>Delete Blog
                  </button>
                </div>
                <div class="d-flex gap-3">
                  <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-x me-2"></i>Cancel
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-check me-2"></i>Update Blog Post
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

<!-- Delete Blog Modal -->
<div class="modal fade" id="deleteBlogModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Blog Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <i class="icon-base ti tabler-alert-triangle text-warning mb-3" style="font-size: 3rem;"></i>
          <h6>Are you sure you want to delete this blog post?</h6>
          <p class="text-muted">This action cannot be undone. The blog post and its featured image will be permanently deleted.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="icon-base ti tabler-trash me-2"></i>Delete Blog Post
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
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    let originalSlug = slugInput.value;
    
    titleInput.addEventListener('input', function() {
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
    const metaTitleCount = document.getElementById('meta-title-count');
    const metaDescCount = document.getElementById('meta-desc-count');
    
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

    // Image preview
    const imageInput = document.getElementById('featured_image');
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
        if (confirm('Are you sure you want to remove the current featured image?')) {
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