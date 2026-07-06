@extends('layouts.app')

@section('title', $software->meta_title ?: $software->title)
@section('meta_description', $software->meta_description ?: $software->excerpt(160))
@section('meta_keywords', $software->meta_keywords)

@section('content')
<div class="container-xxl">
    <div class="container-p-y">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('software') }}">Software</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $software->title }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <!-- Software Header -->
                        <div class="d-flex align-items-start mb-4">
                            <div class="avatar avatar-lg me-3 bg-label-primary">
                                <i class="icon-base ti tabler-download fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h1 class="h3 mb-2">{{ $software->title }}</h1>
                                <p class="text-muted mb-2">{{ $software->one_line_description }}</p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($software->developer)
                                        <span class="badge bg-label-info">by {{ $software->developer }}</span>
                                    @endif
                                    @if($software->version)
                                        <span class="badge bg-label-secondary">v{{ $software->version }}</span>
                                    @endif
                                    @if($software->is_free)
                                        <span class="badge bg-label-success">Free</span>
                                    @else
                                        <span class="badge bg-label-warning">${{ $software->price }}</span>
                                    @endif
                                    @if($software->featured)
                                        <span class="badge bg-label-primary">Featured</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="icon-base ti tabler-download me-1"></i>
                                    {{ number_format($software->download_count) }} downloads
                                    @if($software->size)
                                        <span class="mx-2">•</span>
                                        <i class="icon-base ti tabler-file me-1"></i>
                                        {{ $software->file_size_formatted }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Download Button -->
                        @if($software->hasDownload())
                            <div class="mb-4">
                                <a href="{{ route('software.download', $software->slug ?: $software->id) }}" 
                                   class="btn btn-primary btn-lg">
                                    <i class="icon-base ti tabler-download me-2"></i>
                                    Download {{ $software->title }}
                                    @if($software->file_type === 'external')
                                        <i class="icon-base ti tabler-external-link ms-2"></i>
                                    @endif
                                </a>
                            </div>
                        @endif

                        <!-- Description -->
                        <div class="mb-4">
                            <h4 class="mb-3">Description</h4>
                            <div class="content">
                                {!! nl2br(e($software->description)) !!}
                            </div>
                        </div>

                        <!-- System Requirements -->
                        @if(count($software->requirements_list) > 0)
                            <div class="mb-4">
                                <h4 class="mb-3">System Requirements</h4>
                                <ul class="list-unstyled">
                                    @foreach($software->requirements_list as $requirement)
                                        <li class="mb-1">
                                            <i class="icon-base ti tabler-check text-success me-2"></i>
                                            {{ $requirement }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Supported Platforms -->
                        @if(count($software->platforms_list) > 0)
                            <div class="mb-4">
                                <h4 class="mb-3">Supported Platforms</h4>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($software->platforms_list as $platform)
                                        <span class="badge bg-label-info">{{ $platform }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Tags -->
                        @if(count($software->tags_list) > 0)
                            <div class="mb-4">
                                <h4 class="mb-3">Tags</h4>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($software->tags_list as $tag)
                                        <span class="badge bg-label-secondary">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Software Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Software Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-5 fw-medium">Category:</div>
                            <div class="col-sm-7">{{ $software->full_category }}</div>
                        </div>
                        @if($software->license)
                            <div class="row mb-3">
                                <div class="col-sm-5 fw-medium">License:</div>
                                <div class="col-sm-7">{{ $software->license }}</div>
                            </div>
                        @endif
                        @if($software->released_at)
                            <div class="row mb-3">
                                <div class="col-sm-5 fw-medium">Released:</div>
                                <div class="col-sm-7">{{ $software->released_at->format('M d, Y') }}</div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-sm-5 fw-medium">Downloads:</div>
                            <div class="col-sm-7">{{ number_format($software->download_count) }}</div>
                        </div>
                        @if($software->size)
                            <div class="row mb-3">
                                <div class="col-sm-5 fw-medium">File Size:</div>
                                <div class="col-sm-7">{{ $software->file_size_formatted }}</div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-5 fw-medium">Type:</div>
                            <div class="col-sm-7">
                                @if($software->is_free)
                                    <span class="text-success">Free</span>
                                @else
                                    <span class="text-warning">${{ $software->price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Software -->
                @if($relatedSoftware->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Related Software</h5>
                        </div>
                        <div class="card-body">
                            @foreach($relatedSoftware as $related)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-3 bg-label-primary">
                                        <i class="icon-base ti tabler-{{ $related->file_type === 'external' ? 'link' : 'download' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">
                                            <a href="{{ $related->url }}" class="text-decoration-none">
                                                {{ Str::limit($related->title, 25) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $related->excerpt(40) }}</small>
                                        @if($related->is_free)
                                            <small class="text-success d-block">Free</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection