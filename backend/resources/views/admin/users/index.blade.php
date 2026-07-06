@extends('layouts.app')

@section('title', 'Users Management - Realtime Biometrics')

@push('styles')
<link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="card-title mb-0">Users Management</h5>
            <small class="text-muted">Manage system users and their permissions</small>
          </div>
          <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
              <i class="icon-base ti tabler-plus"></i>Add New User
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Role</label>
              <select name="role_id" class="form-select">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                  <option value="{{ $role->_id }}" {{ request('role_id') == $role->_id ? 'selected' : '' }}>
                    {{ $role->display_name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">&nbsp;</label>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Filter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-datatable table-responsive">
          <table class="datatables-users table">
            <thead class="border-top">
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td>
                    <div class="d-flex justify-content-start align-items-center user-name">
                      <div class="avatar-wrapper">
                        <div class="avatar me-3">
                          @if($user->profile_image)
                            <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle">
                          @else
                            <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($user->name, 0, 2) }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="d-flex flex-column">
                        <h6 class="user-name text-body mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->phone ?: 'No phone' }}</small>
                      </div>
                    </div>
                  </td>
                  <td>{{ $user->email }}</td>
                  <td>
                    @if($user->role)
                      <span class="badge bg-label-info">{{ $user->role->display_name }}</span>
                    @else
                      <span class="badge bg-label-secondary">No Role</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-label-{{ $user->status ? 'success' : 'danger' }}">
                      {{ $user->status ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                          <i class="icon-base ti tabler-eye"></i>View
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                          <i class="icon-base ti tabler-pencil me-1"></i>Edit
                        </a>
                        <a class="dropdown-item toggle-status" href="#" data-url="{{ route('admin.users.toggle-status', $user) }}">
                          <i class="icon-base ti tabler-{{ $user->status ? 'user-minus' : 'user-plus' }} me-1"></i>
                          {{ $user->status ? 'Deactivate' : 'Activate' }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                          @csrf
                          @method('DELETE')
                          <button class="dropdown-item text-danger delete-btn" type="submit">
                            <i class="icon-base ti tabler-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4">
                    <div class="text-center">
                      <img src="../assets/img/illustrations/girl-doing-yoga-light.png" alt="No users found" class="img-fluid" style="height: 150px;">
                      <p class="mt-4 mb-0">No users found</p>
                      <small class="text-muted">Try adjusting your search filters</small>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($users->hasPages())
          <div class="card-footer">
            {{ $users->appends(request()->query())->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable with minimal features for server-side pagination
    $('.datatables-users').DataTable({
        pageLength: 15,
        lengthChange: false,
        searching: false,
        ordering: false,
        info: false,
        paging: false
    });
});
</script>
@endpush