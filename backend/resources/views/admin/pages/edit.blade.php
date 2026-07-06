@extends('layouts.app')

@section('title', 'Edit Page - Admin Panel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Edit Page</h4>
            <p class="mb-0">Update page content and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-info me-2">
                <i class="icon-base ti tabler-eye me-1"></i>View Page
            </a>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i>Back to Pages
            </a>
        </div>
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-12 col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Page Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $page->title) }}" 
                                       placeholder="Enter page title" required>
                                <div id="title-counter" class="form-text">
                                    <span id="title-count">{{ strlen($page->title ?? '') }}</span>/255 characters
                                </div>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $page->slug) }}" 
                                       placeholder="Auto-generated from title" required>
                                <div class="form-text">URL-friendly version of the page title</div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="template" class="form-label">Template</label>
                                <select class="form-select @error('template') is-invalid @enderror" 
                                        id="template" name="template">
                                    @foreach($templates as $template)
                                        <option value="{{ $template }}" {{ old('template', $page->template) == $template ? 'selected' : '' }}>
                                            {{ ucfirst($template) }} Template
                                        </option>
                                    @endforeach
                                </select>
                                @error('template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}" 
                                       min="0" placeholder="0">
                                <div class="form-text">Lower numbers appear first in navigation</div>
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="excerpt" class="form-label">Page Excerpt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                      id="excerpt" name="excerpt" rows="3" 
                                      placeholder="Enter a brief description of the page">{{ old('excerpt', $page->excerpt) }}</textarea>
                            <div id="excerpt-counter" class="form-text">
                                <span id="excerpt-count">{{ strlen($page->excerpt ?? '') }}</span>/500 characters
                            </div>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">Page Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="15" 
                                      placeholder="Enter the main content of your page...">{{ old('content', $page->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dynamic Sections -->
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dynamic Sections</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="add-section">
                            <i class="icon-base ti tabler-plus me-1"></i>Add Section
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Add dynamic sections for flexible page layouts. These sections can be reordered and styled differently.</p>
                        
                        <div id="sections-container">
                            @if($page->sections && count($page->sections) > 0)
                                @foreach($page->sections as $index => $section)
                                    <div class="card mb-3 section-item">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span class="section-title">Section {{ $index + 1 }}: {{ $section['title'] ?? 'Untitled' }}</span>
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
                                                            <option value="hero" {{ ($section['type'] ?? '') == 'hero' ? 'selected' : '' }}>Hero Section</option>
                                                            <option value="content" {{ ($section['type'] ?? '') == 'content' ? 'selected' : '' }}>Content Block</option>
                                                            <option value="features" {{ ($section['type'] ?? '') == 'features' ? 'selected' : '' }}>Features List</option>
                                                            <option value="gallery" {{ ($section['type'] ?? '') == 'gallery' ? 'selected' : '' }}>Image Gallery</option>
                                                            <option value="testimonial" {{ ($section['type'] ?? '') == 'testimonial' ? 'selected' : '' }}>Testimonials</option>
                                                            <option value="contact" {{ ($section['type'] ?? '') == 'contact' ? 'selected' : '' }}>Contact Form</option>
                                                            <option value="custom" {{ ($section['type'] ?? '') == 'custom' ? 'selected' : '' }}>Custom HTML</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Section Title</label>
                                                        <input type="text" name="section_titles[]" class="form-control" 
                                                               placeholder="Enter section title" value="{{ $section['title'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Content</label>
                                                <textarea name="section_contents[]" class="form-control" rows="4" 
                                                          placeholder="Enter section content">{{ $section['content'] ?? '' }}</textarea>
                                            </div>
                                            @if(isset($section['image']))
                                            <div class="mb-3">
                                                <label class="form-label">Section Image URL</label>
                                                <input type="text" name="section_images[]" class="form-control" 
                                                       placeholder="Enter image URL" value="{{ $section['image'] ?? '' }}">
                                            </div>
                                            @endif
                                            @if(isset($section['link']))
                                            <div class="mb-3">
                                                <label class="form-label">Section Link</label>
                                                <input type="text" name="section_links[]" class="form-control" 
                                                       placeholder="Enter link URL" value="{{ $section['link'] ?? '' }}">
                                            </div>
                                            @endif
                                            <input type="hidden" name="section_orders[]" value="{{ $section['order'] ?? ($index + 1) }}" class="section-order">
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="icon-base ti tabler-layout-grid display-4 mb-3"></i>
                                    <p>No sections added yet. Click "Add Section" to create dynamic content blocks.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" 
                                   placeholder="Enter meta title for SEO" maxlength="255">
                            <div id="meta-title-counter" class="form-text">
                                <span id="meta-title-count">{{ strlen($page->meta_title ?? '') }}</span>/255 characters
                            </div>
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      id="meta_description" name="meta_description" rows="3" 
                                      placeholder="Enter meta description for SEO" maxlength="500">{{ old('meta_description', $page->meta_description) }}</textarea>
                            <div id="meta-desc-counter" class="form-text">
                                <span id="meta-desc-count">{{ strlen($page->meta_description ?? '') }}</span>/500 characters
                            </div>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Custom Code -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Custom Code</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="custom_css" class="form-label">Custom CSS</label>
                            <textarea class="form-control @error('custom_css') is-invalid @enderror" 
                                      id="custom_css" name="custom_css" rows="8" 
                                      placeholder="Enter custom CSS code for this page">{{ old('custom_css', $page->custom_css) }}</textarea>
                            <div class="form-text">This CSS will be included only on this page</div>
                            @error('custom_css')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="custom_js" class="form-label">Custom JavaScript</label>
                            <textarea class="form-control @error('custom_js') is-invalid @enderror" 
                                      id="custom_js" name="custom_js" rows="8" 
                                      placeholder="Enter custom JavaScript code for this page">{{ old('custom_js', $page->custom_js) }}</textarea>
                            <div class="form-text">This JavaScript will be included only on this page</div>
                            @error('custom_js')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-12 col-lg-4">
                <!-- Publishing Options -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="status" name="status" 
                                   {{ old('status', $page->status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                <strong>Active Status</strong>
                                <div class="form-text">Make this page visible to visitors</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Featured Image</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($page->featured_image)
                                <img id="image-preview" src="{{ asset('storage/' . $page->featured_image) }}" alt="Featured Image" class="img-fluid rounded border" style="max-height: 200px;">
                            @else
                                <div id="image-placeholder" class="bg-light rounded d-flex align-items-center justify-content-center border" style="height: 200px;">
                                    <div class="text-center">
                                        <i class="icon-base ti tabler-photo icon-lg text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No image selected</p>
                                    </div>
                                </div>
                                <img id="image-preview" src="" alt="Featured Image" class="img-fluid rounded border d-none" style="max-height: 200px;">
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="file" name="featured_image" id="featured_image" class="form-control" accept="image/*">
                            @error('featured_image')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($page->featured_image)
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
                                <i class="icon-base ti tabler-trash me-1"></i>Remove Current
                            </button>
                        </div>
                        @endif
                        <small class="text-muted">Recommended size: 1200x630 pixels</small>
                    </div>
                </div>

                <!-- Template Preview -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="mb-0">Template Preview</h5>
                    </div>
                    <div class="card-body">
                        <div id="template-preview" class="text-center">
                            <div class="template-icon mb-2">
                                <i class="icon-base ti tabler-layout display-4 text-primary"></i>
                            </div>
                            <h6 id="template-name">{{ ucfirst($page->template ?? 'default') }} Template</h6>
                            <p id="template-description" class="text-muted small">Template layout description</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ti tabler-check me-1"></i>Update Page
                            </button>
                            <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-outline-info">
                                <i class="icon-base ti tabler-eye me-1"></i>Preview Page
                            </a>
                            <hr class="my-3">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePageModal">
                                <i class="icon-base ti tabler-trash me-1"></i>Delete Page
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Page Information -->
    <div class="row mt-6">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Page Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Page ID:</strong>
                            <p class="mb-0">{{ $page->_id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Created:</strong>
                            <p class="mb-0">{{ $page->created_at ? $page->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Last Updated:</strong>
                            <p class="mb-0">{{ $page->updated_at ? $page->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Current Template:</strong>
                            <p class="mb-0">{{ ucfirst($page->template ?? 'default') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Page Modal -->
<div class="modal fade" id="deletePageModal" tabindex="-1" aria-labelledby="deletePageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deletePageModalLabel">Delete Page</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the page <strong>{{ $page->title }}</strong>?</p>
        <p class="text-danger">This action cannot be undone and will permanently delete:</p>
        <ul class="text-danger">
          <li>Page content and all sections</li>
          <li>Featured image and media files</li>
          <li>SEO settings and custom code</li>
          <li>All page data and configuration</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete Page</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let sectionCounter = {{ $page->sections ? count($page->sections) : 0 }};

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
        'default': 'tabler-layout',
        'home': 'tabler-home',
        'about': 'tabler-users',
        'contact': 'tabler-mail',
        'services': 'tabler-briefcase',
        'custom': 'tabler-code'
    };

    // Auto-generate slug from title
    $('#title').on('input', function() {
        let title = $(this).val();
        let slug = title.toLowerCase()
                      .replace(/[^\w\s-]/g, '') // Remove special characters
                      .replace(/\s+/g, '-')     // Replace spaces with hyphens
                      .replace(/--+/g, '-');    // Replace multiple hyphens with single
        $('#slug').val(slug);
        
        // Update character counter
        $('#title-count').text(title.length);
    });

    // Template change handler
    $('#template').on('change', function() {
        const template = $(this).val();
        const icon = templateIcons[template] || 'tabler-layout';
        const description = templateDescriptions[template] || 'Custom template layout';
        
        $('#template-preview .template-icon i').attr('class', `icon-base ti ${icon} display-4 text-primary`);
        $('#template-name').text(template.charAt(0).toUpperCase() + template.slice(1) + ' Template');
        $('#template-description').text(description);
    });

    // Initialize template preview
    $('#template').trigger('change');

    // Character counters
    function initCharacterCounters() {
        $('#title').trigger('input');
        
        $('#excerpt').on('input', function() {
            $('#excerpt-count').text($(this).val().length);
        });
        
        $('#meta_title').on('input', function() {
            const count = $(this).val().length;
            $('#meta-title-count').text(count);
            if (count > 255) {
                $('#meta-title-counter').addClass('text-danger');
            } else {
                $('#meta-title-counter').removeClass('text-danger');
            }
        });

        $('#meta_description').on('input', function() {
            const count = $(this).val().length;
            $('#meta-desc-count').text(count);
            if (count > 500) {
                $('#meta-desc-counter').addClass('text-danger');
            } else {
                $('#meta-desc-counter').removeClass('text-danger');
            }
        });
    }

    // Initialize character counters
    initCharacterCounters();

    // Featured image preview
    $('#featured_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('d-none');
                $('#image-placeholder').addClass('d-none');
            }
            reader.readAsDataURL(file);
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
                                    <option value="content">Content Block</option>
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
        
        if ($('#sections-container .section-item').length === 0) {
            $('#sections-container').html(sectionHtml);
        } else {
            $('#sections-container').append(sectionHtml);
        }
    });

    // Remove section
    $(document).on('click', '.remove-section', function() {
        if (confirm('Are you sure you want to remove this section?')) {
            $(this).closest('.section-item').remove();
            updateSectionNumbers();
        }
    });

    // Move section up
    $(document).on('click', '.move-up', function() {
        const section = $(this).closest('.section-item');
        const prevSection = section.prev('.section-item');
        if (prevSection.length) {
            section.insertBefore(prevSection);
            updateSectionNumbers();
        }
    });

    // Move section down
    $(document).on('click', '.move-down', function() {
        const section = $(this).closest('.section-item');
        const nextSection = section.next('.section-item');
        if (nextSection.length) {
            section.insertAfter(nextSection);
            updateSectionNumbers();
        }
    });

    // Update section numbers
    function updateSectionNumbers() {
        $('#sections-container .section-item').each(function(index) {
            $(this).find('.section-title').text(`Section ${index + 1}`);
            $(this).find('.section-order').val(index + 1);
        });
    }

    // Update section title on input change
    $(document).on('input', 'input[name="section_titles[]"]', function() {
        const title = $(this).val();
        const sectionItem = $(this).closest('.section-item');
        const index = sectionItem.index() + 1;
        const displayTitle = title ? `Section ${index}: ${title}` : `Section ${index}`;
        sectionItem.find('.section-title').text(displayTitle);
    });
});

function removeImage() {
    if (confirm('Are you sure you want to remove the current featured image?')) {
        $('#image-preview').addClass('d-none').attr('src', '');
        $('#image-placeholder').removeClass('d-none');
        
        // Add hidden field to indicate image removal
        $('<input>').attr({
            type: 'hidden',
            name: 'remove_image',
            value: '1'
        }).appendTo('form');
        
        // Hide the remove button
        $(event.target).closest('.mb-3').hide();
    }
}
</script>
@endpush
@endsection