@extends('layouts.app')

@section('title', 'Add Certification - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Add New Certification</h4>
        <p class="mb-0">Upload authority logo and certificate file</p>
      </div>
      <a href="{{ route('admin.certifications.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Certifications
      </a>
    </div>

    <form action="{{ route('admin.certifications.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Certification Details</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="name">Certification Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="authority_logo">Authority Logo</label>
                <input type="file" class="form-control @error('authority_logo') is-invalid @enderror" id="authority_logo" name="authority_logo" accept="image/*">
                @error('authority_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Supported: JPG, PNG, WEBP. Max 5 MB.</small>
              </div>

              <div class="mb-4">
                <label class="form-label" for="certificate_file">Certificate File</label>
                <input type="file" class="form-control @error('certificate_file') is-invalid @enderror" id="certificate_file" name="certificate_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                @error('certificate_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Supported: PDF, DOC, DOCX, JPG, PNG, WEBP. Max 20 MB.</small>
              </div>

              <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">Active</label>
              </div>

              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Save Certification</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
