@extends('layouts.app')

@section('title', 'Partner Registration Queries - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Partner Registration Queries</h4>
        <p class="mb-0">Manage partner registration requests and inquiries</p>
      </div>
      <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
          <i class="icon-base ti tabler-download me-2"></i>Export
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ route('admin.partner-queries.export', ['format' => 'excel']) }}">
            <i class="icon-base ti tabler-file-spreadsheet me-2"></i>Excel
          </a></li>
          <li><a class="dropdown-item" href="{{ route('admin.partner-queries.export', ['format' => 'csv']) }}">
            <i class="icon-base ti tabler-file-text me-2"></i>CSV
          </a></li>
        </ul>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-6">
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <div class="avatar-initial bg-primary rounded">
                  <i class="icon-base ti tabler-users"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Total Queries</span>
            <h3 class="card-title mb-2">{{ number_format($totalQueries ?? 0) }}</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ti tabler-arrow-up"></i> All time
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <div class="avatar-initial bg-warning rounded">
                  <i class="icon-base ti tabler-clock"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Pending</span>
            <h3 class="card-title mb-2">{{ number_format($pendingQueries ?? 0) }}</h3>
            <small class="text-warning fw-semibold">
              <i class="icon-base ti tabler-minus"></i> Awaiting review
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <div class="avatar-initial bg-success rounded">
                  <i class="icon-base ti tabler-check"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Approved</span>
            <h3 class="card-title mb-2">{{ number_format($approvedQueries ?? 0) }}</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ti tabler-arrow-up"></i> Processed
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <div class="avatar-initial bg-danger rounded">
                  <i class="icon-base ti tabler-x"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Rejected</span>
            <h3 class="card-title mb-2">{{ number_format($rejectedQueries ?? 0) }}</h3>
            <small class="text-danger fw-semibold">
              <i class="icon-base ti tabler-arrow-down"></i> Declined
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.partner-queries.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, company..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
              <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
              <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Partner Type</label>
            <select name="partner_type" class="form-select">
              <option value="">All Types</option>
              <option value="distributor" {{ request('partner_type') == 'distributor' ? 'selected' : '' }}>Distributor</option>
              <option value="reseller" {{ request('partner_type') == 'reseller' ? 'selected' : '' }}>Reseller</option>
              <option value="integrator" {{ request('partner_type') == 'integrator' ? 'selected' : '' }}>System Integrator</option>
              <option value="dealer" {{ request('partner_type') == 'dealer' ? 'selected' : '' }}>Dealer</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Date Range</label>
            <select name="date_range" class="form-select">
              <option value="">All Time</option>
              <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
              <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
              <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
              <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-search me-1"></i>Filter
              </button>
              <a href="{{ route('admin.partner-queries.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-x me-1"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Partner Queries Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Partner Queries ({{ $partnerQueries->total() ?? 0 }})</h5>
        <div class="dropdown">
          <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Bulk Actions
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="bulkAction('approve')">
              <i class="icon-base ti tabler-check me-2"></i>Approve Selected
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="bulkAction('reject')">
              <i class="icon-base ti tabler-x me-2"></i>Reject Selected
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="bulkAction('delete')">
              <i class="icon-base ti tabler-trash me-2"></i>Delete Selected
            </a></li>
          </ul>
        </div>
      </div>
      
      @if(isset($partnerQueries) && $partnerQueries->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th width="40">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                  </div>
                </th>
                <th>Partner Details</th>
                <th>Company</th>
                <th>Partner Type</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($partnerQueries as $query)
                <tr>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input query-checkbox" type="checkbox" value="{{ $query->_id }}">
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial bg-label-primary rounded-circle">
                          <i class="icon-base ti tabler-user"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $query->name ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $query->email ?? 'N/A' }}</small>
                        @if($query->phone)
                          <br><small class="text-muted">{{ $query->phone }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span class="fw-medium">{{ $query->company_name ?? 'N/A' }}</span>
                      @if($query->company_website)
                        <br><a href="{{ $query->company_website }}" target="_blank" class="text-primary small">
                          <i class="icon-base ti tabler-external-link me-1"></i>Website
                        </a>
                      @endif
                      @if($query->location)
                        <br><small class="text-muted">{{ $query->location }}</small>
                      @endif
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-info">
                      {{ ucfirst($query->partner_type ?? 'N/A') }}
                    </span>
                  </td>
                  <td>
                    <span class="badge bg-{{ 
                      $query->status == 'approved' ? 'success' : 
                      ($query->status == 'rejected' ? 'danger' : 
                      ($query->status == 'contacted' ? 'info' : 'warning')) 
                    }}">
                      {{ ucfirst($query->status ?? 'pending') }}
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">
                      {{ $query->created_at ? $query->created_at->format('M d, Y') : 'N/A' }}
                      <br>{{ $query->created_at ? $query->created_at->format('h:i A') : '' }}
                    </small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                              data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewModal{{ $query->_id }}">
                          <i class="icon-base ti tabler-eye me-2"></i>View Details
                        </a>
                        @if($query->status == 'pending')
                        <a class="dropdown-item text-success" href="#" onclick="updateStatus('{{ $query->_id }}', 'approved')">
                          <i class="icon-base ti tabler-check me-2"></i>Approve
                        </a>
                        <a class="dropdown-item text-danger" href="#" onclick="updateStatus('{{ $query->_id }}', 'rejected')">
                          <i class="icon-base ti tabler-x me-2"></i>Reject
                        </a>
                        @endif
                        <a class="dropdown-item text-info" href="#" onclick="updateStatus('{{ $query->_id }}', 'contacted')">
                          <i class="icon-base ti tabler-phone me-2"></i>Mark as Contacted
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="deleteQuery('{{ $query->_id }}')">
                          <i class="icon-base ti tabler-trash me-2"></i>Delete
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer">
          {{ $partnerQueries->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-users-off display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Partner Queries Found</h5>
          <p class="mb-4 text-muted">No partner registration requests have been submitted yet</p>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- View Details Modals -->
@if(isset($partnerQueries))
@foreach($partnerQueries as $query)
<div class="modal fade" id="viewModal{{ $query->_id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Partner Registration Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="fw-bold mb-3">Personal Information</h6>
            <table class="table table-borderless table-sm">
              <tr>
                <td class="fw-medium">Name:</td>
                <td>{{ $query->name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="fw-medium">Email:</td>
                <td>{{ $query->email ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="fw-medium">Phone:</td>
                <td>{{ $query->phone ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="fw-medium">Position:</td>
                <td>{{ $query->position ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold mb-3">Company Information</h6>
            <table class="table table-borderless table-sm">
              <tr>
                <td class="fw-medium">Company:</td>
                <td>{{ $query->company_name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="fw-medium">Website:</td>
                <td>
                  @if($query->company_website)
                    <a href="{{ $query->company_website }}" target="_blank">{{ $query->company_website }}</a>
                  @else
                    N/A
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-medium">Location:</td>
                <td>{{ $query->location ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="fw-medium">Partner Type:</td>
                <td><span class="badge bg-primary">{{ ucfirst($query->partner_type ?? 'N/A') }}</span></td>
              </tr>
            </table>
          </div>
        </div>
        
        @if($query->message)
        <div class="mt-4">
          <h6 class="fw-bold mb-3">Message</h6>
          <div class="bg-light p-3 rounded">
            {{ $query->message }}
          </div>
        </div>
        @endif
        
        <div class="mt-4">
          <h6 class="fw-bold mb-3">Status & Timeline</h6>
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-{{ 
              $query->status == 'approved' ? 'success' : 
              ($query->status == 'rejected' ? 'danger' : 
              ($query->status == 'contacted' ? 'info' : 'warning')) 
            }} fs-6">
              {{ ucfirst($query->status ?? 'pending') }}
            </span>
            <small class="text-muted">
              Submitted: {{ $query->created_at ? $query->created_at->format('M d, Y h:i A') : 'N/A' }}
            </small>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        @if($query->status == 'pending')
        <button type="button" class="btn btn-success" onclick="updateStatus('{{ $query->_id }}', 'approved')">
          <i class="icon-base ti tabler-check me-1"></i>Approve
        </button>
        <button type="button" class="btn btn-danger" onclick="updateStatus('{{ $query->_id }}', 'rejected')">
          <i class="icon-base ti tabler-x me-1"></i>Reject
        </button>
        @endif
        <button type="button" class="btn btn-info" onclick="updateStatus('{{ $query->_id }}', 'contacted')">
          <i class="icon-base ti tabler-phone me-1"></i>Mark as Contacted
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const queryCheckboxes = document.querySelectorAll('.query-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            queryCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Auto-submit form when filter dropdowns change
    document.querySelectorAll('select[name="status"], select[name="partner_type"], select[name="date_range"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});

function updateStatus(queryId, status) {
    if (confirm(`Are you sure you want to mark this query as ${status}?`)) {
        fetch(`/admin/partner-queries/${queryId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
    }
}

function deleteQuery(queryId) {
    if (confirm('Are you sure you want to delete this query? This action cannot be undone.')) {
        fetch(`/admin/partner-queries/${queryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting query');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting query');
        });
    }
}

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.query-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one query');
        return;
    }
    
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (confirm(`Are you sure you want to ${action} the selected queries?`)) {
        fetch('/admin/partner-queries/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                query_ids: selectedIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error performing bulk action');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error performing bulk action');
        });
    }
}
</script>
@endpush