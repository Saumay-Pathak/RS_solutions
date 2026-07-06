@extends('layouts.app')

@section('title', 'Contact Form Queries - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Contact Form Queries</h4>
        <p class="mb-0">Manage customer inquiries and contact requests</p>
      </div>
      <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
          <i class="icon-base ti tabler-download me-2"></i>Export
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ route('admin.contact-queries.export', ['format' => 'excel']) }}">
            <i class="icon-base ti tabler-file-spreadsheet me-2"></i>Excel
          </a></li>
          <li><a class="dropdown-item" href="{{ route('admin.contact-queries.export', ['format' => 'csv']) }}">
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
                  <i class="icon-base ti tabler-mail"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Total Messages</span>
            <h3 class="card-title mb-2">{{ number_format($totalMessages ?? 0) }}</h3>
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
                  <i class="icon-base ti tabler-mail-opened"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Unread</span>
            <h3 class="card-title mb-2">{{ number_format($unreadMessages ?? 0) }}</h3>
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
                <div class="avatar-initial bg-success rounded">
                  <i class="icon-base ti tabler-check"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Replied</span>
            <h3 class="card-title mb-2">{{ number_format($repliedMessages ?? 0) }}</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ti tabler-arrow-up"></i> Responded
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
                  <i class="icon-base ti tabler-calendar"></i>
                </div>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Today's Messages</span>
            <h3 class="card-title mb-2">{{ number_format($todayMessages ?? 0) }}</h3>
            <small class="text-info fw-semibold">
              <i class="icon-base ti tabler-clock"></i> Last 24 hours
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.contact-queries.index') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, subject..." 
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
              <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
              <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
              <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Subject Type</label>
            <select name="subject_type" class="form-select">
              <option value="">All Types</option>
              <option value="general" {{ request('subject_type') == 'general' ? 'selected' : '' }}>General Inquiry</option>
              <option value="support" {{ request('subject_type') == 'support' ? 'selected' : '' }}>Technical Support</option>
              <option value="sales" {{ request('subject_type') == 'sales' ? 'selected' : '' }}>Sales Inquiry</option>
              <option value="partnership" {{ request('subject_type') == 'partnership' ? 'selected' : '' }}>Partnership</option>
              <option value="complaint" {{ request('subject_type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
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
              <a href="{{ route('admin.contact-queries.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-x me-1"></i>Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Contact Queries Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Contact Messages ({{ $contactQueries->total() ?? 0 }})</h5>
        <div class="dropdown">
          <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Bulk Actions
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="bulkAction('read')">
              <i class="icon-base ti tabler-mail-opened me-2"></i>Mark as Read
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="bulkAction('unread')">
              <i class="icon-base ti tabler-mail me-2"></i>Mark as Unread
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="bulkAction('resolved')">
              <i class="icon-base ti tabler-check me-2"></i>Mark as Resolved
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
              <i class="icon-base ti tabler-trash me-2"></i>Delete Selected
            </a></li>
          </ul>
        </div>
      </div>
      
      @if(isset($contactQueries) && $contactQueries->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th width="40">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                  </div>
                </th>
                <th>Contact Details</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Received</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($contactQueries as $query)
                <tr class="{{ $query->status == 'unread' ? 'table-warning' : '' }}">
                  <td>
                    <div class="form-check">
                      <input class="form-check-input query-checkbox" type="checkbox" value="{{ $query->_id }}">
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial bg-label-{{ $query->status == 'unread' ? 'warning' : 'primary' }} rounded-circle">
                          <i class="icon-base ti tabler-{{ $query->status == 'unread' ? 'mail' : 'mail-opened' }}"></i>
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
                      <span class="fw-medium">{{ Str::limit($query->subject ?? 'No Subject', 30) }}</span>
                      <br><small class="text-muted">{{ Str::limit($query->message ?? '', 50) }}</small>
                      @if($query->subject_type)
                        <br><span class="badge bg-label-secondary">{{ ucfirst($query->subject_type) }}</span>
                      @endif
                    </div>
                  </td>
                  <td>
                    @php
                      $priority = $query->priority ?? 'normal';
                      $priorityColor = $priority == 'high' ? 'danger' : ($priority == 'medium' ? 'warning' : 'success');
                    @endphp
                    <span class="badge bg-{{ $priorityColor }}">
                      {{ ucfirst($priority) }}
                    </span>
                  </td>
                  <td>
                    <span class="badge bg-{{ 
                      $query->status == 'unread' ? 'warning' : 
                      ($query->status == 'replied' ? 'success' : 
                      ($query->status == 'resolved' ? 'info' : 'primary')) 
                    }}">
                      {{ ucfirst($query->status ?? 'unread') }}
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
                          <i class="icon-base ti tabler-eye me-2"></i>View Message
                        </a>
                        @if($query->status == 'unread')
                        <a class="dropdown-item" href="#" onclick="updateStatus('{{ $query->_id }}', 'read')">
                          <i class="icon-base ti tabler-mail-opened me-2"></i>Mark as Read
                        </a>
                        @else
                        <a class="dropdown-item" href="#" onclick="updateStatus('{{ $query->_id }}', 'unread')">
                          <i class="icon-base ti tabler-mail me-2"></i>Mark as Unread
                        </a>
                        @endif
                        <a class="dropdown-item text-success" href="#" onclick="replyToMessage('{{ $query->_id }}')">
                          <i class="icon-base ti tabler-reply me-2"></i>Reply
                        </a>
                        <a class="dropdown-item text-info" href="#" onclick="updateStatus('{{ $query->_id }}', 'resolved')">
                          <i class="icon-base ti tabler-check me-2"></i>Mark as Resolved
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
          {{ $contactQueries->links() }}
        </div>
        
      @else
        <div class="card-body text-center py-5">
          <div class="mb-4">
            <i class="icon-base ti tabler-mail-off display-4 text-muted"></i>
          </div>
          <h5 class="mb-3">No Contact Messages Found</h5>
          <p class="mb-4 text-muted">No contact form submissions have been received yet</p>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- View Message Modals -->
@if(isset($contactQueries))
@foreach($contactQueries as $query)
<div class="modal fade" id="viewModal{{ $query->_id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contact Message Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="fw-bold mb-3">Contact Information</h6>
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
              @if($query->company)
              <tr>
                <td class="fw-medium">Company:</td>
                <td>{{ $query->company }}</td>
              </tr>
              @endif
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold mb-3">Message Details</h6>
            <table class="table table-borderless table-sm">
              <tr>
                <td class="fw-medium">Subject:</td>
                <td>{{ $query->subject ?? 'No Subject' }}</td>
              </tr>
              <tr>
                <td class="fw-medium">Type:</td>
                <td><span class="badge bg-secondary">{{ ucfirst($query->subject_type ?? 'general') }}</span></td>
              </tr>
              <tr>
                <td class="fw-medium">Priority:</td>
                <td>
                  @php
                    $priority = $query->priority ?? 'normal';
                    $priorityColor = $priority == 'high' ? 'danger' : ($priority == 'medium' ? 'warning' : 'success');
                  @endphp
                  <span class="badge bg-{{ $priorityColor }}">{{ ucfirst($priority) }}</span>
                </td>
              </tr>
              <tr>
                <td class="fw-medium">Status:</td>
                <td>
                  <span class="badge bg-{{ 
                    $query->status == 'unread' ? 'warning' : 
                    ($query->status == 'replied' ? 'success' : 
                    ($query->status == 'resolved' ? 'info' : 'primary')) 
                  }}">
                    {{ ucfirst($query->status ?? 'unread') }}
                  </span>
                </td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="mt-4">
          <h6 class="fw-bold mb-3">Message</h6>
          <div class="bg-light p-3 rounded">
            {{ $query->message ?? 'No message content' }}
          </div>
        </div>
        
        @if($query->attachments)
        <div class="mt-4">
          <h6 class="fw-bold mb-3">Attachments</h6>
          <div class="d-flex flex-wrap gap-2">
            @foreach($query->attachments as $attachment)
              <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="icon-base ti tabler-paperclip me-1"></i>{{ basename($attachment) }}
              </a>
            @endforeach
          </div>
        </div>
        @endif
        
        <div class="mt-4">
          <h6 class="fw-bold mb-3">Timeline</h6>
          <small class="text-muted">
            Received: {{ $query->created_at ? $query->created_at->format('M d, Y h:i A') : 'N/A' }}
            @if($query->replied_at)
              <br>Replied: {{ $query->replied_at->format('M d, Y h:i A') }}
            @endif
            @if($query->resolved_at)
              <br>Resolved: {{ $query->resolved_at->format('M d, Y h:i A') }}
            @endif
          </small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" onclick="replyToMessage('{{ $query->_id }}')">
          <i class="icon-base ti tabler-reply me-1"></i>Reply
        </button>
        @if($query->status == 'unread')
        <button type="button" class="btn btn-primary" onclick="updateStatus('{{ $query->_id }}', 'read')">
          <i class="icon-base ti tabler-mail-opened me-1"></i>Mark as Read
        </button>
        @endif
        <button type="button" class="btn btn-info" onclick="updateStatus('{{ $query->_id }}', 'resolved')">
          <i class="icon-base ti tabler-check me-1"></i>Mark as Resolved
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endif

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reply to Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="replyForm">
        <div class="modal-body">
          <input type="hidden" id="replyQueryId">
          <div class="mb-3">
            <label class="form-label">To:</label>
            <input type="email" class="form-control" id="replyEmail" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject:</label>
            <input type="text" class="form-control" id="replySubject">
          </div>
          <div class="mb-3">
            <label class="form-label">Message:</label>
            <textarea class="form-control" id="replyMessage" rows="5" placeholder="Type your reply here..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="sendReply()">
            <i class="icon-base ti tabler-send me-1"></i>Send Reply
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
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
    document.querySelectorAll('select[name="status"], select[name="subject_type"], select[name="date_range"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
    
    // Auto-mark as read when viewing modal
    document.querySelectorAll('[data-bs-target^="#viewModal"]').forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-bs-target');
            const queryId = modalId.replace('#viewModal', '');
            
            // Auto-mark as read after a short delay
            setTimeout(() => {
                updateStatus(queryId, 'read', false);
            }, 1000);
        });
    });
});

function updateStatus(queryId, status, showConfirm = true) {
    const proceed = !showConfirm || confirm(`Are you sure you want to mark this message as ${status}?`);
    
    if (proceed) {
        fetch(`/admin/contact-queries/${queryId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && showConfirm) {
                location.reload();
            }
        })
        .catch(error => {
            if (showConfirm) {
                console.error('Error:', error);
                alert('Error updating status');
            }
        });
    }
}

function deleteQuery(queryId) {
    if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        fetch(`/admin/contact-queries/${queryId}`, {
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
                alert('Error deleting message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting message');
        });
    }
}

function replyToMessage(queryId) {
    // Find the query data from the table or modal
    const queryRow = document.querySelector(`input[value="${queryId}"]`).closest('tr');
    const emailCell = queryRow.querySelector('td:nth-child(2) small.text-muted').textContent;
    const subjectCell = queryRow.querySelector('td:nth-child(3) .fw-medium').textContent;
    
    // Populate reply modal
    document.getElementById('replyQueryId').value = queryId;
    document.getElementById('replyEmail').value = emailCell;
    document.getElementById('replySubject').value = `Re: ${subjectCell}`;
    
    // Show reply modal
    const replyModal = new bootstrap.Modal(document.getElementById('replyModal'));
    replyModal.show();
}

function sendReply() {
    const queryId = document.getElementById('replyQueryId').value;
    const subject = document.getElementById('replySubject').value;
    const message = document.getElementById('replyMessage').value;
    
    if (!subject || !message) {
        alert('Please fill in all fields');
        return;
    }
    
    fetch(`/admin/contact-queries/${queryId}/reply`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            subject: subject,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('replyModal')).hide();
            alert('Reply sent successfully');
            location.reload();
        } else {
            alert('Error sending reply');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending reply');
    });
}

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.query-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one message');
        return;
    }
    
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (confirm(`Are you sure you want to ${action} the selected messages?`)) {
        fetch('/admin/contact-queries/bulk-action', {
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