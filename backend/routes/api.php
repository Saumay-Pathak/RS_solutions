<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\HeaderFooterController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ContactFormController;
use App\Http\Controllers\Api\PartnerRegistrationController;
use App\Http\Controllers\Api\SalesRequirementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public Support Ticket API Routes
Route::prefix('support')->name('api.support.')->middleware('throttle:support')->group(function () {
    // Create a new support ticket
    Route::post('tickets', [SupportTicketController::class, 'store'])->name('tickets.store');
    
    // Get ticket status by ticket ID (public endpoint)
    Route::get('tickets/{ticketId}/status', [SupportTicketController::class, 'getStatus'])->name('tickets.status');
    
    // Get full ticket details with email verification
    Route::post('tickets/details', [SupportTicketController::class, 'getTicketDetails'])->name('tickets.details');
    
    // Get form options (categories, priorities, etc.)
    Route::get('form-options', [SupportTicketController::class, 'getFormOptions'])->name('form-options');
    
    // Get public statistics
    Route::get('stats', [SupportTicketController::class, 'getPublicStats'])->name('stats');
});

// Public Content API Routes
Route::prefix('content')->name('api.content.')->middleware('throttle:global')->group(function () {
    
    // Blogs API
    Route::get('blogs', [ContentController::class, 'blogs'])->name('blogs');
    
    // Solutions API
    Route::get('solutions', [ContentController::class, 'solutions'])->name('solutions');

    // Services API
    Route::get('services', [ContentController::class, 'services'])->name('services');
    
    // Integration Modules API
    Route::get('integration-modules', [ContentController::class, 'integrationModules'])->name('integration-modules');
    
    // Software API
    Route::get('software', [ContentController::class, 'software'])->name('software');
    Route::post('software/{slug}/increment-download', [ContentController::class, 'incrementDownload'])
    ->name('software.increment-download');
    
    // Products API
    Route::get('products', [ContentController::class, 'products'])->name('products');
    Route::get('featured-products', [ContentController::class, 'featuredProducts'])->name('featured-products');
    
    // Categories API
    Route::get('categories', [ContentController::class, 'categories'])->name('categories');
    Route::get('categories-tree', [ContentController::class, 'categoriesTree'])->name('categories-tree');
    
    // Testimonials API
    Route::get('testimonials', [ContentController::class, 'testimonials'])->name('testimonials');

    // Clients API
    Route::get('clients', [ContentController::class, 'clients'])->name('clients');

    // Certifications API
    Route::get('certifications', [ContentController::class, 'certifications'])->name('certifications');
    
    // FAQs API
    Route::get('faqs', [ContentController::class, 'faqs'])->name('faqs');
    
    // Pages API
    Route::get('pages', [ContentController::class, 'pages'])->name('pages');
    
    // Popups API
    Route::get('popups', [ContentController::class, 'popups'])->name('popups');
    
    // Hero Slides API
    Route::get('hero-slides', [ContentController::class, 'heroSlides'])->name('hero-slides');

    // Job Openings API
    Route::get('job-openings', [ContentController::class, 'jobOpenings'])->name('job-openings');
    Route::get('job-openings/{identifier}', [ContentController::class, 'jobOpening'])->name('job-openings.show');
    
    // Contact Information API
    Route::get('contact-info', [ContentController::class, 'contactInfo'])->name('contact-info');
    
    // About Us API
    Route::get('about-us', [ContentController::class, 'aboutUs'])->name('about-us');
    
    // Single Item API (works for any content type)
    Route::get('{type}/{identifier}', [ContentController::class, 'show'])->name('show');
    
    // Statistics API
    Route::get('statistics', [ContentController::class, 'statistics'])->name('statistics');
    
    // Filter Options API
    Route::get('filter-options', [ContentController::class, 'filterOptions'])->name('filter-options');
});

// HeaderFooter/SEO API Routes
Route::prefix('site')->name('api.site.')->middleware('throttle:global')->group(function () {
    
    // Get complete header and footer data
    Route::get('header-footer', [HeaderFooterController::class, 'all'])->name('header-footer');
    
    // Get header data only
    Route::get('header', [HeaderFooterController::class, 'header'])->name('header');
    
    // Get footer data only
    Route::get('footer', [HeaderFooterController::class, 'footer'])->name('footer');
    
    // Get SEO meta data
    Route::get('seo', [HeaderFooterController::class, 'seo'])->name('seo');

    // Counters API
    Route::get('counters', [HeaderFooterController::class, 'counters'])->name('counters');

    // Home Sections API
    Route::get('sections', [HeaderFooterController::class, 'sections'])->name('sections');

    // Policy Pages API
    Route::get('privacy-policy', [HeaderFooterController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::get('terms-of-service', [HeaderFooterController::class, 'termsOfService'])->name('terms-of-service');
    Route::get('cookie-policy', [HeaderFooterController::class, 'cookiePolicy'])->name('cookie-policy');
    Route::get('disclaimer', [HeaderFooterController::class, 'disclaimer'])->name('disclaimer');
});

// Analytics & Tracking API Routes
Route::prefix('analytics')->name('api.analytics.')->middleware('throttle:analytics')->group(function () {
    
    // Website Visit Tracking
    Route::post('visits', [AnalyticsController::class, 'recordVisit'])->name('visits.record');
    Route::put('visits', [AnalyticsController::class, 'updateVisit'])->name('visits.update');
    
    // User Activity Tracking
    Route::post('activities', [AnalyticsController::class, 'recordActivity'])->name('activities.record');
    Route::post('activities/batch', [AnalyticsController::class, 'recordActivities'])->name('activities.batch');
    
    // Analytics Statistics
    Route::get('stats', [AnalyticsController::class, 'getStats'])->name('stats');
    Route::get('visit-count', [AnalyticsController::class, 'getVisitCount'])->name('visit-count');
});

// Contact Form API Routes
Route::prefix('contact')->name('api.contact.')->middleware('throttle:contact')->group(function () {
    
    // Form Submissions
    Route::post('submit', [ContactFormController::class, 'submit'])->name('submit');
    Route::post('quote', [ContactFormController::class, 'submitQuote'])->name('quote');
    Route::post('newsletter', [ContactFormController::class, 'subscribeNewsletter'])->name('newsletter');
    
    // Submission Status
    Route::get('status/{submissionId}', [ContactFormController::class, 'getStatus'])->name('status');
    
    // Statistics (for admin dashboard)
    Route::get('stats', [ContactFormController::class, 'getStats'])->name('stats');
});

// Partner Registration API Routes
Route::prefix('partners')->name('api.partners.')->middleware('throttle:partners')->group(function () {
    
    // Partner Registration
    Route::post('register', [PartnerRegistrationController::class, 'register'])->name('register');
    
    // Registration Status
    Route::get('status/{registrationId}', [PartnerRegistrationController::class, 'getStatus'])->name('status');
    
    // Partnership Information
    Route::get('types', [PartnerRegistrationController::class, 'getPartnershipTypes'])->name('types');
    
    // Statistics (for admin dashboard)
    Route::get('stats', [PartnerRegistrationController::class, 'getStats'])->name('stats');
});

// Sales Requirement API Routes
Route::prefix('sales')->name('api.sales.')->middleware('throttle:sales')->group(function () {
    // Submit sales requirement form
    Route::post('requirements', [SalesRequirementController::class, 'submit'])->name('requirements.submit');
});