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
                <label class="form-label" for="title">Software Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title') }}" 
                       placeholder="Enter software title" required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Slug -->
              <div class="mb-4">
                <label class="form-label" for="slug">URL Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                       id="slug" name="slug" value="{{ old('slug') }}" 
                       placeholder="auto-generated-from-title">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Leave empty to auto-generate from title</div>
              </div>

              <!-- One Line Description -->
              <div class="mb-4">
                <label class="form-label" for="one_line_description">One Line Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('one_line_description') is-invalid @enderror" 
                          id="one_line_description" name="one_line_description" rows="2" 
                          placeholder="Brief one-line description..." required>{{ old('one_line_description') }}</textarea>
                @error('one_line_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Keep it concise - used in software cards and previews</div>
              </div>

              <!-- Full Description -->
              <div class="mb-4">
                <label class="form-label" for="description">Full Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="8" 
                          placeholder="Detailed description of the software..." required>{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Complete software details and information</div>
              </div>

              <!-- Categories -->
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="main_category">Main Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('main_category') is-invalid @enderror" id="main_category" name="main_category" required>
                      <option value="">Select Main Category</option>
                      @foreach($mainCategories as $category)
                        <option value="{{ $category }}" {{ old('main_category') === $category ? 'selected' : '' }}>
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
                  <div class="mb-4">
                    <label class="form-label" for="sub_category">Sub Category</label>
                    <select class="form-select @error('sub_category') is-invalid @enderror" id="sub_category" name="sub_category">
                      <option value="">Select Sub Category</option>
                      @foreach($subCategories as $mainCat => $subCats)
                        <optgroup label="{{ $mainCat }}">
                          @foreach($subCats as $subCat)
                            <option value="{{ $subCat }}" {{ old('sub_category') === $subCat ? 'selected' : '' }}>
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
          </div>

          <!-- Software Details -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Software Details</h5>
            </div>
            <div class="card-body">
              <!-- Developer and Version -->
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="developer">Developer</label>
                    <input type="text" class="form-control @error('developer') is-invalid @enderror" 
                           id="developer" name="developer" value="{{ old('developer') }}" 
                           placeholder="Software developer/company">
                    @error('developer')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="version">Version</label>
                    <input type="text" class="form-control @error('version') is-invalid @enderror" 
                           id="version" name="version" value="{{ old('version') }}" 
                           placeholder="e.g., 1.0.0">
                    @error('version')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <!-- License and Price -->
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="license">License</label>
                    <select class="form-select @error('license') is-invalid @enderror" id="license" name="license">
                      <option value="">Select License</option>
                      <option value="Free" {{ old('license') === 'Free' ? 'selected' : '' }}>Free</option>
                      <option value="Trial" {{ old('license') === 'Trial' ? 'selected' : '' }}>Trial</option>
                      <option value="Paid" {{ old('license') === 'Paid' ? 'selected' : '' }}>Paid</option>
                      <option value="Open Source" {{ old('license') === 'Open Source' ? 'selected' : '' }}>Open Source</option>
                    </select>
                    @error('license')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-4">
                    <label class="form-label" for="price">Price (USD)</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control @error('price') is-invalid @enderror" 
                             id="price" name="price" min="0" step="0.01" value="{{ old('price') }}" 
                             placeholder="0.00">
                    </div>
                    @error('price')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <!-- Release Date -->
              <div class="mb-4">
                <label class="form-label" for="released_at">Release Date</label>
                <input type="date" class="form-control @error('released_at') is-invalid @enderror" 
                       id="released_at" name="released_at" value="{{ old('released_at') }}">
                @error('released_at')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <!-- Requirements & Platforms -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">System Requirements & Platforms</h5>
            </div>
            <div class="card-body">
              <!-- System Requirements -->
              <div class="mb-4">
                <label class="form-label">System Requirements</label>
                <div id="requirements-container">
                  @if(old('requirements'))
                    @foreach(old('requirements') as $index => $requirement)
                      <div class="input-group mb-2 requirement-input">
                        <input type="text" class="form-control" name="requirements[]" value="{{ $requirement }}" placeholder="Enter requirement">
                        <button type="button" class="btn btn-outline-danger remove-requirement">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    @endforeach
                  @else
                    <div class="input-group mb-2 requirement-input">
                      <input type="text" class="form-control" name="requirements[]" placeholder="Enter requirement">
                      <button type="button" class="btn btn-outline-danger remove-requirement">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-requirement">
                  <i class="icon-base ti tabler-plus"></i>Add Requirement
                </button>
              </div>

              <!-- Supported Platforms -->
              <div class="mb-4">
                <label class="form-label">Supported Platforms</label>
                <div id="platforms-container">
                  @if(old('platforms'))
                    @foreach(old('platforms') as $index => $platform)
                      <div class="input-group mb-2 platform-input">
                        <input type="text" class="form-control" name="platforms[]" value="{{ $platform }}" placeholder="Enter platform">
                        <button type="button" class="btn btn-outline-danger remove-platform">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    @endforeach
                  @else
                    <div class="input-group mb-2 platform-input">
                      <input type="text" class="form-control" name="platforms[]" placeholder="Enter platform">
                      <button type="button" class="btn btn-outline-danger remove-platform">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-success" id="add-platform">
                  <i class="icon-base ti tabler-plus"></i>Add Platform
                </button>
              </div>

              <!-- Tags -->
              <div class="mb-4">
                <label class="form-label">Tags</label>
                <div id="tags-container">
                  @if(old('tags'))
                    @foreach(old('tags') as $index => $tag)
                      <div class="input-group mb-2 tag-input">
                        <input type="text" class="form-control" name="tags[]" value="{{ $tag }}" placeholder="Enter tag">
                        <button type="button" class="btn btn-outline-danger remove-tag">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    @endforeach
                  @else
                    <div class="input-group mb-2 tag-input">
                      <input type="text" class="form-control" name="tags[]" placeholder="Enter tag">
                      <button type="button" class="btn btn-outline-danger remove-tag">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-info" id="add-tag">
                  <i class="icon-base ti tabler-plus"></i>Add Tag
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
          <!-- File Upload -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Software Source</h5>
            </div>
            <div class="card-body">
              <!-- Source Type Selection -->
              <div class="mb-4">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" name="source_type" id="source_file" value="file" checked>
                  <label class="form-check-label" for="source_file">
                    <i class="icon-base ti tabler-upload me-2"></i>Upload File
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="source_type" id="source_url" value="url">
                  <label class="form-check-label" for="source_url">
                    <i class="icon-base ti tabler-link me-2"></i>External URL
                  </label>
                </div>
              </div>

              <!-- File Upload -->
              <div class="mb-4" id="file-upload-section">
                <label class="form-label" for="file">Upload Software File</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                       id="file" name="file" accept=".exe,.msi,.zip,.rar,.dmg,.pkg,.deb,.rpm">
                @error('file')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Supported: EXE, MSI, ZIP, RAR, DMG, PKG, DEB, RPM. Max: 100MB</div>
              </div>

              <!-- External URL -->
              <div class="mb-4" id="url-section" style="display: none;">
                <label class="form-label" for="external_url">External Download URL</label>
                <input type="url" class="form-control @error('external_url') is-invalid @enderror" 
                       id="external_url" name="external_url" value="{{ old('external_url') }}" 
                       placeholder="https://example.com/software.exe">
                @error('external_url')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Direct link to download the software</div>
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
                         value="1" {{ old('status', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="status">
                    Active Status
                  </label>
                </div>
                <div class="form-text">Enable to make software visible on website</div>
              </div>

              <!-- Featured -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="featured" id="featured" 
                         value="1" {{ old('featured') ? 'checked' : '' }}>
                  <label class="form-check-label" for="featured">
                    Featured Software
                  </label>
                </div>
                <div class="form-text">Featured software appear prominently</div>
              </div>

              <!-- Free Software -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_free" id="is_free" 
                         value="1" {{ old('is_free', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_free">
                    Free Software
                  </label>
                </div>
                <div class="form-text">Mark as free or paid software</div>
              </div>

              <!-- Sort Order -->
              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                       min="0" placeholder="0">
                @error('sort_order')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Lower numbers appear first</div>
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
                       id="meta_title" name="meta_title" value="{{ old('meta_title') }}" 
                       placeholder="SEO title">
                @error('meta_title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                          id="meta_description" name="meta_description" rows="3" 
                          placeholder="SEO description">{{ old('meta_description') }}</textarea>
                @error('meta_description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_keywords">Meta Keywords</label>
                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}" 
                       placeholder="keyword1, keyword2, keyword3">
                @error('meta_keywords')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Separate keywords with commas</div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card">
            <div class="card-body">
              <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="icon-base ti tabler-check me-2"></i>Create Software
              </button>
              <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary w-100">
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

    // Source type toggle
    const sourceRadios = document.querySelectorAll('input[name="source_type"]');
    const fileSection = document.getElementById('file-upload-section');
    const urlSection = document.getElementById('url-section');
    const fileInput = document.getElementById('file');
    const urlInput = document.getElementById('external_url');
    
    function toggleSourceInputs() {
        const selectedSource = document.querySelector('input[name="source_type"]:checked').value;
        
        if (selectedSource === 'file') {
            fileSection.style.display = 'block';
            urlSection.style.display = 'none';
            fileInput.disabled = false;
            urlInput.disabled = true;
            urlInput.value = '';
        } else {
            fileSection.style.display = 'none';
            urlSection.style.display = 'block';
            fileInput.disabled = true;
            urlInput.disabled = false;
        }
    }
    
    sourceRadios.forEach(radio => {
        radio.addEventListener('change', toggleSourceInputs);
    });
    
    // Initialize on page load
    toggleSourceInputs();

    // Dynamic array inputs
    function setupDynamicInputs(containerId, inputClass, addButtonId, removeButtonClass, placeholder) {
        const container = document.getElementById(containerId);
        const addButton = document.getElementById(addButtonId);
        
        addButton.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = `input-group mb-2 ${inputClass}`;
            div.innerHTML = `
                <input type="text" class="form-control" name="${inputClass.replace('-input', '')}[]" placeholder="${placeholder}">
                <button type="button" class="btn btn-outline-danger ${removeButtonClass}">
                    <i class="icon-base ti tabler-trash"></i>
                </button>
            `;
            container.appendChild(div);
        });
        
        container.addEventListener('click', function(e) {
            if (e.target.closest(`.${removeButtonClass}`)) {
                const inputGroup = e.target.closest('.input-group');
                if (container.children.length > 1) {
                    inputGroup.remove();
                }
            }
        });
    }
    
    setupDynamicInputs('requirements-container', 'requirement-input', 'add-requirement', 'remove-requirement', 'Enter requirement');
    setupDynamicInputs('platforms-container', 'platform-input', 'add-platform', 'remove-platform', 'Enter platform');
    setupDynamicInputs('tags-container', 'tag-input', 'add-tag', 'remove-tag', 'Enter tag');
});
</script>
@endpush