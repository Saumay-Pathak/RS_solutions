@extends('layouts.app')

@section('title', 'Add New Software - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Add New Software</h4>
        <p class="mb-0">Upload and manage software applications</p>
      </div>
      <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Software
      </a>
    </div>

    <form action="{{ route('admin.software.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- One Line Description -->
                                        <div class="mb-3">
                                            <label for="one_line_description" class="form-label">One Line Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('one_line_description') is-invalid @enderror" 
                                                      id="one_line_description" name="one_line_description" rows="2" required>{{ old('one_line_description') }}</textarea>
                                            @error('one_line_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Categories -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="main_category" class="form-label">Main Category <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('main_category') is-invalid @enderror" 
                                                            id="main_category" name="main_category" required>
                                                        <option value="">Select Main Category</option>
                                                        @foreach($mainCategories as $category)
                                                            <option value="{{ $category }}" {{ old('main_category') == $category ? 'selected' : '' }}>
                                                                {{ $category }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('main_category')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sub_category" class="form-label">Sub Category</label>
                                                    <select class="form-select @error('sub_category') is-invalid @enderror" 
                                                            id="sub_category" name="sub_category">
                                                        <option value="">Select Sub Category</option>
                                                        @foreach($subCategories as $mainCat => $subCats)
                                                            <optgroup label="{{ $mainCat }}">
                                                                @foreach($subCats as $subCat)
                                                                    <option value="{{ $subCat }}" {{ old('sub_category') == $subCat ? 'selected' : '' }}>
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

                                        <!-- File Upload or External URL -->
                                        <div class="mb-3">
                                            <label class="form-label">Software Source <span class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="source_type" 
                                                               id="source_file" value="file" checked>
                                                        <label class="form-check-label" for="source_file">
                                                            Upload File
                                                        </label>
                                                    </div>
                                                    <input type="file" class="form-control mt-2 @error('file') is-invalid @enderror" 
                                                           id="file" name="file" accept=".exe,.msi,.zip,.rar,.dmg,.pkg,.deb,.rpm">
                                                    @error('file')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="source_type" 
                                                               id="source_url" value="url">
                                                        <label class="form-check-label" for="source_url">
                                                            External URL
                                                        </label>
                                                    </div>
                                                    <input type="url" class="form-control mt-2 @error('external_url') is-invalid @enderror" 
                                                           id="external_url" name="external_url" 
                                                           placeholder="https://example.com/software" value="{{ old('external_url') }}">
                                                    @error('external_url')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Version and Developer -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="version" class="form-label">Version</label>
                                                    <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                                           id="version" name="version" value="{{ old('version') }}">
                                                    @error('version')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="developer" class="form-label">Developer</label>
                                                    <input type="text" class="form-control @error('developer') is-invalid @enderror" 
                                                           id="developer" name="developer" value="{{ old('developer') }}">
                                                    @error('developer')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="license" class="form-label">License</label>
                                                    <select class="form-select @error('license') is-invalid @enderror" 
                                                            id="license" name="license">
                                                        <option value="">Select License</option>
                                                        <option value="Free" {{ old('license') == 'Free' ? 'selected' : '' }}>Free</option>
                                                        <option value="Trial" {{ old('license') == 'Trial' ? 'selected' : '' }}>Trial</option>
                                                        <option value="Paid" {{ old('license') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                                        <option value="Open Source" {{ old('license') == 'Open Source' ? 'selected' : '' }}>Open Source</option>
                                                    </select>
                                                    @error('license')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price and Size -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                               id="price" name="price" min="0" step="0.01" value="{{ old('price') }}">
                                                    </div>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="size" class="form-label">File Size</label>
                                                    <input type="text" class="form-control @error('size') is-invalid @enderror" 
                                                           id="size" name="size" placeholder="e.g., 125 MB" value="{{ old('size') }}">
                                                    @error('size')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Requirements and Platforms -->
                                        <div class="mb-3">
                                            <label class="form-label">System Requirements</label>
                                            <div id="requirements-container">
                                                @if(old('requirements'))
                                                    @foreach(old('requirements') as $index => $requirement)
                                                        <div class="input-group mb-2 requirement-input">
                                                            <input type="text" class="form-control" name="requirements[]" value="{{ $requirement }}" placeholder="Enter requirement">
                                                            <button type="button" class="btn btn-outline-danger remove-requirement">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="input-group mb-2 requirement-input">
                                                        <input type="text" class="form-control" name="requirements[]" placeholder="Enter requirement">
                                                        <button type="button" class="btn btn-outline-danger remove-requirement">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-requirement">
                                                <i class="fas fa-plus"></i> Add Requirement
                                            </button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Supported Platforms</label>
                                            <div id="platforms-container">
                                                @if(old('platforms'))
                                                    @foreach(old('platforms') as $index => $platform)
                                                        <div class="input-group mb-2 platform-input">
                                                            <input type="text" class="form-control" name="platforms[]" value="{{ $platform }}" placeholder="Enter platform">
                                                            <button type="button" class="btn btn-outline-danger remove-platform">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="input-group mb-2 platform-input">
                                                        <input type="text" class="form-control" name="platforms[]" placeholder="Enter platform">
                                                        <button type="button" class="btn btn-outline-danger remove-platform">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-success" id="add-platform">
                                                <i class="fas fa-plus"></i> Add Platform
                                            </button>
                                        </div>

                                        <!-- Tags -->
                                        <div class="mb-3">
                                            <label class="form-label">Tags</label>
                                            <div id="tags-container">
                                                @if(old('tags'))
                                                    @foreach(old('tags') as $index => $tag)
                                                        <div class="input-group mb-2 tag-input">
                                                            <input type="text" class="form-control" name="tags[]" value="{{ $tag }}" placeholder="Enter tag">
                                                            <button type="button" class="btn btn-outline-danger remove-tag">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="input-group mb-2 tag-input">
                                                        <input type="text" class="form-control" name="tags[]" placeholder="Enter tag">
                                                        <button type="button" class="btn btn-outline-danger remove-tag">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-info" id="add-tag">
                                                <i class="fas fa-plus"></i> Add Tag
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings & SEO -->
                            <div class="col-md-4">
                                <!-- Settings -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('status') is-invalid @enderror" 
                                                       type="checkbox" id="status" name="status" value="1" 
                                                       {{ old('status', 1) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status">
                                                    Active
                                                </label>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('featured') is-invalid @enderror" 
                                                       type="checkbox" id="featured" name="featured" value="1" 
                                                       {{ old('featured') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="featured">
                                                    Featured Software
                                                </label>
                                                @error('featured')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('is_free') is-invalid @enderror" 
                                                       type="checkbox" id="is_free" name="is_free" value="1" 
                                                       {{ old('is_free', 1) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_free">
                                                    Free Software
                                                </label>
                                                @error('is_free')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="released_at" class="form-label">Release Date</label>
                                            <input type="date" class="form-control @error('released_at') is-invalid @enderror" 
                                                   id="released_at" name="released_at" value="{{ old('released_at') }}">
                                            @error('released_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">SEO Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                                   id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                      id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                                   id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                            <small class="form-text text-muted">Separate keywords with commas</small>
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.software.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Create Software</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
document.addEventListener('DOMContentLoaded', function() {
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

    const sourceRadios = document.querySelectorAll('input[name="source_type"]');
    const fileInput = document.getElementById('file');
    const urlInput = document.getElementById('external_url');
    
    function toggleSourceInputs() {
        const selectedSource = document.querySelector('input[name="source_type"]:checked').value;
        
        if (selectedSource === 'file') {
            fileInput.disabled = false;
            urlInput.disabled = true;
            urlInput.value = '';
        } else {
            fileInput.disabled = true;
            urlInput.disabled = false;
            // Don't clear file input value - let user keep it in case they change back
        }
    }
    
    sourceRadios.forEach(radio => {
        radio.addEventListener('change', toggleSourceInputs);
    });
    
    // Initialize on page load - ensure file input is enabled since it's checked by default
    fileInput.disabled = false;
    urlInput.disabled = true;
    toggleSourceInputs();
    
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    titleInput.addEventListener('input', function() {
        // Optional: Add slug generation logic if needed
    });
    
    // Dynamic input handlers
    function addDynamicInputHandlers(type) {
        const addButton = document.getElementById(`add-${type}`);
        const container = document.getElementById(`${type}s-container`);
        
        addButton.addEventListener('click', function() {
            const inputGroup = document.createElement('div');
            inputGroup.className = `input-group mb-2 ${type}-input`;
            inputGroup.innerHTML = `
                <input type="text" class="form-control" name="${type}s[]" placeholder="Enter ${type}">
                <button type="button" class="btn btn-outline-danger remove-${type}">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(inputGroup);
        });
        
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains(`remove-${type}`) || e.target.closest(`.remove-${type}`)) {
                const inputGroup = e.target.closest(`.${type}-input`);
                if (container.children.length > 1) {
                    inputGroup.remove();
                }
            }
        });
    }
    
    // Initialize dynamic inputs
    addDynamicInputHandlers('requirement');
    addDynamicInputHandlers('platform');
    addDynamicInputHandlers('tag');
});
</script>
@endpush
@endsection