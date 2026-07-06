@extends('layouts.app')

@section('title', 'Add New FAQ - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Add New FAQ</h4>
        <p class="mb-0">Create a new frequently asked question</p>
      </div>
      <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to FAQs
      </a>
    </div>

    <form action="{{ route('admin.faqs.store') }}" method="POST">
      @csrf
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">FAQ Details</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="question">Question <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('question') is-invalid @enderror" id="question" name="question" value="{{ old('question') }}" placeholder="Enter question" required>
                @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="answer">Answer <span class="text-danger">*</span></label>
                <textarea class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer" rows="6" placeholder="Enter the answer..." required>{{ old('answer') }}</textarea>
                @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" placeholder="0">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Lower numbers appear first. Use 0 for default ordering.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header"><h5 class="mb-0">Options</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="status">Active Status</label>
                </div>
                <div class="form-text">Enable to make FAQ visible on website</div>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="icon-base ti tabler-check me-2"></i>Create FAQ
              </button>
              <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary w-100">
                <i class="icon-base ti tabler-x me-2"></i>Cancel
              </a>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection