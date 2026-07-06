@extends('layouts.app')

@section('title', 'View Hero Slide - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">{{ $heroSlide->title }}</h4>
        <p class="mb-0">View slide details</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.hero-slides.edit', $heroSlide) }}" class="btn btn-primary">
          <i class="icon-base ti tabler-edit me-2"></i>Edit Slide
        </a>
        <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-outline-secondary">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Slides
        </a>
      </div>
    </div>

    <div class="row">
      <!-- Slide Preview -->
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="icon-base ti tabler-slideshow me-2"></i>Slide Preview
            </h5>
          </div>
          <div class="card-body">
            @if($heroSlide->image)
              <div class="position-relative mb-4" style="min-height: 400px; background: {{ $heroSlide->background_color ?? '#000' }};">
                <img src="{{ $heroSlide->image_url }}" alt="{{ $heroSlide->image_alt }}" 
                     class="img-fluid w-100" style="opacity: {{ (100 - ($heroSlide->overlay_opacity ?? 50)) / 100 }};">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-{{ $heroSlide->content_position ?? 'center' }}" 
                     style="color: {{ $heroSlide->text_color ?? '#fff' }}; padding: 2rem;">
                  <div class="text-{{ $heroSlide->content_position ?? 'center' }}" style="max-width: 600px;">
                    <h1>{{ $heroSlide->title }}</h1>
                    @if($heroSlide->subtitle)
                      <p class="lead">{{ $heroSlide->subtitle }}</p>
                    @endif
                    @if($heroSlide->content)
                      <div class="mt-3">{!! $heroSlide->content !!}</div>
                    @endif
                    <div class="mt-4">
                      @if($heroSlide->button_text && $heroSlide->button_link)
                        <a href="{{ $heroSlide->button_link }}" class="btn {{ $heroSlide->button_style ?? 'btn-primary' }}">
                          {{ $heroSlide->button_text }}
                        </a>
                      @endif
                      @if($heroSlide->secondary_button_text && $heroSlide->secondary_button_link)
                        <a href="{{ $heroSlide->secondary_button_link }}" class="btn {{ $heroSlide->secondary_button_style ?? 'btn-secondary' }} ms-2">
                          {{ $heroSlide->secondary_button_text }}
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endif

            @if($heroSlide->content)
              <div class="mt-4">
                <h6>Custom HTML Content:</h6>
                <pre class="bg-light p-3 rounded"><code>{{ $heroSlide->content }}</code></pre>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Slide Details -->
      <div class="col-12 col-lg-4">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="icon-base ti tabler-info-circle me-2"></i>Slide Information
            </h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="text-muted small">Status</label>
              <div>
                @if($heroSlide->is_active)
                  <span class="badge bg-label-success">Active</span>
                @else
                  <span class="badge bg-label-danger">Inactive</span>
                @endif
              </div>
            </div>

            <div class="mb-3">
              <label class="text-muted small">Display Order</label>
              <div><strong>{{ $heroSlide->order }}</strong></div>
            </div>

            <div class="mb-3">
              <label class="text-muted small">Content Position</label>
              <div>
                <span class="badge bg-label-info">
                  <i class="icon-base ti tabler-align-{{ $heroSlide->content_position ?? 'center' }} me-1"></i>
                  {{ ucfirst($heroSlide->content_position ?? 'center') }}
                </span>
              </div>
            </div>

            <div class="mb-3">
              <label class="text-muted small">Animation Type</label>
              <div>{{ ucfirst($heroSlide->animation_type ?? 'fade') }}</div>
            </div>

            @if($heroSlide->auto_play_delay)
              <div class="mb-3">
                <label class="text-muted small">Auto-Play Delay</label>
                <div>{{ $heroSlide->auto_play_delay }}ms ({{ $heroSlide->auto_play_delay / 1000 }}s)</div>
              </div>
            @endif

            @if($heroSlide->overlay_opacity)
              <div class="mb-3">
                <label class="text-muted small">Overlay Opacity</label>
                <div>{{ $heroSlide->overlay_opacity }}%</div>
              </div>
            @endif

            <div class="mb-3">
              <label class="text-muted small">Created</label>
              <div>{{ $heroSlide->created_at->format('M d, Y H:i') }}</div>
            </div>

            <div class="mb-3">
              <label class="text-muted small">Last Updated</label>
              <div>{{ $heroSlide->updated_at->format('M d, Y H:i') }}</div>
            </div>
          </div>
        </div>

        @if($heroSlide->display_from || $heroSlide->display_to)
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="icon-base ti tabler-calendar me-2"></i>Display Schedule
            </h5>
          </div>
          <div class="card-body">
            @if($heroSlide->display_from)
              <div class="mb-3">
                <label class="text-muted small">Start Date</label>
                <div>{{ $heroSlide->display_from->format('M d, Y H:i') }}</div>
              </div>
            @endif

            @if($heroSlide->display_to)
              <div class="mb-3">
                <label class="text-muted small">End Date</label>
                <div>{{ $heroSlide->display_to->format('M d, Y H:i') }}</div>
              </div>
            @endif

            <div class="alert {{ $heroSlide->isDisplayable() ? 'alert-success' : 'alert-warning' }} mb-0">
              @if($heroSlide->isDisplayable())
                <i class="icon-base ti tabler-check me-2"></i>Currently displayable
              @else
                <i class="icon-base ti tabler-alert-circle me-2"></i>Not currently displayable
              @endif
            </div>
          </div>
        </div>
        @endif

        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="icon-base ti tabler-settings me-2"></i>Quick Actions
            </h5>
          </div>
          <div class="card-body">
            <button type="button" class="btn btn-sm btn-outline-{{ $heroSlide->is_active ? 'warning' : 'success' }} w-100 mb-2 toggle-status">
              <i class="icon-base ti tabler-{{ $heroSlide->is_active ? 'x' : 'check' }} me-1"></i>
              {{ $heroSlide->is_active ? 'Deactivate' : 'Activate' }} Slide
            </button>
            
            <form action="{{ route('admin.hero-slides.destroy', $heroSlide) }}" method="POST" class="delete-form">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="ti ti-trash me-1"></i>Delete Slide
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status
    document.querySelector('.toggle-status')?.addEventListener('click', function() {
        fetch('{{ route("admin.hero-slides.toggle-status", $heroSlide) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(error => {
            toastr.error('An error occurred');
        });
    });

    // Delete confirmation
    document.querySelector('.delete-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this slide? This action cannot be undone.')) {
            this.submit();
        }
    });
});
</script>
@endpush
