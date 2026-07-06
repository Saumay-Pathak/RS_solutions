@extends('layouts.app')

@section('title', 'View Software - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Software</h4>
        <p class="mb-0">Software details and download information</p>
      </div>
      <div>
        <a href="{{ route('admin.software.edit', $software) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <!-- Software Overview -->
        <div class="card mb-6">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="mb-1">{{ $software->title }}</h5>
                @if($software->one_line_description)
                  <p class="text-muted mb-0">{{ $software->one_line_description }}</p>
                @endif
              </div>
              @if($software->version)
                <span class="badge bg-info">v{{ $software->version }}</span>
              @endif
            </div>
          </div>
          <div class="card-body">
            <!-- Description -->
            @if($software->description)
            <div class="software-content">
              <h6 class="mb-3">Description</h6>
              {!! nl2br(e($software->description)) !!}
            </div>
            @endif
          </div>
        </div>

        <!-- Software Details -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Software Information</h5>
          </div>
          <div class="card-body">
            <!-- Title -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Title:</strong>
              </div>
              <div class="col-sm-9">
                {{ $software->title }}
              </div>
            </div>

            <!-- Slug -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Slug:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $software->slug }}</code>
              </div>
            </div>

            <!-- Category -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Category:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-info">{{ $software->full_category }}</span>
              </div>
            </div>

            <!-- Version -->
            @if($software->version)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Version:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-primary">{{ $software->version }}</span>
              </div>
            </div>
            @endif

            <!-- Developer -->
            @if($software->developer)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Developer:</strong>
              </div>
              <div class="col-sm-9">
                {{ $software->developer }}
              </div>
            </div>
            @endif

            <!-- License -->
            @if($software->license)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>License:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-secondary">{{ $software->license }}</span>
              </div>
            </div>
            @endif

            <!-- Price -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Price:</strong>
              </div>
              <div class="col-sm-9">
                @if($software->is_free)
                  <span class="badge bg-success">Free</span>
                @else
                  <span class="badge bg-warning text-dark">${{ $software->price ?? 'Contact for pricing' }}</span>
                @endif
              </div>
            </div>

            <!-- File Size -->
            @if($software->size)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>File Size:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-light text-dark">{{ $software->file_size_formatted }}</span>
              </div>
            </div>
            @endif

            <!-- Download Count -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Downloads:</strong>
              </div>
              <div class="col-sm-9">
                <span class="badge bg-info">{{ number_format($software->download_count) }} downloads</span>
              </div>
            </div>

            <!-- Release Date -->
            @if($software->released_at)
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Released:</strong>
              </div>
              <div class="col-sm-9">
                {{ $software->released_at->format('M d, Y') }}
                <small class="text-muted">({{ $software->released_at->diffForHumans() }})</small>
              </div>
            </div>
            @endif

            <!-- Sort Order -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Sort Order:</strong>
              </div>
              <div class="col-sm-9">
                {{ $software->sort_order ?? 0 }}
              </div>
            </div>

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $software->created_at ? $software->created_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $software->updated_at ? $software->updated_at->format('M d, Y \\a\\t H:i') : 'N/A' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Download Information -->
        @if($software->hasDownload())
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Download Information</h5>
          </div>
          <div class="card-body">
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Download Type:</strong>
              </div>
              <div class="col-sm-9">
                @if($software->file_type === 'external')
                  <span class="badge bg-info">External Link</span>
                @elseif($software->file_type === 'file')
                  <span class="badge bg-success">Direct Download</span>
                @endif
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Download URL:</strong>
              </div>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="text" class="form-control" value="{{ $software->download_url }}" readonly>
                  <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $software->download_url }}')">
                    <i class="icon-base ti tabler-copy"></i>
                  </button>
                </div>
              </div>
            </div>

            @if($software->file_type === 'file' && $software->file)
            <div class="row">
              <div class="col-sm-3">
                <strong>File Path:</strong>
              </div>
              <div class="col-sm-9">
                <code>{{ $software->file }}</code>
              </div>
            </div>
            @endif
          </div>
        </div>
        @endif

        <!-- System Requirements -->
        @if($software->requirements && count($software->requirements) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">System Requirements</h5>
          </div>
          <div class="card-body">
            <div class="row">
              @foreach($software->requirements as $requirement)
                <div class="col-md-6 mb-3">
                  <div class="d-flex align-items-start">
                    <i class="icon-base ti tabler-cpu text-primary me-2 mt-1"></i>
                    <span>{{ $requirement }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Supported Platforms -->
        @if($software->platforms && count($software->platforms) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Supported Platforms</h5>
          </div>
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
              @foreach($software->platforms as $platform)
                <span class="badge bg-primary">{{ $platform }}</span>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Tags -->
        @if($software->tags && count($software->tags) > 0)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Tags</h5>
          </div>
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
              @foreach($software->tags as $tag)
                <span class="badge bg-secondary">{{ $tag }}</span>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- SEO Information -->
        @if($software->meta_title || $software->meta_description || $software->meta_keywords)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">SEO Information</h5>
          </div>
          <div class="card-body">
            @if($software->meta_title)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Title:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $software->meta_title }}
                </div>
              </div>
            @endif

            @if($software->meta_description)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Meta Description:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $software->meta_description }}
                </div>
              </div>
            @endif

            @if($software->meta_keywords)
              <div class="row">
                <div class="col-sm-3">
                  <strong>Meta Keywords:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $software->meta_keywords }}
                </div>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Status & Options -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Status & Options</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <strong>Status:</strong>
                <br>
                <span class="badge bg-{{ $software->status ? 'success' : 'danger' }} mt-1">
                  {{ $software->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="col-12">
                <strong>Featured:</strong>
                <br>
                <span class="badge bg-{{ $software->featured ? 'warning' : 'secondary' }} mt-1">
                  {{ $software->featured ? 'Featured' : 'Not Featured' }}
                </span>
              </div>
              <div class="col-12">
                <strong>Type:</strong>
                <br>
                <span class="badge bg-{{ $software->is_free ? 'success' : 'warning' }} mt-1">
                  {{ $software->is_free ? 'Free Software' : 'Paid Software' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Download Statistics -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Download Statistics</h5>
          </div>
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="icon-base ti tabler-download icon-xl text-primary"></i>
            </div>
            <h3 class="mb-1">{{ number_format($software->download_count) }}</h3>
            <p class="text-muted mb-0">Total Downloads</p>
            @if($software->released_at)
              <small class="text-muted">Since {{ $software->released_at->format('M Y') }}</small>
            @endif
          </div>
        </div>

        <!-- Software Categories -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Categories</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <strong>Main Category:</strong>
              <div><span class="badge bg-primary">{{ $software->main_category }}</span></div>
            </div>
            @if($software->sub_category)
            <div>
              <strong>Sub Category:</strong>
              <div><span class="badge bg-info">{{ $software->sub_category }}</span></div>
            </div>
            @endif
          </div>
        </div>

        <!-- Software URL -->
        @if($software->slug)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Public URL</h5>
          </div>
          <div class="card-body">
            <div class="input-group">
              <input type="text" class="form-control" value="{{ $software->url }}" readonly>
              <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $software->url }}')">
                <i class="icon-base ti tabler-copy"></i>
              </button>
            </div>
            <small class="text-muted">Public-facing software URL</small>
          </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('admin.software.edit', $software) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Software
              </a>
              
              <button type="button" class="btn btn-{{ $software->status ? 'warning' : 'success' }} w-100"
                      onclick="toggleStatus('{{ $software->_id }}')">
                <i class="icon-base ti tabler-toggle-{{ $software->status ? 'left' : 'right' }} me-2"></i>
                {{ $software->status ? 'Deactivate' : 'Activate' }}
              </button>

              <button type="button" class="btn btn-{{ $software->featured ? 'outline-warning' : 'warning' }} w-100"
                      onclick="toggleFeatured('{{ $software->_id }}')">
                <i class="icon-base ti tabler-star{{ $software->featured ? '' : '-filled' }} me-2"></i>
                {{ $software->featured ? 'Remove Featured' : 'Make Featured' }}
              </button>

              @if($software->hasDownload())
              <a href="{{ $software->download_url }}" target="_blank" class="btn btn-outline-success">
                <i class="icon-base ti tabler-download me-2"></i>Test Download
              </a>
              @endif

              @if($software->slug)
              <a href="{{ $software->url }}" target="_blank" class="btn btn-outline-info">
                <i class="icon-base ti tabler-external-link me-2"></i>View Public Page
              </a>
              @endif

              <hr class="my-3">

              <form method="POST" action="{{ route('admin.software.destroy', $software) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this software? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Software
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('URL copied to clipboard!');
    });
}

function toggleStatus(softwareId) {
    if (confirm('Are you sure you want to change the software status?')) {
        $.ajax({
            url: `/admin/software/${softwareId}/toggle-status`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating software status');
                }
            },
            error: function() {
                alert('Error updating software status');
            }
        });
    }
}

function toggleFeatured(softwareId) {
    if (confirm('Are you sure you want to change the software featured status?')) {
        $.ajax({
            url: `/admin/software/${softwareId}/toggle-featured`,
            type: 'PATCH',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating software featured status');
                }
            },
            error: function() {
                alert('Error updating software featured status');
            }
        });
    }
}
</script>
@endpush
@endsection