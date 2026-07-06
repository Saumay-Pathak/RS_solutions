@extends('layouts.app')

@section('title', 'Certifications - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Certifications</h4>
        <p class="mb-0">Manage authority logos and certificates</p>
      </div>
      <a href="{{ route('admin.certifications.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i>Add Certification
      </a>
    </div>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Authority Logo</th>
              <th>Certificate</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($certifications as $certification)
              <tr>
                <td>
                  <a href="{{ route('admin.certifications.show', $certification) }}">{{ $certification->name }}</a>
                </td>
                <td>
                  @if($certification->authority_logo)
                    <img src="{{ asset('storage/' . $certification->authority_logo) }}" alt="Logo" style="height:36px;width:auto;" class="rounded border">
                  @else
                    <span class="text-muted">No logo</span>
                  @endif
                </td>
                <td>
                  @if($certification->certificate_file)
                    <a href="{{ asset('storage/' . $certification->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                  @else
                    <span class="text-muted">No file</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $certification->status ? 'success' : 'danger' }}">{{ $certification->status ? 'Active' : 'Inactive' }}</span>
                </td>
                <td class="text-nowrap">
                  <a href="{{ route('admin.certifications.edit', $certification) }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="icon-base ti tabler-edit"></i>
                  </a>
                  <form action="{{ route('admin.certifications.destroy', $certification) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this certification?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="icon-base ti tabler-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">No certifications found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-3">{{ $certifications->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

