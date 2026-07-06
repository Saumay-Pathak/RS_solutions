@extends('layouts.app')

@section('title', 'Products - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Products Management</h4>
        <p class="mb-0">Manage your product catalog and inventory</p>
      </div>
      <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Product
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search products..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category->_id }}" {{ request('category_id') == $category->_id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Featured</label>
            <select name="featured" class="form-select">
              <option value="">All</option>
              <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured</option>
              <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Standard</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Products Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Products ({{ $products->total() }})</h5>
      </div>
      
      @if($products->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Features</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $product)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3 overflow-hidden">
                        @if($product->images && count($product->images) > 0)
                          <img src="{{ asset('storage/' . $product->images[0]) }}" 
                               alt="{{ $product->title }}" class="rounded w-100 h-100 object-fit-cover">
                        @else
                          <span class="avatar-initial bg-label-info rounded">
                            <i class="icon-base ti tabler-package"></i>
                          </span>
                        @endif
                      </div>
                      <div>
                        <h6 class="mb-0">{{ Str::limit($product->title, 25) }}</h6>
                        <small class="text-muted">{{ $product->slug }}</small>
                        @if($product->description)
                          <br><small class="text-muted">{{ Str::limit(strip_tags($product->description), 40) }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($product->category)
                      <span class="badge bg-label-info">{{ $product->category->name }}</span>
                    @else
                      <span class="text-muted">No Category</span>
                    @endif
                  </td>
                  <td>
                    @if($product->features && count($product->features) > 0)
                      <div class="d-flex flex-wrap gap-1">
                        @foreach(array_slice($product->features, 0, 2) as $feature)
                          @php
                            $title = is_array($feature) ? ($feature['title'] ?? '') : $feature;
                            $icon = is_array($feature) ? ($feature['icon'] ?? '') : '';
                          @endphp
                          <span class="badge bg-primary d-inline-flex align-items-center gap-1">
                            @if(!empty($icon))
                              <i class="icon-base ti {{ $icon }}"></i>
                            @endif
                            {{ Str::limit($title, 15) }}
                          </span>
                        @endforeach
                        @if(count($product->features) > 2)
                          <span class="badge bg-secondary">+{{ count($product->features) - 2 }}</span>
                        @endif
                      </div>
                    @else
                      <span class="text-muted">No features</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-label-secondary">{{ $product->sort_order ?? 0 }}</span>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" 
                          style="display: inline;">
                      @csrf
                      @method('POST')
                      <button type="submit" class="btn btn-sm btn-{{ $product->status ? 'success' : 'danger' }} border-0">
                        {{ $product->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.products.toggle-featured', $product) }}" 
                          style="display: inline;">
                      @csrf
                      @method('POST')
                      <button type="submit" class="btn btn-sm btn-{{ $product->featured ? 'warning' : 'secondary' }} border-0">
                        {{ $product->featured ? 'Featured' : 'Standard' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $product->created_at ? $product->created_at->format('M d, Y') : 'N/A' }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.products.show', $product) }}">
                          <i class="icon-base ti tabler-eye"></i>View Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.products.edit', $product) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this product?')"
                              style="display: inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">
                            <i class="icon-base ti tabler-trash"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer">
          {{ $products->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-package-off display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Products Found</h5>
          <p class="mb-4 text-muted">Get started by adding your first product</p>
          <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Product
          </a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter dropdowns change
    document.querySelectorAll('select[name="category_id"], select[name="status"], select[name="featured"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});

    function toggleStatus(productId) {
        fetch(`/admin/products/${productId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const button = document.getElementById(`status-toggle-${productId}`);
                const span = button.querySelector('span');
                
                if (data.status) {
                    button.classList.remove('bg-gray-200');
                    button.classList.add('bg-green-500');
                    span.classList.remove('translate-x-0');
                    span.classList.add('translate-x-5');
                } else {
                    button.classList.remove('bg-green-500');
                    button.classList.add('bg-gray-200');
                    span.classList.remove('translate-x-5');
                    span.classList.add('translate-x-0');
                }
                
                // Show toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg transform transition-all duration-500 translate-y-0 z-50';
                toast.textContent = data.message;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.add('translate-y-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function toggleFeatured(productId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${productId}/toggle-featured`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush