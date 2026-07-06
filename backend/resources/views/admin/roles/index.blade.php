@extends('layouts.app')

@section('title', 'Roles - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Roles Management</h4>
        <p class="mb-0">Manage user roles and permissions</p>
      </div>
      <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Role
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search roles..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Roles Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Roles ({{ $roles->total() }})</h5>
      </div>
      
      @if($roles->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Role</th>
                <th>Permissions</th>
                <th>Page Access</th>
                <th>Users</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial bg-label-primary rounded">
                          <i class="icon-base ti tabler-shield"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $role->display_name }}</h6>
                        <small class="text-muted">{{ $role->name }}</small>
                        @if($role->description)
                          <br><small class="text-muted">{{ Str::limit($role->description, 50) }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($role->permissions && count($role->permissions) > 0)
                      <div class="d-flex flex-wrap gap-1">
                        @foreach(array_slice($role->permissions, 0, 2) as $permission)
                          <span class="badge bg-primary">{{ str_replace('_', ' ', ucwords($permission, '_')) }}</span>
                        @endforeach
                        @if(count($role->permissions) > 2)
                          <span class="badge bg-secondary" data-bs-toggle="tooltip" 
                                title="{{ implode(', ', array_slice($role->permissions, 2)) }}">
                            +{{ count($role->permissions) - 2 }} more
                          </span>
                        @endif
                      </div>
                    @else
                      <span class="text-muted">No permissions</span>
                    @endif
                  </td>
                  <td>
                    @if($role->page_access && count($role->page_access) > 0)
                      <div class="d-flex flex-wrap gap-1">
                        @foreach(array_slice($role->page_access, 0, 2) as $page)
                          <span class="badge bg-info">{{ ucfirst($page) }}</span>
                        @endforeach
                        @if(count($role->page_access) > 2)
                          <span class="badge bg-secondary" data-bs-toggle="tooltip" 
                                title="{{ implode(', ', array_slice($role->page_access, 2)) }}">
                            +{{ count($role->page_access) - 2 }} more
                          </span>
                        @endif
                      </div>
                    @else
                      <span class="text-muted">No access</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $userCount = $role->users()->count();
                    @endphp
                    <span class="fw-semibold">{{ number_format($userCount) }}</span>
                    <br><small class="text-muted">users</small>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.roles.toggle-status', $role) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $role->status ? 'success' : 'danger' }} border-0">
                        {{ $role->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.roles.show', $role) }}">
                          <i class="icon-base ti tabler-eye"></i>View Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit Role
                        </a>
                        @if($userCount == 0)
                          <div class="dropdown-divider"></div>
                          <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" 
                                onsubmit="return confirm('Are you sure you want to delete this role?')"
                                style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                              <i class="icon-base ti tabler-trash"></i>Delete Role
                            </button>
                          </form>
                        @else
                          <div class="dropdown-divider"></div>
                          <span class="dropdown-item-text text-muted">
                            <i class="icon-base ti tabler-users me-2"></i>Cannot delete - has users
                          </span>
                        @endif
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
          {{ $roles->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-shield-off display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Roles Found</h5>
          <p class="mb-4 text-muted">Get started by creating your first role</p>
          <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Role
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
    document.querySelectorAll('select[name="status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush