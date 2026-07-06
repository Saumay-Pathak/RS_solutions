<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\SolutionController as AdminSolutionController;
use App\Http\Controllers\Admin\SoftwareController as AdminSoftwareController;
use App\Http\Controllers\Admin\ExtrasController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\ContactInfoController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\HeaderFooterController;
use App\Http\Controllers\Admin\PartnerQueryController;
use App\Http\Controllers\Admin\ContactQueryController;
use App\Http\Controllers\Admin\SalesRequirementQueryController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\JobOpeningController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HomeSectionsController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\IntegrationModuleController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CertificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\SoftwareController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to admin dashboard
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Public Routes
Route::get('/solutions', [SolutionController::class, 'index'])->name('solutions');
Route::get('/solutions/{slug}', [SolutionController::class, 'show'])->name('solutions.show');
Route::get('/api/solutions', [SolutionController::class, 'api'])->name('solutions.api');

Route::get('/software', [SoftwareController::class, 'index'])->name('software');
Route::get('/software/{slug}', [SoftwareController::class, 'show'])->name('software.show');
Route::get('/software/{slug}/download', [SoftwareController::class, 'download'])->name('software.download');
Route::get('/api/software', [SoftwareController::class, 'api'])->name('software.api');

// Admin Routes (Protected by Auth)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::post('/dashboard/refresh', [AdminController::class, 'refreshStats'])->name('dashboard.refresh');
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');
    Route::get('/search', [AdminController::class, 'searchGlobal'])->name('search');
    
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminController::class, 'changePassword'])->name('profile.password');
    
    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Role Management
    Route::resource('roles', RoleController::class);
    Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::get('permissions', [RoleController::class, 'getPermissions'])->name('permissions');
    
    // Category Management
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // Product Management
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::post('/products/{product}/add-feature', [ProductController::class, 'addFeature'])->name('products.add-feature');
    Route::delete('products/{product}/features/{index}', [ProductController::class, 'removeFeature'])->name('products.remove-feature');
    Route::post('products/{product}/specifications', [ProductController::class, 'addSpecification'])->name('products.add-specification');
    Route::delete('products/{product}/specifications/{index}', [ProductController::class, 'removeSpecification'])->name('products.remove-specification');
    Route::delete('products/{product}/images', [ProductController::class, 'deleteImage'])->name('products.delete-image');
    
    // Blog Management
    Route::resource('blogs', BlogController::class);
    Route::post('blogs/{blog}/toggle-status', [BlogController::class, 'toggleStatus'])->name('blogs.toggle-status');
    
    // Page Management
    Route::resource('pages', PageController::class);
    Route::post('pages/{page}/toggle-status', [PageController::class, 'toggleStatus'])->name('pages.toggle-status');
    Route::post('pages/{page}/sections', [PageController::class, 'addSection'])->name('pages.add-section');
    Route::delete('pages/{page}/sections/{index}', [PageController::class, 'removeSection'])->name('pages.remove-section');
    Route::put('pages/{page}/sections/{index}', [PageController::class, 'updateSection'])->name('pages.update-section');
    
    // Testimonial Management
    Route::resource('testimonials', TestimonialController::class);
    Route::patch('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');
    Route::patch('testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
    
    // Solution Management
    Route::resource('solutions', AdminSolutionController::class);
    Route::patch('solutions/{solution}/toggle-status', [AdminSolutionController::class, 'toggleStatus'])->name('solutions.toggle-status');
    Route::patch('solutions/{solution}/toggle-featured', [AdminSolutionController::class, 'toggleFeatured'])->name('solutions.toggle-featured');

    // Software Management
    Route::resource('software', AdminSoftwareController::class);
    Route::patch('software/{software}/toggle-status', [AdminSoftwareController::class, 'toggleStatus'])->name('software.toggle-status');
    Route::patch('software/{software}/toggle-featured', [AdminSoftwareController::class, 'toggleFeatured'])->name('software.toggle-featured');

    // Service Management
    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // Clients Management
    Route::resource('clients', ClientController::class);
    Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');
    Route::patch('clients/{client}/toggle-featured', [ClientController::class, 'toggleFeatured'])->name('clients.toggle-featured');

    // Certifications Management
    Route::resource('certifications', CertificationController::class);
    Route::patch('certifications/{certification}/toggle-status', [CertificationController::class, 'toggleStatus'])->name('certifications.toggle-status');
    
    // Extras/Settings Management
    Route::get('extras', [ExtrasController::class, 'index'])->name('extras.index');
    Route::patch('extras', [ExtrasController::class, 'update'])->name('extras.update');
    Route::post('extras/single', [ExtrasController::class, 'updateSingle'])->name('extras.update-single');
    Route::post('extras/reset', [ExtrasController::class, 'resetDefaults'])->name('extras.reset');
    Route::get('extras/export', [ExtrasController::class, 'export'])->name('extras.export');
    Route::post('extras/import', [ExtrasController::class, 'import'])->name('extras.import');
    Route::post('extras/clear-cache', [ExtrasController::class, 'clearCache'])->name('extras.clear-cache');
    
    // API routes for settings
    Route::get('api/settings', [ExtrasController::class, 'getAllSettings'])->name('api.settings.all');
    Route::get('api/settings/{key}', [ExtrasController::class, 'getSetting'])->name('api.settings.get');
    
    // Popup Management
    Route::resource('popups', PopupController::class);
    Route::patch('popups/{popup}/toggle-status', [PopupController::class, 'toggleStatus'])->name('popups.toggle-status');
    Route::get('popups/{popup}/preview', [PopupController::class, 'preview'])->name('popups.preview');

    // Galary Management
    Route::resource('galary', \App\Http\Controllers\Admin\GalaryController::class);
    Route::patch('galary/{item}/toggle-status', [\App\Http\Controllers\Admin\GalaryController::class, 'toggleStatus'])->name('galary.toggle-status');
    
    // About Us Management
    Route::get('about-us', [AboutUsController::class, 'index'])->name('about-us.index');
    Route::patch('about-us', [AboutUsController::class, 'update'])->name('about-us.update');
    Route::post('about-us/features', [AboutUsController::class, 'addFeature'])->name('about-us.add-feature');
    Route::delete('about-us/features/{index}', [AboutUsController::class, 'removeFeature'])->name('about-us.remove-feature');
    Route::post('about-us/custom-sections', [AboutUsController::class, 'addCustomSection'])->name('about-us.add-custom-section');
    Route::delete('about-us/custom-sections/{index}', [AboutUsController::class, 'removeCustomSection'])->name('about-us.remove-custom-section');
    Route::delete('about-us/media', [AboutUsController::class, 'removeMedia'])->name('about-us.remove-media');
    Route::post('about-us/generate-seo', [AboutUsController::class, 'generateSeo'])->name('about-us.generate-seo');
    Route::get('about-us/preview', [AboutUsController::class, 'preview'])->name('about-us.preview');
    
    // Contact Information Management
    Route::get('contact-info', [ContactInfoController::class, 'index'])->name('contact-info.index');
    Route::patch('contact-info', [ContactInfoController::class, 'update'])->name('contact-info.update');
    Route::post('contact-info/reset', [ContactInfoController::class, 'resetToDefaults'])->name('contact-info.reset');
    Route::post('contact-info/duplicate-office', [ContactInfoController::class, 'duplicateOffice'])->name('contact-info.duplicate-office');
    Route::get('api/contact-info', [ContactInfoController::class, 'getContactInfoApi'])->name('api.contact-info');
    
    // Support Ticket Management
    Route::get('support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('support-tickets/{ticket}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::patch('support-tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support-tickets.update-status');
    Route::patch('support-tickets/{ticket}/priority', [SupportTicketController::class, 'updatePriority'])->name('support-tickets.update-priority');
    Route::patch('support-tickets/{ticket}/category', [SupportTicketController::class, 'updateCategory'])->name('support-tickets.update-category');
    Route::patch('support-tickets/{ticket}/assign', [SupportTicketController::class, 'assignTicket'])->name('support-tickets.assign');
    Route::post('support-tickets/{ticket}/response', [SupportTicketController::class, 'addResponse'])->name('support-tickets.add-response');
    Route::post('support-tickets/{ticket}/note', [SupportTicketController::class, 'addNote'])->name('support-tickets.add-note');
    Route::delete('support-tickets/{ticket}', [SupportTicketController::class, 'destroy'])->name('support-tickets.destroy');
    Route::get('support-tickets/export/csv', [SupportTicketController::class, 'exportCsv'])->name('support-tickets.export-csv');
    Route::get('support-tickets/stats/dashboard', [SupportTicketController::class, 'getDashboardStats'])->name('support-tickets.dashboard-stats');
    
    // Header & Footer Settings Management
    Route::get('header-footer', [HeaderFooterController::class, 'index'])->name('header-footer.index');
    Route::put('header-footer', [HeaderFooterController::class, 'update'])->name('header-footer.update');
    Route::post('header-footer/delete-file', [HeaderFooterController::class, 'deleteFile'])->name('header-footer.delete-file');
    Route::get('header-footer/preview', [HeaderFooterController::class, 'preview'])->name('header-footer.preview');
    Route::post('header-footer/clear-cache', [HeaderFooterController::class, 'clearCache'])->name('header-footer.clear-cache');
    
    // Partner Queries Management
    Route::get('partner-queries', [PartnerQueryController::class, 'index'])->name('partner-queries.index');
    Route::get('partner-queries/{id}', [PartnerQueryController::class, 'show'])->name('partner-queries.show');
    Route::patch('partner-queries/{id}/status', [PartnerQueryController::class, 'updateStatus'])->name('partner-queries.update-status');
    Route::delete('partner-queries/{id}', [PartnerQueryController::class, 'destroy'])->name('partner-queries.destroy');
    Route::post('partner-queries/bulk-action', [PartnerQueryController::class, 'bulkAction'])->name('partner-queries.bulk-action');
    Route::get('partner-queries/export', [PartnerQueryController::class, 'export'])->name('partner-queries.export');
    
    // Contact Queries Management
    Route::get('contact-queries', [ContactQueryController::class, 'index'])->name('contact-queries.index');
    Route::get('contact-queries/{id}', [ContactQueryController::class, 'show'])->name('contact-queries.show');
    Route::patch('contact-queries/{id}/status', [ContactQueryController::class, 'updateStatus'])->name('contact-queries.update-status');
    Route::delete('contact-queries/{id}', [ContactQueryController::class, 'destroy'])->name('contact-queries.destroy');
    Route::post('contact-queries/bulk-action', [ContactQueryController::class, 'bulkAction'])->name('contact-queries.bulk-action');
    Route::get('contact-queries/export', [ContactQueryController::class, 'export'])->name('contact-queries.export');

    // Sales Requirement Queries Management
    Route::get('sales-requirement-queries', [SalesRequirementQueryController::class, 'index'])->name('sales-requirement-queries.index');
    Route::get('sales-requirement-queries/{id}', [SalesRequirementQueryController::class, 'show'])->name('sales-requirement-queries.show');
    Route::patch('sales-requirement-queries/{id}/status', [SalesRequirementQueryController::class, 'updateStatus'])->name('sales-requirement-queries.update-status');
    Route::delete('sales-requirement-queries/{id}', [SalesRequirementQueryController::class, 'destroy'])->name('sales-requirement-queries.destroy');
    Route::get('sales-requirement-queries/export', [SalesRequirementQueryController::class, 'export'])->name('sales-requirement-queries.export');
    
    // Hero Slides Management
    Route::resource('hero-slides', HeroSlideController::class);
    Route::patch('hero-slides/{heroSlide}/toggle-status', [HeroSlideController::class, 'toggleStatus'])->name('hero-slides.toggle-status');
    Route::post('hero-slides/update-order', [HeroSlideController::class, 'updateOrder'])->name('hero-slides.update-order');
    Route::post('hero-slides/bulk-delete', [HeroSlideController::class, 'bulkDelete'])->name('hero-slides.bulk-delete');
    Route::post('hero-slides/bulk-toggle-status', [HeroSlideController::class, 'bulkToggleStatus'])->name('hero-slides.bulk-toggle-status');

    // Job Openings Management
    Route::resource('job-openings', JobOpeningController::class);

    // FAQs Management
    Route::resource('faqs', FaqController::class);
    Route::patch('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');

    // 3rd Party Software Integration Modules Management
    Route::resource('integration-modules', IntegrationModuleController::class);
    Route::patch('integration-modules/{integrationModule}/toggle-status', [IntegrationModuleController::class, 'toggleStatus'])->name('integration-modules.toggle-status');

    // Home Sections Configuration
    Route::get('home-sections', [HomeSectionsController::class, 'edit'])->name('home-sections.edit');
    Route::post('home-sections', [HomeSectionsController::class, 'update'])->name('home-sections.update');
});

// Public Galary Routes
Route::get('/galary', [\App\Http\Controllers\GalaryController::class, 'index'])->name('galary');
Route::get('/galary/{slug}', [\App\Http\Controllers\GalaryController::class, 'show'])->name('galary.show');

Route::get('/clear-cache', function () {
	Artisan::call('cache:clear');
	Artisan::call('config:clear');
	Artisan::call('route:clear');
	Artisan::call('view:clear');

	return response()->json(['message' => 'Cache and config cleared successfully!']);
});