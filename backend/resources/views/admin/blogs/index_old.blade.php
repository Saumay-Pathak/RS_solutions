@extends('admin.layouts.admin')

@section('title', 'Blogs')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Blog Posts Management</h5>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-plus"></i>Add Blog Post
                </a>
            </div>
            
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('admin.blogs.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search blog posts...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="author_id" class="form-label">Author</label>
                                <select class="form-select" id="author_id" name="author_id">
                                    <option value="">All Authors</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->_id }}" {{ request('author_id') == $author->_id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="nav-item nav-link search-toggler"></i>Search
                                    </button>
                                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                                        <i class="icon-base ti tabler-refresh me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Blog Posts Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Featured Image</th>
                                <th>Title & Excerpt</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Tags</th>
                                <th>Reading Time</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blogs as $blog)
                            <tr>
                                <td>
                                    @if($blog->featured_image)
                                        <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                                             alt="{{ $blog->title }}" 
                                             class="rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="icon-base ti tabler-article text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="max-width: 300px;">
                                        <h6 class="mb-1">{{ Str::limit($blog->title, 40) }}</h6>
                                        @if($blog->excerpt)
                                            <small class="text-muted">{{ Str::limit($blog->excerpt, 80) }}</small>
                                        @endif
                                        <div class="mt-1">
                                            <code class="small">{{ $blog->slug }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($blog->author)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                @if($blog->author->profile_image)
                                                    <img src="{{ asset('storage/' . $blog->author->profile_image) }}" 
                                                         alt="{{ $blog->author->name }}" 
                                                         class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($blog->author->name, 0, 2)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-sm">{{ $blog->author->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($blog->category)
                                        <span class="badge bg-info">{{ $blog->category }}</span>
                                    @else
                                        <span class="text-muted">Uncategorized</span>
                                    @endif
                                </td>
                                <td>
                                    @if($blog->tags && count($blog->tags) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($blog->tags, 0, 2) as $tag)
                                                <span class="badge bg-primary">{{ $tag }}</span>
                                            @endforeach
                                            @if(count($blog->tags) > 2)
                                                <span class="badge bg-secondary">+{{ count($blog->tags) - 2 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No tags</span>
                                    @endif
                                </td>
                                <td>
                                    @if($blog->reading_time)
                                        <span class="badge bg-dark">{{ $blog->reading_time }} min</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" 
                                               type="checkbox" 
                                               data-id="{{ $blog->_id }}"
                                               data-url="{{ route('admin.blogs.toggle-status', $blog) }}"
                                               {{ $blog->status ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    @if($blog->published_at)
                                        <small class="text-muted">
                                            {{ $blog->published_at->format('M d, Y') }}
                                        </small>
                                    @else
                                        <span class="text-muted">Not published</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle hide-arrow" 
                                                data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('admin.blogs.show', $blog) }}">
                                                <i class="icon-base ti tabler-eye"></i>View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.blogs.edit', $blog) }}">
                                                <i class="icon-base ti tabler-edit"></i>Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.blogs.destroy', $blog) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this blog post?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="icon-base ti tabler-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="icon-base ti tabler-article-off display-4 text-muted mb-3"></i>
                                        <h6 class="text-muted">No blog posts found</h6>
                                        <p class="text-muted">Get started by creating your first blog post.</p>
                                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                                            <i class="icon-base ti tabler-plus"></i>Add Blog Post
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($blogs->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $blogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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