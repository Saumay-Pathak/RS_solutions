@extends('layouts.app')

@section('title', 'Blogs - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Blog Posts Management</h4>
        <p class="mb-0">Create and manage your blog content</p>
      </div>
      <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Blog Post
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search blog posts..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Published</option>
              <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Draft</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                  {{ $category }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Author</label>
            <select name="author_id" class="form-select">
              <option value="">All Authors</option>
              @foreach($authors as $author)
                <option value="{{ $author->_id }}" {{ request('author_id') == $author->_id ? 'selected' : '' }}>
                  {{ $author->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="nav-item nav-link search-toggler"></i>Filter
              </button>
              <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Blog Posts Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Blog Posts ({{ $blogs->total() }})</h5>
      </div>
      
      @if($blogs->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Article</th>
                <th>Author</th>
                <th>Category</th>
                <th>Reading Time</th>
                <th>Views</th>
                <th>Status</th>
                <th>Published</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($blogs as $blog)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3 overflow-hidden">
                        @if($blog->featured_image)
                          <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                               alt="{{ $blog->title }}" class="rounded w-100 h-100 object-fit-cover">
                        @else
                          <span class="avatar-initial bg-label-warning rounded">
                            <i class="icon-base ti tabler-article"></i>
                          </span>
                        @endif
                      </div>
                      <div>
                        <h6 class="mb-0">{{ Str::limit($blog->title, 30) }}</h6>
                        <small class="text-muted">{{ $blog->slug }}</small>
                        @if($blog->excerpt)
                          <br><small class="text-muted">{{ Str::limit($blog->excerpt, 50) }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($blog->author)
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xs me-2">
                          @if($blog->author->profile_image)
                            <img src="{{ asset('storage/' . $blog->author->profile_image) }}" 
                                 alt="{{ $blog->author->name }}" class="rounded-circle">
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
                      <span class="badge bg-label-info">{{ $blog->category }}</span>
                    @else
                      <span class="text-muted">Uncategorized</span>
                    @endif
                  </td>
                  <td>
                    @if($blog->reading_time)
                      <span class="badge bg-label-dark">{{ $blog->reading_time }} min</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <span class="fw-semibold">{{ number_format($blog->views_count ?? 0) }}</span>
                    <br><small class="text-muted">views</small>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.blogs.toggle-status', $blog) }}" 
                          style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $blog->status ? 'success' : 'danger' }} border-0">
                        {{ $blog->status ? 'Published' : 'Draft' }}
                      </button>
                    </form>
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
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.blogs.show', $blog) }}">
                          <i class="icon-base ti tabler-eye"></i>View Details
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.blogs.edit', $blog) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this blog post?')"
                              style="display: inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">
                            <i class="icon-base ti tabler-trash"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer">
          {{ $blogs->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-article-off display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Blog Posts Found</h5>
          <p class="mb-4 text-muted">Get started by creating your first blog post</p>
          <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First Blog Post
          </a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter dropdowns change
    document.querySelectorAll('select[name="status"], select[name="category"], select[name="author_id"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush