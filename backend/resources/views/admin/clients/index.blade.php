@extends('layouts.app')

@section('title', 'Our Clients - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Our Clients</h4>
        <p class="mb-0">Manage client names, logos, and featured status</p>
      </div>
      <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i>Add Client
      </a>
    </div>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Logo</th>
              <th>Featured</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($clients as $client)
              <tr>
                <td>
                  <a href="{{ route('admin.clients.show', $client) }}">{{ $client->name }}</a>
                </td>
                <td>
                  @if($client->logo)
                    <img src="{{ asset('storage/' . $client->logo) }}" alt="Logo" style="height:36px;width:auto;" class="rounded border">
                  @else
                    <span class="text-muted">No logo</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $client->featured ? 'info' : 'secondary' }}">{{ $client->featured ? 'Featured' : 'Regular' }}</span>
                </td>
                <td>
                  <span class="badge bg-{{ $client->status ? 'success' : 'danger' }}">{{ $client->status ? 'Active' : 'Inactive' }}</span>
                </td>
                <td class="text-nowrap">
                  <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="icon-base ti tabler-edit"></i>
                  </a>
                  <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this client?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="icon-base ti tabler-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">No clients found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-3">{{ $clients->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

