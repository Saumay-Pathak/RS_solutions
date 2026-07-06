@extends('admin.layouts.admin')

@section('title', 'Extras & Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 mb-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-1">Site Settings & Configuration</h5>
                        <p class="card-subtitle mb-0">Manage your site features and preferences</p>
                    </div>
                    <div class="dropdown">
                        <button
                            class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1"
                            type="button"
                            id="settingsActions"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsActions">
                            <a class="dropdown-item" href="javascript:void(0);" onclick="exportSettings()">
                                <i class="icon-base ti tabler-download me-2"></i>Export Settings
                            </a>
                            <label class="dropdown-item" for="importFile" style="cursor: pointer;">
                                <i class="icon-base ti tabler-upload me-2"></i>Import Settings
                            </label>
                            <input type="file" id="importFile" style="display: none;" accept=".json" onchange="importSettings(this)">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="clearCache()">
                                <i class="icon-base ti tabler-refresh me-2"></i>Clear Cache
                            </a>
                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="resetDefaults()">
                                <i class="icon-base ti tabler-restore me-2"></i>Reset to Defaults
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.extras.update') }}" method="POST" id="settingsForm">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            @php
                                $groupIcons = [
                                    'analytics' => 'tabler-chart-line',
                                    'popups' => 'tabler-window',
                                    'features' => 'tabler-settings',
                                    'system' => 'tabler-server',
                                    'general' => 'tabler-sliders'
                                ];
                                
                                $groupColors = [
                                    'analytics' => 'primary',
                                    'popups' => 'info', 
                                    'features' => 'success',
                                    'system' => 'warning',
                                    'general' => 'secondary'
                                ];
                            @endphp

                            @foreach($settings as $group => $groupSettings)
                                <div class="col-md-6 col-xxl-4 mb-6">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="card-title mb-0">
                                                <h5 class="mb-1 d-flex align-items-center">
                                                    <div class="badge bg-label-{{ $groupColors[$group] ?? 'primary' }} rounded p-1_5 me-3">
                                                        <i class="icon-base ti {{ $groupIcons[$group] ?? 'tabler-settings' }} icon-md"></i>
                                                    </div>
                                                    {{ ucwords(str_replace('_', ' ', $group)) }}
                                                </h5>
                                                <p class="card-subtitle mb-0">Configure {{ strtolower($group) }} options</p>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="p-0 m-0">
                                                @foreach($groupSettings as $setting)
                                                    <li class="{{ !$loop->last ? 'mb-4' : '' }} d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-start w-100">
                                                            @if($setting->type === 'boolean')
                                                                <div class="w-100">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <h6 class="mb-0">{{ $setting->label }}</h6>
                                                                            @if($setting->description)
                                                                                <small class="text-muted">{{ $setting->description }}</small>
                                                                            @endif
                                                                        </div>
                                                                        <div class="form-check form-switch ms-3">
                                                                            <input class="form-check-input setting-toggle" 
                                                                                   type="checkbox" 
                                                                                   id="setting_{{ $setting->key }}" 
                                                                                   name="settings[{{ $setting->key }}]"
                                                                                   value="1"
                                                                                   {{ $setting->value ? 'checked' : '' }}
                                                                                   data-key="{{ $setting->key }}"
                                                                                   onchange="toggleSetting(this)">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="w-100">
                                                                    <h6 class="mb-1">{{ $setting->label }}</h6>
                                                                    @if($setting->description)
                                                                        <small class="text-muted d-block mb-2">{{ $setting->description }}</small>
                                                                    @endif
                                                                    @if($setting->type === 'text')
                                                                        <input type="text" 
                                                                               class="form-control form-control-sm" 
                                                                               id="setting_{{ $setting->key }}" 
                                                                               name="settings[{{ $setting->key }}]"
                                                                               value="{{ $setting->value }}"
                                                                               placeholder="Enter {{ strtolower($setting->label) }}">
                                                                    @elseif($setting->type === 'textarea')
                                                                        <textarea class="form-control form-control-sm" 
                                                                                  id="setting_{{ $setting->key }}" 
                                                                                  name="settings[{{ $setting->key }}]"
                                                                                  rows="2"
                                                                                  placeholder="Enter {{ strtolower($setting->label) }}">{{ $setting->value }}</textarea>
                                                                    @elseif($setting->type === 'integer')
                                                                        <input type="number" 
                                                                               class="form-control form-control-sm" 
                                                                               id="setting_{{ $setting->key }}" 
                                                                               name="settings[{{ $setting->key }}]"
                                                                               value="{{ $setting->value }}"
                                                                               placeholder="Enter {{ strtolower($setting->label) }}">
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save All Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Form (Hidden) -->
<form id="importForm" action="{{ route('admin.extras.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="file" name="settings_file" id="importFileInput">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Toggle individual setting via AJAX
function toggleSetting(element) {
    const key = element.dataset.key;
    const value = element.checked;
    
    // Show loading state
    element.disabled = true;
    
    fetch('{{ route("admin.extras.update-single") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            key: key,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        element.disabled = false;
        
        if (data.success) {
            // Show success feedback
            showToast('Setting updated successfully!', 'success');
        } else {
            // Revert toggle and show error
            element.checked = !value;
            showToast('Failed to update setting: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        element.disabled = false;
        element.checked = !value;
        showToast('Network error occurred', 'error');
        console.error('Error:', error);
    });
}

// Export settings
function exportSettings() {
    window.location.href = '{{ route("admin.extras.export") }}';
}

// Import settings
function importSettings(input) {
    if (input.files && input.files[0]) {
        if (confirm('Are you sure you want to import settings? This will overwrite existing settings.')) {
            document.getElementById('importFileInput').files = input.files;
            document.getElementById('importForm').submit();
        }
        input.value = '';
    }
}

// Clear cache
function clearCache() {
    if (confirm('Are you sure you want to clear all cache?')) {
        fetch('{{ route("admin.extras.clear-cache") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Cache cleared successfully!', 'success');
            } else {
                showToast('Failed to clear cache', 'error');
            }
        })
        .catch(error => {
            showToast('Network error occurred', 'error');
        });
    }
}

// Reset to defaults
function resetDefaults() {
    if (confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.extras.reset") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Show toast notification
function showToast(message, type) {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Auto-save on form inputs (debounced)
let saveTimeout;
document.querySelectorAll('input[type="text"], input[type="number"], textarea').forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            // Auto-save logic can be implemented here if needed
        }, 1000);
    });
});
</script>

<style>
.form-switch .form-check-input:checked {
    background-color: #696cff;
    border-color: #696cff;
}

.card {
    transition: all 0.2s ease-in-out;
    border: 1px solid #e7eaf3;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
}

.setting-toggle {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}

.toast-container {
    max-width: 350px;
}

.badge.p-1_5 {
    padding: 0.5rem;
}

.card-title h5 {
    color: #566a7f;
    font-weight: 500;
}

.card-subtitle {
    color: #a7acb2;
    font-size: 0.8125rem;
}

.btn-text-secondary {
    color: #a7acb2 !important;
}

.btn-text-secondary:hover {
    color: #566a7f !important;
}

ul li {
    list-style: none;
}

.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.775rem;
}

@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection