@extends('layouts.app')

@section('title', 'Edit Certification - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Edit Certification</h4>
        <p class="mb-0">Update certification details</p>
      </div>
      <a href="{{ route('admin.certifications.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Certifications
      </a>
    </div>

    <form action="{{ route('admin.certifications.update', $certification) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Certification Details</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="name">Certification Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $certification->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label">Current Authority Logo</label>
                <div class="mb-2">
                  @if($certification->authority_logo)
                    <img src="{{ asset('storage/' . $certification->authority_logo) }}" alt="Logo" style="height:48px;width:auto;" class="rounded border">
                  @else
                    <span class="text-muted">No logo uploaded</span>
                  @endif
                </div>
                <label class="form-label" for="authority_logo">Replace Authority Logo</label>
                <input type="file" class="form-control @error('authority_logo') is-invalid @enderror" id="authority_logo" name="authority_logo" accept="image/*">
                @error('authority_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Supported: JPG, PNG, WEBP. Max 5 MB.</small>
                @if($certification->authority_logo)
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_authority_logo" name="remove_authority_logo" value="1">
                    <label class="form-check-label" for="remove_authority_logo">Remove current logo</label>
                  </div>
                @endif
              </div>

              <div class="mb-4">
                <label class="form-label">Current Certificate</label>
                <div class="mb-2">
                  @if($certification->certificate_file)
                    <a href="{{ asset('storage/' . $certification->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Current Certificate</a>
                  @else
                    <span class="text-muted">No file uploaded</span>
                  @endif
                </div>
                <label class="form-label" for="certificate_file">Replace Certificate</label>
                <input type="file" class="form-control @error('certificate_file') is-invalid @enderror" id="certificate_file" name="certificate_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                @error('certificate_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Supported: PDF, DOC, DOCX, JPG, PNG, WEBP. Max 200 MB.</small>
                @if($certification->certificate_file)
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_certificate_file" name="remove_certificate_file" value="1">
                    <label class="form-check-label" for="remove_certificate_file">Remove current certificate</label>
                  </div>
                @endif
              </div>

              <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $certification->status) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">Active</label>
              </div>

              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $certification->sort_order ?? 0) }}" min="0">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update Certification</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

