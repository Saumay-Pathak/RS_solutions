@extends('layouts.app')

@section('title', 'Services - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Services</h4>
        <p class="mb-0">Manage your service offerings</p>
      </div>
      <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i>Add Service
      </a>
    </div>

    <form method="GET" class="card mb-6">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title or description">
          </div>
          <div class="col-md-3">
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-3 d-grid">
            <button class="btn btn-outline-secondary" type="submit">
              <i class="icon-base ti tabler-search me-2"></i>Filter
            </button>
          </div>
        </div>
      </div>
    </form>

    <div class="card">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Image</th>
              <th>Status</th>
              <th>Created</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($services as $service)
              <tr>
                <td>
                  <a href="{{ route('admin.services.show', $service) }}" class="fw-medium">{{ $service->title }}</a>
                  <div class="text-muted small">{{ $service->excerpt(80) }}</div>
                </td>
                <td style="width:100px">
                  @if($service->image)
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="img-fluid rounded" />
                  @else
                    <span class="text-muted">No image</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $service->status ? 'success' : 'secondary' }}">{{ $service->status ? 'Active' : 'Inactive' }}</span>
                </td>
                <td>{{ $service->created_at?->format('Y-m-d') }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary"><i class="icon-base ti tabler-edit"></i></a>
                  <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this service?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="icon-base ti tabler-trash"></i></button>
                  </form>
                  <form action="{{ route('admin.services.toggle-status', $service) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-sm btn-outline-secondary">{{ $service->status ? 'Deactivate' : 'Activate' }}</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-5">No services found</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer">
        {{ $services->links() }}
      </div>
    </div>
  </div>
</div>
@endsection