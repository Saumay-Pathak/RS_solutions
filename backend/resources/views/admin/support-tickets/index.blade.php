@extends('admin.layouts.admin')

@section('title', 'Support Tickets')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header with Stats Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">Support Tickets</h1>
                    <p class="mb-0">Manage customer support tickets and responses</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.support-tickets.export-csv') }}" class="btn btn-outline-success">
                        <i class="ti tabler-download me-1"></i> Export CSV
                    </a>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">
                        <i class="ti tabler-refresh me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4" id="stats-cards">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Open Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="open-count">
                                {{ $stats['open'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="ti tabler-ticket fs-2 text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                In Progress
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="in-progress-count">
                                {{ $stats['in_progress'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="ti tabler-clock fs-2 text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolved-count">
                                {{ $stats['resolved'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="ti tabler-circle-check fs-2 text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="overdue-count">
                                {{ $stats['overdue'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="ti tabler-alert-triangle fs-2 text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.support-tickets.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="priority">Priority</label>
                        <select name="priority" id="priority" class="form-control" onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="category">Category</label>
                        <select name="category" id="category" class="form-control" onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Categories</option>
                            <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                            <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="product" {{ request('category') === 'product' ? 'selected' : '' }}>Product</option>
                            <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Billing</option>
                            <option value="complaint" {{ request('category') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="search">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Ticket ID, Name, Email..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="ti tabler-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Support Tickets 
                @if($tickets->total() > 0)
                    ({{ $tickets->firstItem() }}-{{ $tickets->lastItem() }} of {{ $tickets->total() }})
                @endif
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti tabler-dots-vertical fs-5 text-muted"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Bulk Actions:</div>
                    <a class="dropdown-item" href="#" onclick="bulkAction('mark_in_progress')">Mark as In Progress</a>
                    <a class="dropdown-item" href="#" onclick="bulkAction('mark_resolved')">Mark as Resolved</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="bulkAction('delete')" style="color: #e74a3b;">Delete Selected</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($tickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tickets-table">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Ticket ID</th>
                                <th>Customer</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Age</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr class="{{ $ticket->is_overdue ? 'table-warning' : '' }}">
                                    <td>
                                        <input type="checkbox" class="ticket-select" value="{{ $ticket->_id }}">
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="font-weight-bold text-primary">
                                            {{ $ticket->ticket_id }}
                                        </a>
                                        @if($ticket->is_overdue)
                                            <span class="badge badge-warning badge-sm ml-1">Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $ticket->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $ticket->email }}</small>
                                            <br>
                                            <small class="text-muted">{{ $ticket->phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $ticket->message }}">
                                            {{ Str::limit($ticket->message, 80) }}
                                        </div>
                                        <small class="text-muted">{{ $ticket->full_address }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $ticket->category_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : ($ticket->priority === 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $ticket->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : ($ticket->status === 'in_progress' ? 'info' : 'warning')) }}">
                                            {{ $ticket->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ticket->assigned_to)
                                            <span class="text-sm">{{ $ticket->assignedUser->name ?? 'Unknown User' }}</span>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $ticket->age_in_days }}d</span>
                                        @if($ticket->response_time)
                                            <br><small class="text-muted">Response: {{ number_format($ticket->response_time, 1) }}h</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.support-tickets.show', $ticket) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="ti tabler-eye"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti tabler-settings"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="quickUpdateStatus('{{ $ticket->_id }}', 'in_progress')">
                                                        Mark In Progress
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="quickUpdateStatus('{{ $ticket->_id }}', 'resolved')">
                                                        Mark Resolved
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="assignTicket('{{ $ticket->_id }}')">
                                                        Assign User
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteTicket('{{ $ticket->_id }}')">
                                                        Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        {{ $tickets->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ti tabler-ticket fs-1 text-muted mb-3"></i>
                    <h5 class="text-gray-600">No Support Tickets Found</h5>
                    <p class="text-muted">There are no tickets matching your current filters.</p>
                    @if(request()->hasAny(['status', 'priority', 'category', 'search']))
                        <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-outline-primary">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Ticket Modal -->
<div class="modal fade" id="assignTicketModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignTicketForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assigned_to">Assign to User:</label>
                        <select id="assigned_to" name="assigned_to" class="form-control" required>
                            <option value="">Select User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->_id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentTicketId = null;

// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.ticket-select');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Quick status update
function quickUpdateStatus(ticketId, status) {
    if (!confirm('Are you sure you want to update this ticket status?')) return;
    
    fetch(`/admin/support-tickets/${ticketId}/status`, {
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
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status');
    });
}

// Assign ticket
function assignTicket(ticketId) {
    currentTicketId = ticketId;
    $('#assignTicketModal').modal('show');
}

// Handle assign ticket form submission
document.getElementById('assignTicketForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const assignedTo = document.getElementById('assigned_to').value;
    
    fetch(`/admin/support-tickets/${currentTicketId}/assign`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ assigned_to: assignedTo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#assignTicketModal').modal('hide');
            location.reload();
        } else {
            alert('Error assigning ticket: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the ticket');
    });
});

// Delete ticket
function deleteTicket(ticketId) {
    if (!confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) return;
    
    fetch(`/admin/support-tickets/${ticketId}`, {
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
            alert('Error deleting ticket: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the ticket');
    });
}

// Bulk actions
function bulkAction(action) {
    const selectedTickets = Array.from(document.querySelectorAll('.ticket-select:checked')).map(cb => cb.value);
    
    if (selectedTickets.length === 0) {
        alert('Please select at least one ticket');
        return;
    }
    
    let confirmMessage = '';
    switch(action) {
        case 'mark_in_progress':
            confirmMessage = `Mark ${selectedTickets.length} ticket(s) as In Progress?`;
            break;
        case 'mark_resolved':
            confirmMessage = `Mark ${selectedTickets.length} ticket(s) as Resolved?`;
            break;
        case 'delete':
            confirmMessage = `Delete ${selectedTickets.length} ticket(s)? This action cannot be undone.`;
            break;
        default:
            return;
    }
    
    if (!confirm(confirmMessage)) return;
    
    // Handle bulk actions here based on your implementation
    console.log('Bulk action:', action, 'Tickets:', selectedTickets);
    alert('Bulk action functionality to be implemented');
}

// Auto-refresh stats every 30 seconds
setInterval(function() {
    fetch('/admin/support-tickets/stats/dashboard')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('open-count').textContent = data.data.open || 0;
                document.getElementById('in-progress-count').textContent = data.data.in_progress || 0;
                document.getElementById('resolved-count').textContent = data.data.resolved || 0;
                document.getElementById('overdue-count').textContent = data.data.overdue || 0;
            }
        })
        .catch(error => console.error('Error refreshing stats:', error));
}, 30000);
</script>
@endpush