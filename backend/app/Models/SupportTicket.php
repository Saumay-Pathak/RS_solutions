<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class SupportTicket extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'support_tickets';

    protected $fillable = [
        'ticket_id',
        'name',
        'phone',
        'email',
        'pin_code',
        'state',
        'city',
        'area',
        'message',
        'status',
        'priority',
        'category',
        'assigned_to',
        'closed_by',
        'closed_at',
        'response',
        'internal_notes',
        'notes',
        'attachments',
        'source',
        'ip_address',
        'user_agent',
        'resolved_at',
        'first_response_at',
        'last_activity_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'internal_notes' => 'array',
        'notes' => 'array',
        'closed_at' => 'datetime',
        'resolved_at' => 'datetime',
        'first_response_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'open',
        'priority' => 'medium',
        'source' => 'website',
    ];

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CLOSED = 'closed';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_PENDING = 'pending';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Category constants
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_PRODUCT = 'product';
    const CATEGORY_COMPLAINT = 'complaint';

    /**
     * Boot method to auto-generate ticket ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ticket_id)) {
                $model->ticket_id = self::generateTicketId();
            }
            $model->last_activity_at = now();
        });

        static::updating(function ($model) {
            $model->last_activity_at = now();
            
            // Set first response time
            if (!$model->first_response_at && $model->response) {
                $model->first_response_at = now();
            }

            // Set resolved time
            if ($model->status === self::STATUS_RESOLVED && !$model->resolved_at) {
                $model->resolved_at = now();
            }

            // Set closed time and closed_by
            if ($model->status === self::STATUS_CLOSED && !$model->closed_at) {
                $model->closed_at = now();
                if (auth()->check()) {
                    $model->closed_by = auth()->user()->name ?? auth()->user()->email;
                }
            }
        });
    }

    /**
     * Generate unique ticket ID
     */
    public static function generateTicketId()
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $lastTicket = self::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastTicket ? (int)substr($lastTicket->ticket_id, -4) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    /**
     * Get all available priorities
     */
    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * Get all available categories
     */
    public static function getCategories()
    {
        return [
            self::CATEGORY_GENERAL => 'General Inquiry',
            self::CATEGORY_TECHNICAL => 'Technical Support',
            self::CATEGORY_PRODUCT => 'Product Support',
            self::CATEGORY_BILLING => 'Billing/Payment',
            self::CATEGORY_COMPLAINT => 'Complaint',
        ];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        $classes = [
            self::STATUS_OPEN => 'bg-primary',
            self::STATUS_IN_PROGRESS => 'bg-warning',
            self::STATUS_PENDING => 'bg-info',
            self::STATUS_RESOLVED => 'bg-success',
            self::STATUS_CLOSED => 'bg-secondary',
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeAttribute()
    {
        $classes = [
            self::PRIORITY_LOW => 'bg-success',
            self::PRIORITY_MEDIUM => 'bg-primary',
            self::PRIORITY_HIGH => 'bg-warning',
            self::PRIORITY_URGENT => 'bg-danger',
        ];

        return $classes[$this->priority] ?? 'bg-primary';
    }

    /**
     * Get assigned user relationship
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get user who added the response
     */
    public function responseUser()
    {
        return $this->belongsTo(User::class, 'response_by');
    }

    /**
     * Get formatted status
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get formatted priority
     */
    public function getPriorityLabelAttribute()
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }

    /**
     * Get formatted category
     */
    public function getCategoryLabelAttribute()
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->area, $this->city, $this->state, $this->pin_code]);
        return implode(', ', $parts);
    }

    /**
     * Get response time in hours
     */
    public function getResponseTimeAttribute()
    {
        if (!$this->first_response_at) {
            return null;
        }
        
        return $this->created_at->diffInHours($this->first_response_at);
    }

    /**
     * Get resolution time in hours
     */
    public function getResolutionTimeAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }
        
        return $this->created_at->diffInHours($this->resolved_at);
    }

    /**
     * Get age of ticket in days
     */
    public function getAgeInDaysAttribute()
    {
        return (int) $this->created_at->diffInDays(now());
    }

    /**
     * Check if ticket is overdue (open for more than 24 hours)
     */
    public function getIsOverdueAttribute()
    {
        if (in_array($this->status, [self::STATUS_CLOSED, self::STATUS_RESOLVED])) {
            return false;
        }
        
        return $this->age_in_days > 1;
    }

    /**
     * Scope: Open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope: In progress tickets
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope: Closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope: Recent tickets (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Scope: Overdue tickets
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CLOSED, self::STATUS_RESOLVED])
                     ->where('created_at', '<', now()->subHours(24));
    }

    /**
     * Scope: By priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: By category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Add internal note
     */
    public function addNote($note, $author = null)
    {
        $notes = $this->notes ?? [];
        $notes[] = [
            'note' => $note,
            'added_by' => auth()->user()->_id ?? null,
            'added_by_name' => $author ?? (auth()->user()->name ?? 'System'),
            'created_at' => now()->toISOString(),
        ];
        
        $this->update(['notes' => $notes]);
    }

    /**
     * Change status
     */
    public function changeStatus($newStatus, $note = null)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        
        if ($note) {
            $this->addNote("Status changed from {$oldStatus} to {$newStatus}. Note: {$note}");
        } else {
            $this->addNote("Status changed from {$oldStatus} to {$newStatus}");
        }
        
        $this->save();
    }
}