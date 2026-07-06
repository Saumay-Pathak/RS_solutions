@extends('layouts.app')

@section('title', 'Edit Client - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Client</h4>
        <p class="mb-0">Update client details</p>
      </div>
      <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Clients
      </a>
    </div>

    <form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Client Details</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="name">Client Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $client->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label">Current Logo</label>
                <div class="mb-2">
                  @if($client->logo)
                    <img src="{{ asset('storage/' . $client->logo) }}" alt="Logo" style="height:48px;width:auto;" class="rounded border">
                  @else
                    <span class="text-muted">No logo uploaded</span>
                  @endif
                </div>
                <label class="form-label" for="logo">Replace Logo</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Supported: JPG, PNG, WEBP. Max 5 MB.</small>
                @if($client->logo)
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo" value="1">
                    <label class="form-check-label" for="remove_logo">Remove current logo</label>
                  </div>
                @endif
              </div>

              <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" {{ old('featured', $client->featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="featured">Featured</label>
              </div>

              <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $client->status) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">Active</label>
              </div>

              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $client->sort_order ?? 0) }}" min="0">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update Client</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

