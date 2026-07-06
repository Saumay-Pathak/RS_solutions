@extends('layouts.app')

@section('title', 'Upload to Galary')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Upload to Galary</h4>
        <p class="mb-0">Add a new image or video item</p>
      </div>
      <a href="{{ route('admin.galary.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left"></i> Back to List
      </a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.galary.store') }}" method="post" enctype="multipart/form-data" class="row g-4">
          @csrf
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="image" {{ old('type')==='image'?'selected':'' }}>Image</option>
              <option value="video" {{ old('type')==='video'?'selected':'' }}>Video</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">File</label>
            <input type="file" name="file" class="form-control" accept="image/*,video/*" required />
            <div class="form-text">Supported: images (jpeg, png, webp, gif) or videos (mp4, webm, ogg). Max 200 MB.</div>
          </div>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit">
              <i class="icon-base ti tabler-upload"></i> Upload
            </button>
            <a href="{{ route('admin.galary.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection