<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\PartnerRegistration;
use App\Models\ContactFormSubmission;

class MenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        try {
            // Get pending partner queries count
            $pendingPartnerQueries = PartnerRegistration::where('status', 'new')->count();
            
            // Get unread contact queries count  
            $unreadContactQueries = ContactFormSubmission::where('status', 'new')->count();
            
            $view->with([
                'pendingPartnerQueries' => $pendingPartnerQueries,
                'unreadContactQueries' => $unreadContactQueries
            ]);
        } catch (\Exception $e) {
            // If there's an error (like database connection), provide default values
            $view->with([
                'pendingPartnerQueries' => 0,
                'unreadContactQueries' => 0
            ]);
        }
    }
}