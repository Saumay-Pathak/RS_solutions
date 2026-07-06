@extends('admin.layouts.admin')

@section('title', 'Roles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Roles Management</h5>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-plus"></i>Add Role
                </a>
            </div>
            
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('admin.roles.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search roles...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="nav-item nav-link search-toggler"></i>Search
                                    </button>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                        <i class="icon-base ti tabler-refresh me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Roles Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Page Access</th>
                                <th>Users Count</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $role->display_name }}</h6>
                                        <small class="text-muted">{{ $role->name }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 250px;">
                                        {{ Str::limit($role->description, 100) }}
                                    </div>
                                </td>
                                <td>
                                    @if($role->permissions && count($role->permissions) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($role->permissions, 0, 3) as $permission)
                                                <span class="badge bg-primary">{{ str_replace('_', ' ', ucwords($permission, '_')) }}</span>
                                            @endforeach
                                            @if(count($role->permissions) > 3)
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" 
                                                      title="{{ implode(', ', array_slice($role->permissions, 3)) }}">
                                                    +{{ count($role->permissions) - 3 }} more
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
                                            @foreach(array_slice($role->page_access, 0, 3) as $page)
                                                <span class="badge bg-info">{{ ucfirst($page) }}</span>
                                            @endforeach
                                            @if(count($role->page_access) > 3)
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" 
                                                      title="{{ implode(', ', array_slice($role->page_access, 3)) }}">
                                                    +{{ count($role->page_access) - 3 }} more
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
                                    @if($userCount > 0)
                                        <span class="badge bg-success">{{ $userCount }} users</span>
                                    @else
                                        <span class="text-muted">No users</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" 
                                               type="checkbox" 
                                               data-id="{{ $role->_id }}"
                                               data-url="{{ route('admin.roles.toggle-status', $role) }}"
                                               {{ $role->status ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle hide-arrow" 
                                                data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('admin.roles.show', $role) }}">
                                                <i class="icon-base ti tabler-eye"></i>View Details
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}">
                                                <i class="icon-base ti tabler-edit"></i>Edit Role
                                            </a>
                                            @if($userCount == 0)
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.roles.destroy', $role) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="icon-base ti tabler-trash me-2"></i>Delete Role
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
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="icon-base ti tabler-shield-off display-4 text-muted mb-3"></i>
                                        <h6 class="text-muted">No roles found</h6>
                                        <p class="text-muted">Get started by creating your first role.</p>
                                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                            <i class="icon-base ti tabler-plus"></i>Add Role
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($roles->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Status toggle functionality
    $('.status-toggle').change(function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    checkbox.prop('checked', !isChecked);
                    toastr.error('Failed to update status');
                }
            },
            error: function() {
                checkbox.prop('checked', !isChecked);
                toastr.error('Failed to update status');
            }
        });
    });
});
</script>
@endpush
@endsection