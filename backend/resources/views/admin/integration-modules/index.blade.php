@extends('layouts.app')

@section('title', 'Integration Modules - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">3rd Party Software Integration Modules</h4>
        <p class="mb-0">Manage modules for external integrations</p>
      </div>
      <a href="{{ route('admin.integration-modules.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add Module
      </a>
    </div>

    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.integration-modules.index') }}" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by title or description..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Updated</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($modules as $module)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="{{ $module->cover_image_url }}" alt="Cover" class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
                      <div>
                        <div class="fw-semibold">{{ $module->title }}</div>
                        <small class="text-muted">{{ Str::limit(strip_tags($module->description), 80) }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-{{ $module->status ? 'success' : 'secondary' }}">{{ $module->status ? 'Active' : 'Inactive' }}</span>
                  </td>
                  <td><small class="text-muted">{{ $module->updated_at?->diffForHumans() }}</small></td>
                  <td class="text-end">
                    <div class="btn-group">
                      <a href="{{ route('admin.integration-modules.edit', $module) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="icon-base ti tabler-edit"></i> Edit
                      </a>
                      <form action="{{ route('admin.integration-modules.toggle-status', $module) }}" method="POST" class="ms-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-info">
                          <i class="icon-base ti tabler-power"></i> {{ $module->status ? 'Disable' : 'Enable' }}
                        </button>
                      </form>
                      <form action="{{ route('admin.integration-modules.destroy', $module) }}" method="POST" class="ms-2" onsubmit="return confirm('Delete this module?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="icon-base ti tabler-trash"></i> Delete
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">No modules found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{ $modules->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

