@extends('admin.layouts.admin')

@section('title', 'Pages')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pages Management</h5>
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-plus"></i>Add Page
                </a>
            </div>
            
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('admin.pages.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search pages...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="template" class="form-label">Template</label>
                                <select class="form-select" id="template" name="template">
                                    <option value="">All Templates</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template }}" {{ request('template') == $template ? 'selected' : '' }}>
                                            {{ ucfirst($template) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="nav-item nav-link search-toggler"></i>Search
                                    </button>
                                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                                        <i class="icon-base ti tabler-refresh me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Pages Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title & Slug</th>
                                <th>Template</th>
                                <th>Sections</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                            <tr>
                                <td>
                                    <div style="max-width: 300px;">
                                        <h6 class="mb-1">
                                            {{ $page->title }}
                                            @if(isset($systemPages[$page->slug]))
                                                <span class="badge bg-warning ms-2">System</span>
                                            @endif
                                        </h6>
                                        @if($page->excerpt)
                                            <small class="text-muted">{{ Str::limit($page->excerpt, 60) }}</small>
                                        @endif
                                        <div class="mt-1">
                                            <code class="small">{{ $page->slug }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($page->template ?? 'default') }}</span>
                                </td>
                                <td>
                                    @if($page->sections && count($page->sections) > 0)
                                        <div class="d-flex align-items-center">
                                            <i class="icon-base ti tabler-layout-grid me-2 text-primary"></i>
                                            <span class="badge bg-primary">{{ count($page->sections) }} sections</span>
                                        </div>
                                        <div class="mt-1">
                                            @foreach(array_slice($page->sections, 0, 3) as $section)
                                                <span class="badge bg-outline-secondary me-1">{{ $section['type'] ?? 'content' }}</span>
                                            @endforeach
                                            @if(count($page->sections) > 3)
                                                <span class="badge bg-secondary">+{{ count($page->sections) - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="icon-base ti tabler-layout me-1"></i>Basic content
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $page->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" 
                                               type="checkbox" 
                                               data-id="{{ $page->_id }}"
                                               data-url="{{ route('admin.pages.toggle-status', $page) }}"
                                               {{ $page->status ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $page->updated_at ? $page->updated_at->format('M d, Y') : ($page->created_at ? $page->created_at->format('M d, Y') : 'N/A') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle hide-arrow" 
                                                data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('admin.pages.show', $page) }}">
                                                <i class="icon-base ti tabler-eye"></i>View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.pages.edit', $page) }}">
                                                <i class="icon-base ti tabler-edit"></i>Edit
                                            </a>
                                            @if($page->sections && count($page->sections) > 0)
                                                <div class="dropdown-divider"></div>
                                                <h6 class="dropdown-header">Sections</h6>
                                                <a class="dropdown-item" href="{{ route('admin.pages.edit', $page) }}#sections">
                                                    <i class="icon-base ti tabler-layout-grid me-2"></i>Manage Sections
                                                </a>
                                            @endif
                                            @if(!isset($systemPages[$page->slug]))
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.pages.destroy', $page) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this page?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="icon-base ti tabler-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            @else
                                                <div class="dropdown-divider"></div>
                                                <span class="dropdown-item-text text-muted">
                                                    <i class="icon-base ti tabler-lock me-2"></i>System Page
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="icon-base ti tabler-file-off display-4 text-muted mb-3"></i>
                                        <h6 class="text-muted">No pages found</h6>
                                        <p class="text-muted">Get started by creating your first page.</p>
                                        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                                            <i class="icon-base ti tabler-plus"></i>Add Page
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pages->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge.bg-outline-secondary {
    background-color: transparent !important;
    border: 1px solid #6c757d;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Status toggle functionality
    $('.status-toggle').change(function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    checkbox.prop('checked', !isChecked);
                    toastr.error('Failed to update status');
                }
            },
            error: function() {
                checkbox.prop('checked', !isChecked);
                toastr.error('Failed to update status');
            }
        });
    });
});
</script>
@endpush
@endsection