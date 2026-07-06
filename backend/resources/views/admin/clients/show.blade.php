@extends('layouts.app')

@section('title', 'Client Details - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Client Details</h4>
        <p class="mb-0">View client information</p>
      </div>
      <div>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary me-2">
          <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Clients
        </a>
        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary">
          <i class="icon-base ti tabler-edit me-2"></i>Edit
        </a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-8">
            <h5 class="mb-2">{{ $client->name }}</h5>
            <p class="mb-1"><strong>Featured:</strong> {{ $client->featured ? 'Yes' : 'No' }}</p>
            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $client->status ? 'success' : 'danger' }}">{{ $client->status ? 'Active' : 'Inactive' }}</span></p>
            <p class="mb-0"><strong>Sort Order:</strong> {{ $client->sort_order ?? 0 }}</p>
          </div>
          <div class="col-md-4">
            <label class="form-label">Client Logo</label>
            <div class="border rounded p-2 text-center">
              @if($client->logo)
                <img src="{{ asset('storage/' . $client->logo) }}" alt="Logo" style="height:80px;width:auto;">
              @else
                <span class="text-muted">No logo uploaded</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

