@extends('layouts.app')

@section('title', 'Certification Details - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Certification Details</h4>
        <p class="mb-0">View certification information</p>
      </div>
      <div>
        <a href="{{ route('admin.certifications.index') }}" class="btn btn-outline-secondary me-2">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Certifications
        </a>
        <a href="{{ route('admin.certifications.edit', $certification) }}" class="btn btn-primary">
          <i class="icon-base ti tabler-edit me-2"></i>Edit
        </a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-7">
            <h5 class="mb-2">{{ $certification->name }}</h5>
            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $certification->status ? 'success' : 'danger' }}">{{ $certification->status ? 'Active' : 'Inactive' }}</span></p>
            <p class="mb-0"><strong>Sort Order:</strong> {{ $certification->sort_order ?? 0 }}</p>
          </div>
          <div class="col-md-5">
            <label class="form-label">Authority Logo</label>
            <div class="border rounded p-2 text-center mb-3">
              @if($certification->authority_logo)
                <img src="{{ asset('storage/' . $certification->authority_logo) }}" alt="Authority Logo" style="height:80px;width:auto;">
              @else
                <span class="text-muted">No logo uploaded</span>
              @endif
            </div>

            <label class="form-label">Certificate File</label>
            <div>
              @if($certification->certificate_file)
                <a href="{{ asset('storage/' . $certification->certificate_file) }}" target="_blank" class="btn btn-outline-secondary">Open Certificate</a>
              @else
                <span class="text-muted">No file uploaded</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

