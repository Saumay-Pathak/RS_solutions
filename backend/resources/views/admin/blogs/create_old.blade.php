@extends('admin.layouts.admin')

@section('title', 'Add New Blog Post')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Blog Post</h5>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Blog Posts
                </a>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Blog Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                       id="title" name="title" value="{{ old('title') }}" 
                                                       placeholder="Enter blog post title" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug" class="form-label">Slug</label>
                                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                       id="slug" name="slug" value="{{ old('slug') }}" 
                                                       placeholder="Auto-generated from title">
                                                <div class="form-text">Leave empty to auto-generate from blog title</div>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category</label>
                                                <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                                       id="category" name="category" value="{{ old('category') }}" 
                                                       placeholder="Enter blog category">
                                                @error('category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_id" class="form-label">Author <span class="text-danger">*</span></label>
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
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Excerpt</label>
                                        <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                                  id="excerpt" name="excerpt" rows="3" 
                                                  placeholder="Enter a brief summary of the blog post">{{ old('excerpt') }}</textarea>
                                        <div class="form-text">Brief summary that appears in blog listings</div>
                                        @error('excerpt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  id="content" name="content" rows="12" 
                                                  placeholder="Write your blog post content here..." required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                               id="tags" name="tags" value="{{ old('tags') }}" 
                                               placeholder="Enter tags separated by commas">
                                        <div class="form-text">Separate multiple tags with commas (e.g., technology, web, coding)</div>
                                        @error('tags')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">SEO Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                               id="meta_title" name="meta_title" value="{{ old('meta_title') }}" 
                                               placeholder="Enter meta title for SEO" maxlength="255">
                                        <div class="form-text">
                                            <span id="meta-title-count">0</span>/255 characters
                                        </div>
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                  id="meta_description" name="meta_description" rows="3" 
                                                  placeholder="Enter meta description for SEO" maxlength="500">{{ old('meta_description') }}</textarea>
                                        <div class="form-text">
                                            <span id="meta-desc-count">0</span>/500 characters
                                        </div>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings & Media -->
                        <div class="col-md-4">
                            <!-- Publishing Options -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Publishing Options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                               {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            <strong>Publish Status</strong>
                                            <div class="form-text">Make this blog post visible to readers</div>
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="published_at" class="form-label">Publish Date</label>
                                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                               id="published_at" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                                        <div class="form-text">When to publish this post</div>
                                        @error('published_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Featured Image</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                               id="featured_image" name="featured_image" accept="image/*">
                                        <div class="form-text">Max size: 2MB. Supported: JPG, PNG, GIF</div>
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div id="image-preview" class="text-center" style="display: none;">
                                        <img id="preview-img" src="" alt="Featured Image Preview" 
                                             class="img-fluid rounded" style="max-height: 200px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                                                <i class="icon-base ti tabler-trash me-1"></i>Remove Image
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Blog Statistics -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Blog Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="reading_time" class="form-label">Reading Time (minutes)</label>
                                        <input type="number" class="form-control @error('reading_time') is-invalid @enderror" 
                                               id="reading_time" name="reading_time" value="{{ old('reading_time') }}" 
                                               min="1" placeholder="Auto-calculated">
                                        <div class="form-text">Leave empty to auto-calculate based on content length</div>
                                        @error('reading_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-base ti tabler-check me-1"></i>Create Blog Post
                                        </button>
                                        <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary">
                                            <i class="icon-base ti tabler-file me-1"></i>Save as Draft
                                        </button>
                                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                                            <i class="icon-base ti tabler-trash me-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from title
    $('#title').on('input', function() {
        if ($('#slug').val() === '') {
            let title = $(this).val();
            let slug = title.toLowerCase()
                          .replace(/[^\w\s-]/g, '') // Remove special characters
                          .replace(/\s+/g, '-')     // Replace spaces with hyphens
                          .replace(/--+/g, '-');    // Replace multiple hyphens with single
            $('#slug').val(slug);
        }
    });

    // Character counters
    $('#meta_title').on('input', function() {
        const count = $(this).val().length;
        $('#meta-title-count').text(count);
        if (count > 255) {
            $('#meta-title-count').parent().addClass('text-danger');
        } else {
            $('#meta-title-count').parent().removeClass('text-danger');
        }
    });

    $('#meta_description').on('input', function() {
        const count = $(this).val().length;
        $('#meta-desc-count').text(count);
        if (count > 500) {
            $('#meta-desc-count').parent().addClass('text-danger');
        } else {
            $('#meta-desc-count').parent().removeClass('text-danger');
        }
    });

    // Auto-calculate reading time based on content
    $('#content').on('input', function() {
        if ($('#reading_time').val() === '') {
            const content = $(this).val();
            const wordsPerMinute = 200;
            const wordCount = content.trim().split(/\s+/).length;
            const readingTime = Math.ceil(wordCount / wordsPerMinute);
            
            if (wordCount > 50) { // Only auto-calculate for substantial content
                $('#reading_time').attr('placeholder', readingTime + ' min (auto-calculated)');
            }
        }
    });

    // Convert tags input to array format on form submit
    $('form').on('submit', function() {
        const tagsInput = $('#tags').val();
        if (tagsInput) {
            // Convert comma-separated tags to proper format for backend
            const tagsArray = tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
            $('#tags').val(tagsArray.join(','));
        }
    });

    // Image preview functionality
    $('#featured_image').change(function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2097152) { // 2MB
                toastr.error('File size must be less than 2MB');
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove image preview
    $('#remove-image').click(function() {
        $('#featured_image').val('');
        $('#image-preview').hide();
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        if ($('#title').val().trim() === '') {
            isValid = false;
            toastr.error('Blog title is required');
        }
        
        if ($('#author_id').val() === '') {
            isValid = false;
            toastr.error('Author selection is required');
        }
        
        if ($('#content').val().trim() === '') {
            isValid = false;
            toastr.error('Blog content is required');
        }
        
        // Check meta title length
        if ($('#meta_title').val().length > 255) {
            isValid = false;
            toastr.error('Meta title must be 255 characters or less');
        }
        
        // Check meta description length
        if ($('#meta_description').val().length > 500) {
            isValid = false;
            toastr.error('Meta description must be 500 characters or less');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });

    // Auto-fill meta title and description if empty
    $('#title, #excerpt').on('blur', function() {
        if ($('#meta_title').val() === '' && $('#title').val() !== '') {
            $('#meta_title').val($('#title').val()).trigger('input');
        }
        
        if ($('#meta_description').val() === '' && $('#excerpt').val() !== '') {
            $('#meta_description').val($('#excerpt').val()).trigger('input');
        }
    });

    // Initialize character counters on page load
    $('#meta_title').trigger('input');
    $('#meta_description').trigger('input');
    $('#content').trigger('input');
});
</script>
@endpush
@endsection