@extends('layouts.app')

@section('title', 'Solutions - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Solutions Management</h4>
        <p class="mb-0">Manage your business solutions and services</p>
      </div>
      <a href="{{ route('admin.solutions.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Solution
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.solutions.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search solutions..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                  {{ $category }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Featured</label>
            <select name="featured" class="form-select">
              <option value="">All</option>
              <option value="yes" {{ request('featured') === 'yes' ? 'selected' : '' }}>Featured</option>
              <option value="no" {{ request('featured') === 'no' ? 'selected' : '' }}>Not Featured</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.solutions.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Solutions Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Solutions ({{ $solutions->total() }})</h5>
      </div>
      
      @if($solutions->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Solution</th>
                <th>Category</th>
                <th>Price Range</th>
                <th>Features</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($solutions as $solution)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3 overflow-hidden">
                        @if($solution->image)
                          <img src="{{ $solution->image_url }}" alt="{{ $solution->title }}" class="rounded w-100 h-100 object-fit-cover">
                        @else
                          <span class="avatar-initial rounded bg-label-primary">
                            {{ substr($solution->title, 0, 2) }}
                          </span>
                        @endif
                      </div>
                      <div>
                        <h6 class="mb-0">{{ Str::limit($solution->title, 30) }}</h6>
                        <small class="text-muted">{{ $solution->excerpt(60) }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($solution->category)
                      <span class="badge bg-label-info">{{ $solution->category }}</span>
                    @else
                      <span class="text-muted">No Category</span>
                    @endif
                  </td>
                  <td>
                    {{ $solution->price_range ?: 'N/A' }}
                  </td>
                  <td>
                    @if($solution->features_list && count($solution->features_list) > 0)
                      <span class="badge bg-label-secondary">{{ count($solution->features_list) }} features</span>
                    @else
                      <span class="text-muted">No features</span>
                    @endif
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.solutions.toggle-status', $solution) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $solution->status ? 'success' : 'danger' }} border-0">
                        {{ $solution->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.solutions.toggle-featured', $solution) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $solution->featured ? 'warning' : 'outline-warning' }} border-0">
                        <i class="icon-base ti tabler-star{{ $solution->featured ? '-filled' : '' }}"></i>
                      </button>
                    </form>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $solution->created_at->format('M d, Y') }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ $solution->url }}" target="_blank">
                          <i class="icon-base ti tabler-eye"></i>View Public
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.solutions.show', $solution) }}">
                          <i class="icon-base ti tabler-info-circle me-2"></i>Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.solutions.edit', $solution) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.solutions.destroy', $solution) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this solution?')"
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
          {{ $solutions->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-bulb display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Solutions Found</h5>
          <p class="mb-4 text-muted">Get started by adding your first solution</p>
          <a href="{{ route('admin.solutions.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Solution
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
    document.querySelectorAll('select[name="category"], select[name="status"], select[name="featured"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush