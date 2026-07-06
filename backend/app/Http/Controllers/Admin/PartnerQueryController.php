<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PartnerQueryController extends Controller
{
    /**
     * Display a listing of partner queries.
     */
    public function index(Request $request)
    {
        $query = PartnerRegistration::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Paginate
        $perPage = $request->get('per_page', 25);
        $queries = $query->paginate($perPage);
        
        // Get statistics
        $stats = PartnerRegistration::getStats();
        
        if ($request->ajax()) {
            return response()->json([
                'queries' => $queries,
                'stats' => $stats
            ]);
        }
        
        // Extract individual stats for the view
        $totalQueries = $stats['total'] ?? 0;
        $pendingQueries = $stats['new'] ?? 0;
        $approvedQueries = $stats['approved'] ?? 0;
        $rejectedQueries = $stats['rejected'] ?? 0;
        $partnerQueries = $queries;
        
        return view('admin.partner-queries.index', compact(
            'queries', 'stats', 'totalQueries', 'pendingQueries', 
            'approvedQueries', 'rejectedQueries', 'partnerQueries'
        ));
    }
    
    /**
     * Display the specified partner query.
     */
    public function show($id)
    {
        $query = PartnerRegistration::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json(['query' => $query]);
        }
        
        return view('admin.partner-queries.show', compact('query'));
    }
    
    /**
     * Update the status of the specified partner query.
     */
    public function updateStatus(Request $request, $id)
    {
        $query = PartnerRegistration::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:new,under_review,approved,rejected,on_hold'
        ]);
        
        $oldStatus = $query->status;
        
        switch ($request->status) {
            case 'under_review':
                $query->markUnderReview(auth()->id());
                break;
            case 'approved':
                $query->approve(auth()->id());
                break;
            case 'rejected':
                $query->reject($request->rejection_reason, auth()->id());
                break;
            default:
                $query->update([
                    'status' => $request->status,
                    'assigned_to' => auth()->id()
                ]);
                break;
        }
        
        // Add note if provided
        if ($request->filled('note')) {
            $query->addNote($request->note, auth()->id());
        }
        
        return response()->json([
            'message' => 'Status updated successfully',
            'query' => $query->fresh()
        ]);
    }
    
    /**
     * Remove the specified partner query.
     */
    public function destroy($id)
    {
        $query = PartnerRegistration::findOrFail($id);
        $query->delete();
        
        return response()->json(['message' => 'Partner query deleted successfully']);
    }
    
    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,update_status,mark_priority',
            'ids' => 'required|array',
            'ids.*' => 'exists:partner_registrations,_id'
        ]);
        
        $queries = PartnerRegistration::whereIn('_id', $request->ids);
        
        switch ($request->action) {
            case 'delete':
                $count = $queries->count();
                $queries->delete();
                return response()->json(['message' => "{$count} partner queries deleted successfully"]);
                
            case 'update_status':
                $request->validate(['status' => 'required|in:new,under_review,approved,rejected,on_hold']);
                $count = $queries->update([
                    'status' => $request->status,
                    'assigned_to' => auth()->id()
                ]);
                return response()->json(['message' => "{$count} partner queries updated successfully"]);
                
            case 'mark_priority':
                $request->validate(['priority' => 'required|in:low,medium,high']);
                $count = $queries->update(['priority' => $request->priority]);
                return response()->json(['message' => "{$count} partner queries priority updated successfully"]);
        }
        
        return response()->json(['message' => 'Bulk action completed successfully']);
    }
    
    /**
     * Export partner queries to CSV or Excel.
     */
    public function export(Request $request)
    {
        $query = PartnerRegistration::query();
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        $queries = $query->orderBy('created_at', 'desc')->get();
        
        $format = $request->get('format', 'csv');
        $filename = 'partner_queries_' . date('Y-m-d_H-i-s') . '.' . $format;
        
        if ($format === 'excel') {
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
        } else {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
        }
        
        $callback = function() use ($queries) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Company Name',
                'Contact Person', 
                'Email',
                'Phone',
                'Website',
                'Business Type',
                'Partnership Type',
                'Status',
                'Priority',
                'Created At',
                'Updated At'
            ]);
            
            // Add data rows
            foreach ($queries as $query) {
                fputcsv($file, [
                    $query->_id,
                    $query->company_name,
                    $query->contact_person,
                    $query->email,
                    $query->phone,
                    $query->website,
                    $query->business_type,
                    $query->partnership_type,
                    $query->status,
                    $query->priority,
                    $query->created_at,
                    $query->updated_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}