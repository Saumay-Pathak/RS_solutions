@extends('admin.layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Create New Product</h4>
        <p class="mb-0">Add a new product to your inventory</p>
      </div>
      <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Products
      </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
          <!-- Basic Information -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-package me-2"></i>Product Information
              </h5>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="title">Product Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('title') is-invalid @enderror" 
                         id="title" name="title" value="{{ old('title') }}" 
                         placeholder="Enter product name" required>
                  @error('title')
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
                <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4" 
                          placeholder="Describe your product" required>{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                  <select class="form-select @error('category_id') is-invalid @enderror" 
                          id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                      <option value="{{ $category->_id }}" {{ old('category_id') == $category->_id ? 'selected' : '' }}>
                        {{ $category->parent ? $category->parent->name . ' > ' : '' }}{{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('category_id')
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

          <!-- Features -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-list-check me-2"></i>Product Features
              </h5>
              <button type="button" class="btn btn-sm btn-primary" id="add-feature">
                <i class="icon-base ti tabler-plus"></i>Add Feature
              </button>
            </div>
            <div class="card-body">
              <div id="features-container">
                @if(old('features'))
                  @foreach(old('features') as $index => $feature)
                    <div class="mb-3 feature-item">
                      <div class="input-group">
                        <input type="text" name="features[]" class="form-control" 
                               placeholder="Enter product feature" value="{{ $feature }}">
                        <button type="button" class="btn btn-outline-danger remove-feature">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="mb-3 feature-item">
                    <div class="input-group">
                      <input type="text" name="features[]" class="form-control" 
                             placeholder="Enter product feature">
                      <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  </div>
                @endif
              </div>
              @error('features')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Specifications -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-settings me-2"></i>Specifications
              </h5>
              <button type="button" class="btn btn-sm btn-info" id="add-specification">
                <i class="icon-base ti tabler-plus"></i>Add Specification
              </button>
            </div>
            <div class="card-body">
              <div id="specifications-container">
                @if(old('specification_titles'))
                  @foreach(old('specification_titles') as $index => $title)
                    <div class="row mb-3 specification-item">
                      <div class="col-md-4">
                        <input type="text" name="specification_titles[]" class="form-control" 
                               placeholder="Specification title" value="{{ $title }}">
                      </div>
                      <div class="col-md-7">
                        <input type="text" name="specification_values[]" class="form-control" 
                               placeholder="Specification value" value="{{ old('specification_values')[$index] ?? '' }}">
                      </div>
                      <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-specification">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="row mb-3 specification-item">
                    <div class="col-md-4">
                      <input type="text" name="specification_titles[]" class="form-control" 
                             placeholder="Specification title">
                    </div>
                    <div class="col-md-7">
                      <input type="text" name="specification_values[]" class="form-control" 
                             placeholder="Specification value">
                    </div>
                    <div class="col-md-1">
                      <button type="button" class="btn btn-outline-danger remove-specification">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  </div>
                @endif
              </div>
              @error('specification_titles')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- FAQs -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-help-circle me-2"></i>FAQs
              </h5>
              <button type="button" class="btn btn-sm btn-success" id="add-faq">
                <i class="icon-base ti tabler-plus"></i>Add FAQ
              </button>
            </div>
            <div class="card-body">
              <div id="faqs-container">
                @if(old('faq_questions'))
                  @foreach(old('faq_questions') as $index => $q)
                    <div class="mb-4 faq-item">
                      <div class="row g-3 align-items-start">
                        <div class="col-md-5">
                          <input type="text" name="faq_questions[]" class="form-control" placeholder="Question" value="{{ $q }}">
                        </div>
                        <div class="col-md-6">
                          <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer">{{ old('faq_answers')[$index] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-1">
                          <button type="button" class="btn btn-outline-danger remove-faq" title="Remove FAQ">
                            <i class="icon-base ti tabler-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="mb-4 faq-item">
                    <div class="row g-3 align-items-start">
                      <div class="col-md-5">
                        <input type="text" name="faq_questions[]" class="form-control" placeholder="Question">
                      </div>
                      <div class="col-md-6">
                        <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer"></textarea>
                      </div>
                      <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-faq" title="Remove FAQ">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <!-- A+ Content (HTML) -->
          <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-file-text me-2"></i>A+ Content (HTML)
              </h5>
              <small class="text-muted">Paste HTML or upload .html file</small>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="a_plus_content">A+ Content HTML</label>
                <textarea class="form-control @error('a_plus_content') is-invalid @enderror"
                          id="a_plus_content" name="a_plus_content" rows="10"
                          placeholder="Enter rich HTML content">{{ old('a_plus_content') }}</textarea>
                <div class="form-text">This renders as-is on the product page. Admin-only field.</div>
                @error('a_plus_content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-2">
                <label class="form-label" for="a_plus_content_file">Or upload HTML file</label>
                <input type="file" class="form-control @error('a_plus_content_file') is-invalid @enderror"
                       id="a_plus_content_file" name="a_plus_content_file" accept=".html,.htm,.txt">
                <div class="form-text">Max 10MB. If provided, uploaded file content overrides the textarea.</div>
                @error('a_plus_content_file')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
          <!-- Product Settings -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-settings me-2"></i>Product Settings
              </h5>
            </div>
            <div class="card-body">
              <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  <span class="fw-semibold">Active Status</span>
                  <div class="form-text">Make this product visible to users</div>
                </label>
              </div>

              <!-- Product Stats Preview -->
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

          <!-- Product Images -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-photo me-2"></i>Product Images
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <input type="file" class="form-control @error('images') is-invalid @enderror" 
                       id="images" name="images[]" accept="image/*" multiple>
                <div class="form-text">Max 2MB per image. JPG, PNG, GIF supported</div>
                @error('images')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <!-- Images Preview -->
              <div id="images-preview" class="row g-2 d-none">
                <!-- Preview images will be added here -->
              </div>

              <!-- Default Preview -->
              <div id="default-images-preview" class="text-center p-4 bg-light rounded">
                <i class="icon-base ti tabler-photo display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">Upload product images</p>
              </div>
            </div>
          </div>

          <!-- Product Documents -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ti tabler-file-text me-2"></i>Product Documents
              </h5>
            </div>
            <div class="card-body">
              <div class="row g-4">
                <div class="col-md-6">
                  <label for="datasheet_document" class="form-label">Datasheet</label>
                  <input type="file" class="form-control @error('datasheet_document') is-invalid @enderror" 
                         id="datasheet_document" name="datasheet_document" accept=".pdf,.doc,.docx">
                  <div class="form-text">Upload PDF/DOC (specifications)</div>
                  @error('datasheet_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="connection_diagram_document" class="form-label">Connection Diagram</label>
                  <input type="file" class="form-control @error('connection_diagram_document') is-invalid @enderror" 
                         id="connection_diagram_document" name="connection_diagram_document" accept=".pdf,.doc,.docx">
                  <div class="form-text">Upload wiring/connection guide</div>
                  @error('connection_diagram_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="user_manual_document" class="form-label">User Manual</label>
                  <input type="file" class="form-control @error('user_manual_document') is-invalid @enderror" 
                         id="user_manual_document" name="user_manual_document" accept=".pdf,.doc,.docx">
                  <div class="form-text">Upload operation manual</div>
                  @error('user_manual_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="catalogue_document" class="form-label">Catalogue</label>
                  <input type="file" class="form-control @error('catalogue_document') is-invalid @enderror" 
                         id="catalogue_document" name="catalogue_document" accept=".pdf,.doc,.docx">
                  <div class="form-text">Upload marketing catalogue</div>
                  @error('catalogue_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
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
                  <i class="icon-base ti tabler-plus"></i>Create Product
                </button>
                <button type="submit" name="create_another" value="1" class="btn btn-outline-primary">
                  <i class="icon-base ti tabler-plus"></i>Create & Add Another
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
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

    // Features management
    const featuresContainer = document.getElementById('features-container');
    const addFeatureBtn = document.getElementById('add-feature');
    
    addFeatureBtn.addEventListener('click', function() {
        const featureItem = document.createElement('div');
        featureItem.className = 'mb-3 feature-item';
        featureItem.innerHTML = `
            <div class="input-group">
                <input type="text" name="features[]" class="form-control" 
                       placeholder="Enter product feature">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="icon-base ti tabler-trash"></i>
                </button>
            </div>
        `;
        featuresContainer.appendChild(featureItem);
    });
    
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            const featureItems = featuresContainer.querySelectorAll('.feature-item');
            if (featureItems.length > 1) {
                e.target.closest('.feature-item').remove();
            }
        }
    });

    // Specifications management
    const specificationsContainer = document.getElementById('specifications-container');
    const addSpecBtn = document.getElementById('add-specification');
    
    addSpecBtn.addEventListener('click', function() {
        const specItem = document.createElement('div');
        specItem.className = 'row mb-3 specification-item';
        specItem.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="specification_titles[]" class="form-control" 
                       placeholder="Specification title">
            </div>
            <div class="col-md-7">
                <input type="text" name="specification_values[]" class="form-control" 
                       placeholder="Specification value">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger remove-specification">
                    <i class="icon-base ti tabler-trash"></i>
                </button>
            </div>
        `;
        specificationsContainer.appendChild(specItem);
    });
    
    specificationsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-specification')) {
            const specItems = specificationsContainer.querySelectorAll('.specification-item');
            if (specItems.length > 1) {
                e.target.closest('.specification-item').remove();
            }
        }
    });

    // FAQs management
    const faqsContainer = document.getElementById('faqs-container');
    const addFaqBtn = document.getElementById('add-faq');

    addFaqBtn.addEventListener('click', function() {
        const faqItem = document.createElement('div');
        faqItem.className = 'mb-4 faq-item';
        faqItem.innerHTML = `
            <div class="row g-3 align-items-start">
                <div class="col-md-5">
                    <input type="text" name="faq_questions[]" class="form-control" placeholder="Question">
                </div>
                <div class="col-md-6">
                    <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger remove-faq" title="Remove FAQ">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </div>
            </div>
        `;
        faqsContainer.appendChild(faqItem);
    });

    faqsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-faq')) {
            const faqItems = faqsContainer.querySelectorAll('.faq-item');
            if (faqItems.length > 1) {
                e.target.closest('.faq-item').remove();
            }
        }
    });

    // Multiple images preview
    const imagesInput = document.getElementById('images');
    const imagesPreview = document.getElementById('images-preview');
    const defaultImagesPreview = document.getElementById('default-images-preview');
    
    imagesInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        imagesPreview.innerHTML = '';
        
        if (files.length > 0) {
            imagesPreview.classList.remove('d-none');
            defaultImagesPreview.classList.add('d-none');
            
            files.forEach((file, index) => {
                if (file.size > 2 * 1024 * 1024) {
                    alert(`File ${file.name} is too large (max 2MB)`);
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-image-preview" data-index="${index}">
                                <i class="icon-base ti tabler-trash" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    `;
                    imagesPreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        } else {
            imagesPreview.classList.add('d-none');
            defaultImagesPreview.classList.remove('d-none');
        }
    });
    
    imagesPreview.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image-preview')) {
            e.target.closest('.col-6, .col-sm-4, .col-md-3').remove();
            if (imagesPreview.children.length === 0) {
                imagesPreview.classList.add('d-none');
                defaultImagesPreview.classList.remove('d-none');
                imagesInput.value = '';
            }
        }
    });

    // Document inputs (optional preview could be added later)

    // Form validation enhancement
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const titleValue = titleInput.value.trim();
        const categoryValue = document.getElementById('category_id').value;
        const descriptionValue = descriptionTextarea.value.trim();
        
        if (!titleValue) {
            e.preventDefault();
            titleInput.focus();
            titleInput.classList.add('is-invalid');
            alert('Product title is required');
        }
        
        if (!categoryValue) {
            e.preventDefault();
            document.getElementById('category_id').focus();
            alert('Category selection is required');
        }
        
        if (!descriptionValue) {
            e.preventDefault();
            descriptionTextarea.focus();
            descriptionTextarea.classList.add('is-invalid');
            alert('Product description is required');
        }
    });
});
</script>
@endpush
