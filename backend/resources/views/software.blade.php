@extends('layouts.app')

@section('title', 'Software Downloads')
@section('meta_description', 'Download free and premium software applications, tools, and utilities.')

@section('content')
<div class="container-xxl">
    <div class="container-p-y">
        
        <!-- Page Header -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h1 class="mb-2">Software Downloads</h1>
                        <p class="text-muted mb-0">Discover and download the best software applications, tools, and utilities</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="free" {{ request('type') === 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="paid" {{ request('type') === 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="icon-base ti tabler-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('software') }}" class="btn btn-outline-secondary">
                                    <i class="icon-base ti tabler-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Software -->
        @if($featuredSoftware->count() > 0)
            <div class="row mb-6">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Featured Software</h3>
                    </div>
                    
                    <div class="row">
                        @foreach($featuredSoftware as $software)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="avatar avatar-md me-3 bg-label-primary">
                                                <i class="icon-base ti tabler-{{ $software->file_type === 'external' ? 'link' : 'download' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1">
                                                    <a href="{{ $software->url }}" class="text-decoration-none">
                                                        {{ Str::limit($software->title, 30) }}
                                                    </a>
                                                </h5>
                                                @if($software->developer)
                                                    <small class="text-muted">by {{ $software->developer }}</small>
                                                @endif
                                            </div>
                                            @if($software->is_free)
                                                <span class="badge bg-success">Free</span>
                                            @else
                                                <span class="badge bg-warning">${{ $software->price }}</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-muted small mb-3">{{ $software->excerpt(100) }}</p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="icon-base ti tabler-download me-1"></i>
                                                {{ number_format($software->download_count) }}
                                                @if($software->size)
                                                    <span class="mx-2">•</span>
                                                    {{ $software->file_size_formatted }}
                                                @endif
                                            </div>
                                            <a href="{{ $software->url }}" class="btn btn-sm btn-primary">
                                                View Details
                                            </a>
                                        </div>
                                        
                                        @if(count($software->tags_list) > 0)
                                            <div class="mt-3">
                                                @foreach(array_slice($software->tags_list, 0, 3) as $tag)
                                                    <span class="badge bg-label-secondary me-1">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- All Software -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">All Software</h3>
                    <span class="text-muted">{{ $allSoftware->total() }} total results</span>
                </div>
                
                @if($allSoftware->count() > 0)
                    <div class="row">
                        @foreach($allSoftware as $software)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="avatar avatar-md me-3 bg-label-{{ $software->is_free ? 'success' : 'warning' }}">
                                                <i class="icon-base ti tabler-{{ $software->file_type === 'external' ? 'link' : 'download' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1">
                                                    <a href="{{ $software->url }}" class="text-decoration-none">
                                                        {{ Str::limit($software->title, 30) }}
                                                    </a>
                                                </h5>
                                                @if($software->developer)
                                                    <small class="text-muted">by {{ $software->developer }}</small>
                                                @endif
                                            </div>
                                            @if($software->is_free)
                                                <span class="badge bg-success">Free</span>
                                            @else
                                                <span class="badge bg-warning">${{ $software->price }}</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-muted small mb-3">{{ $software->excerpt(100) }}</p>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="icon-base ti tabler-folder me-1"></i>
                                                {{ $software->full_category }}
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="icon-base ti tabler-download me-1"></i>
                                                {{ number_format($software->download_count) }}
                                                @if($software->size)
                                                    <span class="mx-2">•</span>
                                                    {{ $software->file_size_formatted }}
                                                @endif
                                            </div>
                                            <a href="{{ $software->url }}" class="btn btn-sm btn-primary">
                                                View Details
                                            </a>
                                        </div>
                                        
                                        @if(count($software->platforms_list) > 0)
                                            <div class="mt-3">
                                                @foreach(array_slice($software->platforms_list, 0, 3) as $platform)
                                                    <span class="badge bg-label-info me-1">{{ $platform }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $allSoftware->links() }}
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-xl mx-auto mb-3 bg-label-secondary">
                                <i class="icon-base ti tabler-search fs-1"></i>
                            </div>
                            <h4 class="mb-2">No Software Found</h4>
                            <p class="text-muted">Try adjusting your search criteria or browse all available software.</p>
                            <a href="{{ route('software') }}" class="btn btn-primary">
                                <i class="icon-base ti tabler-refresh me-1"></i>View All Software
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection