<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrationModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IntegrationModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = IntegrationModule::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', true);
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            }
        }

        $modules = $query->ordered()->paginate(10)->appends($request->query());
        return view('admin.integration-modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.integration-modules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'software_file' => 'nullable|file|max:512000',
            'production_base_url' => 'nullable|url',
            'staging_base_url' => 'nullable|url',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            // APIs extended inputs
            'api_methods' => 'nullable|array',
            'api_methods.*' => 'nullable|string|in:GET,POST,PUT,PATCH,DELETE',
            'api_headers' => 'nullable|array',
            'api_bodies' => 'nullable|array',
        ]);

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('integration/modules', 'public');
        }

        // Handle software file upload (downloadable asset)
        if ($request->hasFile('software_file')) {
            $validated['download_file'] = $request->file('software_file')->store('integration/software', 'public');
        }

        // Collect arrays
        $validated['key_features'] = array_values(array_filter($request->input('key_features', [])));
        $validated['api_features'] = array_values(array_filter($request->input('api_features', [])));

        // API documentations (title + url)
        $docTitles = $request->input('doc_titles', []);
        $docUrls = $request->input('doc_urls', []);
        $apiDocs = [];
        foreach ($docTitles as $i => $t) {
            $title = trim($t);
            $url = $docUrls[$i] ?? null;
            if ($title || $url) {
                $apiDocs[] = [
                    'title' => $title,
                    'url' => $url,
                ];
            }
        }
        $validated['api_documentations'] = $apiDocs;

        // Demo credentials
        $validated['demo_credentials'] = [
            'username' => $request->input('demo_username'),
            'password' => $request->input('demo_password'),
            'notes' => $request->input('demo_notes'),
        ];

        // APIs details
        $apiNames = $request->input('api_names', []);
        $apiTypes = $request->input('api_types', []);
        $apiMethods = $request->input('api_methods', []);
        $apiBaseUrls = $request->input('api_base_urls', []);
        $apiDescriptions = $request->input('api_descriptions', []);
        $apiHeaders = $request->input('api_headers', []);
        $apiBodies = $request->input('api_bodies', []);
        $apis = [];
        $count = max(count($apiNames), count($apiTypes), count($apiMethods), count($apiBaseUrls), count($apiDescriptions), count($apiHeaders), count($apiBodies));
        for ($i = 0; $i < $count; $i++) {
            $name = $apiNames[$i] ?? null;
            $type = $apiTypes[$i] ?? null;
            $method = $apiMethods[$i] ?? null;
            $baseUrl = $apiBaseUrls[$i] ?? null;
            $desc = $apiDescriptions[$i] ?? null;
            $headersRaw = $apiHeaders[$i] ?? null;
            $bodyRaw = $apiBodies[$i] ?? null;
            if ($name || $type || $method || $baseUrl || $desc || $headersRaw || $bodyRaw) {
                $apis[] = [
                    'name' => $name,
                    'type' => $type,
                    'method' => $method,
                    'base_url' => $baseUrl,
                    'description' => $desc,
                    'headers' => $this->parseStructuredInput($headersRaw),
                    'body' => $this->parseStructuredInput($bodyRaw),
                ];
            }
        }
        $validated['apis'] = $apis;

        // Services
        $validated['services_api'] = array_values(array_filter($request->input('services_api', [])));
        $validated['services_other'] = array_values(array_filter($request->input('services_other', [])));

        $module = IntegrationModule::create($validated);
        return redirect()->route('admin.integration-modules.edit', $module)->with('success', 'Integration module created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IntegrationModule $integrationModule)
    {
        return view('admin.integration-modules.edit', ['module' => $integrationModule]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IntegrationModule $integrationModule)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'software_file' => 'nullable|file|max:512000',
            'production_base_url' => 'nullable|url',
            'staging_base_url' => 'nullable|url',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            // APIs extended inputs
            'api_methods' => 'nullable|array',
            'api_methods.*' => 'nullable|string|in:GET,POST,PUT,PATCH,DELETE',
            'api_headers' => 'nullable|array',
            'api_bodies' => 'nullable|array',
        ]);

        if ($request->hasFile('cover_image')) {
            // Delete old if exists
            if ($integrationModule->cover_image && Storage::disk('public')->exists($integrationModule->cover_image)) {
                Storage::disk('public')->delete($integrationModule->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('integration/modules', 'public');
        }

        // Handle software file upload/replacement
        if ($request->hasFile('software_file')) {
            if ($integrationModule->download_file && Storage::disk('public')->exists($integrationModule->download_file)) {
                Storage::disk('public')->delete($integrationModule->download_file);
            }
            $validated['download_file'] = $request->file('software_file')->store('integration/software', 'public');
        }

        // Optional: remove existing software file
        if ($request->boolean('remove_software_file')) {
            if ($integrationModule->download_file && Storage::disk('public')->exists($integrationModule->download_file)) {
                Storage::disk('public')->delete($integrationModule->download_file);
            }
            $validated['download_file'] = null;
        }

        $validated['key_features'] = array_values(array_filter($request->input('key_features', [])));
        $validated['api_features'] = array_values(array_filter($request->input('api_features', [])));

        $docTitles = $request->input('doc_titles', []);
        $docUrls = $request->input('doc_urls', []);
        $apiDocs = [];
        foreach ($docTitles as $i => $t) {
            $title = trim($t);
            $url = $docUrls[$i] ?? null;
            if ($title || $url) {
                $apiDocs[] = [
                    'title' => $title,
                    'url' => $url,
                ];
            }
        }
        $validated['api_documentations'] = $apiDocs;

        $validated['demo_credentials'] = [
            'username' => $request->input('demo_username'),
            'password' => $request->input('demo_password'),
            'notes' => $request->input('demo_notes'),
        ];

        $apiNames = $request->input('api_names', []);
        $apiTypes = $request->input('api_types', []);
        $apiMethods = $request->input('api_methods', []);
        $apiBaseUrls = $request->input('api_base_urls', []);
        $apiDescriptions = $request->input('api_descriptions', []);
        $apiHeaders = $request->input('api_headers', []);
        $apiBodies = $request->input('api_bodies', []);
        $apis = [];
        $count = max(count($apiNames), count($apiTypes), count($apiMethods), count($apiBaseUrls), count($apiDescriptions), count($apiHeaders), count($apiBodies));
        for ($i = 0; $i < $count; $i++) {
            $name = $apiNames[$i] ?? null;
            $type = $apiTypes[$i] ?? null;
            $method = $apiMethods[$i] ?? null;
            $baseUrl = $apiBaseUrls[$i] ?? null;
            $desc = $apiDescriptions[$i] ?? null;
            $headersRaw = $apiHeaders[$i] ?? null;
            $bodyRaw = $apiBodies[$i] ?? null;
            if ($name || $type || $method || $baseUrl || $desc || $headersRaw || $bodyRaw) {
                $apis[] = [
                    'name' => $name,
                    'type' => $type,
                    'method' => $method,
                    'base_url' => $baseUrl,
                    'description' => $desc,
                    'headers' => $this->parseStructuredInput($headersRaw),
                    'body' => $this->parseStructuredInput($bodyRaw),
                ];
            }
        }
        $validated['apis'] = $apis;

        $validated['services_api'] = array_values(array_filter($request->input('services_api', [])));
        $validated['services_other'] = array_values(array_filter($request->input('services_other', [])));

        $integrationModule->update($validated);
        return back()->with('success', 'Integration module updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IntegrationModule $integrationModule)
    {
        if ($integrationModule->cover_image && Storage::disk('public')->exists($integrationModule->cover_image)) {
            Storage::disk('public')->delete($integrationModule->cover_image);
        }
        $integrationModule->delete();
        return redirect()->route('admin.integration-modules.index')->with('success', 'Integration module deleted');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(IntegrationModule $integrationModule)
    {
        $integrationModule->update(['status' => !$integrationModule->status]);
        return back()->with('success', 'Status updated');
    }

    /**
     * Parse textarea input as JSON or key:value pairs to array.
     */
    private function parseStructuredInput($raw)
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (!is_string($raw)) {
            return [];
        }
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        // Try JSON first
        if (str_starts_with($raw, '{') || str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        // Fallback: parse lines of "Key: Value"
        $result = [];
        $lines = preg_split('/\r?\n/', $raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                if ($key !== '') {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}
