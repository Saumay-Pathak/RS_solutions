@extends('layouts.app')

@section('title', 'Hero Slides - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Hero Slides Management</h4>
        <p class="mb-0">Manage hero slider images and content for your website</p>
      </div>
      <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i>Add New Slide
      </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-6">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="icon-base ti tabler-slideshow ti-28px"></i>
                </span>
              </div>
              <div>
                <p class="mb-0 text-muted">Total Slides</p>
                <h4 class="mb-0">{{ $stats['total'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="icon-base ti tabler-check ti-28px"></i>
                </span>
              </div>
              <div>
                <p class="mb-0 text-muted">Active Slides</p>
                <h4 class="mb-0">{{ $stats['active'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-4">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="icon-base ti tabler-x ti-28px"></i>
                </span>
              </div>
              <div>
                <p class="mb-0 text-muted">Inactive Slides</p>
                <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Actions -->
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Slides</h5>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-outline-danger" id="bulk-delete-btn" style="display: none;">
            <i class="icon-base ti tabler-trash me-1"></i>Delete Selected
          </button>
        </div>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('admin.hero-slides.index') }}" id="filter-form">
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <input type="text" class="form-control" name="search" placeholder="Search by title..." 
                     value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
              <select class="form-select" name="status">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" name="position">
                <option value="">All Positions</option>
                <option value="left" {{ request('position') === 'left' ? 'selected' : '' }}>Left</option>
                <option value="center" {{ request('position') === 'center' ? 'selected' : '' }}>Center</option>
                <option value="right" {{ request('position') === 'right' ? 'selected' : '' }}>Right</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">
                <i class="icon-base ti tabler-search me-1"></i>Filter
              </button>
            </div>
          </div>
        </form>

        <!-- Slides Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th width="40">
                  <input type="checkbox" class="form-check-input" id="select-all">
                </th>
                <th width="80">Order</th>
                <th width="100">Image</th>
                <th>Title</th>
                <th width="150">Position</th>
                <th width="100">Status</th>
                <th width="150">Display Schedule</th>
                <th width="200">Actions</th>
              </tr>
            </thead>
            <tbody id="sortable-slides">
              @forelse($slides as $slide)
              <tr data-id="{{ $slide->_id }}">
                <td>
                  <input type="checkbox" class="form-check-input slide-checkbox" value="{{ $slide->_id }}">
                </td>
                <td>
                  <span class="badge bg-label-secondary">{{ $slide->order }}</span>
                </td>
                <td>
                  @if($slide->image)
                    <img src="{{ $slide->image_url }}" alt="{{ $slide->image_alt }}" 
                         class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                  @else
                    <div class="bg-label-secondary rounded d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 40px;">
                      <i class="icon-base ti tabler-photo-off"></i>
                    </div>
                  @endif
                </td>
                <td>
                  <div>
                    <strong>{{ $slide->title }}</strong>
                    @if($slide->subtitle)
                      <br><small class="text-muted">{{ Str::limit($slide->subtitle, 50) }}</small>
                    @endif
                  </div>
                </td>
                <td>
                  <span class="badge bg-label-info">
                    <i class="icon-base ti tabler-align-{{ $slide->content_position ?? 'center' }} me-1"></i>
                    {{ ucfirst($slide->content_position ?? 'center') }}
                  </span>
                </td>
                <td>
                  <div class="form-check form-switch">
                    <input class="form-check-input toggle-status" type="checkbox" 
                           data-id="{{ $slide->_id }}" {{ $slide->is_active ? 'checked' : '' }}>
                  </div>
                </td>
                <td>
                  @if($slide->display_from || $slide->display_to)
                    <small class="text-muted">
                      @if($slide->display_from)
                        From: {{ $slide->display_from->format('M d, Y') }}<br>
                      @endif
                      @if($slide->display_to)
                        To: {{ $slide->display_to->format('M d, Y') }}
                      @endif
                    </small>
                  @else
                    <span class="badge bg-label-secondary">Always</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('admin.hero-slides.show', $slide->_id) }}" 
                       class="btn btn-sm btn-icon btn-outline-info" title="View">
                      <i class="icon-base ti tabler-eye"></i>
                    </a>
                    <a href="{{ route('admin.hero-slides.edit', $slide->_id) }}" 
                       class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                      <i class="icon-base ti tabler-edit"></i>
                    </a>
                    <form action="{{ route('admin.hero-slides.destroy', $slide->_id) }}" 
                          method="POST" class="d-inline delete-form">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                        <i class="icon-base ti tabler-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-5">
                  <div class="text-muted">
                    <i class="icon-base ti tabler-slideshow ti-3x mb-3"></i>
                    <h5>No slides found</h5>
                    <p>Create your first hero slide to get started</p>
                    <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary">
                      <i class="icon-base ti tabler-plus me-2"></i>Add New Slide
                    </a>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        @if($slides->hasPages())
        <div class="mt-4">
          {{ $slides->links() }}
        </div>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const slideCheckboxes = document.querySelectorAll('.slide-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    selectAllCheckbox?.addEventListener('change', function() {
        slideCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    slideCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.slide-checkbox:checked').length;
        bulkDeleteBtn.style.display = checkedCount > 0 ? 'block' : 'none';
    }

    // Bulk delete
    bulkDeleteBtn?.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.slide-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedIds.length === 0) return;

        if (confirm(`Are you sure you want to delete ${selectedIds.length} slide(s)?`)) {
            fetch('{{ route("admin.hero-slides.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                toastr.error('An error occurred while deleting slides');
            });
        }
    });

    // Toggle status
    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const slideId = this.dataset.id;
            const isActive = this.checked;

            fetch(`/admin/hero-slides/${slideId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                } else {
                    this.checked = !isActive;
                    toastr.error('Failed to update status');
                }
            })
            .catch(error => {
                this.checked = !isActive;
                toastr.error('An error occurred');
            });
        });
    });

    // Delete confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this slide?')) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
