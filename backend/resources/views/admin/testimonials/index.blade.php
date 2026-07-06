@extends('layouts.app')

@section('title', 'Testimonials - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Testimonials Management</h4>
        <p class="mb-0">Manage customer testimonials and reviews</p>
      </div>
      <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Testimonial
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.testimonials.index') }}" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search testimonials..." 
                   value="{{ request('search') }}">
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
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Testimonials Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Testimonials ({{ $testimonials->total() }})</h5>
      </div>
      
      @if($testimonials->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Customer</th>
                <th>Company</th>
                <th>Rating</th>
                <th>Content</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($testimonials as $testimonial)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        @if($testimonial->image)
                          <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" class="rounded-circle">
                        @else
                          <span class="avatar-initial rounded-circle bg-label-primary">
                            {{ substr($testimonial->name, 0, 2) }}
                          </span>
                        @endif
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $testimonial->name }}</h6>
                        @if($testimonial->position)
                          <small class="text-muted">{{ $testimonial->position }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    {{ $testimonial->company ?? 'N/A' }}
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      @for($i = 1; $i <= 5; $i++)
                        <i class="icon-base ti tabler-star{{ $i <= $testimonial->rating ? '-filled text-warning' : ' text-muted' }}"></i>
                      @endfor
                      <span class="ms-2 small text-muted">({{ $testimonial->rating }})</span>
                    </div>
                  </td>
                  <td>
                    <div class="text-truncate" style="max-width: 200px;" title="{{ $testimonial->content }}">
                      {{ $testimonial->excerpt }}
                    </div>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.testimonials.toggle-status', $testimonial) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $testimonial->status ? 'success' : 'danger' }} border-0">
                        {{ $testimonial->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.testimonials.toggle-featured', $testimonial) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $testimonial->featured ? 'warning' : 'outline-warning' }} border-0">
                        <i class="icon-base ti tabler-star{{ $testimonial->featured ? '-filled' : '' }}"></i>
                      </button>
                    </form>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $testimonial->created_at->format('M d, Y') }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.testimonials.show', $testimonial) }}">
                          <i class="icon-base ti tabler-eye"></i>View
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.testimonials.edit', $testimonial) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this testimonial?')"
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
          {{ $testimonials->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-message-2 display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Testimonials Found</h5>
          <p class="mb-4 text-muted">Get started by adding your first testimonial</p>
          <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Testimonial
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
    document.querySelectorAll('select[name="status"], select[name="featured"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush