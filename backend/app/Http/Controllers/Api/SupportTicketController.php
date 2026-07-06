<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class SupportTicketController extends Controller
{
    /**
     * Create a new support ticket
     */
    public function store(Request $request)
    {
        // Rate limiting to prevent spam (with MongoDB-safe fallback)
        $key = 'create-ticket:' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 300; // 5 minutes

        try {
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => 'Too many ticket creation attempts. Please try again in ' . $seconds . ' seconds.',
                    'retry_after' => $seconds
                ], 429);
            }
        } catch (\Throwable $t) {
            // Fallback for environments where cache/database insertIgnore is unsupported (e.g., MongoDB)
            $recentCount = SupportTicket::where('ip_address', $request->ip())
                ->where('created_at', '>=', now()->subSeconds($decaySeconds))
                ->count();

            if ($recentCount >= $maxAttempts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many ticket creation attempts from this IP. Please try again later.',
                    'retry_after' => $decaySeconds
                ], 429);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'pin_code' => 'required|string|max:10',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'area' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
            'category' => 'nullable|string|in:general,technical,product,billing,complaint',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Attempt to record a hit; ignore failures in MongoDB-only environments
            try {
                RateLimiter::hit($key, $decaySeconds);
            } catch (\Throwable $t) {
                // no-op fallback
            }

            $ticketData = $validator->validated();
            
            // Add additional data
            $ticketData['category'] = $ticketData['category'] ?? 'general';
            $ticketData['priority'] = $ticketData['priority'] ?? 'medium';
            $ticketData['source'] = 'api';
            $ticketData['ip_address'] = $request->ip();
            $ticketData['user_agent'] = $request->userAgent();

            $ticket = SupportTicket::create($ticketData);

            return response()->json([
                'success' => true,
                'message' => 'Support ticket created successfully',
                'data' => [
                    'ticket_id' => $ticket->ticket_id,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at->toISOString(),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the ticket',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get ticket status by ticket ID
     */
    public function getStatus(Request $request, $ticketId)
    {
        try {
            $ticket = SupportTicket::where('ticket_id', $ticketId)->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Only return public information
            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_id' => $ticket->ticket_id,
                    'status' => $ticket->status,
                    'status_label' => $ticket->status_label,
                    'priority' => $ticket->priority,
                    'priority_label' => $ticket->priority_label,
                    'category' => $ticket->category,
                    'category_label' => $ticket->category_label,
                    'response' => $ticket->response,
                    'created_at' => $ticket->created_at->toISOString(),
                    'updated_at' => $ticket->updated_at->toISOString(),
                    'response_time_hours' => $ticket->response_time,
                    'is_resolved' => in_array($ticket->status, ['resolved', 'closed']),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving ticket status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get ticket details with email verification
     */
    public function getTicketDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ticket = SupportTicket::where('ticket_id', $request->ticket_id)
                                  ->where('email', $request->email)
                                  ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found or email does not match'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_id' => $ticket->ticket_id,
                    'name' => $ticket->name,
                    'email' => $ticket->email,
                    'phone' => $ticket->phone,
                    'address' => $ticket->full_address,
                    'message' => $ticket->message,
                    'category' => $ticket->category,
                    'category_label' => $ticket->category_label,
                    'priority' => $ticket->priority,
                    'priority_label' => $ticket->priority_label,
                    'status' => $ticket->status,
                    'status_label' => $ticket->status_label,
                    'response' => $ticket->response,
                    'created_at' => $ticket->created_at->toISOString(),
                    'updated_at' => $ticket->updated_at->toISOString(),
                    'age_in_days' => $ticket->age_in_days,
                    'response_time_hours' => $ticket->response_time,
                    'is_resolved' => in_array($ticket->status, ['resolved', 'closed']),
                    'is_overdue' => $ticket->is_overdue,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving ticket details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get support categories and priorities
     */
    public function getFormOptions()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => SupportTicket::getCategories(),
                    'priorities' => SupportTicket::getPriorities(),
                    'statuses' => SupportTicket::getStatuses(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving form options',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get public statistics (for display on website)
     */
    public function getPublicStats()
    {
        try {
            $stats = [
                'total_tickets_resolved' => SupportTicket::where('status', 'resolved')->count(),
                'average_response_time_hours' => SupportTicket::whereNotNull('first_response_at')
                    ->get()
                    ->avg('response_time'),
                'customer_satisfaction_rate' => 95, // This could be calculated from feedback if you implement that
                'active_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}