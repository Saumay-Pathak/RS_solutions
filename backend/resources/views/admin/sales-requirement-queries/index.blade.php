@extends('layouts.app')

@section('title', 'Sales Requirement Queries - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Sales Requirement Queries</h4>
        <p class="mb-0">Manage submissions from "Send Us Your Requirement" form</p>
      </div>
      <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
          <i class="icon-base ti tabler-download me-2"></i>Export
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ route('admin.sales-requirement-queries.export', ['format' => 'excel']) }}">
            <i class="icon-base ti tabler-file-spreadsheet me-2"></i>Excel
          </a></li>
          <li><a class="dropdown-item" href="{{ route('admin.sales-requirement-queries.export', ['format' => 'csv']) }}">
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
                  <i class="icon-base ti tabler-clipboard-list"></i>
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
                  <i class="icon-base ti tabler-alert-circle"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">New</span>
            <h3 class="card-title mb-2">{{ number_format($newQueries ?? 0) }}</h3>
            <small class="text-warning fw-semibold">
              <i class="icon-base ti tabler-minus"></i> Requires attention
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <div class="avatar-initial bg-info rounded">
                  <i class="icon-base ti tabler-phone-call"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Contacted</span>
            <h3 class="card-title mb-2">{{ number_format($contactedQueries ?? 0) }}</h3>
            <small class="text-info fw-semibold">
              <i class="icon-base ti tabler-clock"></i> Follow-up
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
            <span class="fw-semibold d-block mb-1">Closed</span>
            <h3 class="card-title mb-2">{{ number_format($closedQueries ?? 0) }}</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ti tabler-arrow-up"></i> Completed
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.sales-requirement-queries.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, message" 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
              <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
              <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
              <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
              <option value="">All</option>
              <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
              <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
              <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Requirement Type</label>
            <select name="requirement_type" class="form-select">
              <option value="">All Types</option>
              <option value="face_fingerprint" {{ request('requirement_type') == 'face_fingerprint' ? 'selected' : '' }}>Face + Fingerprint Device</option>
              <option value="face_device" {{ request('requirement_type') == 'face_device' ? 'selected' : '' }}>Face Device</option>
              <option value="aadhar_device" {{ request('requirement_type') == 'aadhar_device' ? 'selected' : '' }}>Aadhar Device</option>
              <option value="fingerprint_device" {{ request('requirement_type') == 'fingerprint_device' ? 'selected' : '' }}>Fingerprint Device</option>
              <option value="router_4g_wifi" {{ request('requirement_type') == 'router_4g_wifi' ? 'selected' : '' }}>4G WiFi Router</option>
              <option value="cameras_4g_wifi" {{ request('requirement_type') == 'cameras_4g_wifi' ? 'selected' : '' }}>4G/WiFi Cameras</option>
              <option value="poe" {{ request('requirement_type') == 'poe' ? 'selected' : '' }}>POE</option>
              <option value="accessories" {{ request('requirement_type') == 'accessories' ? 'selected' : '' }}>Accessories</option>
              <option value="support" {{ request('requirement_type') == 'support' ? 'selected' : '' }}>Support</option>
              <option value="others" {{ request('requirement_type') == 'others' ? 'selected' : '' }}>Others</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Source</label>
            <select name="source" class="form-select">
              <option value="">All Sources</option>
              <option value="general" {{ request('source') == 'general' ? 'selected' : '' }}>General</option>
              <option value="social_media_ad" {{ request('source') == 'social_media_ad' ? 'selected' : '' }}>Social Media Ad</option>
              <option value="others" {{ request('source') == 'others' ? 'selected' : '' }}>Others</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control" value="{{ request('state') }}" placeholder="State">
          </div>
          <div class="col-md-2">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" value="{{ request('country') }}" placeholder="Country">
          </div>
          <div class="col-md-2">
            <label class="form-label">Date From</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Date To</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-search me-1"></i>Filter
              </button>
              <a href="{{ route('admin.sales-requirement-queries.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-x me-1"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Queries Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Sales Requirement Queries ({{ $salesRequirementQueries->total() ?? 0 }})</h5>
      </div>
      
      @if(isset($salesRequirementQueries) && $salesRequirementQueries->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Contact</th>
                <th>Location</th>
                <th>Requirement</th>
                <th>Product</th>
                <th>Source</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($salesRequirementQueries as $query)
                <tr>
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
                          <br><small class="text-muted">{{ ($query->phone_country_code ? $query->phone_country_code.' ' : '') . $query->phone }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span class="fw-medium">{{ $query->state ?? 'N/A' }}</span>
                      <br><small class="text-muted">{{ $query->country ?? 'N/A' }}</small>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-info">{{ ucfirst(str_replace('_',' ', $query->requirement_type ?? 'N/A')) }}</span>
                  </td>
                  <td>
                    <small class="text-muted">{{ $query->product ?? '—' }}</small>
                  </td>
                  <td>
                    <span class="badge bg-label-secondary">{{ ucfirst(str_replace('_',' ', $query->source ?? 'N/A')) }}</span>
                  </td>
                  <td>
                    @php
                      $priority = $query->priority ?? 'low';
                      $priorityColor = $priority == 'high' ? 'danger' : ($priority == 'medium' ? 'warning' : 'success');
                    @endphp
                    <span class="badge bg-{{ $priorityColor }}">{{ ucfirst($priority) }}</span>
                  </td>
                  <td>
                    <span class="badge bg-{{ 
                      $query->status == 'closed' ? 'success' : 
                      ($query->status == 'contacted' ? 'info' : 
                      ($query->status == 'read' ? 'secondary' : 'warning')) 
                    }}">
                      {{ ucfirst($query->status ?? 'new') }}
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">{{ optional($query->created_at)->format('Y-m-d H:i') }}</small>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.sales-requirement-queries.show', $query->_id) }}" class="btn btn-sm btn-outline-primary">
                      <i class="icon-base ti tabler-eye"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted">Showing {{ $salesRequirementQueries->firstItem() }} to {{ $salesRequirementQueries->lastItem() }} of {{ $salesRequirementQueries->total() }} entries</small>
          </div>
          <div>
            {{ $salesRequirementQueries->withQueryString()->links() }}
          </div>
        </div>
      @else
        <div class="p-4">
          <div class="alert alert-info mb-0">
            <i class="icon-base ti tabler-info-circle me-2"></i>No sales requirement queries found.
          </div>
        </div>
      @endif
    </div>

  </div>
</div>
@endsection