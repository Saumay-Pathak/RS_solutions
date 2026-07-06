<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactQueryController extends Controller
{
    /**
     * Display a listing of contact queries.
     */
    public function index(Request $request)
    {
        $query = ContactFormSubmission::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
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
                $q->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
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
        $stats = ContactFormSubmission::getStats();
        
        if ($request->ajax()) {
            return response()->json([
                'queries' => $queries,
                'stats' => $stats
            ]);
        }
        
        // Extract individual stats for the view
        $totalQueries = $stats['total'] ?? 0;
        $newQueries = $stats['new'] ?? 0;
        $readQueries = $stats['read'] ?? 0;
        $repliedQueries = $stats['replied'] ?? 0;
        $closedQueries = $stats['closed'] ?? 0;
        $contactQueries = $queries;
        
        return view('admin.contact-queries.index', compact(
            'queries', 'stats', 'totalQueries', 'newQueries', 
            'readQueries', 'repliedQueries', 'closedQueries', 'contactQueries'
        ));
    }
    
    /**
     * Display the specified contact query.
     */
    public function show($id)
    {
        $query = ContactFormSubmission::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json(['query' => $query]);
        }
        
        return view('admin.contact-queries.show', compact('query'));
    }
    
    /**
     * Update the status of the specified contact query.
     */
    public function updateStatus(Request $request, $id)
    {
        $query = ContactFormSubmission::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:new,read,replied,closed'
        ]);
        
        $updateData = [
            'status' => $request->status,
            'assigned_to' => auth()->id()
        ];
        
        // Set timestamps based on status
        switch ($request->status) {
            case 'replied':
                $updateData['replied_at'] = now();
                break;
            case 'closed':
                $updateData['closed_at'] = now();
                break;
        }
        
        $query->update($updateData);
        
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
     * Remove the specified contact query.
     */
    public function destroy($id)
    {
        $query = ContactFormSubmission::findOrFail($id);
        $query->delete();
        
        return response()->json(['message' => 'Contact query deleted successfully']);
    }
    
    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,update_status,mark_priority',
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_form_submissions,_id'
        ]);
        
        $queries = ContactFormSubmission::whereIn('_id', $request->ids);
        
        switch ($request->action) {
            case 'delete':
                $count = $queries->count();
                $queries->delete();
                return response()->json(['message' => "{$count} contact queries deleted successfully"]);
                
            case 'update_status':
                $request->validate(['status' => 'required|in:new,read,replied,closed']);
                
                $updateData = [
                    'status' => $request->status,
                    'assigned_to' => auth()->id()
                ];
                
                // Set timestamps based on status
                switch ($request->status) {
                    case 'replied':
                        $updateData['replied_at'] = now();
                        break;
                    case 'closed':
                        $updateData['closed_at'] = now();
                        break;
                }
                
                $count = $queries->update($updateData);
                return response()->json(['message' => "{$count} contact queries updated successfully"]);
                
            case 'mark_priority':
                $request->validate(['priority' => 'required|in:low,medium,high']);
                $count = $queries->update(['priority' => $request->priority]);
                return response()->json(['message' => "{$count} contact queries priority updated successfully"]);
        }
        
        return response()->json(['message' => 'Bulk action completed successfully']);
    }
    
    /**
     * Export contact queries to CSV or Excel.
     */
    public function export(Request $request)
    {
        $query = ContactFormSubmission::query();
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        $queries = $query->orderBy('created_at', 'desc')->get();
        
        $format = $request->get('format', 'csv');
        $filename = 'contact_queries_' . date('Y-m-d_H-i-s') . '.' . $format;
        
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
                'Name',
                'Email',
                'Phone',
                'Company',
                'Subject',
                'Message',
                'Form Type',
                'Status',
                'Priority',
                'Created At',
                'Updated At'
            ]);
            
            // Add data rows
            foreach ($queries as $query) {
                fputcsv($file, [
                    $query->_id,
                    $query->name,
                    $query->email,
                    $query->phone,
                    $query->company,
                    $query->subject,
                    $query->message,
                    $query->form_type,
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