<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartnerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PartnerRegistrationController extends Controller
{
    /**
     * Submit partner registration
     */
    /**
     * Submit partner registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'director_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'pin_code' => 'required|string|max:20',
            'district' => 'required|string|max:100',
            'area' => 'nullable|string|max:100',
            'gst_number' => 'required|string|max:50',
            
            'engineer_name_1' => 'nullable|string|max:255',
            'engineer_number_1' => 'nullable|string|max:20',
            'engineer_name_2' => 'nullable|string|max:255',
            'engineer_number_2' => 'nullable|string|max:20',
            'engineer_name_3' => 'nullable|string|max:255',
            'engineer_number_3' => 'nullable|string|max:20',
            'engineer_name_4' => 'nullable|string|max:255',
            'engineer_number_4' => 'nullable|string|max:20',

            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max

            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string',
            'page_url' => 'nullable|url',
            'referrer' => 'nullable|string',
            'utm_source' => 'nullable|string',
            'utm_medium' => 'nullable|string',
            'utm_campaign' => 'nullable|string',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $validator->validated();
            
            // Handle File Upload
            $documentUrl = '';
            if ($request->hasFile('document_file')) {
                // Store in public disk
                $path = $request->file('document_file')->store('partners/documents', 'public');
                $data['document_file_path'] = $path;
                $documentUrl = url('storage/' . $path);
            }

            // Set Defaults and Map to existing schema for backward compatibility
            $data['status'] = $request->input('status', 'new');
            $data['priority'] = $request->input('priority', 'medium');
            $data['ip_address'] = $request->ip();
            $data['user_agent'] = $request->userAgent();
            
            // Map new fields to old fields if they exist
            $data['contact_person'] = $data['director_name'] ?? null;
            $data['phone'] = $data['mobile_number'] ?? null;
            $data['city'] = $data['district'] ?? null;
            $data['postal_code'] = $data['pin_code'] ?? null;

            // Create Record
            $registration = PartnerRegistration::create($data);

            // Send to External API
            $externalApiUrl = 'https://www.freeonlinerealsoft.com/api/AddPartner/AddPartner';
            
            $queryParams = [
                'Personname' => $registration->director_name ?? '',
                'BankPhoneno' => $registration->mobile_number ?? '',
                'CompanyName' => $registration->company_name ?? '',
                'City' => $registration->district ?? '',
                'State' => $registration->state ?? '',
                'Email' => $registration->email ?? '',
                'Engineer1' => $registration->engineer_name_1 ?? '',
                'Engineer1Mobile' => $registration->engineer_number_1 ?? '',
                'Engineer2' => $registration->engineer_name_2 ?? '',
                'Engineer2Mobile' => $registration->engineer_number_2 ?? '',
                'Engineer3' => $registration->engineer_name_3 ?? '',
                'Engineer3Mobile' => $registration->engineer_number_3 ?? '',
                'Engineer4' => $registration->engineer_name_4 ?? '',
                'Engineer4Mobile' => $registration->engineer_number_4 ?? '',
                'Gsnno' => $registration->gst_number ?? '',
                'SellerName' => $registration->director_name ?? '',
                'pincode' => $registration->pin_code ?? '',
                'imagepath' => $documentUrl,
                'Address' => $registration->address ?? '',
                'area' => $registration->area ?? '',
            ];

            // Execute External Request
            try {
                Log::info('Partner Registration - Sending to External API', [
                    'url' => $externalApiUrl,
                    'params' => $queryParams
                ]);

                $response = Http::get($externalApiUrl, $queryParams);

                Log::info('Partner Registration - External API Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

            } catch (\Exception $ex) {
                // Log external API failure but don't fail the request
                Log::error('Partner Registration - External API Failed', [
                    'error' => $ex->getMessage(),
                    'trace' => $ex->getTraceAsString()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Your partner registration has been submitted successfully.',
                'data' => $registration
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting registration',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get registration status (public endpoint with limited info)
     */
    public function getStatus(Request $request, $registrationId)
    {
        try {
            $registration = PartnerRegistration::find($registrationId);

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not found'
                ], 404);
            }

            // Only return basic status information
            $statusData = [
                'registration_id' => $registration->id,
                'company_name' => $registration->company_name,
                'status' => $registration->status,
                'submitted_at' => $registration->created_at->format('Y-m-d H:i:s'),
                'partnership_type' => $registration->partnership_type,
            ];

            // Add status-specific information
            switch ($registration->status) {
                case 'under_review':
                    $statusData['message'] = 'Your application is currently under review.';
                    if ($registration->reviewed_at) {
                        $statusData['reviewed_at'] = $registration->reviewed_at->format('Y-m-d H:i:s');
                    }
                    break;
                case 'approved':
                    $statusData['message'] = 'Congratulations! Your partner application has been approved.';
                    if ($registration->approved_at) {
                        $statusData['approved_at'] = $registration->approved_at->format('Y-m-d H:i:s');
                    }
                    break;
                case 'rejected':
                    $statusData['message'] = 'Unfortunately, your partner application has been declined.';
                    if ($registration->rejection_reason) {
                        $statusData['reason'] = $registration->rejection_reason;
                    }
                    break;
                case 'on_hold':
                    $statusData['message'] = 'Your application is currently on hold pending additional information.';
                    break;
                default:
                    $statusData['message'] = 'Your application has been received and will be reviewed soon.';
            }

            return response()->json([
                'success' => true,
                'data' => $statusData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching registration status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get partner registration statistics
     */
    public function getStats(Request $request)
    {
        try {
            $period = $request->input('period', 'month'); // today, week, month, year

            $stats = PartnerRegistration::getStats();
            
            // Add period-specific stats
            $periodStats = $this->getPeriodStats($period);
            $stats = array_merge($stats, $periodStats);

            // Add breakdown by partnership type
            $stats['by_partnership_type'] = PartnerRegistration::select('partnership_type')
                                                             ->get()
                                                             ->groupBy('partnership_type')
                                                             ->map(function($group) {
                                                                 return $group->count();
                                                             })
                                                             ->toArray();

            // Add breakdown by business type
            $stats['by_business_type'] = PartnerRegistration::select('business_type')
                                                          ->get()
                                                          ->groupBy('business_type')
                                                          ->map(function($group) {
                                                              return $group->count();
                                                          })
                                                          ->toArray();

            // Add geographic distribution
            $stats['by_country'] = PartnerRegistration::select('country')
                                                    ->get()
                                                    ->groupBy('country')
                                                    ->map(function($group) {
                                                        return $group->count();
                                                    })
                                                    ->sortDesc()
                                                    ->take(10)
                                                    ->toArray();

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
     * Get partnership types and their descriptions
     */
    public function getPartnershipTypes()
    {
        try {
            $partnershipTypes = [
                'reseller' => [
                    'title' => 'Reseller Partner',
                    'description' => 'Sell our products directly to end customers with attractive margins.',
                    'requirements' => ['Established sales network', 'Minimum revenue commitment', 'Sales training completion'],
                    'benefits' => ['Competitive pricing', 'Sales support', 'Marketing materials', 'Lead sharing']
                ],
                'distributor' => [
                    'title' => 'Distribution Partner',
                    'description' => 'Distribute our products through your channel network in specific regions.',
                    'requirements' => ['Regional presence', 'Channel network', 'Inventory management capability'],
                    'benefits' => ['Exclusive territory rights', 'Volume discounts', 'Channel support', 'Co-marketing opportunities']
                ],
                'integrator' => [
                    'title' => 'System Integrator',
                    'description' => 'Integrate our solutions into comprehensive systems for enterprise clients.',
                    'requirements' => ['Technical expertise', 'Project management experience', 'Integration certifications'],
                    'benefits' => ['Technical support', 'Integration tools', 'Certification programs', 'Project referrals']
                ],
                'consultant' => [
                    'title' => 'Solution Consultant',
                    'description' => 'Provide consulting services and recommend our solutions to clients.',
                    'requirements' => ['Industry expertise', 'Consulting experience', 'Client base'],
                    'benefits' => ['Referral commissions', 'Sales tools', 'Product training', 'Marketing support']
                ],
                'technology_partner' => [
                    'title' => 'Technology Partner',
                    'description' => 'Develop complementary solutions or integrations with our platform.',
                    'requirements' => ['Development capabilities', 'Technical resources', 'Compatible solutions'],
                    'benefits' => ['API access', 'Technical documentation', 'Co-development opportunities', 'Joint marketing']
                ],
                'referral_partner' => [
                    'title' => 'Referral Partner',
                    'description' => 'Refer potential customers to us and earn commissions on successful sales.',
                    'requirements' => ['Business network', 'Industry connections'],
                    'benefits' => ['Commission payments', 'Referral tracking', 'Marketing materials', 'Simple onboarding']
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $partnershipTypes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching partnership types',
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
     * Determine priority based on partnership type
     */
    private function determinePriority($partnershipType)
    {
        $priorities = [
            'distributor' => 'high',
            'technology_partner' => 'high',
            'integrator' => 'medium',
            'reseller' => 'medium',
            'consultant' => 'low',
            'referral_partner' => 'low'
        ];

        return $priorities[$partnershipType] ?? 'medium';
    }

    /**
     * Get statistics for a specific period
     */
    private function getPeriodStats($period)
    {
        $query = PartnerRegistration::query();

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
                                     ->groupBy('partnership_type')
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