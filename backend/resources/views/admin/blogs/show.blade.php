@extends('layouts.app')

@section('title', 'View Blog Post - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Blog Post</h4>
        <p class="mb-0">Blog post details and information</p>
      </div>
      <div>
        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <!-- Blog Content -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">{{ $blog->title }}</h5>
          </div>
          <div class="card-body">
            <!-- Featured Image -->
            @if($blog->featured_image)
            <div class="mb-4">
              <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" 
                   class="img-fluid rounded" style="width: 100%; max-height: 400px; object-fit: cover; cursor: pointer;"
                   onclick="openImageModal('{{ asset('storage/' . $blog->featured_image) }}')">
            </div>
            @endif

            <!-- Excerpt -->
            @if($blog->excerpt)
            <div class="mb-4">
              <div class="alert alert-info">
                <strong>Excerpt:</strong>
                <p class="mb-0 mt-2">{{ $blog->excerpt }}</p>
              </div>
            </div>
            @endif

            <!-- Content -->
            <div class="blog-content">
              {!! nl2br(e($blog->content)) !!}
            </div>
          </div>
        </div>

        <!-- Blog Details -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Blog Details</h5>
          </div>
          <div class="card-body">
            <!-- Title -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Title:</strong>
              </div>
              <div class="col-sm-9">
                {{ $blog->title }}
              </div>
            </div>

            <!-- Slug -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Slug:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $blog->slug }}</code>
              </div>
            </div>

            <!-- Author -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Author:</strong>
              </div>
              <div class="col-sm-9">
                <div class="d-flex align-items-center">
                  @if($blog->author->profile_image)
                    <img src="{{ asset('storage/' . $blog->author->profile_image) }}" alt="{{ $blog->author->name }}" 
                         class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                  @else
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                      <span class="text-white fw-bold">{{ substr($blog->author->name, 0, 1) }}</span>
                    </div>
                  @endif
                  <div>
                    <strong>{{ $blog->author->name }}</strong>
                    <div class="small text-muted">{{ $blog->author->email }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Category -->
            @if($blog->category)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Category:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-info">{{ $blog->category }}</span>
              </div>
            </div>
            @endif

            <!-- Tags -->
            @if($blog->tags && count($blog->tags) > 0)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Tags:</strong>
              </div>
              <div class="col-sm-9">
                <div class="d-flex flex-wrap gap-1">
                  @foreach($blog->tags as $tag)
                    <span class="badge bg-secondary">{{ $tag }}</span>
                  @endforeach
                </div>
              </div>
            </div>
            @endif

            <!-- Reading Time -->
            @if($blog->reading_time)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Reading Time:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-light text-dark">
                  <i class="icon-base ti tabler-clock me-1"></i>
                  {{ $blog->reading_time }} min{{ $blog->reading_time > 1 ? 's' : '' }} read
                </span>
              </div>
            </div>
            @endif

            <!-- Publishing Info -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Status:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-{{ $blog->isPublished() ? 'success' : ($blog->status ? 'warning' : 'danger') }}">
                  @if($blog->isPublished())
                    Published
                  @elseif($blog->status)
                    Scheduled
                  @else
                    Draft
                  @endif
                </span>
              </div>
            </div>

            @if($blog->published_at)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Published:</strong>
              </div>
              <div class="col-sm-9">
                {{ $blog->published_at->format('M d, Y \\a\\t H:i') }}
                @if($blog->published_at > now())
                  <small class="text-warning">(Scheduled)</small>
                @endif
              </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $blog->created_at ? $blog->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $blog->updated_at ? $blog->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- SEO Information -->
        @if($blog->meta_title || $blog->meta_description)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">SEO Information</h5>
          </div>
          <div class="card-body">
            @if($blog->meta_title)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Title:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $blog->meta_title }}
                </div>
              </div>
            @endif

            @if($blog->meta_description)
              <div class="row">
                <div class="col-sm-3">
                  <strong>Meta Description:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $blog->meta_description }}
                </div>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Publishing Status -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Publishing Status</h5>
          </div>
          <div class="card-body">
            <div class="text-center mb-3">
              @if($blog->isPublished())
                <div class="mb-2">
                  <i class="icon-base ti tabler-check-circle icon-lg text-success"></i>
                </div>
                <h6 class="text-success mb-1">Published</h6>
                <small class="text-muted">This post is live and visible to readers</small>
              @elseif($blog->status && $blog->published_at && $blog->published_at > now())
                <div class="mb-2">
                  <i class="icon-base ti tabler-clock icon-lg text-warning"></i>
                </div>
                <h6 class="text-warning mb-1">Scheduled</h6>
                <small class="text-muted">Will be published on {{ $blog->published_at->format('M d, Y \\a\\t H:i') }}</small>
              @elseif($blog->status)
                <div class="mb-2">
                  <i class="icon-base ti tabler-eye icon-lg text-info"></i>
                </div>
                <h6 class="text-info mb-1">Ready to Publish</h6>
                <small class="text-muted">Post is ready but needs publication date</small>
              @else
                <div class="mb-2">
                  <i class="icon-base ti tabler-edit icon-lg text-muted"></i>
                </div>
                <h6 class="text-muted mb-1">Draft</h6>
                <small class="text-muted">This post is not published yet</small>
              @endif
            </div>
            
            @if($blog->reading_time)
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
              <small class="text-muted">Reading Time:</small>
              <span class="badge bg-light text-dark">{{ $blog->reading_time }} min{{ $blog->reading_time > 1 ? 's' : '' }}</span>
            </div>
            @endif
          </div>
        </div>

        <!-- Featured Image -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Featured Image</h5>
          </div>
          <div class="card-body text-center">
            @if($blog->featured_image)
              <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" 
                   class="img-fluid rounded mb-3" style="max-height: 200px; cursor: pointer;"
                   onclick="openImageModal('{{ asset('storage/' . $blog->featured_image) }}')">
            @else
              <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                <div class="text-center">
                  <i class="icon-base ti tabler-photo icon-lg text-muted mb-2"></i>
                  <p class="text-muted mb-0">No featured image</p>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- Author Info -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Author Information</h5>
          </div>
          <div class="card-body">
            <div class="text-center">
              @if($blog->author->profile_image)
                <img src="{{ asset('storage/' . $blog->author->profile_image) }}" alt="{{ $blog->author->name }}" 
                     class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
              @else
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
                  <span class="text-white fw-bold fs-3">{{ substr($blog->author->name, 0, 1) }}</span>
                </div>
              @endif
              <h6 class="mb-1">{{ $blog->author->name }}</h6>
              <small class="text-muted">{{ $blog->author->email }}</small>
              @if($blog->author->role)
                <div class="mt-2">
                  <span class="badge bg-info">{{ $blog->author->role->display_name ?? 'User' }}</span>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Blog Post
              </a>
              
              <button type="button" class="btn btn-{{ $blog->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $blog->_id }}')">
                <i class="icon-base ti tabler-toggle-{{ $blog->status ? 'left' : 'right' }} me-2"></i>
                {{ $blog->status ? 'Unpublish' : 'Publish' }}
              </button>

              @if(!$blog->isPublished())
              <button type="button" class="btn btn-info" onclick="publishNow('{{ $blog->_id }}')">
                <i class="icon-base ti tabler-send me-2"></i>Publish Now
              </button>
              @endif

              <a href="{{ route('admin.blogs.index', ['author_id' => $blog->author_id]) }}" class="btn btn-outline-info">
                <i class="icon-base ti tabler-user me-2"></i>View Author's Posts
              </a>

              <hr class="my-3">

              <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this blog post? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Blog Post
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">{{ $blog->title }} - Featured Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Blog Image" class="img-fluid">
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openImageModal(imageSrc) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}

function toggleStatus(blogId) {
    if (confirm('Are you sure you want to change the blog post status?')) {
        $.ajax({
            url: `/admin/blogs/${blogId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating blog post status');
                }
            },
            error: function() {
                alert('Error updating blog post status');
            }
        });
    }
}

function publishNow(blogId) {
    if (confirm('Are you sure you want to publish this blog post immediately?')) {
        $.ajax({
            url: `/admin/blogs/${blogId}/publish-now`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error publishing blog post');
                }
            },
            error: function() {
                alert('Error publishing blog post');
            }
        });
    }
}
</script>
@endpush
@endsection