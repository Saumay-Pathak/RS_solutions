@extends('admin.layouts.admin')

@section('title', 'Add New Category')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Category</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Categories
                </a>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
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
                                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}" 
                                                       placeholder="Enter category name" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug" class="form-label">Slug</label>
                                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                       id="slug" name="slug" value="{{ old('slug') }}" 
                                                       placeholder="Auto-generated from name">
                                                <div class="form-text">Leave empty to auto-generate from category name</div>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="4" 
                                                  placeholder="Enter category description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="parent_id" class="form-label">Parent Category</label>
                                                <select class="form-select @error('parent_id') is-invalid @enderror" 
                                                        id="parent_id" name="parent_id">
                                                    <option value="">No Parent (Root Category)</option>
                                                    @foreach($parentCategories as $parent)
                                                        <option value="{{ $parent->_id }}" {{ old('parent_id') == $parent->_id ? 'selected' : '' }}>
                                                            {{ $parent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('parent_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sort_order" class="form-label">Sort Order</label>
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

                        <!-- Settings & Image -->
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
                                            <strong>Active Status</strong>
                                            <div class="form-text">Make this category visible to users</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Category Image -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Category Image</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <div class="form-text">Max size: 2MB. Supported: JPG, PNG, GIF</div>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div id="image-preview" class="text-center" style="display: none;">
                                        <img id="preview-img" src="" alt="Category Image Preview" 
                                             class="img-fluid rounded" style="max-height: 200px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                                                <i class="icon-base ti tabler-trash me-1"></i>Remove Image
                                            </button>
                                        </div>
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
                                            <i class="icon-base ti tabler-check me-1"></i>Create Category
                                        </button>
                                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
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
    // Auto-generate slug from name
    $('#name').on('input', function() {
        if ($('#slug').val() === '') {
            let name = $(this).val();
            let slug = name.toLowerCase()
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

    // Image preview functionality
    $('#image').change(function() {
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
        $('#image').val('');
        $('#image-preview').hide();
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        if ($('#name').val().trim() === '') {
            isValid = false;
            toastr.error('Category name is required');
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
});
</script>
@endpush
@endsection