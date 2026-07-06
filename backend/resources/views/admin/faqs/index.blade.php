@extends('layouts.app')

@section('title', 'FAQs - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">FAQs Management</h4>
        <p class="mb-0">Manage website-wide frequently asked questions</p>
      </div>
      <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus"></i>Add FAQ
      </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search FAQs..." value="{{ request('search') }}">
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
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Filter</button>
              <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-trash me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- FAQs Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All FAQs ({{ $faqs->total() }})</h5>
      </div>

      @if($faqs->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Question</th>
                <th>Answer</th>
                <th>Status</th>
                <th>Sort</th>
                <th>Created</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($faqs as $faq)
                <tr>
                  <td>
                    <div class="text-truncate" style="max-width: 320px;" title="{{ $faq->question }}">
                      {{ $faq->question }}
                    </div>
                  </td>
                  <td>
                    <div class="text-truncate" style="max-width: 380px;" title="{{ $faq->answer }}">
                      {{ $faq->excerpt }}
                    </div>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('admin.faqs.toggle-status', $faq) }}" style="display:inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm btn-{{ $faq->status ? 'success' : 'danger' }} border-0">
                        {{ $faq->status ? 'Active' : 'Inactive' }}
                      </button>
                    </form>
                  </td>
                  <td>
                    <span class="badge bg-label-primary">{{ $faq->sort_order }}</span>
                  </td>
                  <td>
                    <small class="text-muted">{{ $faq->created_at?->format('M d, Y') }}</small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.faqs.show', $faq) }}">
                          <i class="icon-base ti tabler-eye"></i>View
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.faqs.edit', $faq) }}">
                          <i class="icon-base ti tabler-edit"></i>Edit
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')" style="display:inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">
                            <i class="icon-base ti tabler-trash"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $faqs->links() }}
        </div>
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-help display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No FAQs Found</h5>
          <p class="mb-4 text-muted">Create your first frequently asked question</p>
          <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i>Add First FAQ
          </a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('select[name="status"]').forEach(function(select) {
    select.addEventListener('change', function() { this.closest('form').submit(); });
  });
});
</script>
@endpush