<aside id="layout-menu" class="layout-menu menu-vertical menu">
  <div class="app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="CMS Admin Logo" class="w-px-40 h-auto"
          style="max-height: 40px;">
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-3">
        CMS Admin
      </span>
    </a>


    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
      <i class="icon-base ti tabler-x d-block d-xl-none"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    @if(!auth()->user()->role || in_array('dashboard', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin') || request()->is('admin/dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-smart-home"></i>
          <div data-i18n="Dashboard">Dashboard</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('clients', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/clients*') ? 'active' : '' }}">
        <a href="{{ route('admin.clients.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-users-group"></i>
          <div data-i18n="Our Clients">Our Clients</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('certifications', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/certifications*') ? 'active' : '' }}">
        <a href="{{ route('admin.certifications.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-certificate"></i>
          <div data-i18n="Certifications">Certifications</div>
        </a>
      </li>
    @endif

    <!-- User Management -->
    @php
      $userPages = ['users', 'roles'];
      $hasUserAccess = !auth()->user()->role ||
        array_intersect($userPages, auth()->user()->role->page_access ?? []) ||
        auth()->user()->role->name === 'super_admin';
    @endphp
    @if($hasUserAccess)
      <li class="menu-header small">
        <span class="menu-header-text" data-i18n="User Management">User Management</span>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('users', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/users*') ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-users"></i>
          <div data-i18n="Users">Users</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('roles', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/roles*') ? 'active' : '' }}">
        <a href="{{ route('admin.roles.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-user-check"></i>
          <div data-i18n="Roles & Permissions">Roles & Permissions</div>
        </a>
      </li>
    @endif

    <!-- Content Management -->
    @php
      $contentPages = ['categories', 'products', 'blogs', 'pages', 'about-us', 'contact-info', 'support-tickets', 'testimonials', 'faqs', 'solutions', 'software', 'integration-modules', 'galary', 'popups', 'hero-slides', 'job-openings'];
      $hasContentAccess = !auth()->user()->role ||
        array_intersect($contentPages, auth()->user()->role->page_access ?? []) ||
        auth()->user()->role->name === 'super_admin';
    @endphp
    @if($hasContentAccess)
      <li class="menu-header small">
        <span class="menu-header-text" data-i18n="Content Management">Content Management</span>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('categories', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/categories*') ? 'active' : '' }}">
        <a href="{{ route('admin.categories.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-category"></i>
          <div data-i18n="Categories">Categories</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('products', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/products*') ? 'active' : '' }}">
        <a href="{{ route('admin.products.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-package"></i>
          <div data-i18n="Products">Products</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('blogs', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/blogs*') ? 'active' : '' }}">
        <a href="{{ route('admin.blogs.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-article"></i>
          <div data-i18n="Blogs">Blogs</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('pages', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/pages*') ? 'active' : '' }}">
        <a href="{{ route('admin.pages.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-file-text"></i>
          <div data-i18n="Pages">Pages</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('about-us', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/about-us*') ? 'active' : '' }}">
        <a href="{{ route('admin.about-us.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-info-circle"></i>
          <div data-i18n="About Us">About Us</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('contact-info', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/contact-info*') ? 'active' : '' }}">
        <a href="{{ route('admin.contact-info.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-phone"></i>
          <div data-i18n="Contact Info">Contact Info</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('support-tickets', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/support-tickets*') ? 'active' : '' }}">
        <a href="{{ route('admin.support-tickets.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-ticket"></i>
          <div data-i18n="Support Tickets">Support Tickets</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('testimonials', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/testimonials*') ? 'active' : '' }}">
        <a href="{{ route('admin.testimonials.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-message-star"></i>
          <div data-i18n="Testimonials">Testimonials</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('faqs', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/faqs*') ? 'active' : '' }}">
        <a href="{{ route('admin.faqs.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-help"></i>
          <div data-i18n="FAQs">FAQs</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('solutions', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/solutions*') ? 'active' : '' }}">
        <a href="{{ route('admin.solutions.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-bulb"></i>
          <div data-i18n="Solutions">Solutions</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('services', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/services*') ? 'active' : '' }}">
        <a href="{{ route('admin.services.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-tools"></i>
          <div data-i18n="Services">Services</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('software', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/software*') ? 'active' : '' }}">
        <a href="{{ route('admin.software.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-download"></i>
          <div data-i18n="Software">Software</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('integration-modules', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/integration-modules*') ? 'active' : '' }}">
        <a href="{{ route('admin.integration-modules.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-plug"></i>
          <div data-i18n="Integration Modules">Integration Modules</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('galary', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/galary*') ? 'active' : '' }}">
        <a href="{{ route('admin.galary.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-photo"></i>
          <div data-i18n="Galary">Galary</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('popups', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/popups*') ? 'active' : '' }}">
        <a href="{{ route('admin.popups.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-window"></i>
          <div data-i18n="Popups">Popups</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('hero-slides', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/hero-slides*') ? 'active' : '' }}">
        <a href="{{ route('admin.hero-slides.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-slideshow"></i>
          <div data-i18n="Hero Slides">Hero Slides</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('job-openings', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/job-openings*') ? 'active' : '' }}">
        <a href="{{ route('admin.job-openings.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-briefcase"></i>
          <div data-i18n="Job Openings">Job Openings</div>
        </a>
      </li>
    @endif

    <!-- Customer Inquiries -->
    @php
      $inquiryPages = ['partner-queries', 'contact-queries', 'sales-requirement-queries'];
      $hasInquiryAccess = !auth()->user()->role ||
        array_intersect($inquiryPages, auth()->user()->role->page_access ?? []) ||
        auth()->user()->role->name === 'super_admin';
    @endphp
    @if($hasInquiryAccess)
      <li class="menu-header small">
        <span class="menu-header-text" data-i18n="Customer Inquiries">Customer Inquiries</span>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('partner-queries', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/partner-queries*') ? 'active' : '' }}">
        <a href="{{ route('admin.partner-queries.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-printer"></i>
          <div data-i18n="Partner Queries">Partner Queries</div>
          @if(isset($pendingPartnerQueries) && $pendingPartnerQueries > 0)
            <div class="badge text-bg-warning rounded-pill ms-auto">{{ $pendingPartnerQueries }}</div>
          @endif
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('contact-queries', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/contact-queries*') ? 'active' : '' }}">
        <a href="{{ route('admin.contact-queries.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-mail"></i>
          <div data-i18n="Contact Queries">Contact Queries</div>
          @if(isset($unreadContactQueries) && $unreadContactQueries > 0)
            <div class="badge text-bg-danger rounded-pill ms-auto">{{ $unreadContactQueries }}</div>
          @endif
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('sales-requirement-queries', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/sales-requirement-queries*') ? 'active' : '' }}">
        <a href="{{ route('admin.sales-requirement-queries.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-list-details"></i>
          <div data-i18n="Sales Requirement Queries">Sales Requirement Queries</div>
        </a>
      </li>
    @endif

    <!-- System -->
    @php
      $systemPages = ['header-footer', 'home-sections', 'extras', 'profile'];
      $hasSystemAccess = !auth()->user()->role ||
        array_intersect($systemPages, auth()->user()->role->page_access ?? []) ||
        auth()->user()->role->name === 'super_admin';
    @endphp
    @if($hasSystemAccess)
      <li class="menu-header small">
        <span class="menu-header-text" data-i18n="System">System</span>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('header-footer', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/header-footer*') ? 'active' : '' }}">
        <a href="{{ route('admin.header-footer.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-layout-navbar"></i>
          <div data-i18n="Header & Footer">Header & Footer</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('home-sections', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/home-sections*') ? 'active' : '' }}">
        <a href="{{ route('admin.home-sections.edit') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-layout-grid"></i>
          <div data-i18n="Home Sections">Home Sections</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('extras', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/extras*') ? 'active' : '' }}">
        <a href="{{ route('admin.extras.index') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-settings"></i>
          <div data-i18n="Extras & Settings">Extras & Settings</div>
        </a>
      </li>
    @endif

    @if(!auth()->user()->role || in_array('profile', auth()->user()->role->page_access ?? []) || auth()->user()->role->name === 'super_admin')
      <li class="menu-item {{ request()->is('admin/profile*') ? 'active' : '' }}">
        <a href="{{ route('admin.profile') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-user"></i>
          <div data-i18n="Profile">Profile</div>
        </a>
      </li>
    @endif

    <li class="menu-item">
      <form action="{{ route('logout') }}" method="POST" style="display: none;" id="logout-form">
        @csrf
      </form>
      <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-link">
        <i class="menu-icon icon-base ti tabler-logout"></i>
        <div data-i18n="Logout">Logout</div>
      </a>
    </li>
  </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
    <i class="ti tabler-menu icon-base"></i>
    <i class="ti tabler-chevron-right icon-base"></i>
  </a>
</div>
