<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    /**
     * Map PHP upload error codes to human-friendly messages
     */
    private function uploadErrorMessage(string $field): ?string
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return null;
        }
        $error = $_FILES[$field]['error'] ?? UPLOAD_ERR_OK;
        // UPLOAD_ERR_OK (0) means success
        // UPLOAD_ERR_NO_FILE (4) means no file selected, which is fine for nullable fields
        if ($error === UPLOAD_ERR_OK || $error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds server upload_max_filesize (' . ini_get('upload_max_filesize') . ').';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds form MAX_FILE_SIZE limit.';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder on server.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            default:
                return 'Unknown upload error (code: ' . $error . ').';
        }
    }
    public function index(Request $request)
    {
        $query = Certification::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
        $certifications = $query->paginate(20);

        return view('admin.certifications.index', compact('certifications'));
    }

    public function create()
    {
        return view('admin.certifications.create');
    }

    public function store(Request $request)
    {
        // Pre-check: show precise upload error reasons from PHP before validation
        if ($msg = $this->uploadErrorMessage('certificate_file')) {
            return back()->withInput()->with('error', 'Certificate upload error: ' . $msg);
        }
        if ($msg = $this->uploadErrorMessage('authority_logo')) {
            return back()->withInput()->with('error', 'Authority logo upload error: ' . $msg);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'authority_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // Align certificate size with app-wide non-software limit (20MB)
            'certificate_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,image/webp|max:20480',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'certificate_file.max' => 'Certificate must be at most 20MB.',
            'certificate_file.mimes' => 'Unsupported certificate type. Allowed: PDF, DOC, DOCX, JPG, JPEG, PNG, WEBP.',
            'certificate_file.file' => 'Invalid certificate upload. Please try again.',
            'authority_logo.max' => 'Authority logo must be at most 5MB.',
            'authority_logo.image' => 'Authority logo must be an image file.',
        ]);

        try {
            if ($request->hasFile('authority_logo')) {
                $validated['authority_logo'] = $request->file('authority_logo')->store('certifications/logos', 'public');
            }

            if ($request->hasFile('certificate_file')) {
                $validated['certificate_file'] = $request->file('certificate_file')->store('certifications/files', 'public');
            }
        } catch (\Throwable $e) {
            \Log::error('Certification file upload failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'File upload failed. Please ensure the file is under 20MB and is a supported format (PDF, DOC, DOCX, JPG, PNG, WEBP).');
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $item = Certification::create($validated);

        return redirect()->route('admin.certifications.show', $item)->with('success', 'Certification created successfully');
    }

    public function show(Certification $certification)
    {
        return view('admin.certifications.show', compact('certification'));
    }

    public function edit(Certification $certification)
    {
        return view('admin.certifications.edit', compact('certification'));
    }

    public function update(Request $request, Certification $certification)
    {
        // Pre-check: show precise upload error reasons from PHP before validation
        if ($msg = $this->uploadErrorMessage('certificate_file')) {
            return back()->withInput()->with('error', 'Certificate upload error: ' . $msg);
        }
        if ($msg = $this->uploadErrorMessage('authority_logo')) {
            return back()->withInput()->with('error', 'Authority logo upload error: ' . $msg);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'authority_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // Align certificate size with app-wide non-software limit (20MB)
            'certificate_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,image/webp|max:20480',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'certificate_file.max' => 'Certificate must be at most 20MB.',
            'certificate_file.mimes' => 'Unsupported certificate type. Allowed: PDF, DOC, DOCX, JPG, JPEG, PNG, WEBP.',
            'certificate_file.file' => 'Invalid certificate upload. Please try again.',
            'authority_logo.max' => 'Authority logo must be at most 5MB.',
            'authority_logo.image' => 'Authority logo must be an image file.',
        ]);

        try {
            if ($request->hasFile('authority_logo')) {
                if ($certification->authority_logo && Storage::disk('public')->exists($certification->authority_logo)) {
                    Storage::disk('public')->delete($certification->authority_logo);
                }
                $validated['authority_logo'] = $request->file('authority_logo')->store('certifications/logos', 'public');
            }

            if ($request->hasFile('certificate_file')) {
                if ($certification->certificate_file && Storage::disk('public')->exists($certification->certificate_file)) {
                    Storage::disk('public')->delete($certification->certificate_file);
                }
                $validated['certificate_file'] = $request->file('certificate_file')->store('certifications/files', 'public');
            }
        } catch (\Throwable $e) {
            \Log::error('Certification file upload failed (update)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'File upload failed. Please ensure the file is under 20MB and is a supported format.');
        }

        if ($request->has('remove_authority_logo')) {
            if ($certification->authority_logo && Storage::disk('public')->exists($certification->authority_logo)) {
                Storage::disk('public')->delete($certification->authority_logo);
            }
            $validated['authority_logo'] = null;
        }
        if ($request->has('remove_certificate_file')) {
            if ($certification->certificate_file && Storage::disk('public')->exists($certification->certificate_file)) {
                Storage::disk('public')->delete($certification->certificate_file);
            }
            $validated['certificate_file'] = null;
        }

        $validated['status'] = $request->boolean('status', $certification->status);
        $validated['sort_order'] = $validated['sort_order'] ?? $certification->sort_order ?? 0;

        $certification->update($validated);

        return redirect()->route('admin.certifications.show', $certification)->with('success', 'Certification updated successfully');
    }

    public function destroy(Certification $certification)
    {
        if ($certification->authority_logo && Storage::disk('public')->exists($certification->authority_logo)) {
            Storage::disk('public')->delete($certification->authority_logo);
        }
        if ($certification->certificate_file && Storage::disk('public')->exists($certification->certificate_file)) {
            Storage::disk('public')->delete($certification->certificate_file);
        }
        $certification->delete();
        return redirect()->route('admin.certifications.index')->with('success', 'Certification deleted successfully');
    }

    public function toggleStatus(Certification $certification)
    {
        $certification->status = !$certification->status;
        $certification->save();
        return back()->with('success', 'Certification status updated');
    }
}