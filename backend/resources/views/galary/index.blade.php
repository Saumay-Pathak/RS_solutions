@extends('layouts.app')

@section('title', 'Galary')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Galary</h1>
  <div class="row g-3">
    @forelse($items as $item)
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100">
          @if($item->type==='image')
            <img src="{{ $item->file_url }}" class="card-img-top" alt="{{ $item->title }}" />
          @else
            <div class="ratio ratio-16x9">
              <video src="{{ $item->file_url }}" preload="metadata"></video>
            </div>
          @endif
          <div class="card-body">
            <h6 class="card-title">{{ $item->title }}</h6>
            <a href="{{ route('galary.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">Open</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">No galary items yet.</div>
      </div>
    @endforelse
  </div>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
@endsection