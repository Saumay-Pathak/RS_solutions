<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Display the Support Tickets listing
     */
    public function index(Request $request)
    {
        $query = SupportTicket::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Default ordering
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $tickets = $query->paginate(15);

        // Get statistics
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::open()->count(),
            'in_progress' => SupportTicket::inProgress()->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::closed()->count(),
            'overdue' => SupportTicket::overdue()->count(),
        ];

        $statuses = SupportTicket::getStatuses();
        $priorities = SupportTicket::getPriorities();
        $categories = SupportTicket::getCategories();
        $users = User::where('status', true)->orderBy('name')->get();

        return view('admin.support-tickets.index', compact(
            'tickets', 'stats', 'statuses', 'priorities', 'categories', 'users'
        ));
    }

    /**
     * Display the specified ticket
     */
    public function show($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $users = User::where('status', true)->orderBy('name')->get();

        return view('admin.support-tickets.show', compact('ticket', 'users'));
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,pending,resolved,closed',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $ticket = SupportTicket::findOrFail($id);
            $oldStatus = $ticket->status;
            
            $ticket->changeStatus($request->status, $request->note);

            return response()->json([
                'success' => true,
                'message' => "Ticket status updated from {$oldStatus} to {$request->status}",
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating ticket status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        try {
            $ticket = SupportTicket::findOrFail($id);
            $oldPriority = $ticket->priority;
            
            $ticket->update(['priority' => $request->priority]);
            $ticket->addNote("Priority changed from {$oldPriority} to {$request->priority}");

            return response()->json([
                'success' => true,
                'message' => "Ticket priority updated to {$request->priority}",
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating ticket priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket category
     */
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|in:general,technical,product,billing,complaint',
        ]);

        try {
            $ticket = SupportTicket::findOrFail($id);
            $oldCategory = $ticket->category;
            
            $ticket->update(['category' => $request->category]);
            $ticket->addNote("Category changed from {$oldCategory} to {$request->category}");

            return response()->json([
                'success' => true,
                'message' => "Ticket category updated to {$request->category}",
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating ticket category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add response to ticket
     */
    public function addResponse(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:2000',
        ]);

        try {
            $ticket = SupportTicket::findOrFail($id);
            
            $ticket->update([
                'response' => $request->response,
                'first_response_at' => $ticket->first_response_at ?? now(),
            ]);

            $ticket->addNote("Response added by " . (auth()->user()->name ?? 'Admin'));

            return response()->json([
                'success' => true,
                'message' => 'Response added successfully',
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding response: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add internal note
     */
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->addNote($request->note);

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding note: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign ticket to user
     */
    public function assignTicket(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|string|max:255',
        ]);

        try {
            $ticket = SupportTicket::findOrFail($id);
            $oldAssignee = $ticket->assigned_to;
            
            $ticket->update(['assigned_to' => $request->assigned_to]);
            
            $message = $oldAssignee 
                ? "Ticket reassigned from {$oldAssignee} to {$request->assigned_to}"
                : "Ticket assigned to {$request->assigned_to}";
                
            $ticket->addNote($message);

            return response()->json([
                'success' => true,
                'message' => $message,
                'ticket' => $ticket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete ticket
     */
    public function destroy($id)
    {
        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticketId = $ticket->ticket_id;
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => "Ticket {$ticketId} deleted successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export tickets to CSV
     */
    public function export(Request $request)
    {
        $query = SupportTicket::query();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $filename = 'support_tickets_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Ticket ID', 'Name', 'Email', 'Phone', 'City', 'State', 'Pin Code',
                'Category', 'Priority', 'Status', 'Message', 'Response', 
                'Assigned To', 'Closed By', 'Created At', 'Updated At'
            ]);

            // Add data rows
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_id,
                    $ticket->name,
                    $ticket->email,
                    $ticket->phone,
                    $ticket->city,
                    $ticket->state,
                    $ticket->pin_code,
                    $ticket->category_label,
                    $ticket->priority_label,
                    $ticket->status_label,
                    $ticket->message,
                    $ticket->response,
                    $ticket->assigned_to,
                    $ticket->closed_by,
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get ticket statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::open()->count(),
            'in_progress_tickets' => SupportTicket::inProgress()->count(),
            'closed_tickets' => SupportTicket::closed()->count(),
            'overdue_tickets' => SupportTicket::overdue()->count(),
            'today_tickets' => SupportTicket::whereDate('created_at', today())->count(),
            'this_week_tickets' => SupportTicket::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_tickets' => SupportTicket::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Priority distribution
        $priority_stats = [];
        foreach (SupportTicket::getPriorities() as $key => $label) {
            $priority_stats[$key] = SupportTicket::byPriority($key)->count();
        }

        // Category distribution
        $category_stats = [];
        foreach (SupportTicket::getCategories() as $key => $label) {
            $category_stats[$key] = SupportTicket::byCategory($key)->count();
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'priority_stats' => $priority_stats,
            'category_stats' => $category_stats,
        ]);
    }

    /**
     * Export tickets to CSV (route alias)
     */
    public function exportCsv(Request $request)
    {
        return $this->export($request);
    }

    /**
     * Get dashboard statistics (route alias)
     */
    public function getDashboardStats(Request $request)
    {
        return $this->getStats();
    }
}
