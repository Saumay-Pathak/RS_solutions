<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ContactInfo extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contact_info';

    protected $fillable = [
        // Support Numbers
        'customer_support_number',
        'partner_support_number',
        'enquiry_number',
        'service_center_number',
        
        // Email Addresses
        'general_email',
        'business_email',
        'support_email',
        
        // Corporate Headquarters - Delhi
        'hq_name',
        'hq_address',
        'hq_city',
        'hq_state',
        'hq_country',
        'hq_postal_code',
        'hq_email',
        'hq_phone',
        
        // UK Office
        'uk_name',
        'uk_address',
        'uk_city',
        'uk_state',
        'uk_country',
        'uk_postal_code',
        'uk_email',
        'uk_phone',
        
        // Manufacturing Unit - UP
        'manufacturing_name',
        'manufacturing_address',
        'manufacturing_city',
        'manufacturing_state',
        'manufacturing_country',
        'manufacturing_postal_code',
        'manufacturing_email',
        'manufacturing_phone',
        
        // Additional Fields
        'website_url',
        'social_media_links',
        'business_hours',
        'emergency_contact',
        'whatsapp_number',
        'fax_number',
        
        // Settings
        'is_active',
        'display_order',
        'updated_by',
    ];

    protected $casts = [
        'social_media_links' => 'array',
        'business_hours' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'display_order' => 1,
    ];

    /**
     * Get the singleton ContactInfo instance (only one record should exist)
     */
    public static function getInstance()
    {
        $contactInfo = self::first();
        
        if (!$contactInfo) {
            $contactInfo = self::create([
                // Support Numbers
                'customer_support_number' => '+91-8743 8743 34',
                'partner_support_number' => '+91-87 4409 4409',
                'enquiry_number' => '+91-8860 8860 86',
                'service_center_number' => '+91-7303 9600 28',
                
                // Email Addresses
                'general_email' => 'info@realtimebiometrics.com',
                'business_email' => 'business@realtimebiometrics.com',
                'support_email' => 'support@realtimebiometrics.com',
                
                // Corporate Headquarters - Delhi
                'hq_name' => 'Corporate Headquarters',
                'hq_address' => 'C-83, Ganesh Nagar Pandav Nagar complex',
                'hq_city' => 'Delhi',
                'hq_state' => 'Delhi',
                'hq_country' => 'India',
                'hq_postal_code' => '110092',
                'hq_email' => 'info@realtimebiometrics.com',
                'hq_phone' => '+91-8743 8743 34',
                
                // UK Office
                'uk_name' => 'United Kingdom Office',
                'uk_address' => '122 Island Business Centre, 18-36 Wellington Street',
                'uk_city' => 'London',
                'uk_state' => 'England',
                'uk_country' => 'United Kingdom',
                'uk_postal_code' => 'SE18 6PF',
                'uk_email' => 'business@realtimebiometrics.com',
                'uk_phone' => '+44 1235330124',
                
                // Manufacturing Unit
                'manufacturing_name' => 'Manufacturing Unit',
                'manufacturing_address' => 'E-1, E-2, E-3, UPSIDC Industrial area, Gajraula-II',
                'manufacturing_city' => 'Amroha',
                'manufacturing_state' => 'Uttar Pradesh',
                'manufacturing_country' => 'India',
                'manufacturing_postal_code' => '',
                'manufacturing_email' => 'info@realtimebiometrics.com',
                'manufacturing_phone' => '+91-8743 8743 34',
                
                // Additional
                'website_url' => 'https://realtimebiometrics.com',
                'whatsapp_number' => '+91-8743 8743 34',
                'social_media_links' => [
                    'facebook' => 'https://facebook.com/realtimebiometrics',
                    'linkedin' => 'https://linkedin.com/company/realtimebiometrics',
                    'twitter' => 'https://twitter.com/realtimebiometrics',
                    'instagram' => 'https://instagram.com/realtimebiometrics',
                ],
                'business_hours' => [
                    'monday' => '9:00 AM - 6:00 PM',
                    'tuesday' => '9:00 AM - 6:00 PM',
                    'wednesday' => '9:00 AM - 6:00 PM',
                    'thursday' => '9:00 AM - 6:00 PM',
                    'friday' => '9:00 AM - 6:00 PM',
                    'saturday' => '9:00 AM - 2:00 PM',
                    'sunday' => 'Closed',
                ],
                'is_active' => true,
                'display_order' => 1,
            ]);
        }

        return $contactInfo;
    }

    /**
     * Get active contact info
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first() ?: self::getInstance();
    }

    /**
     * Format phone number for display
     */
    public function formatPhone($phone)
    {
        return $phone ? str_replace(' ', '-', $phone) : null;
    }

    /**
     * Get formatted full address for headquarters
     */
    public function getHqFullAddressAttribute()
    {
        $parts = array_filter([
            $this->hq_address,
            $this->hq_city,
            $this->hq_state,
            $this->hq_country,
            $this->hq_postal_code
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get formatted full address for UK office
     */
    public function getUkFullAddressAttribute()
    {
        $parts = array_filter([
            $this->uk_address,
            $this->uk_city,
            $this->uk_state,
            $this->uk_country,
            $this->uk_postal_code
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get formatted full address for manufacturing unit
     */
    public function getManufacturingFullAddressAttribute()
    {
        $parts = array_filter([
            $this->manufacturing_address,
            $this->manufacturing_city,
            $this->manufacturing_state,
            $this->manufacturing_country,
            $this->manufacturing_postal_code
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get all support numbers as array
     */
    public function getSupportNumbersAttribute()
    {
        return [
            'Customer Support' => $this->customer_support_number,
            'Partner Support' => $this->partner_support_number,
            'Enquiry' => $this->enquiry_number,
            'Service Center' => $this->service_center_number,
        ];
    }

    /**
     * Get all email addresses as array
     */
    public function getEmailAddressesAttribute()
    {
        return [
            'General' => $this->general_email,
            'Business' => $this->business_email,
            'Support' => $this->support_email,
        ];
    }

    /**
     * Boot method for auto-setting updated_by
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->updated_by = auth()->user()->name ?? 'System';
        });
    }
}