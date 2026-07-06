@extends('layouts.app')

@section('title', 'Edit Job Opening')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Edit Job Opening</h4>
        <a href="{{ route('admin.job-openings.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.job-openings.update', $job) }}" method="POST" class="card p-4">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-8">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $job->title) }}" required>
            </div>
            <div class="col-md-4">
                <label for="employment_type" class="form-label">Employment Type</label>
                <select name="employment_type" id="employment_type" class="form-select">
                    <option value="" {{ old('employment_type', $job->employment_type) == '' ? 'selected' : '' }}>Select type</option>
                    @foreach(['Full-time','Part-time','Contract','Internship'] as $type)
                        <option value="{{ $type }}" {{ old('employment_type', $job->employment_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $job->location) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="is_active">Active Status</label>
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $job->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="6">{{ old('description', $job->description) }}</textarea>
            </div>

            <div class="col-md-6">
                <label for="display_from" class="form-label">Display From</label>
                <input type="datetime-local" name="display_from" id="display_from" class="form-control" value="{{ old('display_from', optional($job->display_from)->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-md-6">
                <label for="display_to" class="form-label">Display To</label>
                <input type="datetime-local" name="display_to" id="display_to" class="form-control" value="{{ old('display_to', optional($job->display_to)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="col-md-4">
                <label for="order" class="form-label">Order</label>
                <input type="number" name="order" id="order" class="form-control" value="{{ old('order', $job->order) }}" min="1">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Job</button>
            <a href="{{ route('admin.job-openings.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/js/summernote-lite.min.css') }}">
<style>
    .note-editor .note-editable {
        line-height: 1.5;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/summernote-lite.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Summernote
    $('#description').summernote({
        placeholder: 'Enter job description...',
        tabsize: 2,
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                // Ensure textarea is updated for form submission
                $('#description').val(contents);
            }
        }
    });
});
</script>
@endpush