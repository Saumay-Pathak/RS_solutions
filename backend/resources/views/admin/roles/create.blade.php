@extends('layouts.app')

@section('title', 'Create Role - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Create New Role</h4>
        <p class="mb-0">Define permissions and page access for the new role</p>
      </div>
      <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Roles
      </a>
    </div>

    <!-- Create Role Form -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Role Information</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.roles.store') }}">
          @csrf
          
          <!-- Basic Information -->
          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label" for="name">Role Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" 
                     id="name" name="name" value="{{ old('name') }}" 
                     placeholder="e.g., admin, editor, viewer">
              <div class="form-text">Used internally (lowercase, no spaces)</div>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="display_name">Display Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                     id="display_name" name="display_name" value="{{ old('display_name') }}" 
                     placeholder="e.g., Administrator, Content Editor">
              <div class="form-text">Human-readable name shown to users</div>
              @error('display_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-12">
              <label class="form-label" for="description">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="3" 
                        placeholder="Brief description of this role's purpose">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Status -->
          <div class="row mb-6">
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status') ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  Active Status
                </label>
                <div class="form-text">Only active roles can be assigned to users</div>
              </div>
            </div>
          </div>

          <!-- Permissions Section -->
          <div class="row mb-6">
            <div class="col-12">
              <h6 class="mb-3">
                <i class="icon-base ti tabler-shield me-2"></i>Permissions
                <small class="text-muted">(What this role can do)</small>
              </h6>
              <div class="row">
                @foreach($availablePermissions as $key => $permission)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" 
                             id="permission_{{ $key }}" 
                             name="permissions[]" 
                             value="{{ $key }}"
                             {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                      <label class="form-check-label" for="permission_{{ $key }}">
                        {{ $permission }}
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
              @error('permissions')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Page Access Section -->
          <div class="row mb-6">
            <div class="col-12">
              <h6 class="mb-3">
                <i class="icon-base ti tabler-layout-dashboard me-2"></i>Page Access
                <small class="text-muted">(Which pages this role can access)</small>
              </h6>
              <div class="row">
                @foreach($availablePages as $key => $page)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" 
                             id="page_{{ $key }}" 
                             name="page_access[]" 
                             value="{{ $key }}"
                             {{ in_array($key, old('page_access', [])) ? 'checked' : '' }}>
                      <label class="form-check-label" for="page_{{ $key }}">
                        {{ $page }}
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
              @error('page_access')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                  <i class="icon-base ti tabler-x me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ti tabler-plus"></i>Create Role
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate role name from display name
    const displayNameInput = document.getElementById('display_name');
    const nameInput = document.getElementById('name');
    
    displayNameInput.addEventListener('input', function() {
        if (!nameInput.value || nameInput.dataset.manual !== 'true') {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .trim();
            nameInput.value = slug;
        }
    });
    
    nameInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Select All functionality
    function addSelectAllButton(containerId, checkboxName, title) {
        const container = document.querySelector(containerId);
        if (container) {
            const selectAllBtn = document.createElement('button');
            selectAllBtn.type = 'button';
            selectAllBtn.className = 'btn btn-sm btn-outline-primary me-2 mb-3';
            selectAllBtn.innerHTML = `<i class="icon-base ti tabler-check me-1"></i>Select All ${title}`;
            
            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll(`input[name="${checkboxName}"]`);
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
                this.innerHTML = allChecked ? 
                    `<i class="icon-base ti tabler-check me-1"></i>Select All ${title}` : 
                    `<i class="icon-base ti tabler-trash me-2"></i>Clear All ${title}`;
            });
            
            container.parentNode.insertBefore(selectAllBtn, container);
        }
    }
    
    // Add select all buttons
    const permissionsRow = document.querySelector('.row:has(input[name="permissions[]"])');
    if (permissionsRow) {
        const selectAllPerms = document.createElement('div');
        selectAllPerms.className = 'col-12 mb-3';
        selectAllPerms.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-primary me-2" id="selectAllPermissions">
                <i class="icon-base ti tabler-check me-1"></i>Select All Permissions
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllPermissions">
                <i class="icon-base ti tabler-trash me-2"></i>Clear All
            </button>
        `;
        permissionsRow.prepend(selectAllPerms);
        
        document.getElementById('selectAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
        });
        
        document.getElementById('clearAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
        });
    }
    
    const pagesRow = document.querySelector('.row:has(input[name="page_access[]"])');
    if (pagesRow) {
        const selectAllPages = document.createElement('div');
        selectAllPages.className = 'col-12 mb-3';
        selectAllPages.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-info me-2" id="selectAllPages">
                <i class="icon-base ti tabler-check me-1"></i>Select All Pages
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllPages">
                <i class="icon-base ti tabler-trash me-2"></i>Clear All
            </button>
        `;
        pagesRow.prepend(selectAllPages);
        
        document.getElementById('selectAllPages').addEventListener('click', function() {
            document.querySelectorAll('input[name="page_access[]"]').forEach(cb => cb.checked = true);
        });
        
        document.getElementById('clearAllPages').addEventListener('click', function() {
            document.querySelectorAll('input[name="page_access[]"]').forEach(cb => cb.checked = false);
        });
    }
});
</script>
@endpush