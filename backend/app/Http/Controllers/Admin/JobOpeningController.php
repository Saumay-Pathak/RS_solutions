<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use Illuminate\Http\Request;

class JobOpeningController extends Controller
{
    public function index()
    {
        $jobs = JobOpening::orderBy('order')->paginate(15);
        return view('admin.job-openings.index', compact('jobs'));
    }

    public function create()
    {
        $nextOrder = JobOpening::getNextOrder();
        return view('admin.job-openings.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'display_from' => ['nullable', 'date'],
            'display_to' => ['nullable', 'date', 'after_or_equal:display_from'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $validated['order'] ?? JobOpening::getNextOrder();

        $job = JobOpening::create($validated);

        return redirect()->route('admin.job-openings.index')
            ->with('success', 'Job opening created successfully.');
    }

    public function show(JobOpening $job_opening)
    {
        return view('admin.job-openings.show', ['job' => $job_opening]);
    }

    public function edit(JobOpening $job_opening)
    {
        return view('admin.job-openings.edit', ['job' => $job_opening]);
    }

    public function update(Request $request, JobOpening $job_opening)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'display_from' => ['nullable', 'date'],
            'display_to' => ['nullable', 'date', 'after_or_equal:display_from'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $validated['order'] ?? $job_opening->order;

        $job_opening->update($validated);

        return redirect()->route('admin.job-openings.index')
            ->with('success', 'Job opening updated successfully.');
    }

    public function destroy(JobOpening $job_opening)
    {
        $job_opening->delete();
        return redirect()->route('admin.job-openings.index')
            ->with('success', 'Job opening deleted successfully.');
    }
}