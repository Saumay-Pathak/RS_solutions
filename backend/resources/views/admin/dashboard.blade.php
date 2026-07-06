@extends('layouts.app')

@section('title', 'Dashboard - Realtime Biometrics')

@section('content')
  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <!-- Welcome Section -->
      <div class="card bg-transparent shadow-none my-6 border-0">
        <div class="card-body row p-0 pb-6 g-6">
          <div class="col-12 col-lg-8 card-separator">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Welcome back,<span class="h4"> Admin 👋🏻</span></h5>
                <div class="d-flex align-items-center gap-2">
                    @if(isset($lastUpdated))
                        <small class="text-muted d-none d-sm-block">Updated: {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}</small>
                    @endif
                    <form action="{{ route('admin.dashboard.refresh') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-refresh me-1"></i> Refresh
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-12 col-lg-7">
              <p>Your admin panel is performing great. Track your website's activity and manage content efficiently!</p>
            </div>
            <div class="d-flex justify-content-between flex-wrap gap-4 me-12">
              <div class="d-flex align-items-center gap-4 me-6 me-sm-0">
                <div class="avatar avatar-lg">
                  <div class="avatar-initial bg-label-primary rounded">
                    <div class="text-primary">
                      <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                          <path opacity="0.2" d="M9.5 28.5h19v-19h-19v19z" fill="currentColor" />
                          <path
                            d="M28.5 7.125h-19a1.9 1.9 0 00-1.9 1.9v19a1.9 1.9 0 001.9 1.9h19a1.9 1.9 0 001.9-1.9v-19a1.9 1.9 0 00-1.9-1.9zM12.35 22.8V15.2h3.8v7.6h-3.8zm1.9-8.55a2.28 2.28 0 110-4.56 2.28 2.28 0 010 4.56zm11.4 8.55h-3.8v-3.8c0-1.425-.575-2.375-1.9-2.375-1.045 0-1.615.665-1.9 1.33-.095.235-.095.57-.095.9v3.995h-3.8s.05-6.46 0-7.125h3.8v1.045c.475-.76 1.33-1.805 3.23-1.805 2.375 0 4.14 1.52 4.14 4.75v3.08h.325z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Total Users</p>
                  <h4 class="text-primary mb-0">{{ $stats['total_users'] }}</h4>
                </div>
              </div>
              <div class="d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                  <div class="avatar-initial bg-label-info rounded">
                    <div class="text-info">
                      <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                          <path opacity="0.2" d="M28.5 9.5L19 4.75l-9.5 4.75v14.25L19 33.25l9.5-9.5V9.5z"
                            fill="currentColor" />
                          <path d="M19 4.75L9.5 9.5v14.25L19 33.25l9.5-9.5V9.5L19 4.75z" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          <path d="M19 4.75v28.5M9.5 9.5l19 14.25M28.5 9.5L9.5 23.75" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Products</p>
                  <h4 class="text-info mb-0">{{ $stats['total_products'] }}</h4>
                </div>
              </div>
              <div class="d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                  <div class="avatar-initial bg-label-warning rounded">
                    <div class="text-warning">
                      <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                          <path opacity="0.2" d="M28.5 7.125H9.5v23.75h19V7.125z" fill="currentColor" />
                          <path
                            d="M28.5 7.125H9.5v23.75h19V7.125zM14.25 11.875h9.5M14.25 16.625h9.5M14.25 21.375h9.5M14.25 26.125h4.75"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Blog Posts</p>
                  <h4 class="text-warning mb-0">{{ $stats['total_blogs'] }}</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-4 ps-md-4 ps-lg-6">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div>
                  <h5 class="mb-1">System Activity</h5>
                  <p class="mb-9">Weekly overview</p>
                </div>
                <div class="time-spending-chart">
                  <h4 class="mb-2">{{ $stats['active_users'] }}<span class="text-body"> Active</span></h4>
                  <span class="badge bg-label-success">{{ $stats['active_products'] }} Products Live</span>
                </div>
              </div>
              <div id="activityChart"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- Welcome Section End -->

      <!-- Analytics Dashboard -->
      <div class="row mb-6 g-6">
        <!-- Website Analytics -->
        <div class="col-12 col-xl-8">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0 me-2">Website Analytics</h5>
              <small class="text-muted">Real-time visitor tracking</small>
            </div>
            <div class="card-body">
              <!-- Analytics Stats Row -->
              <div class="row g-4 mb-4">
                <div class="col-6 col-lg-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial bg-label-primary rounded">
                        <i class="icon-base ti tabler-eye icon-20px"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ number_format($analytics['total_visits'] ?? 0) }}</h6>
                      <small class="text-muted">Total Visits</small>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-lg-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial bg-label-success rounded">
                        <i class="icon-base ti tabler-users icon-20px"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ number_format($analytics['today_visits'] ?? 0) }}</h6>
                      <small class="text-muted">Today's Visits</small>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-lg-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial bg-label-info rounded">
                        <i class="icon-base ti tabler-user-check icon-20px"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ number_format($analytics['unique_visitors_today'] ?? 0) }}</h6>
                      <small class="text-muted">Unique Today</small>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-lg-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial bg-label-warning rounded">
                        <i class="icon-base ti tabler-trending-up icon-20px"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ $analytics['bounce_rate'] ?? 0 }}%</h6>
                      <small class="text-muted">Bounce Rate</small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Visits Trend Chart -->
              <div class="mb-4">
                <h6 class="mb-3">7-Day Visits Trend</h6>
                <div id="visitsTrendChart" style="min-height: 200px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Forms & Partners Stats -->
        <div class="col-12 col-xl-4">
          <div class="row g-6">
            <!-- Contact Forms Card -->
            <div class="col-12">
              <div class="card">
                <div class="card-header pb-0">
                  <h6 class="card-title mb-3">Contact Forms</h6>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-primary">{{ $contactStats['total'] ?? 0 }}</h5>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-success">{{ $contactStats['new'] ?? 0 }}</h5>
                        <small class="text-muted">New</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-info">{{ $contactStats['today'] ?? 0 }}</h5>
                        <small class="text-muted">Today</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-warning">{{ $contactStats['replied'] ?? 0 }}</h5>
                        <small class="text-muted">Replied</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Partner Registration Card -->
            <div class="col-12">
              <div class="card">
                <div class="card-header pb-0">
                  <h6 class="card-title mb-3">Partner Registrations</h6>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-primary">{{ $partnerStats['total'] ?? 0 }}</h5>
                        <small class="text-muted">Total</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-success">{{ $partnerStats['approved'] ?? 0 }}</h5>
                        <small class="text-muted">Approved</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-info">{{ $partnerStats['new'] ?? 0 }}</h5>
                        <small class="text-muted">Pending</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h5 class="mb-1 text-warning">{{ $partnerStats['under_review'] ?? 0 }}</h5>
                        <small class="text-muted">Review</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Pages and Device Analytics -->
      <div class="row mb-6 g-6">
        <!-- Top Pages -->
        <div class="col-12 col-xl-6">
          <div class="card">
            <div class="card-header">
              <h6 class="card-title mb-0">Top Pages</h6>
            </div>
            <div class="card-body">
              @if(isset($analytics['top_pages']) && count($analytics['top_pages']) > 0)
                @foreach($analytics['top_pages'] as $page)
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h6 class="mb-0">{{ Str::limit($page['page_title'] ?: 'Untitled', 30) }}</h6>
                      <small class="text-muted">{{ Str::limit($page['url'], 50) }}</small>
                    </div>
                    <div class="text-end">
                      <span class="badge bg-label-primary">{{ number_format($page['visits']) }}</span>
                    </div>
                  </div>
                @endforeach
              @else
                <p class="text-muted text-center mb-0">No page visits recorded yet</p>
              @endif
            </div>
          </div>
        </div>

        <!-- Device Analytics -->
        <div class="col-12 col-xl-6">
          <div class="card">
            <div class="card-header">
              <h6 class="card-title mb-0">Device Breakdown</h6>
            </div>
            <div class="card-body">
              @if(isset($analytics['device_breakdown']) && count($analytics['device_breakdown']) > 0)
                <div id="deviceChart" style="min-height: 200px;"></div>
                <div class="mt-3">
                  @foreach($analytics['device_breakdown'] as $device)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="fw-medium">{{ $device['device'] }}</span>
                      <span class="badge bg-label-info">{{ number_format($device['count']) }}</span>
                    </div>
                  @endforeach
                </div>
              @else
                <div id="deviceChart" style="min-height: 200px;"></div>
                <p class="text-muted text-center mb-0 mt-3">No device data available</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="row mb-6 g-6">
        <!-- Recent Visits -->
        <div class="col-12 col-xl-6">
          <div class="card">
            <div class="card-header">
              <h6 class="card-title mb-0">Recent Visits</h6>
            </div>
            <div class="card-body">
              @if($recent_visits && $recent_visits->count() > 0)
                @foreach($recent_visits as $visit)
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="min-width: 0; flex: 1;">
                      <h6 class="mb-0 text-truncate">{{ $visit->page_title ?: 'Untitled' }}</h6>
                      <small class="text-muted">
                        <i class="ti ti-device-{{ strtolower($visit->device_type ?? 'desktop') === 'mobile' ? 'mobile' : 'desktop' }} me-1"></i>
                        {{ ucfirst($visit->device_type ?: 'Desktop') }}
                      </small>
                    </div>
                    <div class="text-end ms-3 flex-shrink-0">
                      <small class="text-muted d-block">{{ $visit->visited_at->diffForHumans() }}</small>
                      @if($visit->is_unique_visitor)
                        <span class="badge bg-label-success">New</span>
                      @else
                        <span class="badge bg-label-secondary">Return</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              @else
                <p class="text-muted text-center mb-0">No visits recorded yet</p>
              @endif
            </div>
          </div>
        </div>

        <!-- Recent Contact Forms -->
        <div class="col-12 col-xl-6">
          <div class="card">
            <div class="card-header">
              <h6 class="card-title mb-0">Recent Contact Forms</h6>
            </div>
            <div class="card-body">
              @if($recent_contacts && $recent_contacts->count() > 0)
                @foreach($recent_contacts as $contact)
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h6 class="mb-0">{{ $contact->name }}</h6>
                      <small class="text-muted">{{ $contact->email }} •
                        {{ ucfirst($contact->form_type ?: 'contact') }}</small>
                    </div>
                    <div class="text-end">
                      <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small>
                      <br><span
                        class="badge bg-label-{{ $contact->status === 'new' ? 'success' : ($contact->status === 'replied' ? 'info' : 'warning') }}">{{ ucfirst($contact->status) }}</span>
                    </div>
                  </div>
                @endforeach
              @else
                <p class="text-muted text-center mb-0">No contact forms submitted yet</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics and Management -->
      <div class="row mb-6 g-6">
        <div class="col-12 col-xl-8">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0 me-2">Content Management Overview</h5>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="contentOverview" data-bs-toggle="dropdown" aria-haspopup="true"
                  aria-expanded="false">
                  <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="contentOverview">
                  <a class="dropdown-item" href="javascript:void(0);">Refresh Data</a>
                  <a class="dropdown-item" href="{{ route('admin.users.index') }}">Manage Users</a>
                  <a class="dropdown-item" href="{{ route('admin.products.index') }}">Manage Products</a>
                  <a class="dropdown-item" href="{{ route('admin.blogs.index') }}">Manage Blogs</a>
                </div>
              </div>
            </div>
            <div class="card-body row g-3">
              <div class="col-md-8">
                <div id="contentChart"></div>
              </div>
              <div class="col-md-4 d-flex justify-content-around align-items-center">
                <div>
                  <div class="d-flex align-items-baseline">
                    <span class="text-primary me-2"><i class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                    <div>
                      <p class="mb-0">Users</p>
                      <h5>{{ $stats['total_users'] }}</h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-baseline my-12">
                    <span class="text-info me-2"><i class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                    <div>
                      <p class="mb-0">Products</p>
                      <h5>{{ $stats['total_products'] }}</h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <span class="text-warning me-2"><i class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                    <div>
                      <p class="mb-0">Blogs</p>
                      <h5>{{ $stats['total_blogs'] }}</h5>
                    </div>
                  </div>
                </div>
                <div>
                  <div class="d-flex align-items-baseline">
                    <span class="text-success me-2"><i class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                    <div>
                      <p class="mb-0">Categories</p>
                      <h5>{{ $stats['total_categories'] }}</h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-baseline my-12">
                    <span class="text-danger me-2"><i class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                    <div>
                      <p class="mb-0">Published</p>
                      <h5>{{ $stats['published_blogs'] }}</h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <span class="text-secondary me-2"><i class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                    <div>
                      <p class="mb-0">Active</p>
                      <h5>{{ $stats['active_users'] }}</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-xl-4 col-md-6">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <div class="card-title mb-0">
                <h5 class="m-0 me-2">Recent Users</h5>
              </div>
              <div class="dropdown">
                <button class="btn text-body-secondary p-0" type="button" id="recentUsers" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentUsers">
                  <a class="dropdown-item" href="{{ route('admin.users.index') }}">View All</a>
                  <a class="dropdown-item" href="{{ route('admin.users.create') }}">Add New User</a>
                </div>
              </div>
            </div>
            <div class="px-5 py-4 border border-start-0 border-end-0">
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-uppercase">Name</p>
                <p class="mb-0 text-uppercase">Role</p>
              </div>
            </div>
            <div class="card-body">
              @if($recent_users->count() > 0)
                @foreach($recent_users as $user)
                  <div class="d-flex justify-content-between align-items-center mb-6">
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar me-4">
                        @if($user->profile_image)
                          <img src="{{ Storage::url($user->profile_image) }}" alt="Avatar" class="rounded-circle" />
                        @else
                          <span class="avatar-initial bg-label-primary rounded-circle">{{ substr($user->name, 0, 2) }}</span>
                        @endif
                      </div>
                      <div style="min-width: 0;">
                        <h6 class="mb-0 text-truncate">{{ $user->name }}</h6>
                        <small class="text-truncate text-body d-block" style="max-width: 150px;">{{ $user->email }}</small>
                      </div>
                    </div>
                    <div class="text-end flex-shrink-0 ms-2">
                      @if($user->role)
                        <span class="badge bg-label-info text-wrap" style="max-width: 100px; white-space: normal; font-size: 0.7rem;">{{ $user->role->display_name }}</span>
                      @else
                        <span class="badge bg-label-secondary">User</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              @else
                <p class="text-muted text-center">No recent users</p>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12 col-xl-4 col-md-6">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0 me-2">Latest Content</h5>
              <div class="dropdown">
                <button class="btn text-body-secondary p-0" type="button" id="latestContent" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="icon-base ti tabler-dots-vertical icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="latestContent">
                  <a class="dropdown-item" href="{{ route('admin.products.index') }}">View Products</a>
                  <a class="dropdown-item" href="{{ route('admin.blogs.index') }}">View Blogs</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                @if($recent_products->count() > 0)
                  @foreach($recent_products->take(2) as $product)
                    <li class="d-flex mb-6 align-items-center">
                      <div class="avatar flex-shrink-0 me-4">
                        <span class="avatar-initial rounded bg-label-info"><i
                            class="icon-base ti tabler-package icon-lg"></i></span>
                      </div>
                      <div class="row w-100 align-items-center">
                        <div class="col-sm-8 col-md-12 col-xxl-8 mb-1 mb-sm-0 mb-md-1 mb-xxl-0">
                          <h6 class="mb-0">{{ Str::limit($product->title, 25) }}</h6>
                        </div>
                        <div class="col-sm-4 col-md-12 col-xxl-4 d-flex justify-content-xxl-end">
                          <div class="badge bg-label-{{ $product->status ? 'success' : 'warning' }}">
                            {{ $product->status ? 'Active' : 'Draft' }}</div>
                        </div>
                      </div>
                    </li>
                  @endforeach
                @endif
                @if($recent_blogs->count() > 0)
                  @foreach($recent_blogs->take(2) as $blog)
                    <li class="d-flex mb-6 align-items-center">
                      <div class="avatar flex-shrink-0 me-4">
                        <span class="avatar-initial rounded bg-label-warning"><i
                            class="icon-base ti tabler-article icon-lg"></i></span>
                      </div>
                      <div class="row w-100 align-items-center">
                        <div class="col-sm-8 col-md-12 col-xxl-8 mb-1 mb-sm-0 mb-md-1 mb-xxl-0">
                          <h6 class="mb-0">{{ Str::limit($blog->title, 25) }}</h6>
                        </div>
                        <div class="col-sm-4 col-md-12 col-xxl-4 d-flex justify-content-xxl-end">
                          <div class="badge bg-label-{{ $blog->status ? 'success' : 'warning' }}">
                            {{ $blog->status ? 'Published' : 'Draft' }}</div>
                        </div>
                      </div>
                    </li>
                  @endforeach
                @endif
                @if($recent_products->count() === 0 && $recent_blogs->count() === 0)
                  <li class="text-center text-muted">No recent content</li>
                @endif
              </ul>
            </div>
          </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0 me-2">Quick Actions</h5>
              <small class="text-muted">Manage your content efficiently</small>
            </div>
            <div class="card-body">
              <div class="row g-6">
                <div class="col-12 col-sm-6 col-lg-3">
                  <div class="card bg-primary text-white h-100">
                    <div class="card-body d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-4">
                        <span class="avatar-initial rounded bg-white text-primary">
                          <i class="icon-base ti tabler-user-plus icon-24px"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-2 text-white">Add User</h6>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-light">Create</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                  <div class="card bg-info text-white h-100">
                    <div class="card-body d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-4">
                        <span class="avatar-initial rounded bg-white text-info">
                          <i class="icon-base ti tabler-package icon-24px"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-2 text-white">Add Product</h6>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-outline-light">Create</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                  <div class="card bg-warning text-white h-100">
                    <div class="card-body d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-4">
                        <span class="avatar-initial rounded bg-white text-warning">
                          <i class="icon-base ti tabler-article icon-24px"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-2 text-white">Write Blog</h6>
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-outline-light">Create</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                  <div class="card bg-success text-white h-100">
                    <div class="card-body d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-4">
                        <span class="avatar-initial rounded bg-white text-success">
                          <i class="icon-base ti tabler-category icon-24px"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-2 text-white">Add Category</h6>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-outline-light">Create</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->
  </div>
  <!-- / Content wrapper -->
@endsection

@push('vendor-scripts')
  <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('scripts')
  <script>
    $(document).ready(function () {
      // Auto-refresh stats every 5 minutes
      setInterval(function () {
        $.ajax({
          url: '{{ route("admin.stats") }}',
          method: 'GET',
          success: function (data) {
            console.log('Stats updated');
          }
        });
      }, 300000);

      // Initialize charts if ApexCharts is available
      if (typeof ApexCharts !== 'undefined') {
        // Activity Chart
        const activityChartConfig = {
          chart: {
            height: 100,
            type: 'donut',
            toolbar: { show: false }
          },
          series: [{{ $stats['active_users'] }}, {{ $stats['total_users'] - $stats['active_users'] }}],
          labels: ['Active', 'Inactive'],
          colors: ['#28a745', '#dc3545'],
          legend: { show: false },
          dataLabels: { enabled: false }
        };

        if (document.querySelector('#activityChart')) {
          const activityChart = new ApexCharts(document.querySelector('#activityChart'), activityChartConfig);
          activityChart.render();
        }

        // Content Chart
        const contentChartConfig = {
          chart: {
            height: 300,
            type: 'bar',
            toolbar: { show: false }
          },
          plotOptions: {
            bar: {
              horizontal: true,
              borderRadius: 8
            }
          },
          series: [{
            data: [{{ $stats['total_users'] }}, {{ $stats['total_products'] }}, {{ $stats['total_blogs'] }}, {{ $stats['total_categories'] }}]
          }],
          xaxis: {
            categories: ['Users', 'Products', 'Blogs', 'Categories']
          },
          colors: ['#007bff', '#17a2b8', '#ffc107', '#28a745'],
          dataLabels: { enabled: false }
        };

        if (document.querySelector('#contentChart')) {
          const contentChart = new ApexCharts(document.querySelector('#contentChart'), contentChartConfig);
          contentChart.render();
        }

        // Visits Trend Chart
        @if(isset($analytics['visits_trend']) && count($analytics['visits_trend']) > 0)
          const visitsTrendConfig = {
            chart: {
              height: 300,
              type: 'area',
              toolbar: { show: false }
            },
            series: [{
              name: 'Visits',
              data: {!! json_encode(array_column($analytics['visits_trend'], 'visits')) !!}
            }],
            xaxis: {
              categories: {!! json_encode(array_column($analytics['visits_trend'], 'date')) !!},
              labels: {
                style: {
                  fontSize: '12px'
                }
              }
            },
            yaxis: {
              labels: {
                formatter: function (val) {
                  return Math.floor(val)
                }
              }
            },
            colors: ['#007bff'],
            stroke: {
              curve: 'smooth',
              width: 3
            },
            fill: {
              type: 'gradient',
              gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
              }
            },
            dataLabels: { enabled: false },
            grid: {
              show: true,
              borderColor: '#e0e6ed',
              strokeDashArray: 5
            }
          };

          if (document.querySelector('#visitsTrendChart')) {
            const visitsTrendChart = new ApexCharts(document.querySelector('#visitsTrendChart'), visitsTrendConfig);
            visitsTrendChart.render();
          }
        @endif

          // Device Breakdown Chart
          @if(isset($analytics['device_breakdown']) && count($analytics['device_breakdown']) > 0)
            const deviceChartConfig = {
              chart: {
                height: 200,
                type: 'donut',
                toolbar: { show: false }
              },
              series: {!! json_encode(collect($analytics['device_breakdown'])->pluck('count')->all()) !!},
              labels: {!! json_encode(collect($analytics['device_breakdown'])->pluck('device')->all()) !!},
              colors: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'],
              legend: {
                position: 'bottom',
                fontSize: '12px'
              },
              plotOptions: {
                pie: {
                  donut: {
                    size: '60%'
                  }
                }
              },
              dataLabels: {
                enabled: true,
                formatter: function (val) {
                  return Math.round(val) + '%'
                }
              }
            };

            if (document.querySelector('#deviceChart')) {
              const deviceChart = new ApexCharts(document.querySelector('#deviceChart'), deviceChartConfig);
              deviceChart.render();
            }
          @endif
      }
    });
  </script>
@endpush