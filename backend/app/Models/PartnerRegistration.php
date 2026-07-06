<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class PartnerRegistration extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'partner_registrations';

    protected $fillable = [
        'company_name',
        'director_name', // New field
        'contact_person',
        'email',
        'phone',
        'mobile_number', // New field
        'website',
        'address',
        'city',
        'district', // New field
        'state',
        'country',
        'postal_code',
        'pin_code', // New field
        'area', // New field
        'gst_number', // New field
        'business_type',
        'annual_revenue',
        'employees_count',
        'years_in_business',
        'partnership_type',
        'areas_of_interest',
        'target_markets',
        'existing_partnerships',
        'why_partner',
        'business_plan',
        'references',
        'certifications',
        // Engineers
        'engineer_name_1',
        'engineer_number_1',
        'engineer_name_2',
        'engineer_number_2',
        'engineer_name_3',
        'engineer_number_3',
        'engineer_name_4',
        'engineer_number_4',
        // Tracking
        'ip_address',
        'user_agent',
        'page_url',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'status',
        'priority',
        'assigned_to',
        'notes',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'attachments',
        'documents',
        'document_file_path', // New field for uploaded file
    ];

    protected $casts = [
        'areas_of_interest' => 'array',
        'target_markets' => 'array',
        'existing_partnerships' => 'array',
        'references' => 'array',
        'certifications' => 'array',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attachments' => 'array',
        'documents' => 'array',
        'notes' => 'array',
        'annual_revenue' => 'decimal:2',
        'employees_count' => 'integer',
        'years_in_business' => 'integer',
    ];

    protected $dates = [
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get registrations by status
     */
    public static function byStatus($status = 'new')
    {
        return static::where('status', $status)
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get new registrations
     */
    public static function newRegistrations()
    {
        return static::where('status', 'new')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get approved partners
     */
    public static function approved()
    {
        return static::where('status', 'approved')
                    ->orderBy('approved_at', 'desc');
    }

    /**
     * Mark as under review
     */
    public function markUnderReview($userId = null)
    {
        $this->update([
            'status' => 'under_review',
            'reviewed_at' => Carbon::now(),
            'assigned_to' => $userId
        ]);
        return $this;
    }

    /**
     * Approve registration
     */
    public function approve($userId = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'assigned_to' => $userId
        ]);
        return $this;
    }

    /**
     * Reject registration
     */
    public function reject($reason = null, $userId = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => Carbon::now(),
            'rejection_reason' => $reason,
            'assigned_to' => $userId
        ]);
        return $this;
    }

    /**
     * Add note
     */
    public function addNote($note, $userId = null)
    {
        $notes = $this->notes ?: [];
        $notes[] = [
            'note' => $note,
            'user_id' => $userId,
            'created_at' => Carbon::now()->toDateTimeString()
        ];
        
        $this->update(['notes' => $notes]);
        return $this;
    }

    /**
     * Get registration statistics
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'new' => static::where('status', 'new')->count(),
            'under_review' => static::where('status', 'under_review')->count(),
            'approved' => static::where('status', 'approved')->count(),
            'rejected' => static::where('status', 'rejected')->count(),
            'on_hold' => static::where('status', 'on_hold')->count(),
            'today' => static::whereDate('created_at', Carbon::today())->count(),
            'this_month' => static::whereMonth('created_at', Carbon::now()->month)
                         ->whereYear('created_at', Carbon::now()->year)
                         ->count(),
        ];
    }
}
