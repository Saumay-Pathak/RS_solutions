<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesRequirementQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesRequirementController extends Controller
{
    /**
     * Submit "Send Us Your Requirement" form
     */
    public function submit(Request $request)
    {
            Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            // Contact
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_country_code' => 'nullable|string|max:10', // e.g., +91
            'phone' => 'required|string|max:20',

            // Location
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'zip' => 'nullable|string|max:20',
            'pincode' => 'nullable|string|max:20', // Added pincode support

            // Requirement & source
            'product' => 'nullable|string|max:255',
            'requirement_type' => 'required|string|in:Face + Fingerprint Device,Face Device,Aadhar Device,Fingerprint Device,4G WiFi Router,4G/WiFi Cameras,POE,Accessories,Support,Others',
            'source' => 'required|string|in:General,Social Media Ad,Others',

            // Optional message/details
            'message' => 'nullable|string|max:2000',

            // Tracking
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
            // Rate limiting: max 3 submissions per 5 minutes per IP
            $ipAddress = $this->getRealIpAddress($request);
            $recent = SalesRequirementQuery::where('ip_address', $ipAddress)
                ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                ->count();

            if ($recent >= 11) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many submissions. Please wait before submitting again.'
                ], 429);
            }

            $data = array_merge($validator->validated(), [
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'status' => 'new',
                'priority' => $this->determinePriority($request->input('requirement_type')),
                'zip' => $request->input('zip') ?? $request->input('pincode'), // Map pincode to zip if zip is missing
            ]);

            $submission = SalesRequirementQuery::create($data);

            // Send data to external API (RealSoft)
            $this->sendToExternalApi($data);

            return response()->json([
                'success' => true,
                'message' => 'Thank you. Your requirement has been submitted successfully.',
                'data' => [
                    'submission_id' => $submission->id,
                    'status' => $submission->status,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting requirement',
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
            $zip = !empty($data['zip']) ? $data['zip'] : (!empty($data['pincode']) ? $data['pincode'] : '000000');
            
            $payload = [
                'Name' => $data['name'] ?? '',
                'Email' => $data['email'] ?? '',
                'Mobile' => ($data['phone_country_code'] ?? '') . ($data['phone'] ?? ''),
                'Phone' => ($data['phone_country_code'] ?? '') . ($data['phone'] ?? ''), // Duplicate as Phone just in case
                'State' => !empty($data['state']) ? $data['state'] : 'Unknown',
                'City' => !empty($data['city']) ? $data['city'] : 'Unknown',
                'Country' => !empty($data['country']) ? $data['country'] : 'India',
                'ZipCode' => $zip, // Alternate key
                'Address' => (!empty($data['city']) ? $data['city'] : 'Unknown') . ', ' . (!empty($data['state']) ? $data['state'] : 'Unknown') . ', ' . $zip, // Construct address
                'Product' => !empty($data['product']) ? $data['product'] : 'General',
                'Requirement' => $data['requirement_type'] ?? 'General',
                'Message' => !empty($data['message']) ? $data['message'] : 'No message provided',
                'Source' => $data['source'] ?? 'Website',
                'PageUrl' => $data['page_url'] ?? '',
                'Referrer' => $data['referrer'] ?? '',
                'CompanyName' => !empty($data['company']) ? $data['company'] : 'Individual', // Default to Individual
                'Designation' => 'N/A', // Longer than '-'
            ];

            // Log the attempt for debugging
            Log::info('Attempting to send sales requirement to RealSoft API', [
                'url' => $url,
                'data' => $data,
                'payload' => $payload
            ]);

            // Send as form data (standard for PHP backends)
            $response = Http::asForm()->post($url, $payload);

            if ($response->successful()) {
                Log::info('Successfully sent to RealSoft API', ['response' => $response->body()]);
            } else {
                Log::error('Failed to send to RealSoft API', [
                    'status' => $response->status(), 
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception while sending to RealSoft API: ' . $e->getMessage());
        }
    }

    /**
     * Determine priority from requirement type
     */
    private function determinePriority($type)
    {
        $priorities = [
            'Face + Fingerprint Device' => 'high',
            'Face Device' => 'high',
            'Aadhar Device' => 'high',
            'Fingerprint Device' => 'high',
            '4G/WiFi Cameras' => 'medium',
            '4G WiFi Router' => 'medium',
            'POE' => 'medium',
            'Accessories' => 'low',
            'Support' => 'low',
            'Others' => 'medium',
        ];

        return $priorities[$type] ?? 'medium';
    }

    /**
     * Get real IP address (Cloudflare and proxies aware)
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
}