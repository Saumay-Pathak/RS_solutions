<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesRequirementQuery;
use Illuminate\Http\Request;

class SalesRequirementQueryController extends Controller
{
    /**
     * Display a listing of sales requirement queries.
     */
    public function index(Request $request)
    {
        $query = SalesRequirementQuery::query();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('requirement_type')) {
            $query->where('requirement_type', $request->requirement_type);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 25);
        $queries = $query->paginate($perPage);

        // Stats
        $stats = SalesRequirementQuery::getStats();
        $totalQueries = $stats['total'] ?? 0;
        $newQueries = $stats['new'] ?? 0;
        $contactedQueries = $stats['contacted'] ?? 0;
        $closedQueries = $stats['closed'] ?? 0;
        $salesRequirementQueries = $queries;

        return view('admin.sales-requirement-queries.index', compact(
            'queries', 'stats', 'totalQueries', 'newQueries', 'contactedQueries', 'closedQueries', 'salesRequirementQueries'
        ));
    }

    /**
     * Show single query details.
     */
    public function show($id)
    {
        $query = SalesRequirementQuery::findOrFail($id);
        return view('admin.sales-requirement-queries.show', compact('query'));
    }

    /**
     * Update status of a query.
     */
    public function updateStatus($id, Request $request)
    {
        $request->validate(['status' => 'required|in:new,read,contacted,closed']);
        $query = SalesRequirementQuery::findOrFail($id);
        $update = ['status' => $request->status];

        if ($request->status === 'contacted') {
            $update['contacted_at'] = now();
        }
        if ($request->status === 'closed') {
            $update['closed_at'] = now();
        }

        $query->update($update);

        return response()->json(['success' => true, 'message' => 'Status updated']);
    }

    /**
     * Delete a query.
     */
    public function destroy($id)
    {
        $query = SalesRequirementQuery::findOrFail($id);
        $query->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    /**
     * Export queries to CSV.
     */
    public function export(Request $request)
    {
        $query = SalesRequirementQuery::query();
        // Apply same filters as index for export
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('requirement_type')) $query->where('requirement_type', $request->requirement_type);
        if ($request->filled('source')) $query->where('source', $request->source);
        if ($request->filled('state')) $query->where('state', $request->state);
        if ($request->filled('country')) $query->where('country', $request->country);
        if ($request->filled('date_from')) $query->where('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('created_at', '<=', $request->date_to . ' 23:59:59');

        $items = $query->orderBy('created_at', 'desc')->get();

        $filename = 'sales_requirement_queries_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');
            // Header row
            fputcsv($handle, [
                'Name', 'Email', 'Phone', 'State', 'Country', 'Requirement', 'Product', 'Source', 'Status', 'Priority', 'Submitted At'
            ]);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->email,
                    ($item->phone_country_code ? $item->phone_country_code . ' ' : '') . $item->phone,
                    $item->state,
                    $item->country,
                    $item->requirement_type,
                    $item->product,
                    $item->source,
                    $item->status,
                    $item->priority,
                    optional($item->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}