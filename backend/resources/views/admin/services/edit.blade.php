@extends('layouts.app')

@section('title', 'Edit Service - Admin Panel')

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
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Service</h4>
        <p class="mb-0">Update service details</p>
      </div>
      <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Services
      </a>
    </div>

    <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="title">Service Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $service->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="slug">URL Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $service->slug) }}">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Leave empty to auto-generate from title</div>
              </div>

              <div class="mb-4">
                <label class="form-label" for="short_description">Short Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3" required>{{ old('short_description', $service->short_description) }}</textarea>
                @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="description">Full Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="8" required>{{ old('description', $service->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card mb-6">
            <div class="card-header">
              <h5 class="mb-0">Media & Visibility</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="image">Service Image</label>
                @if($service->image)
                  <div class="mb-2">
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="img-fluid rounded" />
                  </div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($service->image)
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                    <label class="form-check-label" for="remove_image">Remove current image</label>
                  </div>
                @endif
              </div>

              <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $service->status) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">Active</label>
              </div>

              <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" id="hide_from_homepage" name="hide_from_homepage" value="1" {{ old('hide_from_homepage', $service->hide_from_homepage) ? 'checked' : '' }}>
                <label class="form-check-label" for="hide_from_homepage">Hide content from homepage</label>
              </div>

              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}" min="0">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">SEO (optional)</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label" for="meta_title">Meta Title</label>
                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $service->meta_title) }}">
                @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label" for="meta_description">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $service->meta_description) }}</textarea>
                @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label" for="meta_keywords">Meta Keywords</label>
                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $service->meta_keywords) }}" placeholder="keyword1, keyword2">
                @error('meta_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-device-floppy me-2"></i>Update Service</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const titleInput = document.getElementById('title');
  const slugInput = document.getElementById('slug');

  function slugify(text) {
    return (text || '')
      .toString()
      .trim()
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-');
  }

  // Auto-generate slug from title unless user edits slug manually
  if (titleInput && slugInput) {
    titleInput.addEventListener('input', function() {
      if (!slugInput.value || slugInput.dataset.manual !== 'true') {
        slugInput.value = slugify(titleInput.value);
      }
    });

    slugInput.addEventListener('input', function() {
      // Mark as manually edited to stop auto updates
      this.dataset.manual = 'true';
    });
  }
});
</script>
@endpush
