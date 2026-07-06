@extends('layouts.app')

@section('title', 'Home Sections - Admin Panel')

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div>
        <h4 class="mb-1">Home Sections</h4>
        <p class="mb-0">Reorder and enable/disable sections displayed on the homepage</p>
      </div>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Dashboard
      </a>
    </div>

    <!-- Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Manage Home Sections</h5>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <p class="text-muted mb-3">
          Drag to reorder sections and toggle to enable/disable. These are static predefined sections from the frontend.
        </p>

        <form id="sections-form" action="{{ route('admin.home-sections.update') }}" method="POST">
          @csrf

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width:42px">Order</th>
                  <th style="width:42px">Drag</th>
                  <th>Section</th>
                  <th>Component</th>
                  <th class="text-center" style="width:140px">Enabled</th>
                </tr>
              </thead>
              <tbody id="sections-list">
                @foreach($sections as $index => $section)
                  <tr class="draggable-row" draggable="true" data-index="{{ $index }}" data-key="{{ $section['key'] }}">
                    <td class="order-col">
                      <span class="badge bg-primary order-badge">{{ $section['order'] }}</span>
                      <input type="hidden" name="sections[{{ $index }}][order]" value="{{ $section['order'] }}" />
                    </td>
                    <td class="drag-col">
                      <span class="drag-handle" title="Drag to reorder">☰</span>
                    </td>
                    <td>
                      {{ $section['title'] }}
                      <input type="hidden" name="sections[{{ $index }}][title]" value="{{ $section['title'] }}" />
                      <input type="hidden" name="sections[{{ $index }}][key]" value="{{ $section['key'] }}" />
                    </td>
                    <td>
                      <code>{{ $section['component'] }}</code>
                      <input type="hidden" name="sections[{{ $index }}][component]" value="{{ $section['component'] }}" />
                    </td>
                    <td class="text-center">
                      <input type="hidden" name="sections[{{ $index }}][enabled]" value="0" />
                      <input type="checkbox" class="form-check-input" name="sections[{{ $index }}][enabled]" value="1" {{ $section['enabled'] ? 'checked' : '' }} />
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary">
              <i class="icon-base ti tabler-device-floppy me-2"></i>Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .drag-handle { cursor: grab; user-select: none; display: inline-block; padding: 4px 8px; background: #f1f3f5; border-radius: 4px; }
  .draggable-row.dragging { opacity: 0.5; }
  .draggable-row.over { outline: 2px dashed #0091ea; }
  .order-badge { font-weight: 600; }
</style>

<script>
  (function() {
    const list = document.getElementById('sections-list');
    let dragSrcEl = null;

    function handleDragStart(e) {
      const row = e.currentTarget;
      dragSrcEl = row;
      row.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', row.dataset.key);
    }

    function handleDragOver(e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      const row = e.currentTarget;
      row.classList.add('over');
    }

    function handleDragLeave(e) {
      e.currentTarget.classList.remove('over');
    }

    function handleDrop(e) {
      e.stopPropagation();
      const targetRow = e.currentTarget;
      if (dragSrcEl && dragSrcEl !== targetRow) {
        const rows = Array.from(list.querySelectorAll('tr'));
        const srcIndex = rows.indexOf(dragSrcEl);
        const targetIndex = rows.indexOf(targetRow);
        if (srcIndex < targetIndex) {
          list.insertBefore(dragSrcEl, targetRow.nextSibling);
        } else {
          list.insertBefore(dragSrcEl, targetRow);
        }
        refreshOrderInputs();
      }
      targetRow.classList.remove('over');
      return false;
    }

    function handleDragEnd(e) {
      e.currentTarget.classList.remove('dragging');
      const rows = list.querySelectorAll('tr');
      rows.forEach(r => r.classList.remove('over'));
    }

    function refreshOrderInputs() {
      const rows = Array.from(list.querySelectorAll('tr'));
      rows.forEach((row, idx) => {
        const orderInput = row.querySelector('input[name^="sections"][name$="[order]"]');
        const badge = row.querySelector('.order-badge');
        if (orderInput) orderInput.value = idx + 1;
        if (badge) badge.textContent = idx + 1;
        // Also reindex input names so server receives correct arrays
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
          const name = input.getAttribute('name');
          if (!name) return;
          const newName = name.replace(/sections\[\d+\]/, `sections[${idx}]`);
          input.setAttribute('name', newName);
        });
      });
    }

    function initDragAndDrop() {
      const rows = list.querySelectorAll('tr.draggable-row');
      rows.forEach(row => {
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragover', handleDragOver);
        row.addEventListener('dragleave', handleDragLeave);
        row.addEventListener('drop', handleDrop);
        row.addEventListener('dragend', handleDragEnd);
      });
    }

    initDragAndDrop();
  })();
</script>
@endsection