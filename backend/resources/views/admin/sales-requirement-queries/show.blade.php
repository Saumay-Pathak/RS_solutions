@extends('layouts.app')

@section('title', 'Sales Requirement Query Details - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Sales Requirement Query Details</h4>
        <p class="mb-0">View submission details and update status</p>
      </div>
      <a href="{{ route('admin.sales-requirement-queries.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to List
      </a>
    </div>

    <div class="row">
      <div class="col-lg-8 col-md-12">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Contact Information</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name</label>
                <div class="form-control-plaintext">{{ $query->name ?? 'N/A' }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <div class="form-control-plaintext">{{ $query->email ?? 'N/A' }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <div class="form-control-plaintext">{{ ($query->phone_country_code ? $query->phone_country_code.' ' : '') . ($query->phone ?? 'N/A') }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Company</label>
                <div class="form-control-plaintext">{{ $query->company ?? 'N/A' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Requirement & Location</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Requirement Type</label>
                <div class="form-control-plaintext">{{ ucfirst(str_replace('_',' ', $query->requirement_type ?? 'N/A')) }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Product</label>
                <div class="form-control-plaintext">{{ $query->product ?? '—' }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Source</label>
                <div class="form-control-plaintext">{{ ucfirst(str_replace('_',' ', $query->source ?? 'N/A')) }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">State</label>
                <div class="form-control-plaintext">{{ $query->state ?? 'N/A' }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Country</label>
                <div class="form-control-plaintext">{{ $query->country ?? 'N/A' }}</div>
              </div>
              <div class="col-12">
                <label class="form-label">Message</label>
                <div class="form-control-plaintext">{{ $query->message ?? '—' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Metadata</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Submitted At</label>
                <div class="form-control-plaintext">{{ optional($query->created_at)->format('Y-m-d H:i') }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">IP Address</label>
                <div class="form-control-plaintext">{{ $query->ip_address ?? 'N/A' }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">UTM Source</label>
                <div class="form-control-plaintext">{{ $query->utm_source ?? '—' }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">UTM Campaign</label>
                <div class="form-control-plaintext">{{ $query->utm_campaign ?? '—' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Admin Actions</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Status</label>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="updateStatus('read')">
                  <i class="icon-base ti tabler-mail-opened"></i> Read
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="updateStatus('contacted')">
                  <i class="icon-base ti tabler-phone"></i> Contacted
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="updateStatus('closed')">
                  <i class="icon-base ti tabler-check"></i> Closed
                </button>
              </div>
              <div class="mt-2">
                <span class="badge bg-{{ 
                  $query->status == 'closed' ? 'success' : 
                  ($query->status == 'contacted' ? 'info' : 
                  ($query->status == 'read' ? 'secondary' : 'warning')) 
                }}">
                  Current: {{ ucfirst($query->status ?? 'new') }}
                </span>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Priority</label>
              @php
                $priority = $query->priority ?? 'low';
                $priorityColor = $priority == 'high' ? 'danger' : ($priority == 'medium' ? 'warning' : 'success');
              @endphp
              <div class="form-control-plaintext">
                <span class="badge bg-{{ $priorityColor }}">{{ ucfirst($priority) }}</span>
              </div>
            </div>

            <div class="mt-4">
              <form method="POST" action="{{ route('admin.sales-requirement-queries.destroy', $query->_id) }}" onsubmit="return confirm('Delete this query?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">
                  <i class="icon-base ti tabler-trash"></i> Delete Query
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  function updateStatus(status) {
    fetch('{{ route('admin.sales-requirement-queries.update-status', $query->_id) }}', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ status })
    }).then(r => r.json()).then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert('Failed to update status');
      }
    }).catch(() => alert('Failed to update status'));
  }
</script>
@endsection