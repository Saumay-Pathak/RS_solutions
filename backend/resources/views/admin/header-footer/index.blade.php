@extends('admin.layouts.admin')

@section('title', 'Header & Footer Settings')

@push('admin-styles')
<style>
    .nav-pills .nav-link.active {
        background-color: #007bff;
    }
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        margin-top: 10px;
    }
    .file-upload-container {
        position: relative;
        display: inline-block;
    }
    .delete-file-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        z-index: 10;
    }
    .code-editor {
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 12px;
    }
    .form-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .form-section h5 {
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-1 d-flex align-items-center">
                            <span class="badge bg-label-primary rounded p-1_5 me-3">
                                <i class="icon-base ti tabler-settings icon-md"></i>
                            </span>
                            Header & Footer Settings
                        </h5>
                        <p class="card-subtitle mb-0">Manage branding, SEO, policies, and counters</p>
                    </div>
                    <div class="dropdown">
                        <button
                            class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1"
                            type="button"
                            id="hfActions"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="hfActions">
                            <a class="dropdown-item" href="{{ url('/api/site/header-footer') }}" target="_blank">
                                <i class="icon-base ti tabler-eye me-2"></i>Preview API
                            </a>
                            <form action="{{ route('admin.header-footer.clear-cache') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="icon-base ti tabler-refresh me-2"></i>Clear Cache
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="icon-base ti tabler-checks me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="icon-base ti tabler-alert-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="icon-base ti tabler-alert-triangle me-2"></i><strong>Validation Errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.header-footer.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills mb-4" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                                    <i class="icon-base ti tabler-info-circle me-1"></i>General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding" type="button" role="tab">
                                    <i class="icon-base ti tabler-palette me-1"></i>Branding
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="footer-tab" data-bs-toggle="pill" data-bs-target="#footer" type="button" role="tab">
                                    <i class="icon-base ti tabler-address-book me-1"></i>Footer
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="social-tab" data-bs-toggle="pill" data-bs-target="#social" type="button" role="tab">
                                    <i class="icon-base ti tabler-share me-1"></i>Social Media
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seo-tab" data-bs-toggle="pill" data-bs-target="#seo" type="button" role="tab">
                                    <i class="icon-base ti tabler-search me-1"></i>SEO
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="analytics-tab" data-bs-toggle="pill" data-bs-target="#analytics" type="button" role="tab">
                                    <i class="icon-base ti tabler-chart-line me-1"></i>Analytics
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="advanced-tab" data-bs-toggle="pill" data-bs-target="#advanced" type="button" role="tab">
                                    <i class="icon-base ti tabler-code me-1"></i>Advanced
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="policies-tab" data-bs-toggle="pill" data-bs-target="#policies" type="button" role="tab">
                                    <i class="icon-base ti tabler-gavel me-1"></i>Policies
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="counters-tab" data-bs-toggle="pill" data-bs-target="#counters" type="button" role="tab">
                                    <i class="icon-base ti tabler-calculator me-1"></i>Counters
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="settingsTabContent">
                            
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="icon-base ti tabler-world me-2"></i>Site Information</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="site_title" class="form-label">Site Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="site_title" name="site_title" value="{{ old('site_title', $settings->site_title) }}" required>
                                                <div class="form-text">Main title for your website</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="site_tagline" class="form-label">Site Tagline</label>
                                                <input type="text" class="form-control" id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $settings->site_tagline) }}">
                                                <div class="form-text">Short description or slogan</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="meta_description" class="form-label">Meta Description</label>
                                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $settings->meta_description) }}</textarea>
                                                <div class="form-text">Description for search engines (150-160 characters recommended)</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                                <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="3">{{ old('meta_keywords', $settings->meta_keywords) }}</textarea>
                                                <div class="form-text">Comma-separated keywords</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="show_search_in_header" name="show_search_in_header" value="1" {{ old('show_search_in_header', $settings->show_search_in_header) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_search_in_header">
                                                    Show Search in Header
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="show_language_switcher" name="show_language_switcher" value="1" {{ old('show_language_switcher', $settings->show_language_switcher) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_language_switcher">
                                                    Show Language Switcher
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="show_dark_mode_toggle" name="show_dark_mode_toggle" value="1" {{ old('show_dark_mode_toggle', $settings->show_dark_mode_toggle) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_dark_mode_toggle">
                                                    Show Dark Mode Toggle
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Branding Tab -->
                            <div class="tab-pane fade" id="branding" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="icon-base ti tabler-photo me-2"></i>Logo & Icons</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="logo" class="form-label">Main Logo</label>
                                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                                @if($settings->logo_url)
                                                    <div class="file-upload-container mt-2">
                                                        <img src="{{ $settings->logo_url }}" alt="Current Logo" class="image-preview">
                                                        <button type="button" class="btn btn-danger btn-sm delete-file-btn" data-field="logo">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <div class="form-text">Recommended: PNG/SVG format, max 2MB</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="footer_logo" class="form-label">Footer Logo</label>
                                                <input type="file" class="form-control" id="footer_logo" name="footer_logo" accept="image/*">
                                                @if($settings->footer_logo_url)
                                                    <div class="file-upload-container mt-2">
                                                        <img src="{{ $settings->footer_logo_url }}" alt="Current Footer Logo" class="image-preview">
                                                        <button type="button" class="btn btn-danger btn-sm delete-file-btn" data-field="footer_logo">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <div class="form-text">Logo for footer area</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="favicon" class="form-label">Favicon</label>
                                                <input type="file" class="form-control" id="favicon" name="favicon" accept="image/x-icon,image/png">
                                                @if($settings->favicon_url)
                                                    <div class="file-upload-container mt-2">
                                                        <img src="{{ $settings->favicon_url }}" alt="Current Favicon" class="image-preview">
                                                        <button type="button" class="btn btn-danger btn-sm delete-file-btn" data-field="favicon">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <div class="form-text">ICO or PNG format, 16x16 or 32x32 pixels</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="apple_touch_icon" class="form-label">Apple Touch Icon</label>
                                                <input type="file" class="form-control" id="apple_touch_icon" name="apple_touch_icon" accept="image/png">
                                                @if($settings->apple_touch_icon_url)
                                                    <div class="file-upload-container mt-2">
                                                        <img src="{{ $settings->apple_touch_icon_url }}" alt="Apple Touch Icon" class="image-preview">
                                                        <button type="button" class="btn btn-danger btn-sm delete-file-btn" data-field="apple_touch_icon">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <div class="form-text">PNG format, 180x180 pixels recommended</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="header_style" class="form-label">Header Style</label>
                                                <select class="form-select" id="header_style" name="header_style">
                                                    <option value="default" {{ old('header_style', $settings->header_style) == 'default' ? 'selected' : '' }}>Default</option>
                                                    <option value="minimal" {{ old('header_style', $settings->header_style) == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                                    <option value="centered" {{ old('header_style', $settings->header_style) == 'centered' ? 'selected' : '' }}>Centered</option>
                                                    <option value="sticky" {{ old('header_style', $settings->header_style) == 'sticky' ? 'selected' : '' }}>Sticky</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="footer_style" class="form-label">Footer Style</label>
                                                <select class="form-select" id="footer_style" name="footer_style">
                                                    <option value="default" {{ old('footer_style', $settings->footer_style) == 'default' ? 'selected' : '' }}>Default</option>
                                                    <option value="minimal" {{ old('footer_style', $settings->footer_style) == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                                    <option value="centered" {{ old('footer_style', $settings->footer_style) == 'centered' ? 'selected' : '' }}>Centered</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Counters Tab -->
                            <div class="tab-pane fade" id="counters" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-chart-bar me-2"></i>Site Counters</h5>
                                    <p class="text-muted mb-4">Edit the numeric counters shown across pages.</p>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Current Clients</h6>
                                                    <div class="mb-3">
                                                        <label class="form-label">Label</label>
                                                        <input type="text" class="form-control" name="counters[clients][label]" value="{{ old('counters.clients.label', data_get($settings->counters, 'clients.label')) }}" placeholder="Current Clients">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Value</label>
                                                        <input type="text" class="form-control" name="counters[clients][value]" value="{{ old('counters.clients.value', data_get($settings->counters, 'clients.value')) }}" placeholder="10+">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Years Of Experience</h6>
                                                    <div class="mb-3">
                                                        <label class="form-label">Label</label>
                                                        <input type="text" class="form-control" name="counters[experience][label]" value="{{ old('counters.experience.label', data_get($settings->counters, 'experience.label')) }}" placeholder="Years Of Experience">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Value</label>
                                                        <input type="text" class="form-control" name="counters[experience][value]" value="{{ old('counters.experience.value', data_get($settings->counters, 'experience.value')) }}" placeholder="35+">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Awards Winning</h6>
                                                    <div class="mb-3">
                                                        <label class="form-label">Label</label>
                                                        <input type="text" class="form-control" name="counters[awards][label]" value="{{ old('counters.awards.label', data_get($settings->counters, 'awards.label')) }}" placeholder="Awards Winning">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Value</label>
                                                        <input type="text" class="form-control" name="counters[awards][value]" value="{{ old('counters.awards.value', data_get($settings->counters, 'awards.value')) }}" placeholder="10+">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Our Solutions</h6>
                                                    <div class="mb-3">
                                                        <label class="form-label">Label</label>
                                                        <input type="text" class="form-control" name="counters[solutions][label]" value="{{ old('counters.solutions.label', data_get($settings->counters, 'solutions.label')) }}" placeholder="Our Solutions">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Value</label>
                                                        <input type="text" class="form-control" name="counters[solutions][value]" value="{{ old('counters.solutions.value', data_get($settings->counters, 'solutions.value')) }}" placeholder="0+">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Tab -->
                            <div class="tab-pane fade" id="footer" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-info-circle me-2"></i>Footer Information</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="footer_description" class="form-label">Footer Description</label>
                                                <textarea class="form-control" id="footer_description" name="footer_description" rows="3">{{ old('footer_description', $settings->footer_description) }}</textarea>
                                                <div class="form-text">Brief description about your company</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="footer_email" class="form-label">Contact Email</label>
                                                <input type="email" class="form-control" id="footer_email" name="footer_email" value="{{ old('footer_email', $settings->footer_email) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="footer_phone" class="form-label">Contact Phone</label>
                                                <input type="text" class="form-control" id="footer_phone" name="footer_phone" value="{{ old('footer_phone', $settings->footer_phone) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="footer_address" class="form-label">Address</label>
                                                <textarea class="form-control" id="footer_address" name="footer_address" rows="2">{{ old('footer_address', $settings->footer_address) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smart_app_link" class="form-label">Smart App link</label>
                                                <input type="url" class="form-control" id="smart_app_link" name="smart_app_link" value="{{ old('smart_app_link', $settings->smart_app_link) }}" placeholder="https://...">
                                                <div class="form-text">Add the Smart App URL to show in footer/header.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="attendance_app_link" class="form-label">Attendance app link</label>
                                                <input type="url" class="form-control" id="attendance_app_link" name="attendance_app_link" value="{{ old('attendance_app_link', $settings->attendance_app_link) }}" placeholder="https://...">
                                                <div class="form-text">Add the Attendance App URL to show in footer/header.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="footer_copyright" class="form-label">Copyright Text</label>
                                                <input type="text" class="form-control" id="footer_copyright" name="footer_copyright" value="{{ old('footer_copyright', $settings->footer_copyright) }}">
                                                <div class="form-text">e.g., © 2024 Your Company Name. All rights reserved.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Tab -->
                            <div class="tab-pane fade" id="social" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-share-alt me-2"></i>Social Media Links</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_facebook" class="form-label"><i class="fab fa-facebook text-primary me-1"></i>Facebook</label>
                                                <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings->social_facebook) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_twitter" class="form-label"><i class="fab fa-twitter text-info me-1"></i>Twitter</label>
                                                <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings->social_twitter) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_linkedin" class="form-label"><i class="fab fa-linkedin text-primary me-1"></i>LinkedIn</label>
                                                <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $settings->social_linkedin) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_instagram" class="form-label"><i class="fab fa-instagram text-danger me-1"></i>Instagram</label>
                                                <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings->social_instagram) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_youtube" class="form-label"><i class="fab fa-youtube text-danger me-1"></i>YouTube</label>
                                                <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $settings->social_youtube) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_github" class="form-label"><i class="fab fa-github text-dark me-1"></i>GitHub</label>
                                                <input type="url" class="form-control" id="social_github" name="social_github" value="{{ old('social_github', $settings->social_github) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Tab -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-search me-2"></i>SEO Settings</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="robots_meta" class="form-label">Robots Meta</label>
                                                <select class="form-select" id="robots_meta" name="robots_meta">
                                                    <option value="index, follow" {{ old('robots_meta', $settings->robots_meta) == 'index, follow' ? 'selected' : '' }}>Index, Follow</option>
                                                    <option value="noindex, nofollow" {{ old('robots_meta', $settings->robots_meta) == 'noindex, nofollow' ? 'selected' : '' }}>No Index, No Follow</option>
                                                    <option value="index, nofollow" {{ old('robots_meta', $settings->robots_meta) == 'index, nofollow' ? 'selected' : '' }}>Index, No Follow</option>
                                                    <option value="noindex, follow" {{ old('robots_meta', $settings->robots_meta) == 'noindex, follow' ? 'selected' : '' }}>No Index, Follow</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="canonical_url" class="form-label">Canonical URL</label>
                                                <input type="url" class="form-control" id="canonical_url" name="canonical_url" value="{{ old('canonical_url', $settings->canonical_url) }}">
                                                <div class="form-text">Leave empty to use current URL</div>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3"><i class="fab fa-facebook me-1"></i>Open Graph Settings</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_title" class="form-label">OG Title</label>
                                                <input type="text" class="form-control" id="og_title" name="og_title" value="{{ old('og_title', $settings->og_title) }}">
                                                <div class="form-text">Leave empty to use site title</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_type" class="form-label">OG Type</label>
                                                <select class="form-select" id="og_type" name="og_type">
                                                    <option value="website" {{ old('og_type', $settings->og_type) == 'website' ? 'selected' : '' }}>Website</option>
                                                    <option value="article" {{ old('og_type', $settings->og_type) == 'article' ? 'selected' : '' }}>Article</option>
                                                    <option value="business" {{ old('og_type', $settings->og_type) == 'business' ? 'selected' : '' }}>Business</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_description" class="form-label">OG Description</label>
                                                <textarea class="form-control" id="og_description" name="og_description" rows="3">{{ old('og_description', $settings->og_description) }}</textarea>
                                                <div class="form-text">Leave empty to use meta description</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_url" class="form-label">OG URL</label>
                                                <input type="url" class="form-control" id="og_url" name="og_url" value="{{ old('og_url', $settings->og_url) }}">
                                                <div class="form-text">Leave empty to use current URL</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="og_image" class="form-label">OG Image</label>
                                                <input type="file" class="form-control" id="og_image" name="og_image" accept="image/*">
                                                @if($settings->og_image_url)
                                                    <div class="file-upload-container mt-2">
                                                        <img src="{{ $settings->og_image_url }}" alt="OG Image" class="image-preview">
                                                        <button type="button" class="btn btn-danger btn-sm delete-file-btn" data-field="og_image">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                <div class="form-text">Recommended: 1200x630 pixels, max 5MB</div>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3"><i class="fab fa-twitter me-1"></i>Twitter Settings</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="twitter_card" class="form-label">Twitter Card</label>
                                                <select class="form-select" id="twitter_card" name="twitter_card">
                                                    <option value="summary" {{ old('twitter_card', $settings->twitter_card) == 'summary' ? 'selected' : '' }}>Summary</option>
                                                    <option value="summary_large_image" {{ old('twitter_card', $settings->twitter_card) == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="twitter_site" class="form-label">Twitter Site</label>
                                                <input type="text" class="form-control" id="twitter_site" name="twitter_site" value="{{ old('twitter_site', $settings->twitter_site) }}" placeholder="@yoursite">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="twitter_creator" class="form-label">Twitter Creator</label>
                                                <input type="text" class="form-control" id="twitter_creator" name="twitter_creator" value="{{ old('twitter_creator', $settings->twitter_creator) }}" placeholder="@creator">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Analytics Tab -->
                            <div class="tab-pane fade" id="analytics" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-chart-line me-2"></i>Analytics & Tracking</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                                <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id', $settings->google_analytics_id) }}" placeholder="G-XXXXXXXXXX">
                                                <div class="form-text">Google Analytics 4 Measurement ID</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="google_tag_manager_id" class="form-label">Google Tag Manager ID</label>
                                                <input type="text" class="form-control" id="google_tag_manager_id" name="google_tag_manager_id" value="{{ old('google_tag_manager_id', $settings->google_tag_manager_id) }}" placeholder="GTM-XXXXXXX">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="google_search_console" class="form-label">Google Search Console</label>
                                                <input type="text" class="form-control" id="google_search_console" name="google_search_console" value="{{ old('google_search_console', $settings->google_search_console) }}">
                                                <div class="form-text">Verification meta content</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="facebook_pixel_id" class="form-label">Facebook Pixel ID</label>
                                                <input type="text" class="form-control" id="facebook_pixel_id" name="facebook_pixel_id" value="{{ old('facebook_pixel_id', $settings->facebook_pixel_id) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Tab -->
                            <div class="tab-pane fade" id="advanced" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-code me-2"></i>Custom Scripts & Styles</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="header_scripts" class="form-label">Header Scripts</label>
                                                <textarea class="form-control code-editor" id="header_scripts" name="header_scripts" rows="6">{{ old('header_scripts', $settings->header_scripts) }}</textarea>
                                                <div class="form-text">Scripts to include in &lt;head&gt;</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="footer_scripts" class="form-label">Footer Scripts</label>
                                                <textarea class="form-control code-editor" id="footer_scripts" name="footer_scripts" rows="6">{{ old('footer_scripts', $settings->footer_scripts) }}</textarea>
                                                <div class="form-text">Scripts to include before &lt;/body&gt;</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_css" class="form-label">Custom CSS</label>
                                                <textarea class="form-control code-editor" id="custom_css" name="custom_css" rows="6">{{ old('custom_css', $settings->custom_css) }}</textarea>
                                                <div class="form-text">Custom CSS styles</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_js" class="form-label">Custom JavaScript</label>
                                                <textarea class="form-control code-editor" id="custom_js" name="custom_js" rows="6">{{ old('custom_js', $settings->custom_js) }}</textarea>
                                                <div class="form-text">Custom JavaScript code</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="schema_markup" class="form-label">Schema Markup (JSON-LD)</label>
                                                <textarea class="form-control code-editor" id="schema_markup" name="schema_markup" rows="8">{{ old('schema_markup', $settings->schema_markup) }}</textarea>
                                                <div class="form-text">Custom structured data in JSON-LD format</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Policies Tab -->
                            <div class="tab-pane fade" id="policies" role="tabpanel">
                                <div class="form-section">
                                    <h5><i class="fas fa-gavel me-2"></i>Legal & Policy Pages</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="privacy_policy" class="form-label">Privacy Policy</label>
                                                <textarea class="form-control" id="privacy_policy" name="privacy_policy" rows="10" placeholder="Add your Privacy Policy content here...">{{ old('privacy_policy', $settings->privacy_policy) }}</textarea>
                                                <div class="form-text">Supports HTML; shown at <code>/privacy-policy</code>.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="terms_of_service" class="form-label">Terms of Service</label>
                                                <textarea class="form-control" id="terms_of_service" name="terms_of_service" rows="10" placeholder="Add your Terms of Service content here...">{{ old('terms_of_service', $settings->terms_of_service) }}</textarea>
                                                <div class="form-text">Supports HTML; shown at <code>/terms-of-service</code>.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="cookie_policy" class="form-label">Cookie Policy</label>
                                                <textarea class="form-control" id="cookie_policy" name="cookie_policy" rows="10" placeholder="Add your Cookie Policy content here...">{{ old('cookie_policy', $settings->cookie_policy) }}</textarea>
                                                <div class="form-text">Supports HTML; shown at <code>/cookie-policy</code>.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="disclaimer" class="form-label">Disclaimer</label>
                                                <textarea class="form-control" id="disclaimer" name="disclaimer" rows="10" placeholder="Add your Disclaimer content here...">{{ old('disclaimer', $settings->disclaimer) }}</textarea>
                                                <div class="form-text">Supports HTML; shown at <code>/disclaimer</code>.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Save Button -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="icon-base ti tabler-device-floppy me-2"></i>Save All Settings
                                    </button>
                                    <a href="{{ route('admin.header-footer.preview') }}" target="_blank" class="btn btn-outline-info btn-lg">
                                        <i class="icon-base ti tabler-eye me-2"></i>Preview
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
$(document).ready(function() {
    // File delete functionality
    $('.delete-file-btn').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }
        
        const field = $(this).data('field');
        const container = $(this).closest('.file-upload-container');
        
        $.ajax({
            url: '{{ route("admin.header-footer.delete-file") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                field: field
            },
            success: function(response) {
                if (response.success) {
                    container.fadeOut(300, function() {
                        $(this).remove();
                    });
                    toastr.success('File deleted successfully');
                } else {
                    toastr.error(response.message || 'Error deleting file');
                }
            },
            error: function(xhr) {
                toastr.error('Error deleting file');
            }
        });
    });

    // Image preview for file uploads
    $('input[type="file"]').on('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = $(this).siblings('.file-upload-container').find('.image-preview');
                if (preview.length === 0) {
                    const previewHtml = `
                        <div class="file-upload-container mt-2">
                            <img src="${e.target.result}" alt="Preview" class="image-preview">
                        </div>
                    `;
                    $(this).after(previewHtml);
                } else {
                    preview.attr('src', e.target.result);
                }
            }.bind(this);
            reader.readAsDataURL(file);
        }
    });

    // Tab navigation with URL hash
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (tab) {
            const bsTab = new bootstrap.Tab(tab);
            bsTab.show();
        }
    }

    // Update URL hash when tab changes
    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('data-bs-target');
        if (target) {
            window.location.hash = target;
        }
    });

    // Character counter for text areas
    $('textarea[maxlength]').each(function() {
        const maxLength = $(this).attr('maxlength');
        const $counter = $('<div class="form-text text-muted"></div>');
        $(this).after($counter);
        
        const updateCounter = () => {
            const remaining = maxLength - $(this).val().length;
            $counter.text(`${remaining} characters remaining`);
            
            if (remaining < 10) {
                $counter.addClass('text-danger').removeClass('text-muted');
            } else {
                $counter.addClass('text-muted').removeClass('text-danger');
            }
        };
        
        $(this).on('input', updateCounter);
        updateCounter();
    });

    // Form validation feedback
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...');
    });
});
</script>
@endpush