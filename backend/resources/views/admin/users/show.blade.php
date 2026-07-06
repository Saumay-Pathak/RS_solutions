@extends('layouts.app')

@section('title', 'View User - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View User</h4>
        <p class="mb-0">User profile and information</p>
      </div>
      <div>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">User Information</h5>
          </div>
          <div class="card-body">
            <!-- Full Name -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Full Name:</strong>
              </div>
              <div class="col-sm-9">
                {{ $user->name }}
              </div>
            </div>

            <!-- Email -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Email:</strong>
              </div>
              <div class="col-sm-9">
                <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
              </div>
            </div>

            <!-- Phone -->
            @if($user->phone)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Phone:</strong>
              </div>
              <div class="col-sm-9">
                <a href="tel:{{ $user->phone }}" class="text-decoration-none">{{ $user->phone }}</a>
              </div>
            </div>
            @endif

            <!-- Address -->
            @if($user->address)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Address:</strong>
              </div>
              <div class="col-sm-9">
                <div class="border-start border-primary ps-3">
                  {!! nl2br(e($user->address)) !!}
                </div>
              </div>
            </div>
            @endif

            <!-- Role -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Role:</strong>
              </div>
              <div class="col-sm-9">
                @if($user->role)
                  <div class="d-flex align-items-center">
                    <span class="badge bg-info me-2">{{ $user->role->display_name }}</span>
                    <small class="text-muted">({{ $user->role->name }})</small>
                  </div>
                @else
                  <span class="text-muted">No role assigned</span>
                @endif
              </div>
            </div>

            <!-- Account Status -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Account Status:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                  {{ $user->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>

            <!-- Last Login -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Last Login:</strong>
              </div>
              <div class="col-sm-9">
                @if($user->last_login)
                  <span class="text-success">
                    <i class="icon-base ti tabler-clock me-1"></i>
                    {{ $user->last_login->format('M d, Y \\a\\t H:i') }}
                  </span>
                  <small class="text-muted d-block">{{ $user->last_login->diffForHumans() }}</small>
                @else
                  <span class="text-muted">Never logged in</span>
                @endif
              </div>
            </div>

            <!-- Account Created -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Account Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $user->created_at ? $user->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
                @if($user->created_at)
                  <small class="text-muted d-block">{{ $user->created_at->diffForHumans() }}</small>
                @endif
              </div>
            </div>

            <!-- Last Updated -->
            <div class="row">
              <div class="col-sm-3">
                <strong>Last Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $user->updated_at ? $user->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
                @if($user->updated_at)
                  <small class="text-muted d-block">{{ $user->updated_at->diffForHumans() }}</small>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Role & Permissions -->
        @if($user->role)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Role & Permissions</h5>
          </div>
          <div class="card-body">
            <!-- Role Details -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Role Name:</strong>
              </div>
              <div class="col-sm-9">
                <div class="d-flex align-items-center">
                  <h6 class="mb-0 me-2">{{ $user->role->display_name }}</h6>
                  <span class="badge bg-{{ $user->role->status ? 'success' : 'danger' }}">
                    {{ $user->role->status ? 'Active' : 'Inactive' }}
                  </span>
                </div>
                @if($user->role->description)
                  <small class="text-muted">{{ $user->role->description }}</small>
                @endif
              </div>
            </div>

            <!-- Permissions -->
            @if($user->role->permissions && count($user->role->permissions) > 0)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Permissions:</strong>
              </div>
              <div class="col-sm-9">
                <div class="row g-2">
                  @foreach($user->role->permissions as $permission)
                    <div class="col-auto">
                      <span class="badge bg-light text-dark">
                        <i class="icon-base ti tabler-check-circle me-1"></i>
                        {{ $permission }}
                      </span>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
            @endif

            <!-- Page Access -->
            @if($user->role->page_access && count($user->role->page_access) > 0)
            <div class="row">
              <div class="col-sm-3">
                <strong>Page Access:</strong>
              </div>
              <div class="col-sm-9">
                <div class="row g-2">
                  @foreach($user->role->page_access as $page)
                    <div class="col-auto">
                      <span class="badge bg-primary">
                        <i class="icon-base ti tabler-page me-1"></i>
                        {{ ucwords(str_replace('-', ' ', $page)) }}
                      </span>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        @endif

        <!-- User ID and Technical Info -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Technical Information</h5>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-sm-3">
                <strong>User ID:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $user->_id }}</code>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-sm-3">
                <strong>Database Collection:</strong>
              </div>
              <div class="col-sm-9">
                <code>users</code>
              </div>
            </div>

            @if($user->role)
            <div class="row">
              <div class="col-sm-3">
                <strong>Role ID:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $user->role_id }}</code>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Profile Image -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Profile Photo</h5>
          </div>
          <div class="card-body text-center">
            @if($user->profile_image)
              <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" 
                   class="img-fluid rounded mb-3" style="max-height: 300px; cursor: pointer;"
                   onclick="openImageModal('{{ asset('storage/' . $user->profile_image) }}')">
            @else
              <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                <div class="text-center">
                  <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <span class="text-white fw-bold fs-2">{{ substr($user->name, 0, 1) }}</span>
                  </div>
                  <p class="text-muted mb-0">No profile photo</p>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- Account Statistics -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Account Statistics</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-calendar icon-sm text-info"></i>
                  </div>
                  <div class="flex-grow-1">
                    <small class="text-muted">Member Since</small>
                    <div class="fw-semibold">{{ $user->created_at ? $user->created_at->format('M Y') : 'Unknown' }}</div>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-login icon-sm text-success"></i>
                  </div>
                  <div>
                    <small class="text-muted">Login Status</small>
                    <div class="fw-semibold">
                      @if($user->last_login)
                        <span class="text-success">Active</span>
                      @else
                        <span class="text-warning">Never</span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="me-2">
                    <i class="icon-base ti tabler-shield icon-sm text-primary"></i>
                  </div>
                  <div>
                    <small class="text-muted">Role Type</small>
                    <div class="fw-semibold">{{ $user->role ? $user->role->display_name : 'None' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Contact Information</h5>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <i class="icon-base ti tabler-mail me-3 text-primary"></i>
              <div>
                <strong>Email</strong>
                <div><a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a></div>
              </div>
            </div>

            @if($user->phone)
            <div class="d-flex align-items-center">
              <i class="icon-base ti tabler-phone me-3 text-success"></i>
              <div>
                <strong>Phone</strong>
                <div><a href="tel:{{ $user->phone }}" class="text-decoration-none">{{ $user->phone }}</a></div>
              </div>
            </div>
            @else
            <div class="d-flex align-items-center">
              <i class="icon-base ti tabler-phone me-3 text-muted"></i>
              <div>
                <strong>Phone</strong>
                <div class="text-muted">Not provided</div>
              </div>
            </div>
            @endif
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit User
              </a>
              
              <button type="button" class="btn btn-{{ $user->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $user->_id }}')">
                <i class="icon-base ti tabler-toggle-{{ $user->status ? 'left' : 'right' }} me-2"></i>
                {{ $user->status ? 'Deactivate' : 'Activate' }}
              </button>

              <a href="mailto:{{ $user->email }}" class="btn btn-outline-info">
                <i class="icon-base ti tabler-mail me-2"></i>Send Email
              </a>

              @if($user->role)
              <a href="{{ route('admin.roles.show', $user->role) }}" class="btn btn-outline-primary">
                <i class="icon-base ti tabler-shield me-2"></i>View Role Details
              </a>
              @endif

              <hr class="my-3">

              @if(auth()->id() !== $user->_id)
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone and will remove all user data.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete User
                </button>
              </form>
              @else
              <div class="alert alert-warning p-3">
                <small>
                  <i class="icon-base ti tabler-info-circle me-1"></i>
                  You cannot delete your own account.
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

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">{{ $user->name }} - Profile Photo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Profile Photo" class="img-fluid">
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openImageModal(imageSrc) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}

function toggleStatus(userId) {
    if (confirm('Are you sure you want to change the user account status?')) {
        $.ajax({
            url: `/admin/users/${userId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating user status');
                }
            },
            error: function() {
                alert('Error updating user status');
            }
        });
    }
}
</script>
@endpush
@endsection