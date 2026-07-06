@extends('layouts.app')

@section('title', 'Job Openings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Job Openings</h4>
        <a href="{{ route('admin.job-openings.create') }}" class="btn btn-primary">Create Job Opening</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Employment Type</th>
                        <th>Status</th>
                        <th>Display</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>{{ $job->order }}</td>
                        <td><a href="{{ route('admin.job-openings.show', $job) }}">{{ $job->title }}</a></td>
                        <td>{{ $job->location ?? '-' }}</td>
                        <td>{{ $job->employment_type ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $job->is_active ? 'success' : 'secondary' }}">{{ $job->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            @if($job->display_from || $job->display_to)
                                <small>
                                    {{ optional($job->display_from)->format('Y-m-d') ?? '...' }} → {{ optional($job->display_to)->format('Y-m-d') ?? '...' }}
                                </small>
                            @else
                                <small>Always</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.job-openings.edit', $job) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.job-openings.destroy', $job) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this job opening?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No job openings found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $jobs->links() }}</div>
    </div>
</div>
@endsection