@extends('admin.layouts.admin')

@section('title', 'Support Ticket - ' . $ticket->ticket_id)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">Support Tickets</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_id }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 text-gray-800">Ticket #{{ $ticket->ticket_id }}</h1>
                    <p class="mb-0">
                        <span class="badge badge-{{ $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : ($ticket->status === 'in_progress' ? 'info' : 'warning')) }}">
                            {{ $ticket->status_label }}
                        </span>
                        <span class="badge badge-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : ($ticket->priority === 'medium' ? 'info' : 'secondary')) }} ml-1">
                            {{ $ticket->priority_label }}
                        </span>
                        <span class="badge badge-secondary ml-1">{{ $ticket->category_label }}</span>
                        @if($ticket->is_overdue)
                            <span class="badge badge-warning ml-1">Overdue</span>
                        @endif
                    </p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="ti tabler-printer me-1"></i> Print
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                             @if($ticket->status !== 'in_progress')
                                <a class="dropdown-item" href="#" onclick="updateTicketStatus('in_progress')">
                                    <i class="ti tabler-play me-1"></i> Mark In Progress
                                </a>
                            @endif
                            @if($ticket->status !== 'resolved')
                                <a class="dropdown-item" href="#" onclick="updateTicketStatus('resolved')">
                                    <i class="ti tabler-circle-check me-1"></i> Mark Resolved
                                </a>
                            @endif
                            @if($ticket->status !== 'closed')
                                <a class="dropdown-item" href="#" onclick="updateTicketStatus('closed')">
                                    <i class="ti tabler-circle-x me-1"></i> Close Ticket
                                </a>
                            @endif
                             <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="$('#assignModal').modal('show')">
                                <i class="ti tabler-user-plus me-1"></i> Assign User
                            </a>
                            <a class="dropdown-item" href="#" onclick="$('#updatePriorityModal').modal('show')">
                                <i class="ti tabler-flag me-1"></i> Change Priority
                            </a>
                            <a class="dropdown-item" href="#" onclick="$('#updateCategoryModal').modal('show')">
                                <i class="ti tabler-tag me-1"></i> Change Category
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" onclick="deleteTicket()">
                                <i class="ti tabler-trash me-1"></i> Delete Ticket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Ticket Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Customer Information</strong></h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $ticket->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        <a href="mailto:{{ $ticket->email }}">{{ $ticket->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>
                                        <a href="tel:{{ $ticket->phone }}">{{ $ticket->phone }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $ticket->full_address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Ticket Information</strong></h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $ticket->created_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td>{{ $ticket->age_in_days }} days</td>
                                </tr>
                                <tr>
                                    <td><strong>Source:</strong></td>
                                    <td>{{ ucfirst($ticket->source ?? 'manual') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Assigned To:</strong></td>
                                    <td>
                                        @if($ticket->assigned_to)
                                            {{ $ticket->assignedUser->name ?? 'Unknown User' }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($ticket->response_time)
                                    <tr>
                                        <td><strong>Response Time:</strong></td>
                                        <td>{{ number_format($ticket->response_time, 1) }} hours</td>
                                    </tr>
                                @endif
                                @if($ticket->resolution_time)
                                    <tr>
                                        <td><strong>Resolution Time:</strong></td>
                                        <td>{{ number_format($ticket->resolution_time, 1) }} hours</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6><strong>Message</strong></h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $ticket->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Response & Notes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Communication & Notes</h6>
                </div>
                <div class="card-body">
                    @if($ticket->response)
                        <div class="alert alert-info mb-4">
                            <h6><strong>Official Response</strong></h6>
                            <p class="mb-1">{{ $ticket->response }}</p>
                            @if($ticket->response_at)
                                <small class="text-muted">
                                    Responded on {{ $ticket->response_at->format('M j, Y g:i A') }}
                                    @if($ticket->response_by)
                                        by {{ $ticket->responseUser->name ?? 'Unknown User' }}
                                    @endif
                                </small>
                            @endif
                        </div>
                    @endif

                    <!-- Add Response Form -->
                    @if(!$ticket->response || $ticket->status !== 'closed')
                        <form id="responseForm" class="mb-4">
                            <div class="form-group">
                                <label for="response">Add/Update Response</label>
                                <textarea name="response" id="response" class="form-control" rows="4" 
                                          placeholder="Enter your response to the customer...">{{ $ticket->response }}</textarea>
                            </div>
                             <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-reply me-1"></i> {{ $ticket->response ? 'Update' : 'Add' }} Response
                             </button>
                        </form>
                    @endif

                    <!-- Notes Section -->
                    <hr>
                    <h6><strong>Internal Notes</strong></h6>
                    
                    @if($ticket->notes && count($ticket->notes) > 0)
                        <div class="mb-3">
                            @foreach($ticket->notes as $note)
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <p class="mb-1">{{ $note['note'] }}</p>
                                        <small class="text-muted">
                                            By {{ $note['added_by_name'] ?? 'Unknown' }} on 
                                            {{ \Carbon\Carbon::parse($note['created_at'])->format('M j, Y g:i A') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Note Form -->
                    <form id="noteForm">
                        <div class="form-group">
                            <label for="note">Add Internal Note</label>
                            <textarea name="note" id="note" class="form-control" rows="2" 
                                      placeholder="Add an internal note (not visible to customer)..."></textarea>
                        </div>
                         <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="ti tabler-file-text me-1"></i> Add Note
                         </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                     <div class="btn-group-vertical w-100">
                        @if($ticket->status === 'open')
                            <button class="btn btn-info mb-2" onclick="updateTicketStatus('in_progress')">
                                <i class="ti tabler-play me-1"></i> Start Working
                            </button>
                        @endif
                        
                        @if($ticket->status !== 'resolved')
                            <button class="btn btn-success mb-2" onclick="updateTicketStatus('resolved')">
                                <i class="ti tabler-circle-check me-1"></i> Mark Resolved
                            </button>
                        @endif
                        
                        @if($ticket->status === 'resolved')
                            <button class="btn btn-secondary mb-2" onclick="updateTicketStatus('closed')">
                                <i class="ti tabler-circle-x me-1"></i> Close Ticket
                            </button>
                        @endif
                        
                        <button class="btn btn-outline-primary mb-2" onclick="$('#assignModal').modal('show')">
                            <i class="ti tabler-user-plus me-1"></i> 
                            {{ $ticket->assigned_to ? 'Reassign' : 'Assign' }}
                        </button>
                        
                        <a href="mailto:{{ $ticket->email }}" class="btn btn-outline-secondary mb-2">
                            <i class="ti tabler-mail me-1"></i> Send Email
                        </a>
                        
                        <a href="tel:{{ $ticket->phone }}" class="btn btn-outline-secondary">
                            <i class="ti tabler-phone me-1"></i> Call Customer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-right">
                            <div class="h5 mb-0 font-weight-bold text-info">{{ $ticket->age_in_days }}</div>
                            <small class="text-muted">Days Old</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold text-warning">
                                @if($ticket->response_time)
                                    {{ number_format($ticket->response_time, 1) }}h
                                @else
                                    -
                                @endif
                            </div>
                            <small class="text-muted">Response Time</small>
                        </div>
                    </div>
                    @if($ticket->resolution_time)
                        <hr>
                        <div class="text-center">
                            <div class="h5 mb-0 font-weight-bold text-success">
                                {{ number_format($ticket->resolution_time, 1) }}h
                            </div>
                            <small class="text-muted">Resolution Time</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Activity Log</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ $ticket->created_at->format('M j, Y g:i A') }}</small>
                                <p class="mb-0">Ticket created</p>
                            </div>
                        </div>
                        
                        @if($ticket->first_response_at)
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $ticket->first_response_at->format('M j, Y g:i A') }}</small>
                                    <p class="mb-0">First response added</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($ticket->resolved_at)
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $ticket->resolved_at->format('M j, Y g:i A') }}</small>
                                    <p class="mb-0">Ticket resolved</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($ticket->closed_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $ticket->closed_at->format('M j, Y g:i A') }}</small>
                                    <p class="mb-0">Ticket closed</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assigned_to">Assign to User:</label>
                        <select id="assigned_to" name="assigned_to" class="form-control" required>
                            <option value="">Select User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->_id }}" {{ $ticket->assigned_to === $user->_id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Priority Modal -->
<div class="modal fade" id="updatePriorityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Priority</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="priorityForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="priority">Priority Level:</label>
                        <select id="priority" name="priority" class="form-control" required>
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Category Modal -->
<div class="modal fade" id="updateCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="general" {{ $ticket->category === 'general' ? 'selected' : '' }}>General</option>
                            <option value="technical" {{ $ticket->category === 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="product" {{ $ticket->category === 'product' ? 'selected' : '' }}>Product</option>
                            <option value="billing" {{ $ticket->category === 'billing' ? 'selected' : '' }}>Billing</option>
                            <option value="complaint" {{ $ticket->category === 'complaint' ? 'selected' : '' }}>Complaint</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Update ticket status
function updateTicketStatus(status) {
    if (!confirm(`Are you sure you want to mark this ticket as ${status.replace('_', ' ')}?`)) return;
    
    fetch(`{{ route('admin.support-tickets.update-status', $ticket) }}`, {
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
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Handle response form
document.getElementById('responseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const response = document.getElementById('response').value.trim();
    if (!response) {
        alert('Please enter a response');
        return;
    }
    
    fetch(`{{ route('admin.support-tickets.add-response', $ticket) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ response: response })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});

// Handle note form
document.getElementById('noteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const note = document.getElementById('note').value.trim();
    if (!note) {
        alert('Please enter a note');
        return;
    }
    
    fetch(`{{ route('admin.support-tickets.add-note', $ticket) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ note: note })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});

// Handle assign form
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const assignedTo = document.getElementById('assigned_to').value;
    
    fetch(`{{ route('admin.support-tickets.assign', $ticket) }}`, {
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
            $('#assignModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});

// Handle priority form
document.getElementById('priorityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const priority = document.getElementById('priority').value;
    
    fetch(`{{ route('admin.support-tickets.update-priority', $ticket) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ priority: priority })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#updatePriorityModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});

// Handle category form
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const category = document.getElementById('category').value;
    
    fetch(`{{ route('admin.support-tickets.update-category', $ticket) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ category: category })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#updateCategoryModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});

// Delete ticket
function deleteTicket() {
    if (!confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) return;
    
    fetch(`{{ route('admin.support-tickets.destroy', $ticket) }}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.support-tickets.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endpush

@push('styles')
<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    height: 100%;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
}

.timeline-marker {
    position: absolute;
    left: 2px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 4px;
    border-left: 3px solid #007bff;
}
</style>
@endpush