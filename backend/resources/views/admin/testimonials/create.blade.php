@extends('layouts.app')

@section('title', 'Add New Testimonial - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Add New Testimonial</h4>
        <p class="mb-0">Create a new customer testimonial</p>
      </div>
      <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Testimonials
      </a>
    </div>

    <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div class="row">
        <!-- Main Form -->
        <div class="col-12 col-lg-8">
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Testimonial Details</h5>
            </div>
            <div class="card-body">
              <!-- Customer Name -->
              <div class="mb-4">
                <label class="form-label" for="name">Customer Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" 
                       placeholder="Enter customer name" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Position -->
              <div class="mb-4">
                <label class="form-label" for="position">Position/Title</label>
                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                       id="position" name="position" value="{{ old('position') }}" 
                       placeholder="e.g., CEO, Manager, Customer">
                @error('position')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Company -->
              <div class="mb-4">
                <label class="form-label" for="company">Company</label>
                <input type="text" class="form-control @error('company') is-invalid @enderror" 
                       id="company" name="company" value="{{ old('company') }}" 
                       placeholder="Enter company name">
                @error('company')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Testimonial Content -->
              <div class="mb-4">
                <label class="form-label" for="content">Testimonial Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" name="content" rows="6" 
                          placeholder="Enter the testimonial content..." required>{{ old('content') }}</textarea>
                @error('content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Write the complete testimonial or review content.</div>
              </div>

              <!-- Rating -->
              <div class="mb-4">
                <label class="form-label">Rating <span class="text-danger">*</span></label>
                <div class="rating-input">
                  @for($i = 1; $i <= 5; $i++)
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" 
                             value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                      <label class="form-check-label" for="rating{{ $i }}">
                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                        @for($j = 1; $j <= $i; $j++)
                          <i class="icon-base ti tabler-star-filled text-warning"></i>
                        @endfor
                      </label>
                    </div>
                  @endfor
                </div>
                @error('rating')
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
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
                <div class="form-text">Lower numbers appear first. Use 0 for default ordering.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
          <!-- Image Upload -->
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Customer Photo</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="image">Upload Photo</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Upload customer photo (optional). Max size: 2MB</div>
              </div>

              <!-- Image Preview -->
              <div id="imagePreview" class="text-center" style="display: none;">
                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                <div class="mt-2">
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove
                  </button>
                </div>
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
                <div class="form-text">Enable to make testimonial visible on website</div>
              </div>

              <!-- Featured -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="featured" id="featured" 
                         value="1" {{ old('featured') ? 'checked' : '' }}>
                  <label class="form-check-label" for="featured">
                    Featured Testimonial
                  </label>
                </div>
                <div class="form-text">Featured testimonials appear prominently</div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card">
            <div class="card-body">
              <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="icon-base ti tabler-check me-2"></i>Create Testimonial
              </button>
              <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary w-100">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    // Image preview functionality
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Character counter for content
    const contentTextarea = document.getElementById('content');
    const contentContainer = contentTextarea.parentElement;
    
    function updateCharCount() {
        const currentLength = contentTextarea.value.length;
        let counter = contentContainer.querySelector('.char-counter');
        
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter form-text text-end';
            contentContainer.appendChild(counter);
        }
        
        counter.textContent = `${currentLength} characters`;
        
        if (currentLength > 1000) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    }
    
    contentTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count
});

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>
@endpush