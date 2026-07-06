@extends('admin.layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Product</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Products
                </a>
            </div>
            
            <div class="card-body">
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">Basic Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <div class="mb-3">
                  <label class="form-label">Product Title <span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter product title" value="{{ old('title') }}" required>
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="mb-3">
                  <label class="form-label">URL Slug</label>
                  <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="Auto-generated from title" value="{{ old('slug') }}">
                  @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">Leave empty to auto-generate from title</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Category <span class="text-danger">*</span></label>
                  <select name="category_id" class="form-select select2 @error('category_id') is-invalid @enderror" required>
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
                <div class="mb-3">
                  <label class="form-label">Sort Order</label>
                  <input type="number" name="sort_order" class="form-control" placeholder="0" value="{{ old('sort_order', 0) }}" min="0">
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Product Description <span class="text-danger">*</span></label>
                <div id="description-editor">
                  <p>{{ old('description') }}</p>
                </div>
                <textarea name="description" class="d-none @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- Features Section -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Product Features</h5>
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
                      <input type="text" name="features[]" class="form-control" placeholder="Enter product feature" value="{{ $feature }}">
                      <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="mb-3 feature-item">
                  <div class="input-group">
                    <input type="text" name="features[]" class="form-control" placeholder="Enter product feature">
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

        <!-- Specifications Section -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Product Specifications</h5>
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
                      <input type="text" name="specification_titles[]" class="form-control" placeholder="Specification title" value="{{ $title }}">
                    </div>
                    <div class="col-md-7">
                      <input type="text" name="specification_values[]" class="form-control" placeholder="Specification value" value="{{ old('specification_values')[$index] ?? '' }}">
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
                </div>
              @endif
            </div>
            @error('specification_titles')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
            @error('specification_values')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Media & Files -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">Media & Files</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Product Images</label>
                <div class="dropzone" id="product-images-dropzone">
                  <div class="dz-message">
                    <i class="icon-base ti tabler-photo-plus" style="font-size: 2rem;"></i>
                    <h6>Drop images here or click to upload</h6>
                    <small class="text-muted">Upload multiple product images</small>
                  </div>
                </div>
                <div id="image-previews" class="mt-3 row"></div>
                @error('images')
                  <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Product Catalogue</label>
                <div class="mb-3">
                  <input type="file" name="catalogue_document" class="form-control" accept=".pdf,.doc,.docx">
                  <small class="text-muted">Upload product catalogue (PDF, DOC, DOCX)</small>
                </div>
                @error('catalogue_document')
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- SEO & Settings -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">SEO & Settings</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Meta Title</label>
                  <input type="text" name="meta_title" class="form-control" placeholder="SEO meta title" value="{{ old('meta_title') }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" name="status" id="status" {{ old('status', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="status">
                    Published Status
                  </label>
                </div>
              </div>
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Meta Description</label>
                  <textarea name="meta_description" class="form-control" rows="3" placeholder="SEO meta description">{{ old('meta_description') }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-end gap-3">
              <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-check me-1"></i>Create Product
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="../assets/vendor/libs/select2/select2.js"></script>
<script src="../assets/vendor/libs/quill/katex.js"></script>
<script src="../assets/vendor/libs/quill/quill.js"></script>
<script src="../assets/vendor/libs/dropzone/dropzone.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Initialize Quill Editor
    const quill = new Quill('#description-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Update hidden textarea when form is submitted
    $('form').on('submit', function() {
        $('textarea[name="description"]').val(quill.root.innerHTML);
    });

    // Features Management
    $('#add-feature').on('click', function() {
        const featureHtml = `
            <div class="mb-3 feature-item">
                <div class="input-group">
                    <input type="text" name="features[]" class="form-control" placeholder="Enter product feature">
                    <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#features-container').append(featureHtml);
    });

    $(document).on('click', '.remove-feature', function() {
        $(this).closest('.feature-item').remove();
    });

    // Specifications Management
    $('#add-specification').on('click', function() {
        const specHtml = `
            <div class="row mb-3 specification-item">
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
            </div>
        `;
        $('#specifications-container').append(specHtml);
    });

    $(document).on('click', '.remove-specification', function() {
        $(this).closest('.specification-item').remove();
    });

    // Auto-generate slug from title
    $('input[name="title"]').on('keyup', function() {
        if (!$('input[name="slug"]').val()) {
            const slug = $(this).val().toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            $('input[name="slug"]').val(slug);
        }
    });

    // Initialize Dropzone for images
    Dropzone.autoDiscover = false;
    const imageDropzone = new Dropzone('#product-images-dropzone', {
        url: '/temp-upload',
        paramName: 'images',
        maxFiles: 10,
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        init: function() {
            this.on('success', function(file, response) {
                // Handle successful upload
                const hiddenInput = $('<input type="hidden" name="images[]">').val(response.path);
                $('#image-previews').append(hiddenInput);
            });
            
            this.on('removedfile', function(file) {
                // Remove hidden input when file is removed
                $('#image-previews input[value="' + file.upload.uuid + '"]').remove();
            });
        }
    });
});
</script>
@endpush