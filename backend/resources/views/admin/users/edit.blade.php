@extends('layouts.app')

@section('title', 'Edit User - Realtime Biometrics')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="card-title mb-0">Edit User</h5>
            <small class="text-muted">Update user information</small>
          </div>
          <div>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info me-2">
              <i class="icon-base ti tabler-eye me-1"></i>View User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
              <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Users
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- User Form -->
  <div class="row">
    <div class="col-12">
      <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">User Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- Profile Image -->
              <div class="col-md-3 mb-3">
                <div class="card border">
                  <div class="card-body text-center">
                    <div class="mb-3">
                      @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="img-fluid rounded-circle" style="height: 100px; width: 100px;" id="profile-preview">
                      @else
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Profile Image" class="img-fluid rounded-circle" style="height: 100px; width: 100px;" id="profile-preview">
                      @endif
                    </div>
                    <div class="mb-3">
                      <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
                      @error('profile_image')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    <small class="text-muted">Upload new profile image</small>
                    @if($user->profile_image)
                      <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCurrentImage()">
                          <i class="icon-base ti tabler-trash me-1"></i>Remove Current
                        </button>
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              <!-- User Details -->
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current password">
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Leave blank to keep current password</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select select2 @error('role_id') is-invalid @enderror" required>
                      <option value="">Select Role</option>
                      @foreach($roles as $role)
                        <option value="{{ $role->_id }}" {{ old('role_id', $user->role_id) == $role->_id ? 'selected' : '' }}>
                          {{ $role->display_name }}
                        </option>
                      @endforeach
                    </select>
                    @error('role_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone number" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Enter address">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12 mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="status" id="status" {{ old('status', $user->status) ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">
                        Active Status
                      </label>
                    </div>
                    <small class="text-muted">Enable this to allow user to login</small>
                  </div>

                  <!-- Display current role permissions -->
                  @if($user->role)
                  <div class="col-12 mb-3">
                    <div class="alert alert-info">
                      <h6 class="mb-2">Current Role: {{ $user->role->display_name }}</h6>
                      @if($user->role->permissions && count($user->role->permissions) > 0)
                        <small><strong>Permissions:</strong> {{ implode(', ', $user->role->permissions) }}</small>
                      @endif
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-between">
              <div>
                <!-- Delete User Button -->
                @if(auth()->id() !== $user->_id)
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                  <i class="icon-base ti tabler-trash me-1"></i>Delete User
                </button>
                @endif
              </div>
              <div class="d-flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                  <i class="icon-base ti tabler-x me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ti tabler-check me-1"></i>Update User
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Account Information -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Account Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>User ID:</strong>
              <p class="mb-0">{{ $user->_id }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Created:</strong>
              <p class="mb-0">{{ $user->created_at ? $user->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Last Updated:</strong>
              <p class="mb-0">{{ $user->updated_at ? $user->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Last Login:</strong>
              <p class="mb-0">{{ $user->last_login ? $user->last_login->format('M d, Y \\a\\t H:i') : 'Never logged in' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete User Modal -->
@if(auth()->id() !== $user->_id)
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the user <strong>{{ $user->name }}</strong>?</p>
        <p class="text-danger">This action cannot be undone and will permanently delete:</p>
        <ul class="text-danger">
          <li>User account and profile</li>
          <li>All user data and settings</li>
          <li>User's profile image</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete User</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Profile image preview
    $('#profile_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profile-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
});

function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current profile image?')) {
        // Set default image
        $('#profile-preview').attr('src', '{{ asset('assets/img/avatars/1.png') }}');
        
        // Add hidden field to indicate image removal
        $('<input>').attr({
            type: 'hidden',
            name: 'remove_image',
            value: '1'
        }).appendTo('form');
        
        // Hide the remove button
        $(event.target).closest('.mt-2').hide();
    }
}
</script>
@endpush