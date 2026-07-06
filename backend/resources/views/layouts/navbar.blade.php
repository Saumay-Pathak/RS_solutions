<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base ti tabler-menu-2 icon-md"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0">
        <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
          <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
        </a>
      </div>
    </div>

    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
      <li class="nav-item dropdown-language dropdown">
        <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
          href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base ti tabler-language icon-22px text-heading"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-language="en" data-text-direction="ltr">
              <span>English</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-language="fr" data-text-direction="ltr">
              <span>French</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-language="ar" data-text-direction="rtl">
              <span>Arabic</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-language="de" data-text-direction="ltr">
              <span>German</span>
            </a>
          </li>


        </ul>
      </li>
      <!--/ Language -->

      <li class="nav-item">
        <a id="clearCacheBtn" class="nav-link btn btn-icon btn-text-danger rounded-pill" href="javascript:void(0);"
          title="Clear Cache">
          <i class="icon-base ti tabler-refresh icon-22px text-danger"></i>
        </a>
      </li>

      <!-- Style Switcher -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill" id="nav-theme"
          href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
          <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
          <li>
            <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
              aria-pressed="false">
              <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
              aria-pressed="true">
              <span><i class="icon-base ti tabler-moon-stars icon-22px me-3" data-icon="moon-stars"></i>Dark</span>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
              aria-pressed="false">
              <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                  data-icon="device-desktop-analytics"></i>System</span>
            </button>
          </li>
        </ul>
      </li>
      <!-- / Style Switcher-->

      <!-- Quick links  -->
      <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
        <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
          href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
          <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end p-0">
          <div class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h6 class="mb-0 me-auto">Shortcuts</h6>
              <a href="javascript:void(0)"
                class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i
                  class="icon-base ti tabler-plus icon-20px text-heading"></i></a>
            </div>
          </div>
          <div class="dropdown-shortcuts-list scrollable-container">
            <div class="row row-bordered overflow-visible g-0">
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-users icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.users.index') }}" class="stretched-link">Users</a>
                <small>Manage Users</small>
              </div>
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-category icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.categories.index') }}" class="stretched-link">Categories</a>
                <small>Content Categories</small>
              </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-package icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.products.index') }}" class="stretched-link">Products</a>
                <small>Product Management</small>
              </div>
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-article icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.blogs.index') }}" class="stretched-link">Blogs</a>
                <small>Blog Posts</small>
              </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-device-desktop-analytics icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.dashboard') }}" class="stretched-link">Dashboard</a>
                <small>Admin Dashboard</small>
              </div>
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.extras.index') }}" class="stretched-link">Settings</a>
                <small>Site Settings</small>
              </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-download icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.software.index') }}" class="stretched-link">Software</a>
                <small>Software Management</small>
              </div>
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-window icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.popups.index') }}" class="stretched-link">Popups</a>
                <small>Popup Management</small>
              </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
              <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                  <i class="icon-base ti tabler-tools icon-26px text-heading"></i>
                </span>
                <a href="{{ route('admin.services.index') }}" class="stretched-link">Services</a>
                <small>Services Management</small>
              </div>
            </div>
          </div>
        </div>
      </li>
      <!-- Quick links -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            @if(auth()->user()->avatar)
              <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                class="rounded-circle" />
            @else
              <span class="avatar-initial rounded-circle bg-label-primary">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
              </span>
            @endif
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item mt-0" href="{{ route('admin.profile') }}">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                  <div class="avatar avatar-online">
                    @if(auth()->user()->avatar)
                      <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                        class="rounded-circle" />
                    @else
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                      </span>
                    @endif
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                  <!-- <small class="text-body-secondary">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</small> -->
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1 mx-n2"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.profile') }}">
              <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My Profile</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.extras.index') }}">
              <i class="icon-base ti tabler-settings me-3 icon-md"></i><span class="align-middle">Settings</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
              <i class="icon-base ti tabler-device-desktop-analytics me-3 icon-md"></i><span
                class="align-middle">Dashboard</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1 mx-n2"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.users.index') }}">
              <i class="icon-base ti tabler-users me-3 icon-md"></i><span class="align-middle">Users</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.roles.index') }}">
              <i class="icon-base ti tabler-user-check me-3 icon-md"></i><span class="align-middle">Roles &
                Permissions</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1 mx-n2"></div>
          </li>
          <li>
            <div class="d-grid px-2 pt-2 pb-1">
              <form action="{{ route('logout') }}" method="POST" style="display: none;" id="navbar-logout-form">
                @csrf
              </form>
              <a class="btn btn-sm btn-danger d-flex" href="#"
                onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                <small class="align-middle">Logout</small>
                <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
              </a>
            </div>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>
<script>
  document.getElementById('clearCacheBtn').addEventListener('click', function () {
    if (confirm('Are you sure you want to clear the cache?')) {
      fetch('{{ url('clear-cache') }}', { method: 'GET' })
        .then(response => response.json())
        .then(data => {
          alert(data.message || 'Cache cleared successfully!');
        })
        .catch(error => {
          alert('Error clearing cache: ' + error.message);
        });
    }
  });
</script>