@extends('layouts.app')

@section('title', 'View Product - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Product</h4>
        <p class="mb-0">Product details and specifications</p>
      </div>
      <div>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Product Information</h5>
          </div>
          <div class="card-body">
            <!-- Title -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Title:</strong>
              </div>
              <div class="col-sm-9">
                {{ $product->title }}
              </div>
            </div>

            <!-- Slug -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Slug:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $product->slug }}</code>
              </div>
            </div>

            <!-- Category -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Category:</strong>
              </div>
              <div class="col-sm-9">
                @if($product->category)
                  <span class="badge bg-primary">{{ $product->category->name }}</span>
                @else
                  <span class="text-muted">No category assigned</span>
                @endif
              </div>
            </div>

            <!-- Description -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Description:</strong>
              </div>
              <div class="col-sm-9">
                <div class="border-start border-primary ps-3">
                  {!! nl2br(e($product->description)) !!}
                </div>
              </div>
            </div>

            <!-- Sort Order -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Sort Order:</strong>
              </div>
              <div class="col-sm-9">
                {{ $product->sort_order ?? 0 }}
              </div>
            </div>

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $product->created_at ? $product->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $product->updated_at ? $product->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Features -->
        @if($product->features && count($product->features) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Features</h5>
          </div>
          <div class="card-body">
            <div class="row">
              @foreach($product->features as $index => $feature)
                <div class="col-md-6 mb-3">
                  <div class="d-flex align-items-center">
                    @if(is_array($feature) && !empty($feature['icon']))
                      <i class="icon-base ti {{ $feature['icon'] }} text-orange-600 me-2"></i>
                      <span>{{ $feature['title'] ?? '' }}</span>
                    @elseif(is_array($feature))
                      <i class="icon-base ti tabler-check text-success me-2"></i>
                      <span>{{ $feature['title'] ?? '' }}</span>
                    @else
                      <i class="icon-base ti tabler-check text-success me-2"></i>
                      <span>{{ $feature }}</span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Specifications -->
        @if($product->specifications && count($product->specifications) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Specifications</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th style="width: 30%;">Specification</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($product->specifications as $spec)
                    <tr>
                      <td><strong>{{ $spec['title'] }}</strong></td>
                      <td>{{ $spec['value'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        @endif

        <!-- A+ Content -->
        @if($product->a_plus_content)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">A+ Content</h5>
          </div>
          <div class="card-body">
            <div class="border rounded p-3">
              {!! $product->a_plus_content !!}
            </div>
          </div>
        </div>
        @endif

        <!-- SEO Information -->
        @if($product->meta_title || $product->meta_description)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">SEO Information</h5>
          </div>
          <div class="card-body">
            @if($product->meta_title)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Title:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $product->meta_title }}
                </div>
              </div>
            @endif

            @if($product->meta_description)
              <div class="row">
                <div class="col-sm-3">
                  <strong>Meta Description:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $product->meta_description }}
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
                <span class="badge bg-{{ $product->status ? 'success' : 'danger' }} mt-1">
                  {{ $product->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Images -->
        @if($product->images && count($product->images) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Product Images</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              @foreach($product->images as $image)
                <div class="col-6">
                  <img src="{{ asset('storage/' . $image) }}" alt="Product Image" 
                       class="img-fluid rounded border" 
                       style="height: 120px; width: 100%; object-fit: cover; cursor: pointer;"
                       onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Product Documents -->
        @if($product->datasheet_document || $product->connection_diagram_document || $product->user_manual_document || $product->catalogue_document)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Product Documents</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-3">
              @if($product->datasheet_document)
                <a href="{{ asset('storage/' . $product->datasheet_document) }}" target="_blank" class="btn btn-outline-primary w-100">
                  <i class="icon-base ti tabler-download me-2"></i>Download Datasheet
                </a>
              @endif
              @if($product->connection_diagram_document)
                <a href="{{ asset('storage/' . $product->connection_diagram_document) }}" target="_blank" class="btn btn-outline-primary w-100">
                  <i class="icon-base ti tabler-download me-2"></i>Download Connection Diagram
                </a>
              @endif
              @if($product->user_manual_document)
                <a href="{{ asset('storage/' . $product->user_manual_document) }}" target="_blank" class="btn btn-outline-primary w-100">
                  <i class="icon-base ti tabler-download me-2"></i>Download User Manual
                </a>
              @endif
              @if($product->catalogue_document)
                <a href="{{ asset('storage/' . $product->catalogue_document) }}" target="_blank" class="btn btn-outline-primary w-100">
                  <i class="icon-base ti tabler-download me-2"></i>Download Catalogue
                </a>
              @endif
            </div>
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
              <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Product
              </a>
              
              <button type="button" class="btn btn-{{ $product->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $product->id }}')">
                <i class="icon-base ti tabler-toggle-{{ $product->status ? 'left' : 'right' }} me-2"></i>
                {{ $product->status ? 'Deactivate' : 'Activate' }}
              </button>

              <hr class="my-3">

              <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Product
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
        <h5 class="modal-title" id="imageModalLabel">Product Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Product Image" class="img-fluid">
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

function toggleStatus(productId) {
    if (confirm('Are you sure you want to change the product status?')) {
        $.ajax({
            url: `/admin/products/${productId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating product status');
                }
            },
            error: function() {
                alert('Error updating product status');
            }
        });
    }
}
</script>
@endpush
@endsection
