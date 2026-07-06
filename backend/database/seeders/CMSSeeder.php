<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Page;
use Carbon\Carbon;

class CMSSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full access to all system features',
            'permissions' => [
                'user_management',
                'role_management',
                'product_management',
                'category_management',
                'blog_management',
                'page_management',
                'file_upload',
                'system_settings'
            ],
            'page_access' => [
                'dashboard',
                'users',
                'roles',
                'categories',
                'products',
                'blogs',
                'pages',
                'settings'
            ],
            'status' => true
        ]);

        $editorRole = Role::create([
            'name' => 'editor',
            'display_name' => 'Content Editor',
            'description' => 'Can manage content but not users',
            'permissions' => [
                'product_management',
                'category_management',
                'blog_management',
                'page_management',
                'file_upload'
            ],
            'page_access' => [
                'dashboard',
                'categories',
                'products',
                'blogs',
                'pages'
            ],
            'status' => true
        ]);

        $viewerRole = Role::create([
            'name' => 'viewer',
            'display_name' => 'Viewer',
            'description' => 'Read-only access to content',
            'permissions' => [],
            'page_access' => [
                'dashboard',
                'categories',
                'products',
                'blogs',
                'pages'
            ],
            'status' => true
        ]);

        // Create Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@cms.local',
            'password' => 'password',
            'role_id' => $adminRole->_id,
            'status' => true,
            'phone' => '+1234567890',
            'address' => '123 Admin Street, City, Country'
        ]);

        // Create Editor User
        $editorUser = User::create([
            'name' => 'Content Editor',
            'email' => 'editor@cms.local',
            'password' => 'password',
            'role_id' => $editorRole->_id,
            'status' => true,
            'phone' => '+1234567891',
            'address' => '456 Editor Avenue, City, Country'
        ]);

        // Create Categories
        $electronicsCategory = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
            'status' => true,
            'sort_order' => 1,
            'meta_title' => 'Electronics - Latest Gadgets and Devices',
            'meta_description' => 'Browse our collection of latest electronic devices and gadgets'
        ]);

        $smartphonesCategory = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Latest smartphones and mobile devices',
            'parent_id' => $electronicsCategory->_id,
            'status' => true,
            'sort_order' => 1,
            'meta_title' => 'Smartphones - Latest Mobile Phones',
            'meta_description' => 'Discover the latest smartphones with cutting-edge technology'
        ]);

        $laptopsCategory = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Powerful laptops for work and gaming',
            'parent_id' => $electronicsCategory->_id,
            'status' => true,
            'sort_order' => 2,
            'meta_title' => 'Laptops - High Performance Computers',
            'meta_description' => 'Find the perfect laptop for your needs'
        ]);

        $clothingCategory = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Fashion and apparel for all occasions',
            'status' => true,
            'sort_order' => 2,
            'meta_title' => 'Clothing - Fashion and Apparel',
            'meta_description' => 'Trendy clothing and fashion accessories'
        ]);

        // Create Products
        $smartphone = Product::create([
            'title' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'description' => '<p>The most advanced iPhone ever with titanium design, A17 Pro chip, and revolutionary camera system.</p><p>Experience the future of mobile technology with our most powerful iPhone yet.</p>',
            'category_id' => $smartphonesCategory->_id,
            'features' => [
                'Titanium Design',
                'A17 Pro Chip',
                'ProRAW Photography',
                '120Hz ProMotion Display',
                '5G Connectivity',
                'Face ID Security',
                'Wireless Charging',
                'Water Resistant'
            ],
            'specifications' => [
                ['title' => 'Display', 'value' => '6.7-inch Super Retina XDR'],
                ['title' => 'Chip', 'value' => 'A17 Pro'],
                ['title' => 'Storage', 'value' => '256GB, 512GB, 1TB'],
                ['title' => 'Camera', 'value' => 'Triple 48MP camera system'],
                ['title' => 'Battery', 'value' => 'Up to 29 hours video playback'],
                ['title' => 'Colors', 'value' => 'Natural Titanium, Blue Titanium, White Titanium, Black Titanium'],
                ['title' => 'Operating System', 'value' => 'iOS 17'],
                ['title' => 'Weight', 'value' => '221 grams']
            ],
            'images' => [],
            'status' => true,
            'sort_order' => 1,
            'meta_title' => 'iPhone 15 Pro Max - Most Advanced iPhone',
            'meta_description' => 'Get the iPhone 15 Pro Max with titanium design, A17 Pro chip, and revolutionary camera system.'
        ]);

        $laptop = Product::create([
            'title' => 'MacBook Pro 16-inch M3',
            'slug' => 'macbook-pro-16-m3',
            'description' => '<p>Supercharged by M3 Pro and M3 Max chips for exceptional performance.</p><p>Perfect for professionals, developers, and creators who demand the best.</p>',
            'category_id' => $laptopsCategory->_id,
            'features' => [
                'M3 Pro/Max Chip',
                '16-inch Liquid Retina XDR Display',
                'Up to 128GB Unified Memory',
                'Advanced Thermal Design',
                'Six-speaker Sound System',
                'Studio-quality Three-mic Array',
                'MagSafe 3 Charging',
                'Thunderbolt 4 Ports'
            ],
            'specifications' => [
                ['title' => 'Processor', 'value' => 'Apple M3 Pro/Max'],
                ['title' => 'Memory', 'value' => '18GB, 36GB, 128GB unified memory'],
                ['title' => 'Storage', 'value' => '512GB to 8TB SSD'],
                ['title' => 'Display', 'value' => '16.2-inch Liquid Retina XDR'],
                ['title' => 'Graphics', 'value' => 'Integrated GPU up to 40-core'],
                ['title' => 'Battery', 'value' => 'Up to 22 hours'],
                ['title' => 'Ports', 'value' => '3x Thunderbolt 4, HDMI, SDXC, MagSafe 3'],
                ['title' => 'Weight', 'value' => '2.14 kg (4.7 pounds)']
            ],
            'images' => [],
            'status' => true,
            'sort_order' => 1,
            'meta_title' => 'MacBook Pro 16-inch M3 - Professional Laptop',
            'meta_description' => 'Powerful MacBook Pro with M3 chip, perfect for professionals and creators.'
        ]);

        // Create Blog Posts
        $techBlog = Blog::create([
            'title' => 'The Future of Artificial Intelligence in 2024',
            'slug' => 'future-of-ai-2024',
            'content' => '<h2>Introduction</h2><p>Artificial Intelligence is rapidly evolving and transforming every industry. In 2024, we are witnessing unprecedented advances in AI technology that are reshaping how we work, live, and interact with technology.</p><h2>Key Developments</h2><p>From large language models to computer vision breakthroughs, AI is becoming more accessible and powerful than ever before.</p><p>Machine learning algorithms are now capable of understanding context, generating creative content, and solving complex problems across various domains.</p><h2>Impact on Industries</h2><p>Healthcare, finance, education, and manufacturing are all experiencing AI-driven transformations that improve efficiency and create new possibilities.</p><h2>Conclusion</h2><p>As we move forward, ethical AI development and responsible implementation will be crucial for harnessing the full potential of artificial intelligence.</p>',
            'excerpt' => 'Explore the latest developments in artificial intelligence and how they are transforming industries in 2024.',
            'author_id' => $adminUser->_id,
            'category' => 'Technology',
            'tags' => ['AI', 'Technology', 'Machine Learning', 'Future', 'Innovation'],
            'status' => true,
            'published_at' => Carbon::now()->subDays(5),
            'meta_title' => 'The Future of AI in 2024 - Latest Developments and Trends',
            'meta_description' => 'Discover the latest AI developments and their impact on various industries in 2024.',
            'reading_time' => 5
        ]);

        $productBlog = Blog::create([
            'title' => '10 Essential Features Every Modern Smartphone Should Have',
            'slug' => 'essential-smartphone-features-2024',
            'content' => '<h2>Modern Smartphone Essentials</h2><p>With technology advancing rapidly, smartphones have become more than just communication devices. Here are 10 essential features that every modern smartphone should include.</p><h2>1. High-Refresh Rate Display</h2><p>A 120Hz or higher refresh rate provides smoother scrolling and better gaming experience.</p><h2>2. Multiple Camera System</h2><p>Ultra-wide, telephoto, and standard lenses offer versatility in photography.</p><h2>3. Fast Charging Technology</h2><p>Quick charging capabilities that can power your device from 0 to 80% in under 30 minutes.</p><h2>4. 5G Connectivity</h2><p>Future-proof connectivity for faster downloads and streaming.</p><h2>5. Long-lasting Battery</h2><p>All-day battery life that can handle intensive usage.</p><p>And five more essential features that make smartphones truly smart and useful in our daily lives.</p>',
            'excerpt' => 'Discover the must-have features that define a modern smartphone and enhance user experience.',
            'author_id' => $editorUser->_id,
            'category' => 'Mobile',
            'tags' => ['Smartphones', 'Technology', 'Mobile', 'Features', 'Guide'],
            'status' => true,
            'published_at' => Carbon::now()->subDays(2),
            'meta_title' => 'Essential Smartphone Features 2024 - Complete Guide',
            'meta_description' => 'Learn about the 10 essential features every modern smartphone should have in 2024.',
            'reading_time' => 7
        ]);

        // Create Pages
        $homePage = Page::create([
            'title' => 'Welcome to Our CMS',
            'slug' => 'home',
            'content' => '<div class="hero-section"><h1>Welcome to Our Content Management System</h1><p>Manage your digital content with ease and efficiency.</p></div><div class="features-section"><h2>Key Features</h2><ul><li>User Management with Role-based Access</li><li>Product Catalog with Categories</li><li>Blog Management System</li><li>Dynamic Page Editor</li><li>SEO Optimization Tools</li></ul></div>',
            'excerpt' => 'A powerful content management system for modern businesses',
            'template' => 'home',
            'status' => true,
            'sort_order' => 1,
            'meta_title' => 'CMS - Content Management System',
            'meta_description' => 'Professional content management system with user management, product catalog, and blog functionality.',
            'sections' => [
                [
                    'type' => 'hero',
                    'title' => 'Welcome to Our CMS',
                    'content' => 'Manage your digital content with ease and efficiency.',
                    'order' => 1
                ],
                [
                    'type' => 'features',
                    'title' => 'Key Features',
                    'content' => 'User Management, Product Catalog, Blog System, Page Editor',
                    'order' => 2
                ]
            ]
        ]);

        $aboutPage = Page::create([
            'title' => 'About Us',
            'slug' => 'about',
            'content' => '<h1>About Our Company</h1><p>We are dedicated to providing innovative solutions for content management and digital experiences.</p><h2>Our Mission</h2><p>To empower businesses with powerful, user-friendly content management tools.</p><h2>Our Vision</h2><p>To be the leading provider of comprehensive CMS solutions worldwide.</p>',
            'excerpt' => 'Learn about our company, mission, and vision',
            'template' => 'about',
            'status' => true,
            'sort_order' => 2,
            'meta_title' => 'About Us - Our Company Story',
            'meta_description' => 'Learn about our company, mission, and vision for providing innovative CMS solutions.',
            'sections' => []
        ]);

        $solutionsPage = Page::create([
            'title' => 'Our Solutions',
            'slug' => 'solutions',
            'content' => '<h1>Comprehensive Solutions</h1><p>We offer a complete suite of content management solutions tailored for your business needs.</p><h2>Enterprise CMS</h2><p>Scalable content management for large organizations.</p><h2>E-commerce Integration</h2><p>Seamless integration with popular e-commerce platforms.</p><h2>Multi-language Support</h2><p>Reach global audiences with multi-language capabilities.</p>',
            'excerpt' => 'Explore our comprehensive content management solutions',
            'template' => 'services',
            'status' => true,
            'sort_order' => 3,
            'meta_title' => 'Solutions - Comprehensive CMS Services',
            'meta_description' => 'Discover our comprehensive content management solutions for businesses of all sizes.',
            'sections' => []
        ]);

        $this->command->info('CMS Seeder completed successfully!');
        $this->command->info('Admin User: admin@cms.local / password');
        $this->command->info('Editor User: editor@cms.local / password');
    }
}