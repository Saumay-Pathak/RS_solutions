@extends('admin.layouts.admin')

@section('title', 'About Us Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-1">About Us Management</h5>
                    <p class="card-subtitle mb-0">Manage your About Us page content</p>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.about-us.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#who-we-are">
                                    Who We Are
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#mission-vision">
                                    Mission & Vision
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4">
                            <!-- Who We Are Tab -->
                            <div class="tab-pane fade show active" id="who-we-are">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="who_we_are_title" 
                                                   value="{{ $aboutUs->who_we_are_title ?? '' }}" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Subtitle</label>
                                            <input type="text" class="form-control" name="who_we_are_subtitle" 
                                                   value="{{ $aboutUs->who_we_are_subtitle ?? '' }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Content</label>
                                            <textarea class="form-control" name="who_we_are_content" rows="5" required>{{ $aboutUs->who_we_are_content ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Image</label>
                                            <input type="file" class="form-control" name="who_we_are_image" accept="image/*">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Video (Choose one option)</label>
                                            
                                            <!-- Video URL -->
                                            <div class="mb-2">
                                                <label class="form-label small">Video URL</label>
                                                <input type="url" class="form-control" name="who_we_are_video_url" 
                                                       value="{{ $aboutUs->who_we_are_video_url ?? '' }}"
                                                       placeholder="https://youtube.com/watch?v=...">
                                                <small class="form-text text-muted">YouTube, Vimeo, or direct video URL</small>
                                            </div>
                                            
                                            <!-- Video File Upload -->
                                            <div>
                                                <label class="form-label small">Or Upload Video File</label>
                                                <input type="file" class="form-control" name="who_we_are_video_file" 
                                                       accept="video/mp4,video/avi,video/mov,video/wmv">
                                                <small class="form-text text-muted">Max size: 50MB (MP4, AVI, MOV, WMV)</small>
                                            </div>
                                            
                                            @if($aboutUs->who_we_are_video_file ?? false)
                                                <div class="mt-2">
                                                    <small class="text-success">✓ Video file uploaded</small>
                                                    <video width="200" height="120" controls class="d-block mt-1">
                                                        <source src="{{ $aboutUs->who_we_are_video_url_full }}" type="video/mp4">
                                                    </video>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mission & Vision Tab -->
                            <div class="tab-pane fade" id="mission-vision">
                                <div class="mb-4">
                                    <label class="form-label">Section Title</label>
                                    <input type="text" class="form-control" name="mission_vision_title" 
                                           value="{{ $aboutUs->mission_vision_title ?? '' }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Mission</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Mission Title</label>
                                                    <input type="text" class="form-control" name="mission_title" 
                                                           value="{{ $aboutUs->mission_title ?? '' }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Mission Content</label>
                                                    <textarea class="form-control" name="mission_content" rows="4" required>{{ $aboutUs->mission_content ?? '' }}</textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Mission Image</label>
                                                    <input type="file" class="form-control" name="mission_image" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Vision</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Vision Title</label>
                                                    <input type="text" class="form-control" name="vision_title" 
                                                           value="{{ $aboutUs->vision_title ?? '' }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Vision Content</label>
                                                    <textarea class="form-control" name="vision_content" rows="4" required>{{ $aboutUs->vision_content ?? '' }}</textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Vision Image</label>
                                                    <input type="file" class="form-control" name="vision_image" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" 
                                       {{ ($aboutUs->is_published ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">Publish About Us Page</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection