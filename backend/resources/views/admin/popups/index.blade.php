@extends('admin.layouts.admin')

@section('title', 'Popups Management')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-6">
                <div>
                    <h4 class="mb-1">Popups Management</h4>
                    <p class="mb-0">Create and manage your Popups on your website</p>
                </div>
                <a href="{{ route('admin.popups.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Popup
                </a>
            </div>

    <div class="card mb-6">
                    <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" placeholder="Search popups..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="type">
                                    <option value="">All Types</option>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($popups->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Position</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Schedule</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popups as $popup)
                                    <tr>
                                        <td>
                                            @if($popup->image)
                                                <img src="{{ $popup->image_url }}" alt="{{ $popup->title }}" class="img-thumbnail"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $popup->title }}</div>
                                            @if($popup->content)
                                                <small class="text-muted">{{ Str::limit($popup->content, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $types[$popup->type] ?? $popup->type }}</span>
                                        </td>
                                        <td>{{ ucwords(str_replace('-', ' ', $popup->position)) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $popup->priority }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.popups.toggle-status', $popup) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-{{ $popup->is_active ? 'success' : 'secondary' }}">
                                                    <i class="fas fa-{{ $popup->is_active ? 'eye' : 'eye-slash' }}"></i>
                                                    {{ $popup->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            @if($popup->start_date || $popup->end_date)
                                                <div class="small">
                                                    @if($popup->start_date)
                                                        <div>From: {{ $popup->start_date->format('M j, Y') }}</div>
                                                    @endif
                                                    @if($popup->end_date)
                                                        <div>To: {{ $popup->end_date->format('M j, Y') }}</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Always</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('admin.popups.show', $popup) }}">
                                                            <i class="fas fa-eye me-2"></i>View
                                                        </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('admin.popups.edit', $popup) }}">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('admin.popups.preview', $popup) }}"
                                                            target="_blank">
                                                            <i class="fas fa-external-link-alt me-2"></i>Preview
                                                        </a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.popups.destroy', $popup) }}" method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this popup?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $popups->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-window-restore fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No popups found</h5>
                        <p class="text-muted mb-4">Create your first popup to engage with your visitors</p>
                        <a href="{{ route('admin.popups.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Popup
                        </a>
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>
    </div>
@endsection