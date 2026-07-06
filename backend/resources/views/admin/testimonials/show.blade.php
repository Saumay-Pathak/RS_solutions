@extends('layouts.app')

@section('title', 'View Testimonial - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">View Testimonial</h4>
        <p class="mb-0">Testimonial details and information</p>
      </div>
      <div>
        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary me-2">
          <i class="icon-base ti tabler-edit"></i>Edit
        </a>
        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Main Content -->
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Testimonial Details</h5>
          </div>
          <div class="card-body">
            <!-- Customer Info -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Customer Name:</strong>
              </div>
              <div class="col-sm-9">
                {{ $testimonial->name }}
              </div>
            </div>

            @if($testimonial->position)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Position:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $testimonial->position }}
                </div>
              </div>
            @endif

            @if($testimonial->company)
              <div class="row mb-4">
                <div class="col-sm-3">
                  <strong>Company:</strong>
                </div>
                <div class="col-sm-9">
                  {{ $testimonial->company }}
                </div>
              </div>
            @endif

            <!-- Rating -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Rating:</strong>
              </div>
              <div class="col-sm-9">
                <div class="d-flex align-items-center">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="icon-base ti tabler-star{{ $i <= $testimonial->rating ? '-filled text-warning' : ' text-muted' }} me-1"></i>
                  @endfor
                  <span class="ms-2">({{ $testimonial->rating }}/5)</span>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Content:</strong>
              </div>
              <div class="col-sm-9">
                <div class="border-start border-primary ps-3">
                  <em>"{{ $testimonial->content }}"</em>
                </div>
              </div>
            </div>

            <!-- Sort Order -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Sort Order:</strong>
              </div>
              <div class="col-sm-9">
                {{ $testimonial->sort_order }}
              </div>
            </div>

            <!-- Timestamps -->
            <div class="row mb-4">
              <div class="col-sm-3">
                <strong>Created:</strong>
              </div>
              <div class="col-sm-9">
                {{ $testimonial->created_at->format('M d, Y \a\t H:i') }}
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <strong>Updated:</strong>
              </div>
              <div class="col-sm-9">
                {{ $testimonial->updated_at->format('M d, Y \a\t H:i') }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
        <!-- Image -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Customer Photo</h5>
          </div>
          <div class="card-body text-center">
            @if($testimonial->image)
              <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" 
                   class="img-fluid rounded mb-3" style="max-height: 300px;">
            @else
              <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                <div class="text-center">
                  <i class="icon-base ti tabler-photo icon-lg text-muted mb-2"></i>
                  <p class="text-muted mb-0">No photo uploaded</p>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- Status & Options -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Status & Options</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-6">
                <strong>Status:</strong>
                <br>
                <span class="badge bg-{{ $testimonial->status ? 'success' : 'danger' }} mt-1">
                  {{ $testimonial->status ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="col-6">
                <strong>Featured:</strong>
                <br>
                <span class="badge bg-{{ $testimonial->featured ? 'warning' : 'secondary' }} mt-1">
                  {{ $testimonial->featured ? 'Featured' : 'Not Featured' }}
                </span>
              </div>
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
              <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary">
                <i class="icon-base ti tabler-edit"></i>Edit Testimonial
              </a>
              
              <form method="POST" action="{{ route('admin.testimonials.toggle-status', $testimonial) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $testimonial->status ? 'warning' : 'success' }} w-100">
                  <i class="icon-base ti tabler-toggle-{{ $testimonial->status ? 'left' : 'right' }} me-2"></i>
                  {{ $testimonial->status ? 'Deactivate' : 'Activate' }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.testimonials.toggle-featured', $testimonial) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $testimonial->featured ? 'outline-warning' : 'warning' }} w-100">
                  <i class="icon-base ti tabler-star{{ $testimonial->featured ? '' : '-filled' }} me-2"></i>
                  {{ $testimonial->featured ? 'Remove Featured' : 'Make Featured' }}
                </button>
              </form>

              <hr class="my-3">

              <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" 
                    onsubmit="return confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                  <i class="icon-base ti tabler-trash"></i>Delete Testimonial
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection