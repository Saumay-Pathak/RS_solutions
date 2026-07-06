@extends('layouts.app')

@section('title', 'Galary')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Galary</h4>
        <p class="mb-0">Manage image/video items</p>
      </div>
      <a href="{{ route('admin.galary.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Upload New
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card mb-6">
      <div class="card-body">
        <form method="get" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title or slug" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="1" {{ request('status')==='1'?'selected':'' }}>Active</option>
              <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">
              <i class="icon-base ti tabler-search"></i> Filter
            </button>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <a href="{{ route('admin.galary.index') }}" class="btn btn-outline-secondary w-100">
              <i class="icon-base ti tabler-trash"></i> Clear
            </a>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Galary Items ({{ $items->total() }})</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Title</th>
              <th>Type</th>
              <th>Preview</th>
              <th>Public Link</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
        @forelse($items as $item)
          <tr>
            <td>{{ $item->title }}</td>
            <td><span class="badge bg-info">{{ strtoupper($item->type) }}</span></td>
            <td>
              @if($item->type==='image')
                <img src="{{ $item->file_url }}" alt="{{ $item->title }}" style="height:40px;" />
              @else
                <a href="{{ $item->file_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open Video</a>
              @endif
            </td>
            <td>
              @if($item->file_url)
                <a href="{{ $item->file_url }}" target="_blank">{{ $item->file_url }}</a>
              @else
                <span class="text-muted">No file URL</span>
              @endif
            </td>
            <td>
              @if($item->status)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-secondary">Inactive</span>
              @endif
            </td>
            <td class="d-flex gap-2">
              <form action="{{ route('admin.galary.toggle-status', $item) }}" method="post">
                @csrf
                @method('PATCH')
                <button class="btn btn-sm btn-warning">Toggle Status</button>
              </form>
              <form action="{{ route('admin.galary.destroy', $item) }}" method="post" onsubmit="return confirm('Delete this item?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5">
                <div class="mb-3"><i class="icon-base ti tabler-photo-off display-6 text-muted"></i></div>
                <h6 class="mb-1">No items found</h6>
                <p class="text-muted mb-3">Start by uploading your first image or video</p>
                <a href="{{ route('admin.galary.create') }}" class="btn btn-primary">
                  <i class="icon-base ti tabler-plus"></i> Upload New
                </a>
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        {{ $items->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection