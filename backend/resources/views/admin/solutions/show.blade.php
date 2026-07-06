@extends('layouts.app')

@section('title', 'View Solution - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Solution</h4>
        <p class="mb-0">Solution details and information</p>
      </div>
      <div>
        <a href="{{ route('admin.solutions.edit', $solution) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.solutions.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <!-- Solution Overview -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">{{ $solution->title }}</h5>
          </div>
          <div class="card-body">
            <!-- Image -->
            @if($solution->image)
            <div class="mb-4">
              <img src="{{ $solution->image_url }}" alt="{{ $solution->title }}" 
                   class="img-fluid rounded" style="width: 100%; max-height: 400px; object-fit: cover; cursor: pointer;"
                   onclick="openImageModal('{{ $solution->image_url }}')">
            </div>
            @endif

            <!-- Short Description -->
            @if($solution->short_description)
            <div class="mb-4">
              <div class="alert alert-info">
                <strong>Overview:</strong>
                <p class="mb-0 mt-2">{{ $solution->short_description }}</p>
              </div>
            </div>
            @endif

            <!-- Description -->
            <div class="solution-content">
              <h6 class="mb-3">Description</h6>
              {!! nl2br(e($solution->description)) !!}
            </div>
          </div>
        </div>

        <!-- Solution Details -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Solution Details</h5>
          </div>
          <div class="card-body">
            <!-- Title -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Title:</strong>
              </div>
              <div class="col-sm-9">
                {{ $solution->title }}
              </div>
            </div>

            <!-- Slug -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Slug:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $solution->slug }}</code>
              </div>
            </div>

            <!-- Category -->
            @if($solution->category)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Category:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-info">{{ $solution->category }}</span>
              </div>
            </div>
            @endif

            <!-- Price Range -->
            @if($solution->price_range)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Price Range:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-success">{{ $solution->price_range }}</span>
              </div>
            </div>
            @endif

            <!-- Delivery Time -->
            @if($solution->delivery_time)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Delivery Time:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-warning text-dark">
                  <i class="icon-base ti tabler-clock me-1"></i>
                  {{ $solution->delivery_time }}
                </span>
              </div>
            </div>
            @endif

            <!-- Sort Order -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Sort Order:</strong>
              </div>
              <div class="col-sm-9">
                {{ $solution->sort_order ?? 0 }}
              </div>
            </div>

            <!-- Reading Time -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Reading Time:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-light text-dark">{{ $solution->read_time }}</span>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $solution->created_at ? $solution->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $solution->updated_at ? $solution->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Features -->
        @if($solution->features && count($solution->features) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Features ({{ count($solution->features) }})</h5>
          </div>
          <div class="card-body">
            <div class="row">
              @foreach($solution->features as $feature)
                <div class="col-md-6 mb-3">
                  <div class="d-flex align-items-start">
                    <i class="icon-base ti tabler-check-circle text-success me-2 mt-1"></i>
                    <span>{{ $feature }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Benefits -->
        @if($solution->benefits && count($solution->benefits) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Benefits ({{ count($solution->benefits) }})</h5>
          </div>
          <div class="card-body">
            <div class="row">
              @foreach($solution->benefits as $benefit)
                <div class="col-md-6 mb-3">
                  <div class="d-flex align-items-start">
                    <i class="icon-base ti tabler-star text-warning me-2 mt-1"></i>
                    <span>{{ $benefit }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Technologies -->
        @if($solution->technologies && count($solution->technologies) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Technologies Used</h5>
          </div>
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
              @foreach($solution->technologies as $technology)
                <span class="badge bg-secondary">{{ $technology }}</span>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- SEO Information -->
        @if($solution->meta_title || $solution->meta_description || $solution->meta_keywords)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">SEO Information</h5>
          </div>
          <div class="card-body">
            @if($solution->meta_title)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Title:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $solution->meta_title }}
                </div>
              </div>
            @endif

            @if($solution->meta_description)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Description:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $solution->meta_description }}
                </div>
              </div>
            @endif

            @if($solution->meta_keywords)
              <div class="row">
                <div class="col-sm-3">
                  <strong>Meta Keywords:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $solution->meta_keywords }}
                </div>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Status & Options -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Status & Options</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <strong>Status:</strong>
                <br>
                <span class="badge bg-{{ $solution->status ? 'success' : 'danger' }} mt-1">
                  {{ $solution->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="col-12">
                <strong>Featured:</strong>
                <br>
                <span class="badge bg-{{ $solution->featured ? 'warning' : 'secondary' }} mt-1">
                  {{ $solution->featured ? 'Featured' : 'Not Featured' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Solution Image -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Solution Image</h5>
          </div>
          <div class="card-body text-center">
            @if($solution->image)
              <img src="{{ $solution->image_url }}" alt="{{ $solution->title }}" 
                   class="img-fluid rounded mb-3" style="max-height: 200px; cursor: pointer;"
                   onclick="openImageModal('{{ $solution->image_url }}')">
            @else
              <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                <div class="text-center">
                  <i class="icon-base ti tabler-photo icon-lg text-muted mb-2"></i>
                  <p class="text-muted mb-0">No image uploaded</p>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- Solution Statistics -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Solution Statistics</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-list-check icon-sm text-success"></i>
                  </div>
                  <div class="flex-grow-1">
                    <small class="text-muted">Features</small>
                    <div class="fw-semibold">{{ count($solution->features ?? []) }}</div>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-star icon-sm text-warning"></i>
                  </div>
                  <div>
                    <small class="text-muted">Benefits</small>
                    <div class="fw-semibold">{{ count($solution->benefits ?? []) }}</div>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-code icon-sm text-info"></i>
                  </div>
                  <div>
                    <small class="text-muted">Technologies</small>
                    <div class="fw-semibold">{{ count($solution->technologies ?? []) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Solution URL -->
        @if($solution->slug)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Public URL</h5>
          </div>
          <div class="card-body">
            <div class="input-group">
              <input type="text" class="form-control" value="{{ $solution->url }}" readonly>
              <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $solution->url }}')">
                <i class="icon-base ti tabler-copy"></i>
              </button>
            </div>
            <small class="text-muted">Public-facing solution URL</small>
          </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('admin.solutions.edit', $solution) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Solution
              </a>
              
              <button type="button" class="btn btn-{{ $solution->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $solution->_id }}')">
                <i class="icon-base ti tabler-toggle-{{ $solution->status ? 'left' : 'right' }} me-2"></i>
                {{ $solution->status ? 'Deactivate' : 'Activate' }}
              </button>

              <button type="button" class="btn btn-{{ $solution->featured ? 'outline-warning' : 'warning' }} w-100"
                      onclick="toggleFeatured('{{ $solution->_id }}')">
                <i class="icon-base ti tabler-star{{ $solution->featured ? '' : '-filled' }} me-2"></i>
                {{ $solution->featured ? 'Remove Featured' : 'Make Featured' }}
              </button>

              @if($solution->slug)
              <a href="{{ $solution->url }}" target="_blank" class="btn btn-outline-info">
                <i class="icon-base ti tabler-external-link me-2"></i>View Public Page
              </a>
              @endif

              <hr class="my-3">

              <form method="POST" action="{{ route('admin.solutions.destroy', $solution) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this solution? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Solution
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">{{ $solution->title }} - Solution Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Solution Image" class="img-fluid">
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openImageModal(imageSrc) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You could show a toast notification here
        alert('URL copied to clipboard!');
    });
}

function toggleStatus(solutionId) {
    if (confirm('Are you sure you want to change the solution status?')) {
        $.ajax({
            url: `/admin/solutions/${solutionId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating solution status');
                }
            },
            error: function() {
                alert('Error updating solution status');
            }
        });
    }
}

function toggleFeatured(solutionId) {
    if (confirm('Are you sure you want to change the solution featured status?')) {
        $.ajax({
            url: `/admin/solutions/${solutionId}/toggle-featured`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating solution featured status');
                }
            },
            error: function() {
                alert('Error updating solution featured status');
            }
        });
    }
}
</script>
@endpush
@endsection