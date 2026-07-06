@extends('layouts.app')

@section('title', $item->title)

@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ $item->title }}</h1>
  <div class="mb-3">
    @if($item->type==='image')
      <img src="{{ $item->file_url }}" alt="{{ $item->title }}" class="img-fluid" />
    @else
      <video src="{{ $item->file_url }}" controls preload="metadata" class="w-100" style="max-height:70vh;"></video>
    @endif
  </div>
  <div class="d-flex gap-2">
    <input type="text" class="form-control" value="{{ url('/galary/'.$item->slug) }}" readonly />
    <a href="{{ url('/galary/'.$item->slug) }}" target="_blank" class="btn btn-primary">Open Public Link</a>
  </div>
</div>
@endsection