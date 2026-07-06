@extends('layouts.app')

@section('title', 'Edit Product - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    @if(session('error'))
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="icon-base ti tabler-alert-circle me-2"></i>
        <div>{{ session('error') }}</div>
      </div>
      @push('scripts')
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var msg = @json(session('error'));
          if (typeof toastr !== 'undefined') {
            toastr.error(msg);
          } else {
            alert(msg);
          }
        });
      </script>
      @endpush
    @endif

    @if($errors->any())
      @php $firstError = collect($errors->all())->first(); @endphp
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="icon-base ti tabler-alert-triangle me-2"></i>
        <div>Update failed: {{ $firstError }}</div>
      </div>
      @push('scripts')
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var msg = @json($firstError ?? 'Update failed. Please fix the highlighted errors.');
          if (typeof toastr !== 'undefined') {
            toastr.error(msg);
          } else {
            alert(msg);
          }
        });
      </script>
      @endpush
    @endif
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Product: {{ $product->title }}</h4>
        <p class="mb-0">Update your product information and settings</p>
      </div>
      <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Products
      </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
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
                         id="title" name="title" value="{{ old('title', $product->title) }}" 
                         placeholder="Enter product name" required>
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="slug">URL Slug</label>
                  <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                         id="slug" name="slug" value="{{ old('slug', $product->slug) }}" 
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
                          placeholder="Describe your product" required>{{ old('description', $product->description) }}</textarea>
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
                    @php
                      // Ensure reliable comparison when using Mongo ObjectId types
                      $currentCategoryId = (string) old('category_id', (string) ($product->category_id ?? ''));
                    @endphp
                    @foreach($categories as $category)
                      @php $optionId = (string) ($category->_id ?? ''); @endphp
                      <option value="{{ $optionId }}" {{ $currentCategoryId === $optionId ? 'selected' : '' }}>
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
                         id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" 
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
                @php
                  $featuresData = [];
                  $oldTitles = old('feature_titles');
                  $oldIcons = old('feature_icons');
                  if (is_array($oldTitles)) {
                    foreach ($oldTitles as $i => $t) {
                      $featuresData[] = ['title' => $t, 'icon' => $oldIcons[$i] ?? ''];
                    }
                  } elseif (!empty($product->features)) {
                    foreach ($product->features as $f) {
                      if (is_array($f)) {
                        $featuresData[] = ['title' => $f['title'] ?? '', 'icon' => $f['icon'] ?? ''];
                      } else {
                        $featuresData[] = ['title' => $f, 'icon' => ''];
                      }
                    }
                  }
                @endphp
                @if(!empty($featuresData))
                  @foreach($featuresData as $feature)
                    <div class="mb-3 feature-item">
                      <div class="row g-2 align-items-center">
                        <div class="col-auto">
                          <div class="d-flex align-items-center gap-2">
                            <span class="feature-icon-preview d-flex align-items-center gap-2">
                              @if(!empty($feature['icon']))
                                <i class="icon-base ti {{ $feature['icon'] }} text-orange-600" style="font-size: 1.25rem;"></i>
                                <small class="text-muted feature-icon-name">{{ str_replace('tabler-','',$feature['icon']) }}</small>
                              @else
                                <i class="icon-base ti tabler-star text-muted" style="font-size: 1.25rem;"></i>
                                <small class="text-muted feature-icon-name">none</small>
                              @endif
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary pick-feature-icon">
                              <i class="icon-base ti tabler-icons"></i> Pick Icon
                            </button>
                          </div>
                          <input type="hidden" name="feature_icons[]" class="feature-icon-input" value="{{ $feature['icon'] }}">
                        </div>
                        <div class="col">
                          <input type="text" name="feature_titles[]" class="form-control" placeholder="Feature title" value="{{ $feature['title'] }}">
                        </div>
                        <div class="col-auto">
                          <button type="button" class="btn btn-outline-danger remove-feature">
                            <i class="icon-base ti tabler-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="mb-3 feature-item">
                    <div class="row g-2 align-items-center">
                      <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                          <span class="feature-icon-preview d-flex align-items-center gap-2">
                            <i class="icon-base ti tabler-star text-muted" style="font-size: 1.25rem;"></i>
                            <small class="text-muted feature-icon-name">none</small>
                          </span>
                          <button type="button" class="btn btn-sm btn-outline-primary pick-feature-icon">
                            <i class="icon-base ti tabler-icons"></i> Pick Icon
                          </button>
                        </div>
                        <input type="hidden" name="feature_icons[]" class="feature-icon-input" value="">
                      </div>
                      <div class="col">
                        <input type="text" name="feature_titles[]" class="form-control" placeholder="Feature title">
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger remove-feature">
                          <i class="icon-base ti tabler-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
              @error('feature_titles')
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
                @if(old('specification_titles') || ($product->specifications && count($product->specifications) > 0))
                  @php
                    $specs = old('specification_titles') ? 
                      array_map(function($title, $index) {
                        return ['title' => $title, 'value' => old('specification_values')[$index] ?? ''];
                      }, old('specification_titles'), array_keys(old('specification_titles'))) :
                      ($product->specifications ?? []);
                  @endphp
                  @foreach($specs as $index => $spec)
                    <div class="row mb-3 specification-item">
                      <div class="col-md-4">
                        <input type="text" name="specification_titles[]" class="form-control" 
                               placeholder="Specification title" value="{{ is_array($spec) ? $spec['title'] : $spec }}">
                      </div>
                      <div class="col-md-7">
                        <input type="text" name="specification_values[]" class="form-control" 
                               placeholder="Specification value" value="{{ is_array($spec) ? $spec['value'] : '' }}">
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
                @php
                  $faqs = [];
                  if (old('faq_questions')) {
                    foreach (old('faq_questions') as $index => $q) {
                      $faqs[] = [
                        'question' => $q,
                        'answer' => old('faq_answers')[$index] ?? ''
                      ];
                    }
                  } elseif (!empty($product->faqs)) {
                    $faqs = $product->faqs;
                  }
                @endphp
                @if(!empty($faqs))
                  @foreach($faqs as $faq)
                    <div class="mb-4 faq-item">
                      <div class="row g-3 align-items-start">
                        <div class="col-md-5">
                          <input type="text" name="faq_questions[]" class="form-control" placeholder="Question" value="{{ is_array($faq) ? ($faq['question'] ?? '') : '' }}">
                        </div>
                        <div class="col-md-6">
                          <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer">{{ is_array($faq) ? ($faq['answer'] ?? '') : '' }}</textarea>
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
                          placeholder="Enter rich HTML content">{{ old('a_plus_content', $product->a_plus_content) }}</textarea>
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
                       id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" 
                       placeholder="SEO optimized title" maxlength="255">
                <div class="form-text">
                  <span id="meta-title-count">{{ strlen($product->meta_title ?? '') }}</span>/255 characters
                </div>
                @error('meta_title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                          id="meta_description" name="meta_description" rows="3" 
                          placeholder="Brief description for search engines" maxlength="500">{{ old('meta_description', $product->meta_description) }}</textarea>
                <div class="form-text">
                  <span id="meta-desc-count">{{ strlen($product->meta_description ?? '') }}</span>/500 characters
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
                <i class="icon-base ti tabler-settings me-2"></i>Product Settings
              </h5>
            </div>
            <div class="card-body">
              <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status', $product->status) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  <span class="fw-semibold">Active Status</span>
                  <div class="form-text">Make this product visible to users</div>
                </label>
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
              <!-- Current Images -->
              @if($product->images && count($product->images) > 0)
                <div class="mb-3">
                  <h6 class="mb-3">Current Images:</h6>
                  <div class="row" id="product-images-container">
                    @foreach($product->images as $index => $image)
                      <div class="col-6 mb-3" data-image-index="{{ $index }}">
                        <div class="position-relative">
                          <img src="{{ Storage::url($image) }}" alt="Product Image" 
                               class="img-fluid rounded" style="max-height: 100px; width: 100%; object-fit: cover;">
                          <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-product-image" 
                                  data-image-index="{{ $index }}" 
                                  data-product-id="{{ $product->_id }}"
                                  title="Delete this image">
                            <i class="icon-base ti tabler-trash"></i>
                          </button>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif
              
              <div class="mb-3">
                <input type="file" class="form-control @error('images') is-invalid @enderror" 
                       id="images" name="images[]" accept="image/*" multiple>
                <div class="form-text">Max 2MB each. JPG, PNG, GIF supported. Select multiple files to add new images (will NOT replace existing images).</div>
                @error('images')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                  <label class="form-label">Datasheet</label>
                  @if($product->datasheet_document)
                    <div class="mb-2 d-flex align-items-center justify-content-between">
                      <a href="{{ Storage::url($product->datasheet_document) }}" target="_blank" class="text-decoration-none">
                        <i class="icon-base ti tabler-file-text me-2"></i>View current datasheet
                      </a>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remove_datasheet_document" name="remove_datasheet_document" value="1">
                        <label class="form-check-label" for="remove_datasheet_document">Remove</label>
                      </div>
                    </div>
                  @endif
                  <input type="file" class="form-control @error('datasheet_document') is-invalid @enderror"
                         name="datasheet_document" accept=".pdf,.doc,.docx">
                  @error('datasheet_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label">Connection Diagram</label>
                  @if($product->connection_diagram_document)
                    <div class="mb-2 d-flex align-items-center justify-content-between">
                      <a href="{{ Storage::url($product->connection_diagram_document) }}" target="_blank" class="text-decoration-none">
                        <i class="icon-base ti tabler-file-text me-2"></i>View current connection diagram
                      </a>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remove_connection_diagram_document" name="remove_connection_diagram_document" value="1">
                        <label class="form-check-label" for="remove_connection_diagram_document">Remove</label>
                      </div>
                    </div>
                  @endif
                  <input type="file" class="form-control @error('connection_diagram_document') is-invalid @enderror"
                         name="connection_diagram_document" accept=".pdf,.doc,.docx">
                  @error('connection_diagram_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label">User Manual</label>
                  @if($product->user_manual_document)
                    <div class="mb-2 d-flex align-items-center justify-content-between">
                      <a href="{{ Storage::url($product->user_manual_document) }}" target="_blank" class="text-decoration-none">
                        <i class="icon-base ti tabler-file-text me-2"></i>View current user manual
                      </a>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remove_user_manual_document" name="remove_user_manual_document" value="1">
                        <label class="form-check-label" for="remove_user_manual_document">Remove</label>
                      </div>
                    </div>
                  @endif
                  <input type="file" class="form-control @error('user_manual_document') is-invalid @enderror"
                         name="user_manual_document" accept=".pdf,.doc,.docx">
                  @error('user_manual_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label">Catalogue</label>
                  @if($product->catalogue_document)
                    <div class="mb-2 d-flex align-items-center justify-content-between">
                      <a href="{{ Storage::url($product->catalogue_document) }}" target="_blank" class="text-decoration-none">
                        <i class="icon-base ti tabler-file-text me-2"></i>View current catalogue
                      </a>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remove_catalogue_document" name="remove_catalogue_document" value="1">
                        <label class="form-check-label" for="remove_catalogue_document">Remove</label>
                      </div>
                    </div>
                  @endif
                  <input type="file" class="form-control @error('catalogue_document') is-invalid @enderror"
                         name="catalogue_document" accept=".pdf,.doc,.docx">
                  @error('catalogue_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
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
                  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                    <i class="icon-base ti tabler-trash me-2"></i>Delete Product
                  </button>
                </div>
                <div class="d-flex gap-3">
                  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-x me-2"></i>Cancel
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-check me-2"></i>Update Product
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

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <i class="icon-base ti tabler-alert-triangle text-warning mb-3" style="font-size: 3rem;"></i>
          <h6>Are you sure you want to delete this product?</h6>
          <p class="text-muted">This action cannot be undone. The product and all its images will be permanently deleted.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="icon-base ti tabler-trash me-2"></i>Delete Product
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
    });
    
    metaDescInput.addEventListener('input', function() {
        metaDescCount.textContent = this.value.length;
    });

    // Features with icon picker
    const addFeatureBtn = document.getElementById('add-feature');
    const featuresContainer = document.getElementById('features-container');
    let activeFeatureItem = null;

    addFeatureBtn.addEventListener('click', function() {
        const featureItem = document.createElement('div');
        featureItem.className = 'mb-3 feature-item';
        featureItem.innerHTML = `
            <div class="row g-2 align-items-center">
              <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                  <span class="feature-icon-preview">
                    <i class="icon-base ti tabler-star text-muted" style="font-size: 1.25rem;"></i>
                  </span>
                  <button type="button" class="btn btn-sm btn-outline-primary pick-feature-icon">
                    <i class="icon-base ti tabler-icons"></i> Pick Icon
                  </button>
                </div>
                <input type="hidden" name="feature_icons[]" class="feature-icon-input" value="">
              </div>
              <div class="col">
                <input type="text" name="feature_titles[]" class="form-control" placeholder="Feature title">
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-outline-danger remove-feature">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </div>
            </div>
        `;
        featuresContainer.appendChild(featureItem);
    });

    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            e.target.closest('.feature-item').remove();
            return;
        }
        const pickBtn = e.target.closest('.pick-feature-icon');
        if (pickBtn) {
            // store active feature item globally for modal script
            window.activeFeatureItem = pickBtn.closest('.feature-item');

            const modalEl = document.getElementById('featureIconPickerModal');
            if (!modalEl) {
                console.warn('Feature icon picker modal not found in DOM');
                return;
            }
            const iconPickerModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            iconPickerModal.show();
        }
    });

    // Specifications management
    const addSpecBtn = document.getElementById('add-specification');
    const specsContainer = document.getElementById('specifications-container');
    
    addSpecBtn.addEventListener('click', function() {
        const specItem = document.createElement('div');
        specItem.className = 'row mb-3 specification-item';
        specItem.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="specification_titles[]" class="form-control" placeholder="Specification title">
            </div>
            <div class="col-md-7">
                <input type="text" name="specification_values[]" class="form-control" placeholder="Specification value">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger remove-specification">
                    <i class="icon-base ti tabler-trash"></i>
                </button>
            </div>
        `;
        specsContainer.appendChild(specItem);
    });
    
    specsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-specification')) {
            e.target.closest('.specification-item').remove();
        }
    });

    // FAQs management
    const addFaqBtn = document.getElementById('add-faq');
    const faqsContainer = document.getElementById('faqs-container');

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
            e.target.closest('.faq-item').remove();
        }
    });

    // Delete individual product image
    $(document).on('click', '.delete-product-image', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }
        
        const button = $(this);
        const imageIndex = button.data('image-index');
        const productId = button.data('product-id');
        const imageContainer = button.closest('.col-6');
        
        $.ajax({
            url: '{{ route("admin.products.delete-image", $product) }}',
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',
                image_index: imageIndex
            },
            success: function(response) {
                if (response.success) {
                    imageContainer.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if no images remain
                        if ($('#product-images-container').children().length === 0) {
                            $('#product-images-container').closest('.mb-3').fadeOut(300);
                        }
                    });
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error deleting image');
                    } else {
                        alert(response.message || 'Error deleting image');
                    }
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error deleting image';
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            }
        });
    });
});
</script>
@endpush

@push('modals')
<!-- Feature Icon Picker Modal -->
<div class="modal fade" id="featureIconPickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pick Feature Icon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 d-flex gap-2">
          <input type="text" class="form-control" id="feature-icon-search" placeholder="Search icons (e.g., credit-card, key, lock)">
          <button type="button" class="btn btn-outline-secondary" id="feature-icon-load-more">Load more</button>
        </div>
        <div class="row g-3" id="feature-icon-grid"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const iconSearch = document.getElementById('feature-icon-search');
  const iconGrid = document.getElementById('feature-icon-grid');
  const loadMoreBtn = document.getElementById('feature-icon-load-more');

  let allIcons = [];
  let renderedCount = 0;
  const PAGE_SIZE = 100; // render 100 at a time

  function renderIcons(reset = false) {
    const q = iconSearch.value.trim().toLowerCase();
    const filtered = !q ? allIcons : allIcons.filter(name => name.includes(q));
    const batch = filtered.slice(0, reset ? PAGE_SIZE : renderedCount + PAGE_SIZE);
    if (reset) {
      iconGrid.innerHTML = '';
      renderedCount = 0;
    }
    batch.slice(renderedCount).forEach(icon => {
      const col = document.createElement('div');
      col.className = 'col-6 col-sm-4 col-md-3';
      col.innerHTML = `
        <button type="button" class="btn btn-outline-secondary w-100 pick-icon-option" data-icon="tabler-${icon}" data-name="${icon}">
          <div class="d-flex flex-column align-items-center py-3">
            <i class="icon-base ti tabler-${icon}" style="font-size: 1.5rem;"></i>
            <small class="mt-2 text-muted">${icon}</small>
          </div>
        </button>`;
      iconGrid.appendChild(col);
    });
    renderedCount = batch.length;
    loadMoreBtn.disabled = renderedCount >= filtered.length;

    // Highlight current selection if present
    const current = window.activeFeatureItem?.querySelector('.feature-icon-input')?.value || '';
    if (current) {
      Array.from(iconGrid.querySelectorAll('.pick-icon-option')).forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-icon') === current);
      });
    }
  }

  // Fetch available Tabler icon names by parsing CSS once
  async function loadAllIcons() {
    try {
      const cssUrl = '{{ asset('assets/vendor/fonts/iconify-icons.css') }}';
      const res = await fetch(cssUrl);
      const cssText = await res.text();
      const regex = /\.tabler-([a-z0-9\-]+)\s*\{/g;
      const names = new Set();
      let m;
      while ((m = regex.exec(cssText)) !== null) {
        names.add(m[1]);
      }
      allIcons = Array.from(names);
      allIcons.sort();
      renderIcons(true);
    } catch (e) {
      console.error('Failed to load icon list', e);
    }
  }

  iconGrid.addEventListener('click', function(e) {
    const option = e.target.closest('.pick-icon-option');
    if (!option) return;
    const icon = option.getAttribute('data-icon');
    if (window.activeFeatureItem) {
      const preview = window.activeFeatureItem.querySelector('.feature-icon-preview');
      const hiddenInput = window.activeFeatureItem.querySelector('.feature-icon-input');
      const nameEl = window.activeFeatureItem.querySelector('.feature-icon-name');
      hiddenInput.value = icon;
      preview.querySelector('i').className = `icon-base ti ${icon}`;
      preview.querySelector('i').classList.add('text-orange-600');
      if (nameEl) nameEl.textContent = icon.replace('tabler-','');
      const modalEl = document.getElementById('featureIconPickerModal');
      const modal = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.hide();
    }
  });

  iconSearch.addEventListener('input', function() {
    renderIcons(true);
  });

  loadMoreBtn.addEventListener('click', function() {
    renderIcons(false);
  });

  // Initial load
  loadAllIcons();
});
</script>
@endpush
