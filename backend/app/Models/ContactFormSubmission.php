<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class ContactFormSubmission extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contact_form_submissions';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'form_type',
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
        'replied_at',
        'closed_at',
        'attachments',
        'custom_fields',
        'city',
        'state',
        'country',
        'zip',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'closed_at' => 'datetime',
        'attachments' => 'array',
        'custom_fields' => 'array',
        'notes' => 'array',
    ];

    protected $dates = [
        'replied_at',
        'closed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get submissions by status
     */
    public static function byStatus($status = 'new')
    {
        return static::where('status', $status)
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get today's submissions
     */
    public static function today()
    {
        return static::whereDate('created_at', Carbon::today())
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get unread submissions
     */
    public static function unread()
    {
        return static::where('status', 'new')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
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
     * Get submissions statistics
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'new' => static::where('status', 'new')->count(),
            'read' => static::where('status', 'read')->count(),
            'replied' => static::where('status', 'replied')->count(),
            'closed' => static::where('status', 'closed')->count(),
            'today' => static::whereDate('created_at', Carbon::today())->count(),
        ];
    }
}