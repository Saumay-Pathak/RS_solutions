<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
        $clients = $query->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
        }

        $validated['featured'] = $request->boolean('featured', false);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $client = Client::create($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client created successfully');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('logo')) {
            if ($client->logo && Storage::disk('public')->exists($client->logo)) {
                Storage::disk('public')->delete($client->logo);
            }
            $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
        }

        if ($request->has('remove_logo')) {
            if ($client->logo && Storage::disk('public')->exists($client->logo)) {
                Storage::disk('public')->delete($client->logo);
            }
            $validated['logo'] = null;
        }

        $validated['featured'] = $request->boolean('featured', $client->featured);
        $validated['status'] = $request->boolean('status', $client->status);
        $validated['sort_order'] = $validated['sort_order'] ?? $client->sort_order ?? 0;

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated successfully');
    }

    public function destroy(Client $client)
    {
        if ($client->logo && Storage::disk('public')->exists($client->logo)) {
            Storage::disk('public')->delete($client->logo);
        }
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully');
    }

    public function toggleStatus(Client $client)
    {
        $client->status = !$client->status;
        $client->save();
        return back()->with('success', 'Client status updated');
    }

    public function toggleFeatured(Client $client)
    {
        $client->featured = !$client->featured;
        $client->save();
        return back()->with('success', 'Client featured flag updated');
    }
}
