@extends('layouts.app')

@section('title', 'Software - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Software Management</h4>
        <p class="mb-0">Manage downloadable software and applications</p>
      </div>
      <a href="{{ route('admin.software.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Software
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.software.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search software..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="main_category" class="form-select">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('main_category') === $category ? 'selected' : '' }}>
                  {{ $category }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="is_free" class="form-select">
              <option value="">All Types</option>
              <option value="free" {{ request('is_free') === 'free' ? 'selected' : '' }}>Free</option>
              <option value="paid" {{ request('is_free') === 'paid' ? 'selected' : '' }}>Paid</option>
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
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Software Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Software ({{ $software->total() }})</h5>
      </div>
      
      @if($software->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Software</th>
                <th>Category</th>
                <th>Type</th>
                <th>Version</th>
                <th>Downloads</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($software as $item)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3 bg-label-{{ $item->is_free ? 'success' : 'warning' }}">
                        <i class="icon-base ti tabler-{{ $item->file_type === 'external' ? 'link' : 'download' }}"></i>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ Str::limit($item->title, 30) }}</h6>
                        <small class="text-muted">{{ $item->excerpt(50) }}</small>
                        @if($item->developer)
                          <br><small class="text-primary">by {{ $item->developer }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-info">{{ $item->main_category }}</span>
                    @if($item->sub_category)
                      <br><small class="text-muted">{{ $item->sub_category }}</small>
                    @endif
                  </td>
                  <td>
                    @if($item->is_free)
                      <span class="badge bg-success">Free</span>
                    @else
                      <span class="badge bg-warning">
                        @if($item->price)
                          ${{ $item->price }}
                        @else
                          Paid
                        @endif
                      </span>
                    @endif
                  </td>
                  <td>
                    {{ $item->version ?: 'N/A' }}
                    @if($item->size)
                      <br><small class="text-muted">{{ $item->file_size_formatted }}</small>
                    @endif
                  </td>
                  <td>
                    <span class="fw-semibold">{{ number_format($item->download_count) }}</span>
                    <br><small class="text-muted">downloads</small>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.software.toggle-status', $item) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $item->status ? 'success' : 'danger' }} border-0">
                        {{ $item->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.software.toggle-featured', $item) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $item->featured ? 'warning' : 'outline-warning' }} border-0">
                        <i class="icon-base ti tabler-star{{ $item->featured ? '-filled' : '' }}"></i>
                      </button>
                    </form>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $item->created_at->format('M d, Y') }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        @if($item->hasDownload())
                          <a class="dropdown-item" href="{{ route('software.download', $item->slug ?: $item->id) }}" target="_blank">
                            <i class="icon-base ti tabler-download me-2"></i>Test Download
                          </a>
                        @endif
                        <a class="dropdown-item" href="{{ $item->url }}" target="_blank">
                          <i class="icon-base ti tabler-eye"></i>View Public
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.software.show', $item) }}">
                          <i class="icon-base ti tabler-info-circle me-2"></i>Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.software.edit', $item) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.software.destroy', $item) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this software? This will also delete the associated file.')"
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
          {{ $software->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-download display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Software Found</h5>
          <p class="mb-4 text-muted">Get started by adding your first software</p>
          <a href="{{ route('admin.software.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Software
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
    document.querySelectorAll('select[name="main_category"], select[name="is_free"], select[name="status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush