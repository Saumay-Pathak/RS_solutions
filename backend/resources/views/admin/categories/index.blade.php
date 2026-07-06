@extends('layouts.app')

@section('title', 'Categories - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Categories Management</h4>
        <p class="mb-0">Organize your content with categories</p>
      </div>
      <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Category
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search categories..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Parent Category</label>
            <select name="parent_id" class="form-select">
              <option value="">All Categories</option>
              @foreach($parentCategories as $parent)
                <option value="{{ $parent->_id }}" {{ request('parent_id') == $parent->_id ? 'selected' : '' }}>
                  {{ $parent->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Categories ({{ $categories->total() }})</h5>
      </div>
      
      @if($categories->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Category</th>
                <th>Parent</th>
                <th>Sort Order</th>
                <th>Products Count</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categories as $category)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3 overflow-hidden">
                        @if($category->image)
                          <img src="{{ asset('storage/' . $category->image) }}" 
                               alt="{{ $category->name }}" class="rounded-circle w-100 h-100 object-fit-cover">
                        @else
                          <span class="avatar-initial bg-label-primary rounded-circle">
                            <i class="icon-base ti tabler-folder"></i>
                          </span>
                        @endif
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $category->name }}</h6>
                        <small class="text-muted">{{ $category->slug }}</small>
                        @if($category->description)
                          <br><small class="text-muted">{{ Str::limit($category->description, 40) }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($category->parent)
                      <span class="badge bg-label-info">{{ $category->parent->name }}</span>
                    @else
                      <span class="text-muted">Root Category</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-label-secondary">{{ $category->sort_order ?? 0 }}</span>
                  </td>
                  <td>
                    @php
                      $productCount = $category->products ? $category->products->count() : 0;
                    @endphp
                    <span class="fw-semibold">{{ number_format($productCount) }}</span>
                    <br><small class="text-muted">products</small>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $category->status ? 'success' : 'danger' }} border-0">
                        {{ $category->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.categories.show', $category) }}">
                          <i class="icon-base ti tabler-eye"></i>View Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $category) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this category?')"
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
          {{ $categories->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-folder-off display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Categories Found</h5>
          <p class="mb-4 text-muted">Get started by adding your first category</p>
          <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Category
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
    document.querySelectorAll('select[name="status"], select[name="parent_id"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush
