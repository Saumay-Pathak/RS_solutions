@extends('layouts.app')

@section('title', 'Edit Software - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Edit Software</h4>
            <p class="mb-0">Update software information and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.software.show', $software) }}" class="btn btn-info me-2">
                <i class="icon-base ti tabler-eye me-1"></i>View Software
            </a>
            <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Software
            </a>
        </div>
    </div>

    <form action="{{ route('admin.software.update', $software) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-12 col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="title" class="form-label">Software Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $software->title) }}" required>
                                <div id="title-counter" class="form-text">
                                    <span id="title-count">{{ strlen($software->title ?? '') }}</span>/255 characters
                                </div>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $software->slug) }}">
                                <div class="form-text">URL-friendly version of the title</div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="one_line_description" class="form-label">One Line Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('one_line_description') is-invalid @enderror" 
                                   id="one_line_description" name="one_line_description" 
                                   value="{{ old('one_line_description', $software->one_line_description) }}" 
                                   placeholder="Brief description of the software" required>
                            <div id="oneline-counter" class="form-text">
                                <span id="oneline-count">{{ strlen($software->one_line_description ?? '') }}</span>/255 characters
                            </div>
                            @error('one_line_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="8" 
                                      placeholder="Detailed description of the software" required>{{ old('description', $software->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="main_category" class="form-label">Main Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('main_category') is-invalid @enderror" 
                                        id="main_category" name="main_category" required>
                                    <option value="">Select Main Category</option>
                                    @foreach($mainCategories as $category)
                                        <option value="{{ $category }}" {{ old('main_category', $software->main_category) == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('main_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="sub_category" class="form-label">Sub Category</label>
                                <select class="form-select @error('sub_category') is-invalid @enderror" 
                                        id="sub_category" name="sub_category">
                                    <option value="">Select Sub Category</option>
                                    @foreach($subCategories as $mainCat => $subCats)
                                        <optgroup label="{{ $mainCat }}">
                                            @foreach($subCats as $subCat)
                                                <option value="{{ $subCat }}" {{ old('sub_category', $software->sub_category) == $subCat ? 'selected' : '' }}>
                                                    {{ $subCat }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('sub_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Software Source -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Software Source</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Download Source</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="source_type" 
                                               id="source_file" value="file" {{ ($software->file && !$software->external_url) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="source_file">
                                            Upload File
                                        </label>
                                    </div>
                                    <input type="file" class="form-control mt-2 @error('file') is-invalid @enderror" 
                                           id="file" name="file" accept=".exe,.msi,.zip,.rar,.dmg,.pkg,.deb,.rpm">
                                    @if($software->file)
                                        <div class="mt-2 d-flex align-items-center justify-content-between">
                                            <div class="text-muted small">
                                                Current: {{ basename($software->file) }} ({{ $software->file_size_formatted }})
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="remove_file" name="remove_file" value="1">
                                                <label class="form-check-label" for="remove_file">
                                                    Remove
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="source_type" 
                                               id="source_url" value="url" {{ $software->external_url ? 'checked' : '' }}>
                                        <label class="form-check-label" for="source_url">
                                            External URL
                                        </label>
                                    </div>
                                    <input type="url" class="form-control mt-2 @error('external_url') is-invalid @enderror" 
                                           id="external_url" name="external_url" 
                                           placeholder="https://example.com/software" 
                                           value="{{ old('external_url', $software->external_url) }}">
                                    @error('external_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Software Details -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Software Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="version" class="form-label">Version</label>
                                <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                       id="version" name="version" value="{{ old('version', $software->version) }}" 
                                       placeholder="e.g., 2.1.0">
                                @error('version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="developer" class="form-label">Developer</label>
                                <input type="text" class="form-control @error('developer') is-invalid @enderror" 
                                       id="developer" name="developer" value="{{ old('developer', $software->developer) }}" 
                                       placeholder="Developer name">
                                @error('developer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="license" class="form-label">License</label>
                                <select class="form-select @error('license') is-invalid @enderror" 
                                        id="license" name="license">
                                    <option value="">Select License</option>
                                    <option value="Free" {{ old('license', $software->license) == 'Free' ? 'selected' : '' }}>Free</option>
                                    <option value="Trial" {{ old('license', $software->license) == 'Trial' ? 'selected' : '' }}>Trial</option>
                                    <option value="Paid" {{ old('license', $software->license) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Open Source" {{ old('license', $software->license) == 'Open Source' ? 'selected' : '' }}>Open Source</option>
                                    <option value="Shareware" {{ old('license', $software->license) == 'Shareware' ? 'selected' : '' }}>Shareware</option>
                                    <option value="Freeware" {{ old('license', $software->license) == 'Freeware' ? 'selected' : '' }}>Freeware</option>
                                </select>
                                @error('license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" min="0" step="0.01" 
                                           value="{{ old('price', $software->price) }}">
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="size" class="form-label">File Size</label>
                                <input type="text" class="form-control @error('size') is-invalid @enderror" 
                                       id="size" name="size" placeholder="e.g., 125 MB" 
                                       value="{{ old('size', $software->size) }}">
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="released_at" class="form-label">Release Date</label>
                                <input type="date" class="form-control @error('released_at') is-invalid @enderror" 
                                       id="released_at" name="released_at" 
                                       value="{{ old('released_at', $software->released_at ? $software->released_at->format('Y-m-d') : '') }}">
                                @error('released_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="mb-4">
                            <label for="requirements" class="form-label">System Requirements</label>
                            <div id="requirements-container">
                                @if($software->requirements && count($software->requirements) > 0)
                                    @foreach($software->requirements as $index => $requirement)
                                        <div class="input-group mb-2 requirement-item">
                                            <input type="text" name="requirements[]" class="form-control" 
                                                   placeholder="Enter requirement" value="{{ $requirement }}">
                                            <button type="button" class="btn btn-outline-danger remove-requirement">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 requirement-item">
                                        <input type="text" name="requirements[]" class="form-control" placeholder="Enter requirement">
                                        <button type="button" class="btn btn-outline-danger remove-requirement">
                                            <i class="icon-base ti tabler-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-requirement">
                                <i class="icon-base ti tabler-plus me-1"></i>Add Requirement
                            </button>
                            @error('requirements')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Platforms -->
                        <div class="mb-4">
                            <label for="platforms" class="form-label">Supported Platforms</label>
                            <div id="platforms-container">
                                @if($software->platforms && count($software->platforms) > 0)
                                    @foreach($software->platforms as $index => $platform)
                                        <div class="input-group mb-2 platform-item">
                                            <select name="platforms[]" class="form-select">
                                                <option value="">Select Platform</option>
                                                <option value="Windows" {{ $platform == 'Windows' ? 'selected' : '' }}>Windows</option>
                                                <option value="macOS" {{ $platform == 'macOS' ? 'selected' : '' }}>macOS</option>
                                                <option value="Linux" {{ $platform == 'Linux' ? 'selected' : '' }}>Linux</option>
                                                <option value="Android" {{ $platform == 'Android' ? 'selected' : '' }}>Android</option>
                                                <option value="iOS" {{ $platform == 'iOS' ? 'selected' : '' }}>iOS</option>
                                                <option value="Web" {{ $platform == 'Web' ? 'selected' : '' }}>Web</option>
                                                <option value="Cross-platform" {{ $platform == 'Cross-platform' ? 'selected' : '' }}>Cross-platform</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-danger remove-platform">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 platform-item">
                                        <select name="platforms[]" class="form-select">
                                            <option value="">Select Platform</option>
                                            <option value="Windows">Windows</option>
                                            <option value="macOS">macOS</option>
                                            <option value="Linux">Linux</option>
                                            <option value="Android">Android</option>
                                            <option value="iOS">iOS</option>
                                            <option value="Web">Web</option>
                                            <option value="Cross-platform">Cross-platform</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger remove-platform">
                                            <i class="icon-base ti tabler-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-platform">
                                <i class="icon-base ti tabler-plus me-1"></i>Add Platform
                            </button>
                            @error('platforms')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-4">
                            <label for="tags" class="form-label">Tags</label>
                            <div id="tags-container">
                                @if($software->tags && count($software->tags) > 0)
                                    @foreach($software->tags as $index => $tag)
                                        <div class="input-group mb-2 tag-item">
                                            <input type="text" name="tags[]" class="form-control" 
                                                   placeholder="Enter tag" value="{{ $tag }}">
                                            <button type="button" class="btn btn-outline-danger remove-tag">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 tag-item">
                                        <input type="text" name="tags[]" class="form-control" placeholder="Enter tag">
                                        <button type="button" class="btn btn-outline-danger remove-tag">
                                            <i class="icon-base ti tabler-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-tag">
                                <i class="icon-base ti tabler-plus me-1"></i>Add Tag
                            </button>
                            @error('tags')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
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
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   id="meta_title" name="meta_title" 
                                   value="{{ old('meta_title', $software->meta_title) }}" 
                                   placeholder="SEO title for search engines" maxlength="255">
                            <div id="meta-title-counter" class="form-text">
                                <span id="meta-title-count">{{ strlen($software->meta_title ?? '') }}</span>/255 characters
                            </div>
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      id="meta_description" name="meta_description" rows="3" 
                                      placeholder="SEO description for search engines" maxlength="500">{{ old('meta_description', $software->meta_description) }}</textarea>
                            <div id="meta-desc-counter" class="form-text">
                                <span id="meta-desc-count">{{ strlen($software->meta_description ?? '') }}</span>/500 characters
                            </div>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                   id="meta_keywords" name="meta_keywords" 
                                   value="{{ old('meta_keywords', $software->meta_keywords) }}" 
                                   placeholder="Comma-separated keywords" maxlength="255">
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
                <!-- Publishing Options -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="status" name="status" 
                                   {{ old('status', $software->status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                <strong>Active Status</strong>
                                <div class="form-text">Make this software visible to visitors</div>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                   {{ old('featured', $software->featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">
                                <strong>Featured</strong>
                                <div class="form-text">Show in featured software sections</div>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="is_free" name="is_free" 
                                   {{ old('is_free', $software->is_free) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_free">
                                <strong>Free Software</strong>
                                <div class="form-text">Mark as free software</div>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" min="0" 
                                   value="{{ old('sort_order', $software->sort_order ?? 0) }}">
                            <div class="form-text">Lower numbers appear first</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <strong>Downloads:</strong>
                                <span class="badge bg-primary">{{ number_format($software->download_count ?? 0) }}</span>
                            </div>
                            <div class="col-12">
                                <strong>Created:</strong>
                                <p class="mb-0 small">{{ $software->created_at ? $software->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <strong>Last Updated:</strong>
                                <p class="mb-0 small">{{ $software->updated_at ? $software->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
                            </div>
                        </div>
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
                                <i class="icon-base ti tabler-check me-1"></i>Update Software
                            </button>
                            <a href="{{ route('admin.software.show', $software) }}" class="btn btn-outline-info">
                                <i class="icon-base ti tabler-eye me-1"></i>Preview Software
                            </a>
                            <hr class="my-3">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSoftwareModal">
                                <i class="icon-base ti tabler-trash me-1"></i>Delete Software
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Delete Software Modal -->
<div class="modal fade" id="deleteSoftwareModal" tabindex="-1" aria-labelledby="deleteSoftwareModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSoftwareModalLabel">Delete Software</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the software <strong>{{ $software->title }}</strong>?</p>
        <p class="text-danger">This action cannot be undone and will permanently delete:</p>
        <ul class="text-danger">
          <li>Software information and metadata</li>
          <li>Download files and external links</li>
          <li>System requirements and platform data</li>
          <li>All associated tags and categories</li>
          <li>Download statistics and user data</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.software.destroy', $software) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete Software</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/js/summernote-lite.min.css') }}">
<style>
    .note-editor .note-editable {
        line-height: 1.5;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/summernote-lite.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Character counters
    function initCharacterCounters() {
        $('#title').on('input', function() {
            const count = $(this).val().length;
            $('#title-count').text(count);
        });
        
        $('#one_line_description').on('input', function() {
            const count = $(this).val().length;
            $('#oneline-count').text(count);
        });
        
        $('#meta_title').on('input', function() {
            const count = $(this).val().length;
            $('#meta-title-count').text(count);
            if (count > 255) {
                $('#meta-title-counter').addClass('text-danger');
            } else {
                $('#meta-title-counter').removeClass('text-danger');
            }
        });

        $('#meta_description').on('input', function() {
            const count = $(this).val().length;
            $('#meta-desc-count').text(count);
            if (count > 500) {
                $('#meta-desc-counter').addClass('text-danger');
            } else {
                $('#meta-desc-counter').removeClass('text-danger');
            }
        });
    }

    // Initialize character counters
    initCharacterCounters();

    // Initialize Summernote
    $('#description').summernote({
        placeholder: 'Enter software description...',
        tabsize: 2,
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                // Ensure textarea is updated for form submission
                $('#description').val(contents);
            }
        }
    });

    // Auto-generate slug from title
    $('#title').on('input', function() {
        let title = $(this).val();
        let slug = title.toLowerCase()
                      .replace(/[^\w\s-]/g, '') // Remove special characters
                      .replace(/\s+/g, '-')     // Replace spaces with hyphens
                      .replace(/--+/g, '-');    // Replace multiple hyphens with single
        $('#slug').val(slug);
    });

    // Source type toggle
    $('input[name="source_type"]').on('change', function() {
        if ($(this).val() === 'file') {
            $('#external_url').prop('disabled', true).val('');
            $('#file').prop('disabled', false);
        } else {
            $('#file').prop('disabled', true).val('');
            $('#external_url').prop('disabled', false);
        }
    });

    // Initialize source type based on existing data
    @if($software->external_url)
        $('#source_url').trigger('change');
    @else
        $('#source_file').trigger('change');
    @endif

    // Dynamic array inputs
    function addArrayInput(container, name, placeholder, options = null) {
        let html = `
            <div class="input-group mb-2 ${name}-item">
        `;
        
        if (options) {
            html += `<select name="${name}[]" class="form-select">`;
            html += `<option value="">${placeholder}</option>`;
            options.forEach(option => {
                html += `<option value="${option}">${option}</option>`;
            });
            html += `</select>`;
        } else {
            html += `<input type="text" name="${name}[]" class="form-control" placeholder="${placeholder}">`;
        }
        
        html += `
                <button type="button" class="btn btn-outline-danger remove-${name}">
                    <i class="icon-base ti tabler-x"></i>
                </button>
            </div>
        `;
        
        $(`#${name}-container`).append(html);
    }

    // Add requirement
    $('#add-requirement').on('click', function() {
        addArrayInput('requirements', 'requirements', 'Enter requirement');
    });

    // Add platform
    $('#add-platform').on('click', function() {
        const platformOptions = ['Windows', 'macOS', 'Linux', 'Android', 'iOS', 'Web', 'Cross-platform'];
        addArrayInput('platforms', 'platforms', 'Select Platform', platformOptions);
    });

    // Add tag
    $('#add-tag').on('click', function() {
        addArrayInput('tags', 'tags', 'Enter tag');
    });

    // Remove dynamic inputs
    $(document).on('click', '.remove-requirement', function() {
        $(this).closest('.requirement-item').remove();
    });

    $(document).on('click', '.remove-platform', function() {
        $(this).closest('.platform-item').remove();
    });

    $(document).on('click', '.remove-tag', function() {
        $(this).closest('.tag-item').remove();
    });
});
</script>
@endpush
@endsection