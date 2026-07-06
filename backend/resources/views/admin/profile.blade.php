@extends('admin.layouts.admin')

@section('title', 'Profile Settings')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Profile Settings</h4>
        <p class="mb-0">Manage your account information and preferences</p>
      </div>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Dashboard
      </a>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <!-- Update Profile -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="icon-base ti tabler-user me-2"></i>Profile Information
            </h5>
          </div>
          <div class="card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" 
                         id="name" name="name" value="{{ old('name', $user->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" 
                         id="email" name="email" value="{{ old('email', $user->email) }}" required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="phone">Phone Number</label>
                  <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                         id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                         placeholder="+1 (555) 000-0000">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="profile_image">Profile Image</label>
                  <input type="file" class="form-control @error('profile_image') is-invalid @enderror" 
                         id="profile_image" name="profile_image" accept="image/*">
                  <div class="form-text">Max 2MB. JPG, PNG, GIF supported</div>
                  @error('profile_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label" for="address">Address</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3" 
                          placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Image Preview -->
              <div id="image-preview-container" class="mb-4 d-none">
                <label class="form-label">Preview</label>
                <div class="d-flex align-items-center">
                  <img id="image-preview" src="" alt="Preview" 
                       class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                  <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image-preview">
                    <i class="icon-base ti tabler-trash me-1"></i>Remove
                  </button>
                </div>
              </div>

              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-check me-2"></i>Update Profile
              </button>
            </form>
          </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="icon-base ti tabler-lock me-2"></i>Change Password
            </h5>
          </div>
          <div class="card-body">
            <form action="{{ route('admin.profile.password') }}" method="POST" id="password-form">
              @csrf
              @method('PUT')
              
              <div class="row mb-4">
                <div class="col-md-4">
                  <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                           id="current_password" name="current_password" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password">
                      <i class="icon-base ti tabler-eye"></i>
                    </button>
                  </div>
                  @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="col-md-4">
                  <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required minlength="8">
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                      <i class="icon-base ti tabler-eye"></i>
                    </button>
                  </div>
                  <div class="form-text">Minimum 8 characters</div>
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label" for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required minlength="8">
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation">
                      <i class="icon-base ti tabler-eye"></i>
                    </button>
                  </div>
                  <div class="invalid-feedback" id="password-match-error" style="display: none;">
                    Passwords do not match
                  </div>
                </div>
              </div>

              <!-- Password Strength Indicator -->
              <div class="mb-4">
                <label class="form-label">Password Strength</label>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar" id="password-strength-bar" style="width: 0%"></div>
                </div>
                <small class="text-muted" id="password-strength-text">Enter a password</small>
              </div>

              <button type="submit" class="btn btn-warning" id="change-password-btn" disabled>
                <i class="icon-base ti tabler-key me-2"></i>Change Password
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-6">
          <div class="card-body text-center">
            <div class="mb-4">
              @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" 
                     alt="Profile Image" 
                     class="rounded-circle mb-3" 
                     style="width: 120px; height: 120px; object-fit: cover;">
              @else
                <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                     style="width: 120px; height: 120px;">
                  <span class="display-4 text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                </div>
              @endif
            </div>
            <h5 class="mb-2">{{ $user->name }}</h5>
            <p class="text-muted mb-4">{{ $user->email }}</p>
            
            @if($user->role)
              <div class="badge bg-label-primary mb-3">{{ $user->role->display_name }}</div>
            @endif
            
            @if($user->last_login)
              <p class="small text-muted mb-0">
                <i class="icon-base ti tabler-clock me-1"></i>
                Last login: {{ $user->last_login->format('M d, Y h:i A') }}
              </p>
            @endif
          </div>
        </div>

        <!-- Account Status -->
        <div class="card mb-6">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="icon-base ti tabler-shield-check me-2"></i>Account Status
            </h6>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fw-medium">Status</span>
              <span class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}">
                {{ $user->status ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fw-medium">Member Since</span>
              <span class="text-muted">{{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="fw-medium">Profile Completion</span>
              @php
                $completion = 0;
                if($user->name) $completion += 20;
                if($user->email) $completion += 20;
                if($user->phone) $completion += 20;
                if($user->address) $completion += 20;
                if($user->profile_image) $completion += 20;
              @endphp
              <div class="d-flex align-items-center">
                <div class="progress me-2" style="width: 60px; height: 6px;">
                  <div class="progress-bar" style="width: {{ $completion }}%"></div>
                </div>
                <span class="small text-muted">{{ $completion }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Permissions -->
        @if($user->role && $user->role->permissions)
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="icon-base ti tabler-key me-2"></i>Permissions
            </h6>
          </div>
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
              @foreach($user->role->permissions as $permission)
                <span class="badge bg-primary">{{ str_replace('_', ' ', ucwords($permission, '_')) }}</span>
              @endforeach
            </div>
          </div>
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
    // Image preview
    const imageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const removeImageBtn = document.getElementById('remove-image-preview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
    
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreviewContainer.classList.add('d-none');
    });

    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'icon-base ti tabler-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'icon-base ti tabler-eye';
            }
        });
    });

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    const changePasswordBtn = document.getElementById('change-password-btn');
    const passwordMatchError = document.getElementById('password-match-error');
    
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];
        
        if (password.length >= 8) strength += 1;
        else feedback.push('at least 8 characters');
        
        if (/[a-z]/.test(password)) strength += 1;
        else feedback.push('lowercase letter');
        
        if (/[A-Z]/.test(password)) strength += 1;
        else feedback.push('uppercase letter');
        
        if (/\d/.test(password)) strength += 1;
        else feedback.push('number');
        
        if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
        else feedback.push('special character');
        
        return { strength, feedback };
    }
    
    function updatePasswordStrength() {
        const password = passwordInput.value;
        const { strength, feedback } = checkPasswordStrength(password);
        
        const percentage = (strength / 5) * 100;
        strengthBar.style.width = percentage + '%';
        
        if (strength === 0) {
            strengthBar.className = 'progress-bar';
            strengthText.textContent = 'Enter a password';
        } else if (strength <= 2) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Weak - Add: ' + feedback.slice(0, 2).join(', ');
        } else if (strength <= 3) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Medium - Add: ' + feedback.slice(0, 1).join(', ');
        } else if (strength <= 4) {
            strengthBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Good - Add: ' + feedback.join(', ');
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Excellent';
        }
        
        validatePasswords();
    }
    
    function validatePasswords() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        const currentPassword = document.getElementById('current_password').value;
        
        const isPasswordStrong = checkPasswordStrength(password).strength >= 3;
        const passwordsMatch = password === confirmPassword && password.length > 0;
        const hasCurrentPassword = currentPassword.length > 0;
        
        if (confirmPassword.length > 0 && !passwordsMatch) {
            passwordMatchError.style.display = 'block';
            passwordConfirmInput.classList.add('is-invalid');
        } else {
            passwordMatchError.style.display = 'none';
            passwordConfirmInput.classList.remove('is-invalid');
        }
        
        changePasswordBtn.disabled = !(isPasswordStrong && passwordsMatch && hasCurrentPassword);
    }
    
    passwordInput.addEventListener('input', updatePasswordStrength);
    passwordConfirmInput.addEventListener('input', validatePasswords);
    document.getElementById('current_password').addEventListener('input', validatePasswords);
    
    // Auto-save draft functionality for profile form (optional)
    let autoSaveTimer;
    const profileForm = document.querySelector('form[action*="profile.update"]');
    const formInputs = profileForm.querySelectorAll('input:not([type="file"]), textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                console.log('Auto-saving profile draft...');
                // Could implement auto-save functionality here
            }, 3000);
        });
    });
});
</script>
@endpush