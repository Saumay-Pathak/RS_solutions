<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SoftwareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Software::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('main_category', 'like', "%{$search}%")
                  ->orWhere('sub_category', 'like', "%{$search}%")
                  ->orWhere('developer', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', true);
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            }
        }

        // Filter by main category
        if ($request->filled('main_category')) {
            $query->where('main_category', $request->main_category);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            if ($request->featured === 'yes') {
                $query->where('featured', true);
            } elseif ($request->featured === 'no') {
                $query->where('featured', false);
            }
        }

        // Filter by free/paid
        if ($request->filled('is_free')) {
            if ($request->is_free === 'free') {
                $query->where('is_free', true);
            } elseif ($request->is_free === 'paid') {
                $query->where('is_free', false);
            }
        }

        $software = $query->ordered()->paginate(10)->appends($request->query());
        $categories = Software::active()->distinct('main_category')->pluck('main_category')->filter();

        return view('admin.software.index', compact('software', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mainCategories = $this->getMainCategories();
        $subCategories = $this->getSubCategories();
        return view('admin.software.create', compact('mainCategories', 'subCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:software,slug',
            'one_line_description' => 'required|string|max:255',
            'description' => 'required|string',
            'main_category' => 'required|string|max:100',
            'sub_category' => 'nullable|string|max:100',
            'file' => 'nullable|file|max:204800', // 200MB max
            'external_url' => 'nullable|url|max:500',
            'version' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:50',
            'developer' => 'nullable|string|max:255',
            'license' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'boolean',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'released_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        // Validate that either file or external_url is provided
        if (!$request->hasFile('file') && empty($validated['external_url'])) {
            return back()->withErrors(['file' => 'Either upload a file or provide an external URL.'])->withInput();
        }

        // Debug file upload
        \Log::info('File upload debug', [
            'hasFile' => $request->hasFile('file'),
            'files_count' => count($request->allFiles()),
            'all_files' => array_keys($request->allFiles()),
            'source_type' => $request->input('source_type'),
            'request_file_exists' => $request->file('file') !== null
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            \Log::info('File upload detected', [
                'original_name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType()
            ]);
            
            try {
                $filePath = $request->file('file')->store('software', 'public');
                $validated['file'] = $filePath;
                $validated['size'] = $request->file('file')->getSize();
                
                \Log::info('File stored successfully', ['path' => $filePath]);
            } catch (\Exception $e) {
                \Log::error('File storage failed', ['error' => $e->getMessage()]);
                return back()->withErrors(['file' => 'File upload failed: ' . $e->getMessage()])->withInput();
            }
        } else {
            \Log::info('No file detected in request');
            // Set file to null if no file uploaded
            $validated['file'] = null;
        }

        // Set default values
        $validated['is_free'] = $request->boolean('is_free', true);
        $validated['status'] = $request->boolean('status', true);
        $validated['featured'] = $request->boolean('featured', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['download_count'] = 0;

        Software::create($validated);

        return redirect()->route('admin.software.index')
                        ->with('success', 'Software created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Software $software)
    {
        return view('admin.software.show', compact('software'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Software $software)
    {
        $mainCategories = $this->getMainCategories();
        $subCategories = $this->getSubCategories();
        return view('admin.software.edit', compact('software', 'mainCategories', 'subCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Software $software)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('software', 'slug')->ignore($software->id)],
            'one_line_description' => 'required|string|max:255',
            'description' => 'required|string',
            'main_category' => 'required|string|max:100',
            'sub_category' => 'nullable|string|max:100',
            'file' => 'nullable|file|max:204800', // 200MB max
            'external_url' => 'nullable|url|max:500',
            'version' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:50',
            'developer' => 'nullable|string|max:255',
            'license' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'boolean',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'released_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($software->file && Storage::disk('public')->exists($software->file)) {
                Storage::disk('public')->delete($software->file);
            }
            
            $filePath = $request->file('file')->store('software', 'public');
            $validated['file'] = $filePath;
            $validated['size'] = $request->file('file')->getSize();
        }

        // Handle remove file
        if ($request->has('remove_file')) {
            if ($software->file && Storage::disk('public')->exists($software->file)) {
                Storage::disk('public')->delete($software->file);
            }
            $validated['file'] = null;
            $validated['size'] = null;
        }

        // If external URL is being set, remove the file
        if (!empty($validated['external_url']) && $software->file) {
            if (Storage::disk('public')->exists($software->file)) {
                Storage::disk('public')->delete($software->file);
            }
            $validated['file'] = null;
            $validated['size'] = null;
        }

        // Set boolean values
        $validated['is_free'] = $request->boolean('is_free', true);
        $validated['status'] = $request->boolean('status', true);
        $validated['featured'] = $request->boolean('featured', false);
        $validated['sort_order'] = $validated['sort_order'] ?? $software->sort_order;

        $software->update($validated);

        return redirect()->route('admin.software.index')
                        ->with('success', 'Software updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Software $software)
    {
        // Delete associated file
        if ($software->file && Storage::disk('public')->exists($software->file)) {
            Storage::disk('public')->delete($software->file);
        }

        $software->delete();

        return redirect()->route('admin.software.index')
                        ->with('success', 'Software deleted successfully!');
    }

    /**
     * Toggle software status
     */
    public function toggleStatus(Software $software)
    {
        $software->update(['status' => !$software->status]);
        
        $status = $software->status ? 'activated' : 'deactivated';
        return back()->with('success', "Software {$status} successfully!");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Software $software)
    {
        $software->update(['featured' => !$software->featured]);
        
        $status = $software->featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Software {$status} successfully!");
    }

    /**
     * Get available main categories
     */
    private function getMainCategories()
    {
        return [
            'Development Tools',
            'Multimedia',
            'Office & Productivity',
            'Security',
            'System Utilities',
            'Internet & Network',
            'Games',
            'Education',
            'Graphics & Design',
            'Mobile Apps',
            'Business',
            'Other'
        ];
    }

    /**
     * Get available sub categories
     */
    private function getSubCategories()
    {
        return [
            'Development Tools' => ['IDEs', 'Text Editors', 'Version Control', 'Compilers', 'Frameworks', 'Libraries'],
            'Multimedia' => ['Audio', 'Video', 'Image Editing', 'Media Players', 'Converters'],
            'Office & Productivity' => ['Word Processing', 'Spreadsheets', 'Presentations', 'PDF Tools', 'Note Taking'],
            'Security' => ['Antivirus', 'Firewalls', 'Encryption', 'Password Managers', 'VPN'],
            'System Utilities' => ['System Optimization', 'File Management', 'Backup', 'Disk Tools', 'Registry'],
            'Internet & Network' => ['Browsers', 'Download Managers', 'FTP Clients', 'Network Tools'],
            'Games' => ['Action', 'Strategy', 'Puzzle', 'RPG', 'Simulation', 'Sports'],
            'Education' => ['Language Learning', 'Mathematics', 'Science', 'Programming', 'Tutorials'],
            'Graphics & Design' => ['Image Editors', '3D Graphics', 'CAD', 'Icon Editors', 'Font Tools'],
            'Mobile Apps' => ['Android', 'iOS', 'Cross Platform', 'Development Tools'],
            'Business' => ['Accounting', 'CRM', 'Project Management', 'Inventory', 'Analytics'],
            'Other' => ['Miscellaneous', 'Uncategorized']
        ];
    }
}
