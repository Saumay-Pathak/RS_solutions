@extends('admin.layouts.admin')

@section('title', 'Add New Page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Page</h5>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                    <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Pages
                </a>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.pages.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                       id="title" name="title" value="{{ old('title') }}" 
                                                       placeholder="Enter page title" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                       id="slug" name="slug" value="{{ old('slug') }}" 
                                                       placeholder="Auto-generated from title" required>
                                                <div class="form-text">URL-friendly version of the page title</div>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="template" class="form-label">Template</label>
                                                <select class="form-select @error('template') is-invalid @enderror" 
                                                        id="template" name="template">
                                                    @foreach($templates as $template)
                                                        <option value="{{ $template }}" {{ old('template', 'default') == $template ? 'selected' : '' }}>
                                                            {{ ucfirst($template) }} Template
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('template')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sort_order" class="form-label">Sort Order</label>
                                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                                       min="0" placeholder="0">
                                                <div class="form-text">Lower numbers appear first in navigation</div>
                                                @error('sort_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Page Excerpt</label>
                                        <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                                  id="excerpt" name="excerpt" rows="3" 
                                                  placeholder="Enter a brief description of the page">{{ old('excerpt') }}</textarea>
                                        <div class="form-text">Brief summary that appears in page listings and search results</div>
                                        @error('excerpt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="content" class="form-label">Page Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  id="content" name="content" rows="15" 
                                                  placeholder="Enter the main content of your page..." required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Sections -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Dynamic Sections</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="add-section">
                                        <i class="icon-base ti tabler-plus"></i>Add Section
                                    </button>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">Add dynamic sections for flexible page layouts. These sections can be reordered and styled differently.</p>
                                    
                                    <div id="sections-container">
                                        @if(old('section_types'))
                                            @foreach(old('section_types') as $index => $type)
                                                <div class="card mb-3 section-item">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <span class="section-title">Section {{ $index + 1 }}</span>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-secondary move-up" title="Move Up">
                                                                <i class="icon-base ti tabler-arrow-up"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary move-down" title="Move Down">
                                                                <i class="icon-base ti tabler-arrow-down"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger remove-section" title="Remove Section">
                                                                <i class="icon-base ti tabler-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Section Type</label>
                                                                    <select name="section_types[]" class="form-select section-type">
                                                                        <option value="hero" {{ $type == 'hero' ? 'selected' : '' }}>Hero Section</option>
                                                                        <option value="content" {{ $type == 'content' ? 'selected' : '' }}>Content Block</option>
                                                                        <option value="features" {{ $type == 'features' ? 'selected' : '' }}>Features List</option>
                                                                        <option value="gallery" {{ $type == 'gallery' ? 'selected' : '' }}>Image Gallery</option>
                                                                        <option value="testimonial" {{ $type == 'testimonial' ? 'selected' : '' }}>Testimonials</option>
                                                                        <option value="contact" {{ $type == 'contact' ? 'selected' : '' }}>Contact Form</option>
                                                                        <option value="custom" {{ $type == 'custom' ? 'selected' : '' }}>Custom HTML</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Section Title</label>
                                                                    <input type="text" name="section_titles[]" class="form-control" 
                                                                           placeholder="Enter section title" value="{{ old('section_titles')[$index] ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Section Content</label>
                                                            <textarea name="section_contents[]" class="form-control" rows="4" 
                                                                      placeholder="Enter section content">{{ old('section_contents')[$index] ?? '' }}</textarea>
                                                        </div>
                                                        <input type="hidden" name="section_orders[]" value="{{ $index + 1 }}" class="section-order">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Default empty section -->
                                            <div class="text-center text-muted py-4">
                                                <i class="icon-base ti tabler-layout-grid display-4 mb-3"></i>
                                                <p>No sections added yet. Click "Add Section" to create dynamic content blocks.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">SEO Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                               id="meta_title" name="meta_title" value="{{ old('meta_title') }}" 
                                               placeholder="Enter meta title for SEO" maxlength="255">
                                        <div class="form-text">
                                            <span id="meta-title-count">0</span>/255 characters
                                        </div>
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                  id="meta_description" name="meta_description" rows="3" 
                                                  placeholder="Enter meta description for SEO" maxlength="500">{{ old('meta_description') }}</textarea>
                                        <div class="form-text">
                                            <span id="meta-desc-count">0</span>/500 characters
                                        </div>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings & Actions -->
                        <div class="col-md-4">
                            <!-- Publishing Options -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Publishing Options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                               {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            <strong>Active Status</strong>
                                            <div class="form-text">Make this page visible to visitors</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Page Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Page Type</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="page_type" id="static_page" value="static" checked>
                                            <label class="form-check-label" for="static_page">
                                                Static Page
                                                <div class="form-text">Regular content page</div>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="page_type" id="dynamic_page" value="dynamic">
                                            <label class="form-check-label" for="dynamic_page">
                                                Dynamic Page
                                                <div class="form-text">Page with dynamic sections</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Preview -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Template Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div id="template-preview" class="text-center">
                                        <div class="template-icon mb-2">
                                            <i class="icon-base ti tabler-layout display-4 text-primary"></i>
                                        </div>
                                        <h6 id="template-name">Default Template</h6>
                                        <p id="template-description" class="text-muted small">Standard page layout with header, content, and footer</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-base ti tabler-check me-1"></i>Create Page
                                        </button>
                                        <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary">
                                            <i class="icon-base ti tabler-file me-1"></i>Save as Draft
                                        </button>
                                        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                                            <i class="icon-base ti tabler-x me-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let sectionCounter = 0;

    // Template descriptions
    const templateDescriptions = {
        'default': 'Standard page layout with header, content, and footer',
        'home': 'Homepage layout with hero section and featured content',
        'about': 'About page layout with team and company information',
        'contact': 'Contact page layout with form and location details',
        'services': 'Services page layout with service listings',
        'custom': 'Custom layout for unique page designs'
    };

    // Template icons
    const templateIcons = {
        'default': 'ti-layout',
        'home': 'ti-home',
        'about': 'ti-users',
        'contact': 'ti-mail',
        'services': 'ti-briefcase',
        'custom': 'ti-code'
    };

    // Auto-generate slug from title
    $('#title').on('input', function() {
        if ($('#slug').val() === '') {
            let title = $(this).val();
            let slug = title.toLowerCase()
                          .replace(/[^\w\s-]/g, '') // Remove special characters
                          .replace(/\s+/g, '-')     // Replace spaces with hyphens
                          .replace(/--+/g, '-');    // Replace multiple hyphens with single
            $('#slug').val(slug);
        }
    });

    // Template change handler
    $('#template').on('change', function() {
        const template = $(this).val();
        const icon = templateIcons[template] || 'ti-layout';
        const description = templateDescriptions[template] || 'Custom template layout';
        
        $('#template-preview .template-icon i').attr('class', `ti ${icon} display-4 text-primary`);
        $('#template-name').text(template.charAt(0).toUpperCase() + template.slice(1) + ' Template');
        $('#template-description').text(description);
    });

    // Character counters
    $('#meta_title').on('input', function() {
        const count = $(this).val().length;
        $('#meta-title-count').text(count);
        if (count > 255) {
            $('#meta-title-count').parent().addClass('text-danger');
        } else {
            $('#meta-title-count').parent().removeClass('text-danger');
        }
    });

    $('#meta_description').on('input', function() {
        const count = $(this).val().length;
        $('#meta-desc-count').text(count);
        if (count > 500) {
            $('#meta-desc-count').parent().addClass('text-danger');
        } else {
            $('#meta-desc-count').parent().removeClass('text-danger');
        }
    });

    // Add new section
    $('#add-section').on('click', function() {
        sectionCounter++;
        const sectionHtml = `
            <div class="card mb-3 section-item">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="section-title">Section ${sectionCounter}</span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary move-up" title="Move Up">
                            <i class="icon-base ti tabler-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary move-down" title="Move Down">
                            <i class="icon-base ti tabler-arrow-down"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger remove-section" title="Remove Section">
                            <i class="icon-base ti tabler-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Section Type</label>
                                <select name="section_types[]" class="form-select section-type">
                                    <option value="hero">Hero Section</option>
                                    <option value="content" selected>Content Block</option>
                                    <option value="features">Features List</option>
                                    <option value="gallery">Image Gallery</option>
                                    <option value="testimonial">Testimonials</option>
                                    <option value="contact">Contact Form</option>
                                    <option value="custom">Custom HTML</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Section Title</label>
                                <input type="text" name="section_titles[]" class="form-control" placeholder="Enter section title">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section Content</label>
                        <textarea name="section_contents[]" class="form-control" rows="4" placeholder="Enter section content"></textarea>
                    </div>
                    <input type="hidden" name="section_orders[]" value="${sectionCounter}" class="section-order">
                </div>
            </div>
        `;
        
        // Remove empty state message if it exists
        $('#sections-container .text-center').remove();
        $('#sections-container').append(sectionHtml);
        
        // Set page type to dynamic when adding sections
        $('#dynamic_page').prop('checked', true);
    });

    // Remove section
    $(document).on('click', '.remove-section', function() {
        $(this).closest('.section-item').remove();
        updateSectionNumbers();
        
        // Show empty state if no sections
        if ($('.section-item').length === 0) {
            $('#sections-container').html(`
                <div class="text-center text-muted py-4">
                    <i class="icon-base ti tabler-layout-grid display-4 mb-3"></i>
                    <p>No sections added yet. Click "Add Section" to create dynamic content blocks.</p>
                </div>
            `);
        }
    });

    // Move section up
    $(document).on('click', '.move-up', function() {
        const section = $(this).closest('.section-item');
        const prev = section.prev('.section-item');
        if (prev.length) {
            section.insertBefore(prev);
            updateSectionNumbers();
        }
    });

    // Move section down
    $(document).on('click', '.move-down', function() {
        const section = $(this).closest('.section-item');
        const next = section.next('.section-item');
        if (next.length) {
            section.insertAfter(next);
            updateSectionNumbers();
        }
    });

    // Update section numbers and orders
    function updateSectionNumbers() {
        $('.section-item').each(function(index) {
            $(this).find('.section-title').text('Section ' + (index + 1));
            $(this).find('.section-order').val(index + 1);
        });
    }

    // Auto-fill meta title and description if empty
    $('#title, #excerpt').on('blur', function() {
        if ($('#meta_title').val() === '' && $('#title').val() !== '') {
            $('#meta_title').val($('#title').val()).trigger('input');
        }
        
        if ($('#meta_description').val() === '' && $('#excerpt').val() !== '') {
            $('#meta_description').val($('#excerpt').val()).trigger('input');
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        if ($('#title').val().trim() === '') {
            isValid = false;
            toastr.error('Page title is required');
        }
        
        if ($('#slug').val().trim() === '') {
            isValid = false;
            toastr.error('Page slug is required');
        }
        
        if ($('#content').val().trim() === '') {
            isValid = false;
            toastr.error('Page content is required');
        }
        
        // Check meta title length
        if ($('#meta_title').val().length > 255) {
            isValid = false;
            toastr.error('Meta title must be 255 characters or less');
        }
        
        // Check meta description length
        if ($('#meta_description').val().length > 500) {
            isValid = false;
            toastr.error('Meta description must be 500 characters or less');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });

    // Initialize character counters on page load
    $('#meta_title').trigger('input');
    $('#meta_description').trigger('input');
    
    // Initialize section counter based on existing sections
    sectionCounter = $('.section-item').length;
});
</script>
@endpush
@endsection