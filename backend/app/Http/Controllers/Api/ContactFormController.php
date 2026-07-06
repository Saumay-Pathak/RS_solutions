<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ContactFormController extends Controller
{
    /**
     * Submit contact form
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'form_type' => 'nullable|string|in:contact,quote,support,consultation,partnership,demo',
            'page_url' => 'nullable|url',
            'referrer' => 'nullable|url',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'custom_fields' => 'nullable|array',
            'custom_fields.*' => 'string|max:1000',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Rate limiting check
            $ipAddress = $this->getRealIpAddress($request);
            $recentSubmissions = ContactFormSubmission::where('ip_address', $ipAddress)
                                                   ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                                                   ->count();

            if ($recentSubmissions >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many submissions. Please wait before submitting again.'
                ], 429);
            }

            $submissionData = array_merge($validator->validated(), [
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'status' => 'new',
                'priority' => $this->determinePriority($request->input('form_type', 'contact')),
            ]);

            $submission = ContactFormSubmission::create($submissionData);

            // Send to external API (RealSoft)
            $this->sendToExternalApi($submissionData);

            // You can add email notification here
            // $this->sendNotificationEmail($submission);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon!',
                'data' => [
                    'submission_id' => $submission->id,
                    'status' => $submission->status
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting form',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send data to external API (RealSoft)
     * 
     * @param array $data
     * @return void
     */
    private function sendToExternalApi($data)
    {
        try {
            $url = 'https://markvisitor.com/app/dev/website/saveEnquiry.php';

            // Map local data to external API expected format
            // Use realistic dummy data for fields that might be required by the external API
            $payload = [
                'Name' => $data['name'] ?? '',
                'Email' => $data['email'] ?? '',
                'Mobile' => $data['phone'] ?? '',
                'Phone' => $data['phone'] ?? '', // Duplicate as Phone just in case
                'State' => !empty($data['state']) ? $data['state'] : 'Unknown',
                'City' => !empty($data['city']) ? $data['city'] : 'Unknown',
                'Country' => !empty($data['country']) ? $data['country'] : 'India',
                'Zip' => !empty($data['zip']) ? $data['zip'] : '000000',
                'ZipCode' => !empty($data['zip']) ? $data['zip'] : '000000', // Alternate key
                'Address' => (!empty($data['city']) ? $data['city'] : 'Unknown') . ', ' . (!empty($data['state']) ? $data['state'] : 'Unknown'), // Construct address
                'Requirement' => $data['subject'] ?? ($data['form_type'] ?? 'Contact'),
                'Message' => !empty($data['message']) ? $data['message'] : 'No message provided',
                'Source' => 'Website - ' . ($data['form_type'] ?? 'Contact'),
                'PageUrl' => $data['page_url'] ?? '',
                'Referrer' => $data['referrer'] ?? '',
                'CompanyName' => !empty($data['company']) ? $data['company'] : 'Individual',
                'Designation' => 'N/A', // Longer than '-'
            ];

            // Log the attempt for debugging
            Log::info('Attempting to send contact form to RealSoft API', [
                'url' => $url,
                'payload' => $payload
            ]);

            // Send as form data
            $response = Http::asForm()->post($url, $payload);

            if ($response->successful()) {
                Log::info('Successfully sent contact form to RealSoft API', ['response' => $response->body()]);
            } else {
                Log::error('Failed to send contact form to RealSoft API', [
                    'status' => $response->status(), 
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception while sending contact form to RealSoft API: ' . $e->getMessage());
        }
    }

    /**
     * Submit quote request form
     */
    public function submitQuote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'required|string|max:255',
            'project_type' => 'required|string|max:100',
            'budget_range' => 'nullable|string|max:100',
            'timeline' => 'nullable|string|max:100',
            'description' => 'required|string|max:5000',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'preferred_contact' => 'nullable|string|in:email,phone,both',
            'page_url' => 'nullable|url',
            'referrer' => 'nullable|url',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $submissionData = array_merge($validator->validated(), [
                'form_type' => 'quote',
                'subject' => 'Quote Request - ' . $request->input('project_type'),
                'message' => $request->input('description'),
                'ip_address' => $this->getRealIpAddress($request),
                'user_agent' => $request->userAgent(),
                'status' => 'new',
                'priority' => 'medium',
                'custom_fields' => [
                    'project_type' => $request->input('project_type'),
                    'budget_range' => $request->input('budget_range'),
                    'timeline' => $request->input('timeline'),
                    'requirements' => $request->input('requirements'),
                    'preferred_contact' => $request->input('preferred_contact', 'email'),
                ]
            ]);

            $submission = ContactFormSubmission::create($submissionData);

            return response()->json([
                'success' => true,
                'message' => 'Your quote request has been submitted successfully. We will contact you within 24 hours.',
                'data' => [
                    'submission_id' => $submission->id,
                    'estimated_response_time' => '24 hours'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting quote request',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Submit newsletter subscription
     */
    public function subscribeNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:100',
            'page_url' => 'nullable|url',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Check if already subscribed
            $existingSubscription = ContactFormSubmission::where('email', $request->input('email'))
                                                       ->where('form_type', 'newsletter')
                                                       ->first();

            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already subscribed to our newsletter.'
                ], 409);
            }

            $submissionData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'form_type' => 'newsletter',
                'subject' => 'Newsletter Subscription',
                'message' => 'Newsletter subscription request',
                'ip_address' => $this->getRealIpAddress($request),
                'user_agent' => $request->userAgent(),
                'page_url' => $request->input('page_url'),
                'utm_source' => $request->input('utm_source'),
                'utm_medium' => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
                'status' => 'new',
                'priority' => 'low',
                'custom_fields' => [
                    'interests' => $request->input('interests', [])
                ]
            ];

            $submission = ContactFormSubmission::create($submissionData);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!',
                'data' => [
                    'submission_id' => $submission->id
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error subscribing to newsletter',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get submission status (public endpoint with limited info)
     */
    public function getStatus(Request $request, $submissionId)
    {
        try {
            $submission = ContactFormSubmission::find($submissionId);

            if (!$submission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission not found'
                ], 404);
            }

            // Only return basic status information
            return response()->json([
                'success' => true,
                'data' => [
                    'submission_id' => $submission->id,
                    'status' => $submission->status,
                    'submitted_at' => $submission->created_at->format('Y-m-d H:i:s'),
                    'form_type' => $submission->form_type,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching submission status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get contact form statistics (for admin dashboard)
     */
    public function getStats(Request $request)
    {
        try {
            $period = $request->input('period', 'month'); // today, week, month, year

            $stats = ContactFormSubmission::getStats();
            
            // Add period-specific stats
            $periodStats = $this->getPeriodStats($period);
            $stats = array_merge($stats, $periodStats);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get real IP address
     */
    private function getRealIpAddress(Request $request)
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                return trim($ips[0]);
            }
        }

        return $request->ip();
    }

    /**
     * Determine priority based on form type
     */
    private function determinePriority($formType)
    {
        $priorities = [
            'support' => 'high',
            'quote' => 'medium',
            'partnership' => 'medium',
            'demo' => 'medium',
            'consultation' => 'medium',
            'contact' => 'low',
            'newsletter' => 'low'
        ];

        return $priorities[$formType] ?? 'low';
    }

    /**
     * Get statistics for a specific period
     */
    private function getPeriodStats($period)
    {
        $query = ContactFormSubmission::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }

        return [
            'period' => $period,
            'period_total' => $query->count(),
            'period_by_type' => $query->get()
                                     ->groupBy('form_type')
                                     ->map(function($group) {
                                         return $group->count();
                                     })
                                     ->toArray(),
            'period_by_status' => $query->get()
                                       ->groupBy('status')
                                       ->map(function($group) {
                                           return $group->count();
                                       })
                                       ->toArray(),
        ];
    }
}