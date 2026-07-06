<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactInfoController extends Controller
{
    /**
     * Display the Contact Info management page
     */
    public function index()
    {
        $contactInfo = ContactInfo::getInstance();
        
        return view('admin.contact-info.index', compact('contactInfo'));
    }

    /**
     * Update the Contact Info
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Support Numbers
            'customer_support_number' => 'required|string|max:20',
            'partner_support_number' => 'required|string|max:20',
            'enquiry_number' => 'required|string|max:20',
            'service_center_number' => 'required|string|max:20',
            
            // Email Addresses
            'general_email' => 'required|email|max:100',
            'business_email' => 'required|email|max:100',
            'support_email' => 'nullable|email|max:100',
            
            // Corporate Headquarters - Delhi
            'hq_name' => 'required|string|max:255',
            'hq_address' => 'required|string|max:500',
            'hq_city' => 'required|string|max:100',
            'hq_state' => 'required|string|max:100',
            'hq_country' => 'required|string|max:100',
            'hq_postal_code' => 'nullable|string|max:20',
            'hq_email' => 'nullable|email|max:100',
            'hq_phone' => 'nullable|string|max:20',
            
            // UK Office
            'uk_name' => 'required|string|max:255',
            'uk_address' => 'required|string|max:500',
            'uk_city' => 'required|string|max:100',
            'uk_state' => 'required|string|max:100',
            'uk_country' => 'required|string|max:100',
            'uk_postal_code' => 'nullable|string|max:20',
            'uk_email' => 'nullable|email|max:100',
            'uk_phone' => 'nullable|string|max:20',
            
            // Manufacturing Unit - UP
            'manufacturing_name' => 'required|string|max:255',
            'manufacturing_address' => 'required|string|max:500',
            'manufacturing_city' => 'required|string|max:100',
            'manufacturing_state' => 'required|string|max:100',
            'manufacturing_country' => 'required|string|max:100',
            'manufacturing_postal_code' => 'nullable|string|max:20',
            'manufacturing_email' => 'nullable|email|max:100',
            'manufacturing_phone' => 'nullable|string|max:20',
            
            // Additional Fields
            'website_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'fax_number' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            
            // Social Media
            'social_media_links.facebook' => 'nullable|url|max:255',
            'social_media_links.linkedin' => 'nullable|url|max:255',
            'social_media_links.twitter' => 'nullable|url|max:255',
            'social_media_links.instagram' => 'nullable|url|max:255',
            
            // Business Hours
            'business_hours.monday' => 'nullable|string|max:50',
            'business_hours.tuesday' => 'nullable|string|max:50',
            'business_hours.wednesday' => 'nullable|string|max:50',
            'business_hours.thursday' => 'nullable|string|max:50',
            'business_hours.friday' => 'nullable|string|max:50',
            'business_hours.saturday' => 'nullable|string|max:50',
            'business_hours.sunday' => 'nullable|string|max:50',
            
            // Settings
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $contactInfo = ContactInfo::getInstance();
            $data = $request->except(['_token', '_method']);

            // Handle social media links
            if ($request->has('social_media_links')) {
                $data['social_media_links'] = array_filter($request->input('social_media_links'), function($value) {
                    return !empty($value);
                });
            }

            // Handle business hours
            if ($request->has('business_hours')) {
                $data['business_hours'] = array_filter($request->input('business_hours'), function($value) {
                    return !empty($value);
                });
            }

            // Set default values
            $data['is_active'] = $request->has('is_active') ? true : false;
            $data['updated_by'] = auth()->user()->name ?? 'Admin';

            // Update the contact info
            $contactInfo->update($data);

            return redirect()->route('admin.contact-info.index')
                ->with('success', 'Contact information updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Reset contact info to defaults
     */
    public function resetToDefaults()
    {
        try {
            $contactInfo = ContactInfo::getInstance();
            
            // Delete current record and create new one with defaults
            $contactInfo->delete();
            $newContactInfo = ContactInfo::getInstance();

            return redirect()->route('admin.contact-info.index')
                ->with('success', 'Contact information reset to defaults successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while resetting: ' . $e->getMessage());
        }
    }

    /**
     * Get contact info as JSON (for API usage)
     */
    public function getContactInfoApi()
    {
        try {
            $contactInfo = ContactInfo::getActive();
            
            return response()->json([
                'success' => true,
                'data' => $contactInfo,
                'formatted' => [
                    'support_numbers' => $contactInfo->support_numbers,
                    'email_addresses' => $contactInfo->email_addresses,
                    'offices' => [
                        'headquarters' => [
                            'name' => $contactInfo->hq_name,
                            'address' => $contactInfo->hq_full_address,
                            'email' => $contactInfo->hq_email,
                            'phone' => $contactInfo->hq_phone,
                        ],
                        'uk_office' => [
                            'name' => $contactInfo->uk_name,
                            'address' => $contactInfo->uk_full_address,
                            'email' => $contactInfo->uk_email,
                            'phone' => $contactInfo->uk_phone,
                        ],
                        'manufacturing' => [
                            'name' => $contactInfo->manufacturing_name,
                            'address' => $contactInfo->manufacturing_full_address,
                            'email' => $contactInfo->manufacturing_email,
                            'phone' => $contactInfo->manufacturing_phone,
                        ],
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving contact information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate a specific office information
     */
    public function duplicateOffice(Request $request)
    {
        $request->validate([
            'office_type' => 'required|in:hq,uk,manufacturing',
            'target_office' => 'required|in:hq,uk,manufacturing',
        ]);

        try {
            $contactInfo = ContactInfo::getInstance();
            $sourceType = $request->input('office_type');
            $targetType = $request->input('target_office');

            if ($sourceType === $targetType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Source and target office cannot be the same'
                ], 400);
            }

            // Copy office information
            $sourcePrefix = $sourceType;
            $targetPrefix = $targetType;

            $fieldsTocopy = ['name', 'address', 'city', 'state', 'country', 'postal_code', 'email', 'phone'];

            $updateData = [];
            foreach ($fieldsToUpdate as $field) {
                $sourceField = $sourcePrefix . '_' . $field;
                $targetField = $targetPrefix . '_' . $field;
                $updateData[$targetField] = $contactInfo->{$sourceField};
            }

            $contactInfo->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Office information copied successfully!',
                'data' => $updateData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error copying office information: ' . $e->getMessage()
            ], 500);
        }
    }
}