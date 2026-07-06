@extends('layouts.app')

@section('title', 'Create Integration Module - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Add New Integration Module</h4>
        <p class="mb-0">Define module details, APIs, docs, and services</p>
      </div>
      <a href="{{ route('admin.integration-modules.index') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Modules
      </a>
    </div>

    <form action="{{ route('admin.integration-modules.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-lg-8">
          <!-- Basic Information -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Basic Information</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Module Name" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-4">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Describe the integration">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-4">
                <label class="form-label" for="cover_image">Cover Image</label>
                <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <!-- Features -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Features</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label">Key Features</label>
                <div id="key-features">
                  @foreach(old('key_features', []) as $val)
                    <div class="input-group mb-2"><input type="text" name="key_features[]" class="form-control" value="{{ $val }}"><button class="btn btn-outline-danger remove-item" type="button">Remove</button></div>
                  @endforeach
                  <div class="input-group mb-2"><input type="text" name="key_features[]" class="form-control" placeholder="e.g., OAuth 2.0"><button class="btn btn-outline-danger remove-item" type="button">Remove</button></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('key-features','key_features[]')"><i class="icon-base ti tabler-plus"></i> Add Feature</button>
              </div>
              <div class="mb-4">
                <label class="form-label">API Features</label>
                <div id="api-features">
                  @foreach(old('api_features', []) as $val)
                    <div class="input-group mb-2"><input type="text" name="api_features[]" class="form-control" value="{{ $val }}"><button class="btn btn-outline-danger remove-item" type="button">Remove</button></div>
                  @endforeach
                  <div class="input-group mb-2"><input type="text" name="api_features[]" class="form-control" placeholder="e.g., Webhooks"><button class="btn btn-outline-danger remove-item" type="button">Remove</button></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('api-features','api_features[]')"><i class="icon-base ti tabler-plus"></i> Add API Feature</button>
              </div>
            </div>
          </div>

          <!-- API Documentations -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">API Documentations</h5></div>
            <div class="card-body">
              <div id="api-docs">
                @php($oldTitles = old('doc_titles', []))
                @php($oldUrls = old('doc_urls', []))
                @foreach($oldTitles as $i => $t)
                  <div class="row g-2 mb-2 api-doc-item">
                    <div class="col-md-6"><input type="text" name="doc_titles[]" class="form-control" value="{{ $t }}" placeholder="Doc title"></div>
                    <div class="col-md-5"><input type="url" name="doc_urls[]" class="form-control" value="{{ $oldUrls[$i] ?? '' }}" placeholder="https://docs.example.com"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-item w-100">X</button></div>
                  </div>
                @endforeach
                <div class="row g-2 mb-2 api-doc-item">
                  <div class="col-md-6"><input type="text" name="doc_titles[]" class="form-control" placeholder="Doc title"></div>
                  <div class="col-md-5"><input type="url" name="doc_urls[]" class="form-control" placeholder="https://docs.example.com"></div>
                  <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-item w-100">X</button></div>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addApiDoc()"><i class="icon-base ti tabler-plus"></i> Add Documentation</button>
            </div>
          </div>

          <!-- Base URLs & Demo Credentials -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Environment & Demo</h5></div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="production_base_url">Production Base URL</label>
                  <input type="url" class="form-control @error('production_base_url') is-invalid @enderror" id="production_base_url" name="production_base_url" value="{{ old('production_base_url') }}" placeholder="https://api.example.com">
                  @error('production_base_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="staging_base_url">Staging Base URL</label>
                  <input type="url" class="form-control @error('staging_base_url') is-invalid @enderror" id="staging_base_url" name="staging_base_url" value="{{ old('staging_base_url') }}" placeholder="https://staging-api.example.com">
                  @error('staging_base_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-md-4"><label class="form-label" for="demo_username">Demo Username</label><input type="text" class="form-control" id="demo_username" name="demo_username" value="{{ old('demo_username') }}"></div>
                <div class="col-md-4"><label class="form-label" for="demo_password">Demo Password</label><input type="text" class="form-control" id="demo_password" name="demo_password" value="{{ old('demo_password') }}"></div>
                <div class="col-md-4"><label class="form-label" for="demo_notes">Demo Notes</label><input type="text" class="form-control" id="demo_notes" name="demo_notes" value="{{ old('demo_notes') }}"></div>
              </div>
            </div>
          </div>

          <!-- APIs -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">APIs</h5></div>
            <div class="card-body">
              <div id="apis">
                <div class="row g-2 mb-2 api-item">
                  <div class="col-md-3"><input type="text" name="api_names[]" class="form-control" placeholder="Name e.g., Auth"></div>
                  <div class="col-md-2">
                    <select name="api_types[]" class="form-select">
                      <option value="">Type</option>
                      <option>REST</option>
                      <option>GraphQL</option>
                      <option>SOAP</option>
                      <option>Webhooks</option>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <select name="api_methods[]" class="form-select">
                      <option value="">Method</option>
                      <option>GET</option>
                      <option>POST</option>
                      <option>PUT</option>
                      <option>PATCH</option>
                      <option>DELETE</option>
                    </select>
                  </div>
                  <div class="col-md-3"><input type="url" name="api_base_urls[]" class="form-control" placeholder="https://api.example.com/auth"></div>
                  <div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-item w-100">X</button></div>
                  <div class="col-12"><input type="text" name="api_descriptions[]" class="form-control" placeholder="Short description"></div>
                  <div class="col-md-6"><textarea name="api_headers[]" class="form-control" rows="3" placeholder="Headers (JSON or key: value) e.g. { &quot;Authorization&quot;: &quot;Bearer token&quot; }"></textarea></div>
                  <div class="col-md-6"><textarea name="api_bodies[]" class="form-control" rows="3" placeholder="Body (JSON or form) e.g. { &quot;email&quot;: &quot;test@example.com&quot; }"></textarea></div>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addApiItem()"><i class="icon-base ti tabler-plus"></i> Add API</button>
            </div>
          </div>

          <!-- Services -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Services</h5></div>
            <div class="card-body">
              <div class="mb-4">
                <label class="form-label">API Services</label>
                <div id="services-api">
                  <div class="input-group mb-2"><input type="text" name="services_api[]" class="form-control" placeholder="e.g., Authentication"><button class="btn btn-outline-danger remove-item" type="button">Remove</button></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('services-api','services_api[]')"><i class="icon-base ti tabler-plus"></i> Add Service</button>
              </div>
              <div class="mb-4">
                <label class="form-label">Other Services</label>
                <div id="services-other">
                  <div class="input-group mb-2"><input type="text" name="services_other[]" class="form-control" placeholder="e.g., Reporting"><button class="btn btn-outline-danger remove-item" type="button">Remove</button></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('services-other','services_other[]')"><i class="icon-base ti tabler-plus"></i> Add Other Service</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <!-- Settings -->
          <div class="card mb-6">
            <div class="card-header"><h5 class="mb-0">Settings</h5></div>
            <div class="card-body">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">Active</label>
              </div>
              <div class="mb-3">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
              </div>
            </div>
          </div>

          <!-- SEO -->
          <div class="card">
            <div class="card-header"><h5 class="mb-0">SEO</h5></div>
            <div class="card-body">
              <div class="mb-3"><label class="form-label" for="meta_title">Meta Title</label><input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"></div>
              <div class="mb-3"><label class="form-label" for="meta_description">Meta Description</label><input type="text" class="form-control" id="meta_description" name="meta_description" value="{{ old('meta_description') }}"></div>
              <div class="mb-3"><label class="form-label" for="meta_keywords">Meta Keywords</label><input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-device-floppy me-1"></i>Save Module</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function addItem(containerId, inputName) {
  const container = document.getElementById(containerId);
  const div = document.createElement('div');
  div.className = 'input-group mb-2';
  div.innerHTML = `<input type="text" name="${inputName}" class="form-control"><button class="btn btn-outline-danger remove-item" type="button">Remove</button>`;
  container.appendChild(div);
}

function addApiDoc() {
  const container = document.getElementById('api-docs');
  const row = document.createElement('div');
  row.className = 'row g-2 mb-2 api-doc-item';
  row.innerHTML = `
    <div class="col-md-6"><input type="text" name="doc_titles[]" class="form-control" placeholder="Doc title"></div>
    <div class="col-md-5"><input type="url" name="doc_urls[]" class="form-control" placeholder="https://docs.example.com"></div>
    <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-item w-100">X</button></div>
  `;
  container.appendChild(row);
}

function addApiItem() {
  const container = document.getElementById('apis');
  const row = document.createElement('div');
  row.className = 'row g-2 mb-2 api-item';
  row.innerHTML = `
    <div class="col-md-3"><input type="text" name="api_names[]" class="form-control" placeholder="Name e.g., Auth"></div>
    <div class="col-md-2">
      <select name="api_types[]" class="form-select">
        <option value="">Type</option>
        <option>REST</option>
        <option>GraphQL</option>
        <option>SOAP</option>
        <option>Webhooks</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="api_methods[]" class="form-select">
        <option value="">Method</option>
        <option>GET</option>
        <option>POST</option>
        <option>PUT</option>
        <option>PATCH</option>
        <option>DELETE</option>
      </select>
    </div>
    <div class="col-md-3"><input type="url" name="api_base_urls[]" class="form-control" placeholder="https://api.example.com/auth"></div>
    <div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-item w-100">X</button></div>
    <div class="col-12"><input type="text" name="api_descriptions[]" class="form-control" placeholder="Short description"></div>
    <div class="col-md-6"><textarea name="api_headers[]" class="form-control" rows="3" placeholder="Headers (JSON or key: value) e.g. { &quot;Authorization&quot;: &quot;Bearer token&quot; }"></textarea></div>
    <div class="col-md-6"><textarea name="api_bodies[]" class="form-control" rows="3" placeholder="Body (JSON or form) e.g. { &quot;email&quot;: &quot;test@example.com&quot; }"></textarea></div>
  `;
  container.appendChild(row);
}

document.addEventListener('click', function(e){
  if (e.target && e.target.classList.contains('remove-item')) {
    const group = e.target.closest('.input-group, .api-doc-item, .api-item');
    if (group) group.remove();
  }
});
</script>
@endpush
@endsection
