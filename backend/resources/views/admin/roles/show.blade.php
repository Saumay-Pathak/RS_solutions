@extends('layouts.app')

@section('title', 'View Role - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Role</h4>
        <p class="mb-0">Role details and permissions</p>
      </div>
      <div>
        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Role Information</h5>
          </div>
          <div class="card-body">
            <!-- Display Name -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Display Name:</strong>
              </div>
              <div class="col-sm-9">
                <h6 class="mb-0">{{ $role->display_name }}</h6>
              </div>
            </div>

            <!-- System Name -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>System Name:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $role->name }}</code>
              </div>
            </div>

            <!-- Description -->
            @if($role->description)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Description:</strong>
              </div>
              <div class="col-sm-9">
                <div class="border-start border-primary ps-3">
                  {{ $role->description }}
                </div>
              </div>
            </div>
            @endif

            <!-- Status -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Status:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-{{ $role->status ? 'success' : 'danger' }}">
                  {{ $role->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>

            <!-- Assigned Users -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Assigned Users:</strong>
              </div>
              <div class="col-sm-9">
                <div class="d-flex align-items-center">
                  <span class="badge bg-info me-2">{{ $role->users_count ?? 0 }} users</span>
                  @if($role->users_count > 0)
                    <a href="{{ route('admin.users.index', ['role_id' => $role->_id]) }}" class="btn btn-sm btn-outline-primary">
                      <i class="icon-base ti tabler-users me-1"></i>View Users
                    </a>
                  @endif
                </div>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $role->created_at ? $role->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $role->updated_at ? $role->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Permissions -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Permissions ({{ count($role->permissions ?? []) }})</h5>
          </div>
          <div class="card-body">
            @if($role->permissions && count($role->permissions) > 0)
              <div class="row g-3">
                @foreach($role->permissions as $permission)
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 border rounded">
                      <div class="me-3">
                        <i class="icon-base ti tabler-check-circle text-success icon-sm"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $availablePermissions[$permission] ?? $permission }}</h6>
                        <small class="text-muted">{{ $permission }}</small>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-4">
                <i class="icon-base ti tabler-lock-access display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No permissions assigned to this role</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Page Access -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Page Access ({{ count($role->page_access ?? []) }})</h5>
          </div>
          <div class="card-body">
            @if($role->page_access && count($role->page_access) > 0)
              <div class="row g-3">
                @foreach($role->page_access as $page)
                  <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-center p-3 border rounded">
                      <div class="me-3">
                        <i class="icon-base ti tabler-page text-primary icon-sm"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $availablePages[$page] ?? ucwords(str_replace('-', ' ', $page)) }}</h6>
                        <small class="text-muted">{{ $page }}</small>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-4">
                <i class="icon-base ti tabler-page-break display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No page access configured for this role</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Users with this Role -->
        @if($role->users_count > 0)
        <div class="card mb-6">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Users with this Role ({{ $role->users_count }})</h5>
            <a href="{{ route('admin.users.index', ['role_id' => $role->_id]) }}" class="btn btn-sm btn-outline-primary">
              <i class="icon-base ti tabler-external-link me-1"></i>View All
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($role->users()->take(10)->get() as $user)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" 
                                 class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;">
                          @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                              <span class="text-white fw-bold small">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                          @endif
                          <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->phone ?? 'No phone' }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                      </td>
                      <td>
                        <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                          {{ $user->status ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td>
                        @if($user->last_login)
                          <small>{{ $user->last_login->format('M d, Y') }}</small>
                        @else
                          <small class="text-muted">Never</small>
                        @endif
                      </td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                              <i class="icon-base ti tabler-eye me-2"></i>View
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                              <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a></li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              @if($role->users_count > 10)
                <div class="text-center mt-3">
                  <small class="text-muted">Showing 10 of {{ $role->users_count }} users.</small>
                  <a href="{{ route('admin.users.index', ['role_id' => $role->_id]) }}" class="btn btn-sm btn-link">View All Users</a>
                </div>
              @endif
            </div>
          </div>
        </div>
        @endif
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Role Statistics -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Role Statistics</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-users icon-sm text-primary"></i>
                  </div>
                  <div class="flex-grow-1">
                    <small class="text-muted">Assigned Users</small>
                    <div class="fw-semibold">{{ $role->users_count ?? 0 }}</div>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-lock-access icon-sm text-success"></i>
                  </div>
                  <div>
                    <small class="text-muted">Permissions</small>
                    <div class="fw-semibold">{{ count($role->permissions ?? []) }}</div>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-page icon-sm text-info"></i>
                  </div>
                  <div>
                    <small class="text-muted">Page Access</small>
                    <div class="fw-semibold">{{ count($role->page_access ?? []) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Role Status -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Role Status</h5>
          </div>
          <div class="card-body text-center">
            @if($role->status)
              <div class="mb-2">
                <i class="icon-base ti tabler-check-circle icon-lg text-success"></i>
              </div>
              <h6 class="text-success mb-1">Active Role</h6>
              <small class="text-muted">This role is active and can be assigned to users</small>
            @else
              <div class="mb-2">
                <i class="icon-base ti tabler-x-circle icon-lg text-danger"></i>
              </div>
              <h6 class="text-danger mb-1">Inactive Role</h6>
              <small class="text-muted">This role is inactive and cannot be assigned to new users</small>
            @endif
          </div>
        </div>

        <!-- Technical Information -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Technical Information</h5>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-sm-4">
                <strong>Role ID:</strong>
              </div>
              <div class="col-sm-8">
                <code class="small">{{ $role->_id }}</code>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-sm-4">
                <strong>Collection:</strong>
              </div>
              <div class="col-sm-8">
                <code class="small">roles</code>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-4">
                <strong>System Name:</strong>
              </div>
              <div class="col-sm-8">
                <code class="small">{{ $role->name }}</code>
              </div>
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
              <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Role
              </a>
              
              <button type="button" class="btn btn-{{ $role->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $role->_id }}')">
                <i class="icon-base ti tabler-toggle-{{ $role->status ? 'left' : 'right' }} me-2"></i>
                {{ $role->status ? 'Deactivate' : 'Activate' }}
              </button>

              @if($role->users_count > 0)
              <a href="{{ route('admin.users.index', ['role_id' => $role->_id]) }}" class="btn btn-outline-info">
                <i class="icon-base ti tabler-users me-2"></i>View Users
              </a>
              @endif

              <a href="{{ route('admin.roles.create') }}" class="btn btn-outline-primary">
                <i class="icon-base ti tabler-copy me-2"></i>Clone Role
              </a>

              <hr class="my-3">

              @if($role->users_count === 0)
              <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Role
                </button>
              </form>
              @else
              <div class="alert alert-warning p-3">
                <small>
                  <i class="icon-base ti tabler-info-circle me-1"></i>
                  Cannot delete role with assigned users.
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

@push('scripts')
<script>
function toggleStatus(roleId) {
    if (confirm('Are you sure you want to change the role status?')) {
        $.ajax({
            url: `/admin/roles/${roleId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating role status');
                }
            },
            error: function() {
                alert('Error updating role status');
            }
        });
    }
}
</script>
@endpush
@endsection