@extends('layouts.app')

@section('title', 'Job Opening Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Job Opening Details</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.job-openings.edit', $job) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.job-openings.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <span class="badge bg-{{ $job->is_active ? 'success' : 'secondary' }}">{{ $job->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <h5 class="card-title">{{ $job->title }}</h5>
            <p class="mb-1"><strong>Location:</strong> {{ $job->location ?? '-' }}</p>
            <p class="mb-1"><strong>Employment Type:</strong> {{ $job->employment_type ?? '-' }}</p>
            <p class="mb-1"><strong>Order:</strong> {{ $job->order }}</p>
            <p class="mb-1"><strong>Display:</strong>
                @if($job->display_from || $job->display_to)
                    {{ optional($job->display_from)->format('Y-m-d H:i') ?? '...' }} to {{ optional($job->display_to)->format('Y-m-d H:i') ?? '...' }}
                @else
                    Always
                @endif
            </p>
            <hr>
            <div>
                {!! nl2br(e($job->description)) !!}
            </div>
        </div>
    </div>
</div>
@endsection