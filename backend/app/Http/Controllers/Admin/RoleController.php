<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $availablePermissions = [
        'user_management' => 'User Management',
        'role_management' => 'Role Management', 
        'product_management' => 'Product Management',
        'category_management' => 'Category Management',
        'blog_management' => 'Blog Management',
        'page_management' => 'Page Management',
        'file_upload' => 'File Upload',
        'system_settings' => 'System Settings',
    ];

    private $availablePages = [
        'dashboard' => 'Dashboard',
        'clients' => 'Our Clients',
        'certifications' => 'Certifications',
        'users' => 'Users',
        'roles' => 'Roles & Permissions',
        'categories' => 'Categories',
        'products' => 'Products',
        'blogs' => 'Blogs',
        'pages' => 'Pages',
        'faqs' => 'FAQs',
        'about-us' => 'About Us',
        'contact-info' => 'Contact Info',
        'support-tickets' => 'Support Tickets',
        'testimonials' => 'Testimonials',
        'solutions' => 'Solutions',
        'services' => 'Services',
        'software' => 'Software',
        'integration-modules' => 'Integration Modules',
        'popups' => 'Popups',
        'hero-slides' => 'Hero Slides',
        'job-openings' => 'Job Openings',
        'galary' => 'Galary',
        'header-footer' => 'Header & Footer',
        'home-sections' => 'Home Sections',
        'extras' => 'Extras & Settings',
        'partner-queries' => 'Partner Queries',
        'contact-queries' => 'Contact Queries',
        'sales-requirement-queries' => 'Sales Requirement Queries',
        'profile' => 'Profile',
    ];

    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status') === '1');
        }

        $roles = $query->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create', [
            'availablePermissions' => $this->availablePermissions,
            'availablePages' => $this->availablePages,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->availablePermissions)),
            'page_access' => 'nullable|array',
            'page_access.*' => 'string|in:' . implode(',', array_keys($this->availablePages)),
        ]);

        $roleData = $request->only(['name', 'display_name', 'description']);
        $roleData['permissions'] = $request->get('permissions', []);
        $roleData['page_access'] = $request->get('page_access', []);
        $roleData['status'] = $request->has('status');

        Role::create($roleData);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        // Manual count for MongoDB compatibility
        $role->users_count = $role->users()->count();
        return view('admin.roles.show', [
            'role' => $role,
            'availablePermissions' => $this->availablePermissions,
            'availablePages' => $this->availablePages,
        ]);
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'availablePermissions' => $this->availablePermissions,
            'availablePages' => $this->availablePages,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->_id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->availablePermissions)),
            'page_access' => 'nullable|array',
            'page_access.*' => 'string|in:' . implode(',', array_keys($this->availablePages)),
        ]);

        $roleData = $request->only(['name', 'display_name', 'description']);
        $roleData['permissions'] = $request->get('permissions', []);
        $roleData['page_access'] = $request->get('page_access', []);
        $roleData['status'] = $request->has('status');

        $role->update($roleData);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                            ->with('error', 'Cannot delete role that has associated users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role deleted successfully.');
    }

    public function toggleStatus(Role $role)
    {
        $role->update(['status' => !$role->status]);
        
        return response()->json([
            'success' => true,
            'status' => $role->status,
            'message' => 'Role status updated successfully.'
        ]);
    }

    public function getPermissions()
    {
        return response()->json([
            'permissions' => $this->availablePermissions,
            'pages' => $this->availablePages,
        ]);
    }
}
