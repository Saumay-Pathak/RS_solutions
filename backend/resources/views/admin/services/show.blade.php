@extends('layouts.app')

@section('title', 'Service Details - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Service Details</h4>
        <p class="mb-0">View service information</p>
      </div>
      <div>
        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-primary me-2"><i class="icon-base ti tabler-edit me-2"></i>Edit</a>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary"><i class="icon-base ti tabler-arrow-left me-2"></i>Back</a>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card mb-6">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $service->title }}</h5>
            <span class="badge bg-{{ $service->status ? 'success' : 'secondary' }}">{{ $service->status ? 'Active' : 'Inactive' }}</span>
          </div>
          <div class="card-body">
            @if($service->image)
              <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="img-fluid rounded mb-4" />
            @endif
            <h6>Short Description</h6>
            <p>{{ $service->short_description }}</p>
            <h6>Description</h6>
            <div>{!! nl2br(e($service->description)) !!}</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Meta</h5>
          </div>
          <div class="card-body">
            <div class="mb-3"><span class="text-muted">Slug:</span> {{ $service->slug ?: '—' }}</div>
            <div class="mb-3"><span class="text-muted">Sort Order:</span> {{ $service->sort_order }}</div>
            <div class="mb-3"><span class="text-muted">Created:</span> {{ $service->created_at?->format('Y-m-d H:i') }}</div>
            <div class="mb-3"><span class="text-muted">Updated:</span> {{ $service->updated_at?->format('Y-m-d H:i') }}</div>

            <hr>
            <div class="mb-3"><span class="text-muted">Meta Title:</span> {{ $service->meta_title ?: '—' }}</div>
            <div class="mb-3"><span class="text-muted">Meta Description:</span> {{ $service->meta_description ?: '—' }}</div>
            <div class="mb-3"><span class="text-muted">Meta Keywords:</span> {{ $service->meta_keywords ?: '—' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection