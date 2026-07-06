@extends('layouts.app')

@section('title', 'Add New User - Realtime Biometrics')

@push('styles')
<link rel="stylesheet" href="../assets/vendor/libs/select2/select2.css">
<link rel="stylesheet" href="../assets/vendor/libs/dropzone/dropzone.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="card-title mb-0">Add New User</h5>
            <small class="text-muted">Create a new system user</small>
          </div>
          <div>
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
      <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                      <img src="../assets/img/avatars/1.png" alt="Profile Image" class="img-fluid rounded-circle" style="height: 100px; width: 100px;" id="profile-preview">
                    </div>
                    <div class="mb-3">
                      <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
                      @error('profile_image')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    <small class="text-muted">Upload profile image (optional)</small>
                  </div>
                </div>
              </div>

              <!-- User Details -->
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" value="{{ old('name') }}" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email') }}" required>
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required>
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select select2 @error('role_id') is-invalid @enderror" required>
                      <option value="">Select Role</option>
                      @foreach($roles as $role)
                        <option value="{{ $role->_id }}" {{ old('role_id') == $role->_id ? 'selected' : '' }}>
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
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone number" value="{{ old('phone') }}">
                    @error('phone')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Enter address">{{ old('address') }}</textarea>
                    @error('address')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12 mb-3">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="status" id="status" {{ old('status', true) ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">
                        Active Status
                      </label>
                    </div>
                    <small class="text-muted">Enable this to allow user to login</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-end gap-3">
              <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="icon-base ti tabler-x me-2"></i>Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-check me-1"></i>Create User
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="../assets/vendor/libs/select2/select2.js"></script>
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
</script>
@endpush