<aside id="layout-menu" class="layout-menu menu-vertical menu">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <span class="text-primary">
          <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
              fill="currentColor" />
            <path
              opacity="0.06"
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
              fill="#161616" />
            <path
              opacity="0.06"
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
              fill="#161616" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
              fill="currentColor" />
          </svg>
        </span>
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-3">Vuexy</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
      <i class="icon-base ti tabler-x d-block d-xl-none"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{ request()->is('dashboard*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-smart-home"></i>
        <div data-i18n="Dashboards">Dashboards</div>
        <div class="badge text-bg-danger rounded-pill ms-auto">5</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
          <a href="{{ url('/') }}" class="menu-link">
            <div data-i18n="Analytics">Analytics</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('dashboards-crm') ? 'active' : '' }}">
          <a href="{{ url('dashboards-crm') }}" class="menu-link">
            <div data-i18n="CRM">CRM</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-dashboard') ? 'active' : '' }}">
          <a href="{{ url('app-ecommerce-dashboard') }}" class="menu-link">
            <div data-i18n="eCommerce">eCommerce</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-logistics-dashboard') ? 'active' : '' }}">
          <a href="{{ url('app-logistics-dashboard') }}" class="menu-link">
            <div data-i18n="Logistics">Logistics</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-academy-dashboard') ? 'active' : '' }}">
          <a href="{{ url('app-academy-dashboard') }}" class="menu-link">
            <div data-i18n="Academy">Academy</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Layouts -->
    <li class="menu-item {{ request()->is('layouts*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
        <div data-i18n="Layouts">Layouts</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('layouts-collapsed-menu') }}" class="menu-link">
            <div data-i18n="Collapsed menu">Collapsed menu</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-content-navbar') }}" class="menu-link">
            <div data-i18n="Content navbar">Content navbar</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-content-navbar-with-sidebar') }}" class="menu-link">
            <div data-i18n="Content nav + Sidebar">Content nav + Sidebar</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="../horizontal-menu-template/" class="menu-link" target="_blank">
            <div data-i18n="Horizontal">Horizontal</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-without-menu') }}" class="menu-link">
            <div data-i18n="Without menu">Without menu</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-without-navbar') }}" class="menu-link">
            <div data-i18n="Without navbar">Without navbar</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-fluid') }}" class="menu-link">
            <div data-i18n="Fluid">Fluid</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-container') }}" class="menu-link">
            <div data-i18n="Container">Container</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('layouts-blank') }}" class="menu-link">
            <div data-i18n="Blank">Blank</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Front Pages -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-files"></i>
        <div data-i18n="Front Pages">Front Pages</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="../front-pages/landing-page" class="menu-link" target="_blank">
            <div data-i18n="Landing">Landing</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="../front-pages/pricing-page" class="menu-link" target="_blank">
            <div data-i18n="Pricing">Pricing</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="../front-pages/payment-page" class="menu-link" target="_blank">
            <div data-i18n="Payment">Payment</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="../front-pages/checkout-page" class="menu-link" target="_blank">
            <div data-i18n="Checkout">Checkout</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="../front-pages/help-center-landing" class="menu-link" target="_blank">
            <div data-i18n="Help Center">Help Center</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Apps & Pages -->
    <li class="menu-header small">
      <span class="menu-header-text" data-i18n="Apps & Pages">Apps &amp; Pages</span>
    </li>
    <li class="menu-item {{ request()->is('app-email') ? 'active' : '' }}">
      <a href="{{ url('app-email') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-mail"></i>
        <div data-i18n="Email">Email</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('app-chat') ? 'active' : '' }}">
      <a href="{{ url('app-chat') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-messages"></i>
        <div data-i18n="Chat">Chat</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('app-calendar') ? 'active' : '' }}">
      <a href="{{ url('app-calendar') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-calendar"></i>
        <div data-i18n="Calendar">Calendar</div>
      </a>
    </li>
    <li class="menu-item {{ request()->is('app-kanban') ? 'active' : '' }}">
      <a href="{{ url('app-kanban') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-layout-kanban"></i>
        <div data-i18n="Kanban">Kanban</div>
      </a>
    </li>
    
    <!-- e-commerce-app menu start -->
    <li class="menu-item {{ request()->is('app-ecommerce*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-shopping-cart"></i>
        <div data-i18n="eCommerce">eCommerce</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('app-ecommerce-dashboard') ? 'active' : '' }}">
          <a href="{{ url('app-ecommerce-dashboard') }}" class="menu-link">
            <div data-i18n="Dashboard">Dashboard</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-product*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Products">Products</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->is('app-ecommerce-product-list') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-product-list') }}" class="menu-link">
                <div data-i18n="Product List">Product List</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-product-add') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-product-add') }}" class="menu-link">
                <div data-i18n="Add Product">Add Product</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-category-list') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-category-list') }}" class="menu-link">
                <div data-i18n="Category List">Category List</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-order*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Order">Order</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->is('app-ecommerce-order-list') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-order-list') }}" class="menu-link">
                <div data-i18n="Order List">Order List</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-order-details') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-order-details') }}" class="menu-link">
                <div data-i18n="Order Details">Order Details</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-customer*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Customer">Customer</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->is('app-ecommerce-customer-all') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-customer-all') }}" class="menu-link">
                <div data-i18n="All Customers">All Customers</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-customer-details*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Customer Details">Customer Details</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->is('app-ecommerce-customer-details-overview') ? 'active' : '' }}">
                  <a href="{{ url('app-ecommerce-customer-details-overview') }}" class="menu-link">
                    <div data-i18n="Overview">Overview</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('app-ecommerce-customer-details-security') ? 'active' : '' }}">
                  <a href="{{ url('app-ecommerce-customer-details-security') }}" class="menu-link">
                    <div data-i18n="Security">Security</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('app-ecommerce-customer-details-billing') ? 'active' : '' }}">
                  <a href="{{ url('app-ecommerce-customer-details-billing') }}" class="menu-link">
                    <div data-i18n="Address & Billing">Address & Billing</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('app-ecommerce-customer-details-notifications') ? 'active' : '' }}">
                  <a href="{{ url('app-ecommerce-customer-details-notifications') }}" class="menu-link">
                    <div data-i18n="Notifications">Notifications</div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-manage-reviews') ? 'active' : '' }}">
          <a href="{{ url('app-ecommerce-manage-reviews') }}" class="menu-link">
            <div data-i18n="Manage Reviews">Manage Reviews</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-referral') ? 'active' : '' }}">
          <a href="{{ url('app-ecommerce-referral') }}" class="menu-link">
            <div data-i18n="Referrals">Referrals</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-ecommerce-settings*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Settings">Settings</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->is('app-ecommerce-settings-detail') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-settings-detail') }}" class="menu-link">
                <div data-i18n="Store Details">Store Details</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-settings-payments') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-settings-payments') }}" class="menu-link">
                <div data-i18n="Payments">Payments</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-settings-checkout') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-settings-checkout') }}" class="menu-link">
                <div data-i18n="Checkout">Checkout</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-settings-shipping') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-settings-shipping') }}" class="menu-link">
                <div data-i18n="Shipping & Delivery">Shipping & Delivery</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-settings-locations') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-settings-locations') }}" class="menu-link">
                <div data-i18n="Locations">Locations</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-ecommerce-settings-notifications') ? 'active' : '' }}">
              <a href="{{ url('app-ecommerce-settings-notifications') }}" class="menu-link">
                <div data-i18n="Notifications">Notifications</div>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
    <!-- e-commerce-app menu end -->
    
    <!-- Academy menu start -->
    <li class="menu-item {{ request()->is('app-academy*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-book"></i>
        <div data-i18n="Academy">Academy</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('app-academy-dashboard') ? 'active' : '' }}">
          <a href="{{ url('app-academy-dashboard') }}" class="menu-link">
            <div data-i18n="Dashboard">Dashboard</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-academy-course') ? 'active' : '' }}">
          <a href="{{ url('app-academy-course') }}" class="menu-link">
            <div data-i18n="My Course">My Course</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-academy-course-details') ? 'active' : '' }}">
          <a href="{{ url('app-academy-course-details') }}" class="menu-link">
            <div data-i18n="Course Details">Course Details</div>
          </a>
        </li>
      </ul>
    </li>
    <!-- Academy menu end -->
    
    <li class="menu-item {{ request()->is('app-logistics*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-truck"></i>
        <div data-i18n="Logistics">Logistics</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('app-logistics-dashboard') ? 'active' : '' }}">
          <a href="{{ url('app-logistics-dashboard') }}" class="menu-link">
            <div data-i18n="Dashboard">Dashboard</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-logistics-fleet') ? 'active' : '' }}">
          <a href="{{ url('app-logistics-fleet') }}" class="menu-link">
            <div data-i18n="Fleet">Fleet</div>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menu-item {{ request()->is('app-invoice*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-file-dollar"></i>
        <div data-i18n="Invoice">Invoice</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('app-invoice-list') ? 'active' : '' }}">
          <a href="{{ url('app-invoice-list') }}" class="menu-link">
            <div data-i18n="List">List</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-invoice-preview') ? 'active' : '' }}">
          <a href="{{ url('app-invoice-preview') }}" class="menu-link">
            <div data-i18n="Preview">Preview</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-invoice-edit') ? 'active' : '' }}">
          <a href="{{ url('app-invoice-edit') }}" class="menu-link">
            <div data-i18n="Edit">Edit</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-invoice-add') ? 'active' : '' }}">
          <a href="{{ url('app-invoice-add') }}" class="menu-link">
            <div data-i18n="Add">Add</div>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menu-item {{ request()->is('app-user*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-users"></i>
        <div data-i18n="Users">Users</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('app-user-list') ? 'active' : '' }}">
          <a href="{{ url('app-user-list') }}" class="menu-link">
            <div data-i18n="List">List</div>
          </a>
        </li>

        <li class="menu-item {{ request()->is('app-user-view*') ? 'active open' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="View">View</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->is('app-user-view-account') ? 'active' : '' }}">
              <a href="{{ url('app-user-view-account') }}" class="menu-link">
                <div data-i18n="Account">Account</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-user-view-security') ? 'active' : '' }}">
              <a href="{{ url('app-user-view-security') }}" class="menu-link">
                <div data-i18n="Security">Security</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-user-view-billing') ? 'active' : '' }}">
              <a href="{{ url('app-user-view-billing') }}" class="menu-link">
                <div data-i18n="Billing & Plans">Billing & Plans</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-user-view-notifications') ? 'active' : '' }}">
              <a href="{{ url('app-user-view-notifications') }}" class="menu-link">
                <div data-i18n="Notifications">Notifications</div>
              </a>
            </li>
            <li class="menu-item {{ request()->is('app-user-view-connections') ? 'active' : '' }}">
              <a href="{{ url('app-user-view-connections') }}" class="menu-link">
                <div data-i18n="Connections">Connections</div>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
    
    <li class="menu-item {{ request()->is('app-access*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-settings"></i>
        <div data-i18n="Roles & Permissions">Roles & Permissions</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('app-access-roles') ? 'active' : '' }}">
          <a href="{{ url('app-access-roles') }}" class="menu-link">
            <div data-i18n="Roles">Roles</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('app-access-permission') ? 'active' : '' }}">
          <a href="{{ url('app-access-permission') }}" class="menu-link">
            <div data-i18n="Permission">Permission</div>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-file"></i>
        <div data-i18n="Pages">Pages</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="User Profile">User Profile</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('pages-profile-user') }}" class="menu-link">
                <div data-i18n="Profile">Profile</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-profile-teams') }}" class="menu-link">
                <div data-i18n="Teams">Teams</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-profile-projects') }}" class="menu-link">
                <div data-i18n="Projects">Projects</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-profile-connections') }}" class="menu-link">
                <div data-i18n="Connections">Connections</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Account Settings">Account Settings</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('pages-account-settings-account') }}" class="menu-link">
                <div data-i18n="Account">Account</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-account-settings-security') }}" class="menu-link">
                <div data-i18n="Security">Security</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-account-settings-billing') }}" class="menu-link">
                <div data-i18n="Billing & Plans">Billing & Plans</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-account-settings-notifications') }}" class="menu-link">
                <div data-i18n="Notifications">Notifications</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-account-settings-connections') }}" class="menu-link">
                <div data-i18n="Connections">Connections</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="{{ url('pages-faq') }}" class="menu-link">
            <div data-i18n="FAQ">FAQ</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('pages-pricing') }}" class="menu-link">
            <div data-i18n="Pricing">Pricing</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Misc">Misc</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('pages-misc-error') }}" class="menu-link" target="_blank">
                <div data-i18n="Error">Error</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-misc-under-maintenance') }}" class="menu-link" target="_blank">
                <div data-i18n="Under Maintenance">Under Maintenance</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-misc-comingsoon') }}" class="menu-link" target="_blank">
                <div data-i18n="Coming Soon">Coming Soon</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('pages-misc-not-authorized') }}" class="menu-link" target="_blank">
                <div data-i18n="Not Authorized">Not Authorized</div>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
    
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-lock"></i>
        <div data-i18n="Authentications">Authentications</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Login">Login</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('auth-login-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-login-cover') }}" class="menu-link" target="_blank">
                <div data-i18n="Cover">Cover</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Register">Register</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('auth-register-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-register-cover') }}" class="menu-link" target="_blank">
                <div data-i18n="Cover">Cover</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-register-multisteps') }}" class="menu-link" target="_blank">
                <div data-i18n="Multi-steps">Multi-steps</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Verify Email">Verify Email</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('auth-verify-email-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-verify-email-cover') }}" class="menu-link" target="_blank">
                <div data-i18n="Cover">Cover</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Reset Password">Reset Password</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('auth-reset-password-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-reset-password-cover') }}" class="menu-link" target="_blank">
                <div data-i18n="Cover">Cover</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Forgot Password">Forgot Password</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('auth-forgot-password-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-forgot-password-cover') }}" class="menu-link" target="_blank">
                <div data-i18n="Cover">Cover</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Two Steps">Two Steps</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('auth-two-steps-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('auth-two-steps-cover') }}" class="menu-link" target="_blank">
                <div data-i18n="Cover">Cover</div>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
    
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-forms"></i>
        <div data-i18n="Wizard Examples">Wizard Examples</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('wizard-ex-checkout') }}" class="menu-link">
            <div data-i18n="Checkout">Checkout</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('wizard-ex-property-listing') }}" class="menu-link">
            <div data-i18n="Property Listing">Property Listing</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('wizard-ex-create-deal') }}" class="menu-link">
            <div data-i18n="Create Deal">Create Deal</div>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menu-item {{ request()->is('modal-examples') ? 'active' : '' }}">
      <a href="{{ url('modal-examples') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-square"></i>
        <div data-i18n="Modal Examples">Modal Examples</div>
      </a>
    </li>

    <!-- Components -->
    <li class="menu-header small">
      <span class="menu-header-text" data-i18n="Components">Components</span>
    </li>
    <!-- Cards -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-id"></i>
        <div data-i18n="Cards">Cards</div>
        <div class="badge text-bg-primary rounded-pill ms-auto">5</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('cards-basic') }}" class="menu-link">
            <div data-i18n="Basic">Basic</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('cards-advance') }}" class="menu-link">
            <div data-i18n="Advance">Advance</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('cards-statistics') }}" class="menu-link">
            <div data-i18n="Statistics">Statistics</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('cards-analytics') }}" class="menu-link">
            <div data-i18n="Analytics">Analytics</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('cards-actions') }}" class="menu-link">
            <div data-i18n="Actions">Actions</div>
          </a>
        </li>
      </ul>
    </li>
    
    <!-- User interface -->
    <li class="menu-item">
      <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-color-swatch"></i>
        <div data-i18n="User interface">User interface</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('ui-accordion') }}" class="menu-link">
            <div data-i18n="Accordion">Accordion</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-alerts') }}" class="menu-link">
            <div data-i18n="Alerts">Alerts</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-badges') }}" class="menu-link">
            <div data-i18n="Badges">Badges</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-buttons') }}" class="menu-link">
            <div data-i18n="Buttons">Buttons</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-carousel') }}" class="menu-link">
            <div data-i18n="Carousel">Carousel</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-collapse') }}" class="menu-link">
            <div data-i18n="Collapse">Collapse</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-dropdowns') }}" class="menu-link">
            <div data-i18n="Dropdowns">Dropdowns</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-footer') }}" class="menu-link">
            <div data-i18n="Footer">Footer</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-list-groups') }}" class="menu-link">
            <div data-i18n="List Groups">List groups</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-modals') }}" class="menu-link">
            <div data-i18n="Modals">Modals</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-navbar') }}" class="menu-link">
            <div data-i18n="Navbar">Navbar</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-offcanvas') }}" class="menu-link">
            <div data-i18n="Offcanvas">Offcanvas</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-pagination-breadcrumbs') }}" class="menu-link">
            <div data-i18n="Pagination & Breadcrumbs">Pagination &amp; Breadcrumbs</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-progress') }}" class="menu-link">
            <div data-i18n="Progress">Progress</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-spinners') }}" class="menu-link">
            <div data-i18n="Spinners">Spinners</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-tabs-pills') }}" class="menu-link">
            <div data-i18n="Tabs & Pills">Tabs &amp; Pills</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-toasts') }}" class="menu-link">
            <div data-i18n="Toasts">Toasts</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-tooltips-popovers') }}" class="menu-link">
            <div data-i18n="Tooltips & Popovers">Tooltips &amp; Popovers</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('ui-typography') }}" class="menu-link">
            <div data-i18n="Typography">Typography</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Extended components -->
    <li class="menu-item">
      <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-components"></i>
        <div data-i18n="Extended UI">Extended UI</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('extended-ui-avatar') }}" class="menu-link">
            <div data-i18n="Avatar">Avatar</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-blockui') }}" class="menu-link">
            <div data-i18n="BlockUI">BlockUI</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-drag-and-drop') }}" class="menu-link">
            <div data-i18n="Drag & Drop">Drag &amp; Drop</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-media-player') }}" class="menu-link">
            <div data-i18n="Media Player">Media Player</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-perfect-scrollbar') }}" class="menu-link">
            <div data-i18n="Perfect Scrollbar">Perfect Scrollbar</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-star-ratings') }}" class="menu-link">
            <div data-i18n="Star Ratings">Star Ratings</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-sweetalert2') }}" class="menu-link">
            <div data-i18n="SweetAlert2">SweetAlert2</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-text-divider') }}" class="menu-link">
            <div data-i18n="Text Divider">Text Divider</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <div data-i18n="Timeline">Timeline</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="{{ url('extended-ui-timeline-basic') }}" class="menu-link">
                <div data-i18n="Basic">Basic</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ url('extended-ui-timeline-fullscreen') }}" class="menu-link">
                <div data-i18n="Fullscreen">Fullscreen</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-tour') }}" class="menu-link">
            <div data-i18n="Tour">Tour</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-treeview') }}" class="menu-link">
            <div data-i18n="Treeview">Treeview</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('extended-ui-misc') }}" class="menu-link">
            <div data-i18n="Miscellaneous">Miscellaneous</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Icons -->
    <li class="menu-item">
      <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-brand-tabler"></i>
        <div data-i18n="Icons">Icons</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('icons-tabler') }}" class="menu-link">
            <div data-i18n="Tabler">Tabler</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('icons-font-awesome') }}" class="menu-link">
            <div data-i18n="Font Awesome">Font Awesome</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Forms & Tables -->
    <li class="menu-header small">
      <span class="menu-header-text" data-i18n="Forms & Tables">Forms &amp; Tables</span>
    </li>
    <!-- Forms -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-toggle-left"></i>
        <div data-i18n="Form Elements">Form Elements</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('forms-basic-inputs') }}" class="menu-link">
            <div data-i18n="Basic Inputs">Basic Inputs</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-input-groups') }}" class="menu-link">
            <div data-i18n="Input groups">Input groups</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-custom-options') }}" class="menu-link">
            <div data-i18n="Custom Options">Custom Options</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-editors') }}" class="menu-link">
            <div data-i18n="Editors">Editors</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-file-upload') }}" class="menu-link">
            <div data-i18n="File Upload">File Upload</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-pickers') }}" class="menu-link">
            <div data-i18n="Pickers">Pickers</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-selects') }}" class="menu-link">
            <div data-i18n="Select & Tags">Select &amp; Tags</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-sliders') }}" class="menu-link">
            <div data-i18n="Sliders">Sliders</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-switches') }}" class="menu-link">
            <div data-i18n="Switches">Switches</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('forms-extras') }}" class="menu-link">
            <div data-i18n="Extras">Extras</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-layout-navbar"></i>
        <div data-i18n="Form Layouts">Form Layouts</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('form-layouts-vertical') }}" class="menu-link">
            <div data-i18n="Vertical Form">Vertical Form</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('form-layouts-horizontal') }}" class="menu-link">
            <div data-i18n="Horizontal Form">Horizontal Form</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('form-layouts-sticky') }}" class="menu-link">
            <div data-i18n="Sticky Actions">Sticky Actions</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-text-wrap-disabled"></i>
        <div data-i18n="Form Wizard">Form Wizard</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('form-wizard-numbered') }}" class="menu-link">
            <div data-i18n="Numbered">Numbered</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('form-wizard-icons') }}" class="menu-link">
            <div data-i18n="Icons">Icons</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item">
      <a href="{{ url('form-validation') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-checkbox"></i>
        <div data-i18n="Form Validation">Form Validation</div>
      </a>
    </li>
    <!-- Tables -->
    <li class="menu-item">
      <a href="{{ url('tables-basic') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-table"></i>
        <div data-i18n="Tables">Tables</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-layout-grid"></i>
        <div data-i18n="Datatables">Datatables</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('tables-datatables-basic') }}" class="menu-link">
            <div data-i18n="Basic">Basic</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('tables-datatables-advanced') }}" class="menu-link">
            <div data-i18n="Advanced">Advanced</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('tables-datatables-extensions') }}" class="menu-link">
            <div data-i18n="Extensions">Extensions</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Charts & Maps -->
    <li class="menu-header small">
      <span class="menu-header-text" data-i18n="Charts & Maps">Charts &amp; Maps</span>
    </li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-chart-pie"></i>
        <div data-i18n="Charts">Charts</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ url('charts-apex') }}" class="menu-link">
            <div data-i18n="Apex Charts">Apex Charts</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ url('charts-chartjs') }}" class="menu-link">
            <div data-i18n="ChartJS">ChartJS</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item">
      <a href="{{ url('maps-leaflet') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-map"></i>
        <div data-i18n="Leaflet Maps">Leaflet Maps</div>
      </a>
    </li>

    <!-- Misc -->
    <li class="menu-header small">
      <span class="menu-header-text" data-i18n="Misc">Misc</span>
    </li>

    <!-- Multi Level Menu -->
    <li class="menu-item">
      <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-layout-board"></i>
        <div data-i18n="Multi Level">Multi Level</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="javascript:void(0)" class="menu-link menu-toggle">
            <div data-i18n="Level 2">Level 2</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link">
                <div data-i18n="Level 3">Level 3</div>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </li>
    <li class="menu-item">
      <a href="https://pixinvent.ticksy.com/" target="_blank" class="menu-link">
        <i class="menu-icon icon-base ti tabler-lifebuoy"></i>
        <div data-i18n="Support">Support</div>
      </a>
    </li>
    <li class="menu-item">
      <a
        href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
        target="_blank"
        class="menu-link">
        <i class="menu-icon icon-base ti tabler-file-description"></i>
        <div data-i18n="Documentation">Documentation</div>
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