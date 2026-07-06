@extends('layouts.app')

@section('title', 'View Category - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Category</h4>
        <p class="mb-0">Category details and information</p>
      </div>
      <div>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Category Details</h5>
          </div>
          <div class="card-body">
            <!-- Category Name -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Name:</strong>
              </div>
              <div class="col-sm-9">
                {{ $category->name }}
              </div>
            </div>

            <!-- Slug -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Slug:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $category->slug }}</code>
              </div>
            </div>

            <!-- Parent Category -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Parent Category:</strong>
              </div>
              <div class="col-sm-9">
                @if($category->parent)
                  <span class="badge bg-info">
                    <i class="icon-base ti tabler-folder me-1"></i>
                    {{ $category->parent->name }}
                  </span>
                @else
                  <span class="text-muted">Root Category</span>
                @endif
              </div>
            </div>

            <!-- Description -->
            @if($category->description)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Description:</strong>
              </div>
              <div class="col-sm-9">
                <div class="border-start border-primary ps-3">
                  {!! nl2br(e($category->description)) !!}
                </div>
              </div>
            </div>
            @endif

            <!-- Sort Order -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Sort Order:</strong>
              </div>
              <div class="col-sm-9">
                {{ $category->sort_order ?? 0 }}
              </div>
            </div>

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $category->created_at ? $category->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $category->updated_at ? $category->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Child Categories -->
        @if($category->children && $category->children->count() > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Child Categories ({{ $category->children->count() }})</h5>
          </div>
          <div class="card-body">
            <div class="row">
              @foreach($category->children as $child)
                <div class="col-md-6 mb-3">
                  <div class="card border">
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                          <h6 class="mb-1">{{ $child->name }}</h6>
                          <p class="text-muted small mb-2">{{ Str::limit($child->description, 60) }}</p>
                          <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $child->status ? 'success' : 'danger' }} badge-sm">
                              {{ $child->status ? 'Active' : 'Inactive' }}
                            </span>
                            @if($child->products)
                            <small class="text-muted">{{ $child->products->count() }} products</small>
                            @endif
                          </div>
                        </div>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.categories.show', $child) }}">
                              <i class="icon-base ti tabler-eye me-2"></i>View
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $child) }}">
                              <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Products in Category -->
        @if($category->products && $category->products->count() > 0)
        <div class="card mb-6">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products in Category ({{ $category->products->count() }})</h5>
            <a href="{{ route('admin.products.index', ['category_id' => $category->_id]) }}" class="btn btn-sm btn-outline-primary">
              <i class="icon-base ti tabler-external-link me-1"></i>View All
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Sort Order</th>
                    <th>Updated</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($category->products->take(10) as $product)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          @if($product->images && count($product->images) > 0)
                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->title }}" 
                                 class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                          @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                              <i class="icon-base ti tabler-package text-muted"></i>
                            </div>
                          @endif
                          <div>
                            <h6 class="mb-0">{{ $product->title }}</h6>
                            <small class="text-muted">{{ $product->slug }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-{{ $product->status ? 'success' : 'danger' }}">
                          {{ $product->status ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td>{{ $product->sort_order ?? 0 }}</td>
                      <td>
                        <small>{{ $product->updated_at ? $product->updated_at->format('M d, Y') : 'N/A' }}</small>
                      </td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.products.show', $product) }}">
                              <i class="icon-base ti tabler-eye me-2"></i>View
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product) }}">
                              <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a></li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              @if($category->products->count() > 10)
                <div class="text-center mt-3">
                  <small class="text-muted">Showing 10 of {{ $category->products->count() }} products.</small>
                  <a href="{{ route('admin.products.index', ['category_id' => $category->_id]) }}" class="btn btn-sm btn-link">View All Products</a>
                </div>
              @endif
            </div>
          </div>
        </div>
        @endif

        <!-- SEO Information -->
        @if($category->meta_title || $category->meta_description)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">SEO Information</h5>
          </div>
          <div class="card-body">
            @if($category->meta_title)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Title:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $category->meta_title }}
                </div>
              </div>
            @endif

            @if($category->meta_description)
              <div class="row">
                <div class="col-sm-3">
                  <strong>Meta Description:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $category->meta_description }}
                </div>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Status & Statistics -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Status & Statistics</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <strong>Status:</strong>
                <br>
                <span class="badge bg-{{ $category->status ? 'success' : 'danger' }} mt-1">
                  {{ $category->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-package icon-sm text-primary"></i>
                  </div>
                  <div>
                    <small class="text-muted">Products</small>
                    <div class="fw-semibold">{{ $category->products ? $category->products->count() : 0 }}</div>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-folders icon-sm text-info"></i>
                  </div>
                  <div>
                    <small class="text-muted">Child Categories</small>
                    <div class="fw-semibold">{{ $category->children ? $category->children->count() : 0 }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Category Image -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Category Image</h5>
          </div>
          <div class="card-body text-center">
            @if($category->image)
              <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" 
                   class="img-fluid rounded mb-3" style="max-height: 300px; cursor: pointer;"
                   onclick="openImageModal('{{ asset('storage/' . $category->image) }}')">
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

        <!-- Category Hierarchy -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Category Hierarchy</h5>
          </div>
          <div class="card-body">
            <div class="category-breadcrumb">
              @php
                $breadcrumbs = [];
                $current = $category;
                while($current) {
                  array_unshift($breadcrumbs, $current);
                  $current = $current->parent;
                }
              @endphp
              
              @foreach($breadcrumbs as $index => $breadcrumb)
                @if($index > 0)
                  <i class="icon-base ti tabler-chevron-right text-muted mx-2"></i>
                @endif
                @if($loop->last)
                  <span class="fw-semibold text-primary">{{ $breadcrumb->name }}</span>
                @else
                  <a href="{{ route('admin.categories.show', $breadcrumb) }}" class="text-decoration-none">
                    {{ $breadcrumb->name }}
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Category
              </a>
              
              <button type="button" class="btn btn-{{ $category->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $category->_id }}')">
                <i class="icon-base ti tabler-toggle-{{ $category->status ? 'left' : 'right' }} me-2"></i>
                {{ $category->status ? 'Deactivate' : 'Activate' }}
              </button>

              <a href="{{ route('admin.products.index', ['category_id' => $category->_id]) }}" class="btn btn-outline-info">
                <i class="icon-base ti tabler-package me-2"></i>View Products
              </a>

              <hr class="my-3">

              @if($category->products->count() === 0 && $category->children->count() === 0)
              <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Category
                </button>
              </form>
              @else
              <div class="alert alert-warning p-3">
                <small>
                  <i class="icon-base ti tabler-info-circle me-1"></i>
                  Cannot delete category with products or child categories.
                </small>
              </div>
              @endif
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
        <h5 class="modal-title" id="imageModalLabel">{{ $category->name }} Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Category Image" class="img-fluid">
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

function toggleStatus(categoryId) {
    if (confirm('Are you sure you want to change the category status?')) {
        $.ajax({
            url: `/admin/categories/${categoryId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating category status');
                }
            },
            error: function() {
                alert('Error updating category status');
            }
        });
    }
}
</script>
@endpush
@endsection