# Content Management API Documentation

## Base URL
```
/api/content/
```

All endpoints return JSON responses with the following structure:
```json
{
    "success": true|false,
    "data": {...},
    "message": "Optional message",
    "meta": {...},     // For paginated results
    "links": {...}     // For paginated results
}
```

---

## 📝 Blogs API

### GET `/api/content/blogs`
Get all blogs with comprehensive filtering options.

#### Available Filters:
- `status` - `published` (default), `draft`, `true`, `false`
- `category` - Filter by blog category
- `author_id` - Filter by author ID
- `tags` - Filter by tags (comma-separated or array)
- `reading_time_min` - Minimum reading time in minutes
- `reading_time_max` - Maximum reading time in minutes
- `date_from` - Filter from date (YYYY-MM-DD)
- `date_to` - Filter to date (YYYY-MM-DD)
- `year` - Filter by year
- `month` - Filter by month (1-12)
- `search` - Search in title, excerpt, and content
- `sort_by` - `published_at`, `title`, `reading_time`, `created_at`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/blogs?status=published&category=tech&tags=laravel,php&per_page=20&sort_by=published_at
```

---

## 🚀 Solutions API

### GET `/api/content/solutions`
Get all solutions with filtering options.

#### Available Filters:
- `status` - `true` (default), `false`
- `featured` - `true`, `false`
- `category` - Filter by solution category
- `price_range` - Filter by price range
- `technologies` - Filter by technologies (comma-separated or array)
- `delivery_time_max` - Maximum delivery time
- `search` - Search in title, short description, and description
- `sort_by` - `sort_order`, `created_at`, `title`, `featured`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/solutions?featured=true&technologies=php,laravel&category=web-development
```

---

## 💻 Software API

### GET `/api/content/software`
Get all software with filtering options.

#### Available Filters:
- `status` - `true` (default), `false`
- `featured` - `true`, `false`
- `is_free` - `true`, `false`
- `main_category` - Filter by main category
- `sub_category` - Filter by sub category
- `license` - Filter by license type
- `developer` - Filter by developer
- `platforms` - Filter by platforms (comma-separated or array)
- `tags` - Filter by tags (comma-separated or array)
- `version` - Filter by version (partial match)
- `min_downloads` - Minimum download count
- `date_from` - Filter from release date
- `date_to` - Filter to release date
- `year` - Filter by release year
- `month` - Filter by release month
- `search` - Search in title, description, one line description
- `sort_by` - `sort_order`, `created_at`, `title`, `download_count`, `featured`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/software?is_free=true&platforms=windows,linux&featured=true&sort_by=download_count
```

---

## 📦 Products API

### GET `/api/content/products`
Get all products with filtering options.

#### Available Filters:
- `status` - `true` (default), `false`
- `category_id` - Filter by category ID
- `search` - Search in title and description
- `sort_by` - `sort_order`, `created_at`, `title`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/products?category_id=60f1b2c3d4e5f6789abcdef0&search=biometric
```

#### Response Notes:
- Each product item includes `faqs` as an array of objects `{ question, answer }`.
- Field `a_plus_content_html` mirrors `a_plus_content` for convenience.

---

## 📂 Categories API

### GET `/api/content/categories`
Get all categories with filtering options.

#### Available Filters:
- `status` - `true` (default), `false`
- `parent_only` - `true` - Get only parent categories
- `parent_id` - Get children of specific parent
- `with_product_count` - `true` - Include product count
- `search` - Search in name and description
- `sort_by` - `sort_order`, `name`, `created_at`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/categories?parent_only=true&with_product_count=true&sort_by=name&sort_order=asc
```

---

## ⭐ Testimonials API

### GET `/api/content/testimonials`
Get all testimonials with filtering options.

#### Available Filters:
- `status` - `true` (default), `false`
- `featured` - `true`, `false`
- `min_rating` - Minimum rating (1-5)
- `rating` - Exact rating (1-5)
- `company` - Filter by company name
- `position` - Filter by position
- `search` - Search in name, content, company, position
- `sort_by` - `sort_order`, `rating`, `created_at`, `featured`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/testimonials?featured=true&min_rating=4&sort_by=rating&sort_order=desc
```

---

## 📄 Pages API

### GET `/api/content/pages`
Get all pages with filtering options.

#### Available Filters:
- `status` - `true` (default), `false`
- `template` - Filter by template name
- `search` - Search in title, content, excerpt
- `sort_by` - `sort_order`, `title`, `created_at`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/pages?template=landing&search=home
```

---

## 🔔 Popups API

### GET `/api/content/popups`
Get all popups with filtering options.

#### Available Filters:
- `active_only` - `true` (default), `false` - Only show active popups within date range
- `type` - Filter by popup type (`modal`, `banner`, `slide_in`, `fullscreen`, `video`, `newsletter`, `promotion`, `announcement`)
- `position` - Filter by position (`center`, `top`, `bottom`, `left`, `right`, `top-left`, `top-right`, `bottom-left`, `bottom-right`)
- `page` - Filter by target page
- `target_users` - Filter by target users (`all`, `new`, `returning`, `logged_in`, `guests`)
- `search` - Search in title and content
- `date_from` - Filter from start date
- `date_to` - Filter to end date
- `sort_by` - `priority`, `created_at`, `title`
- `sort_order` - `asc`, `desc` (default)
- `per_page` - Items per page (max 100, default 15)

#### Example:
```
GET /api/content/popups?active_only=true&type=modal&position=center&target_users=all
```

---

## 📞 Contact Information API

### GET `/api/content/contact-info`
Get active contact information (singleton data).

#### Response includes:
- Support numbers
- Email addresses
- Office locations (HQ, UK, Manufacturing)
- Social media links
- Business hours
- Additional contact details

#### Example:
```
GET /api/content/contact-info
```

---

## ℹ️ About Us API

### GET `/api/content/about-us`
Get published about us information (singleton data).

#### Response includes:
- Who We Are section
- Mission & Vision
- Custom sections
- SEO metadata

#### Example:
```
GET /api/content/about-us
```

---

## 🔍 Single Item API

### GET `/api/content/{type}/{identifier}`
Get single item by slug or ID from any content type.

#### Supported Types:
- `blog` or `blogs`
- `solution` or `solutions`
- `software`
- `product` or `products`
- `category` or `categories`
- `testimonial` or `testimonials`
- `page` or `pages`
- `popup` or `popups`

#### Examples:
```
GET /api/content/blog/my-blog-post-slug
GET /api/content/solution/60f1b2c3d4e5f6789abcdef0
GET /api/content/product/biometric-scanner
```

---

## 📊 Statistics API

### GET `/api/content/statistics`
Get comprehensive statistics for all content types.

#### Response includes stats for:
- Blogs (total, categories, recent)
- Solutions (total, featured, categories)
- Software (total, featured, free)
- Products (total, categories)
- Testimonials (total, featured, average rating)
- Pages (total)
- Popups (total, active now)

#### Example:
```
GET /api/content/statistics
```

---

## 🔧 Filter Options API

### GET `/api/content/filter-options`
Get dropdown options for all filterable fields.

#### Response includes:
- Blog categories
- Solution categories
- Software categories & platforms
- Popup types & positions
- Testimonial companies

#### Example:
```
GET /api/content/filter-options
```

---

## Common Query Parameters

### Pagination
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

### Sorting
- `sort_by` - Field to sort by (varies per endpoint)
- `sort_order` - `asc` or `desc` (default: `desc`)

### Search
- `search` - Full-text search across specified fields

### Date Filtering
- `date_from` - Start date (YYYY-MM-DD)
- `date_to` - End date (YYYY-MM-DD)
- `year` - Filter by year
- `month` - Filter by month (1-12)

### Boolean Filters
- Use `true`/`false` or `1`/`0` for boolean parameters

### Array Parameters
- Pass as comma-separated string: `tags=php,laravel,vue`
- Or as array: `tags[]=php&tags[]=laravel&tags[]=vue`

---

## Example Response Format

### Paginated Response:
```json
{
    "success": true,
    "data": [
        {
            "id": "60f1b2c3d4e5f6789abcdef0",
            "title": "Sample Blog Post",
            "slug": "sample-blog-post",
            // ... other fields
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75,
        "from": 1,
        "to": 15
    },
    "links": {
        "first": "/api/content/blogs?page=1",
        "last": "/api/content/blogs?page=5",
        "prev": null,
        "next": "/api/content/blogs?page=2"
    }
}
```

### Single Item Response:
```json
{
    "success": true,
    "data": {
        "id": "60f1b2c3d4e5f6789abcdef0",
        "title": "Sample Blog Post",
        "slug": "sample-blog-post",
        // ... other fields with relationships loaded
    }
}
```

### Error Response:
```json
{
    "success": false,
    "message": "Error message",
    "error": "Detailed error (only in debug mode)"
}
```

---

## Status Codes

- `200` - Success
- `400` - Bad Request (invalid parameters)
- `404` - Not Found
- `500` - Internal Server Error

---

## 🎨 Header & Footer API

### GET `/api/site/header`
Get complete header data including branding, navigation menu, and settings.

#### Response includes:
- Site branding (title, tagline, logo, favicon)
- Dynamic navigation menu with dropdowns
- Header display settings (search, language switcher, dark mode)
- Custom scripts and CSS injection points
- Analytics tracking IDs

#### Example:
```bash
curl -X GET "http://localhost/api/site/header" \
  -H "Accept: application/json"
```

---

### GET `/api/site/footer`
Get complete footer data including contact info, social links, and quick links.

#### Response includes:
- Footer branding and logo
- Contact information (email, phone, address)
- Social media links (Facebook, Twitter, LinkedIn, etc.)
- Quick links organized by categories
- Footer scripts and custom JavaScript

#### Example:
```bash
curl -X GET "http://localhost/api/site/footer" \
  -H "Accept: application/json"
```

---

### GET `/api/site/seo`
Get SEO metadata, Open Graph tags, Twitter cards, and structured data.

#### Query Parameters:
- `url` - Current page URL for canonical and OG URL (optional)
- `page_title` - Override page title (optional)
- `page_description` - Override page description (optional)

#### Response includes:
- Meta tags (title, description, keywords, robots, canonical)
- Open Graph tags for social media sharing
- Twitter Card data
- Icons (favicon, Apple touch icon)
- JSON-LD schema markup for structured data
- Analytics tracking IDs (Google Analytics, GTM, Facebook Pixel)

#### Example:
```bash
curl -X GET "http://localhost/api/site/seo?url=https://example.com&page_title=Custom%20Title" \
  -H "Accept: application/json"
```

---

### GET `/api/site/header-footer`
Get complete header, footer, and SEO data in a single request.

#### Query Parameters:
- Same as individual endpoints (url, page_title, page_description)

#### Response includes:
- All header data
- All footer data  
- All SEO data

#### Example:
```bash
curl -X GET "http://localhost/api/site/header-footer" \
  -H "Accept: application/json"
```

---

## 📋 Header & Footer Response Examples

### Header Response:
```json
{
    "success": true,
    "data": {
        "branding": {
            "site_title": "My Website",
            "site_tagline": "Your tagline here",
            "logo_url": "https://example.com/storage/header-footer/logo.png",
            "favicon_url": "https://example.com/storage/header-footer/favicon.ico"
        },
        "navigation": [
            {
                "title": "Solutions",
                "url": "/solutions",
                "type": "dropdown",
                "children": [
                    {
                        "title": "Web Development",
                        "url": "/solutions/web-development",
                        "slug": "web-development"
                    }
                ]
            },
            {
                "title": "About Us",
                "url": "/about-us",
                "type": "single"
            }
        ],
        "settings": {
            "show_search_in_header": true,
            "show_language_switcher": false,
            "show_dark_mode_toggle": true,
            "header_style": "default"
        },
        "scripts": {
            "header_scripts": "<script>// Custom header script</script>",
            "google_analytics_id": "G-XXXXXXXXXX",
            "google_tag_manager_id": "GTM-XXXXXXX"
        },
        "custom_css": "/* Custom CSS styles */"
    }
}
```

### Footer Response:
```json
{
    "success": true,
    "data": {
        "branding": {
            "footer_logo_url": "https://example.com/storage/header-footer/footer-logo.png",
            "footer_description": "Brief description about your company",
            "footer_copyright": "© 2024 Your Company. All rights reserved."
        },
        "contact": {
            "email": "contact@example.com",
            "phone": "+1 (555) 123-4567",
            "address": "123 Main St, City, Country"
        },
        "social_media": {
            "facebook": "https://facebook.com/yourpage",
            "twitter": "https://twitter.com/yourhandle",
            "linkedin": "https://linkedin.com/company/yourcompany",
            "instagram": "https://instagram.com/yourhandle",
            "youtube": "https://youtube.com/yourchannel",
            "github": "https://github.com/yourusername"
        },
        "quick_links": {
            "company": [
                {"title": "About Us", "url": "/about-us"},
                {"title": "Contact", "url": "/contact"}
            ],
            "products": [
                {"title": "Solutions", "url": "/solutions"},
                {"title": "Software", "url": "/software"}
            ],
            "support": [
                {"title": "Help Center", "url": "/support"},
                {"title": "Documentation", "url": "/docs"}
            ],
            "legal": [
                {"title": "Privacy Policy", "url": "/privacy-policy"},
                {"title": "Terms of Service", "url": "/terms-of-service"}
            ]
        }
    }
}
```

### SEO Response:
```json
{
    "success": true,
    "data": {
        "meta": {
            "title": "My Website - Your tagline here",
            "description": "Meta description for search engines",
            "keywords": "keyword1, keyword2, keyword3",
            "robots": "index, follow",
            "canonical": "https://example.com"
        },
        "open_graph": {
            "og:title": "My Website",
            "og:description": "Meta description for social sharing",
            "og:type": "website",
            "og:url": "https://example.com",
            "og:image": "https://example.com/storage/header-footer/og-image.jpg",
            "og:site_name": "My Website"
        },
        "twitter": {
            "twitter:card": "summary_large_image",
            "twitter:site": "@yoursite",
            "twitter:creator": "@creator",
            "twitter:title": "My Website",
            "twitter:description": "Meta description for Twitter",
            "twitter:image": "https://example.com/storage/header-footer/og-image.jpg"
        },
        "icons": {
            "favicon": "https://example.com/storage/header-footer/favicon.ico",
            "apple_touch_icon": "https://example.com/storage/header-footer/apple-touch-icon.png"
        },
        "schema": {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "My Website",
            "description": "Meta description",
            "url": "https://example.com",
            "logo": "https://example.com/storage/header-footer/logo.png",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+1 (555) 123-4567",
                "email": "contact@example.com",
                "contactType": "customer service"
            },
            "sameAs": [
                "https://facebook.com/yourpage",
                "https://twitter.com/yourhandle"
            ]
        },
        "analytics": {
            "google_analytics": "G-XXXXXXXXXX",
            "google_tag_manager": "GTM-XXXXXXX",
            "google_search_console": "verification-content",
            "facebook_pixel": "123456789"
        }
    }
}
```

---

## 🔧 cURL Examples

### Get Header Data
```bash
curl -X GET "http://localhost/api/site/header" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

### Get Footer Data
```bash
curl -X GET "http://localhost/api/site/footer" \
  -H "Accept: application/json"
```

### Get SEO Data with Custom Parameters
```bash
curl -X GET "http://localhost/api/site/seo?url=https://example.com/about&page_title=About%20Us" \
  -H "Accept: application/json"
```

### Get All Header/Footer Data
```bash
curl -X GET "http://localhost/api/site/header-footer" \
  -H "Accept: application/json"
```

### Get Content API Data (existing endpoints)
```bash
# Get published blogs
curl -X GET "http://localhost/api/content/blogs?status=published&per_page=10" \
  -H "Accept: application/json"

# Get featured solutions
curl -X GET "http://localhost/api/content/solutions?featured=true" \
  -H "Accept: application/json"

# Get contact information
curl -X GET "http://localhost/api/content/contact-info" \
  -H "Accept: application/json"
```

---

## 📊 Analytics & Tracking API

### POST `/api/analytics/visits`
Record a website visit with comprehensive tracking data.

#### Request Body:
```json
{
    "session_id": "unique_session_id",
    "url": "https://example.com/page",
    "page_title": "Page Title",
    "referrer": "https://google.com",
    "utm_source": "google",
    "utm_medium": "organic",
    "utm_campaign": "summer_sale",
    "device_type": "desktop",
    "browser": "Chrome",
    "platform": "Windows",
    "screen_width": 1920,
    "screen_height": 1080
}
```

#### cURL Example:
```bash
curl -X POST "http://localhost/api/analytics/visits" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "session_id": "sess_123456789",
    "url": "https://example.com/products",
    "page_title": "Our Products",
    "referrer": "https://google.com",
    "device_type": "desktop",
    "browser": "Chrome"
  }'
```

---

### PUT `/api/analytics/visits`
Update visit data with time spent and bounce status.

#### Request Body:
```json
{
    "visit_id": "visit_id_from_record_response",
    "time_on_page": 45000,
    "is_bounce": false
}
```

#### cURL Example:
```bash
curl -X PUT "http://localhost/api/analytics/visits" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "visit_id": "670d1234567890abcdef1234",
    "time_on_page": 45000,
    "is_bounce": false
  }'
```

---

### POST `/api/analytics/activities`
Record user interactions and activities.

#### Request Body:
```json
{
    "session_id": "sess_123456789",
    "action": "click",
    "element": "button",
    "element_id": "cta-button",
    "element_text": "Get Started",
    "page_url": "https://example.com/landing",
    "coordinates_x": 500,
    "coordinates_y": 300,
    "device_type": "desktop"
}
```

#### Supported Actions:
- `click` - Element clicks
- `scroll` - Page scrolling
- `hover` - Element hover
- `form_submit` - Form submissions
- `download` - File downloads
- `view` - Element views
- `search` - Search actions
- `share` - Social sharing

#### cURL Example:
```bash
curl -X POST "http://localhost/api/analytics/activities" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "session_id": "sess_123456789",
    "action": "click",
    "element": "button",
    "element_id": "download-btn",
    "page_url": "https://example.com/software"
  }'
```

---

### POST `/api/analytics/activities/batch`
Record multiple activities in a single request (up to 100 activities).

#### Request Body:
```json
{
    "activities": [
        {
            "session_id": "sess_123456789",
            "action": "click",
            "page_url": "https://example.com/page1"
        },
        {
            "session_id": "sess_123456789",
            "action": "scroll",
            "page_url": "https://example.com/page1",
            "scroll_depth": 50
        }
    ]
}
```

---

### GET `/api/analytics/stats`
Get comprehensive analytics statistics.

#### Query Parameters:
- `period` - `today`, `week`, `month`, `30days`, `90days` (default: `30days`)

#### cURL Example:
```bash
curl -X GET "http://localhost/api/analytics/stats?period=week" \
  -H "Accept: application/json"
```

---

## 📝 Contact Forms API

### POST `/api/contact/submit`
Submit a general contact form.

#### Request Body:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1-555-123-4567",
    "company": "Example Corp",
    "subject": "Product Inquiry",
    "message": "I'm interested in your products...",
    "form_type": "contact",
    "page_url": "https://example.com/contact",
    "utm_source": "google",
    "custom_fields": {
        "budget": "$10,000-$50,000",
        "timeline": "3 months"
    }
}
```

#### Form Types:
- `contact` - General contact
- `quote` - Quote requests
- `support` - Support requests
- `consultation` - Consultation requests
- `partnership` - Partnership inquiries
- `demo` - Demo requests

#### cURL Example:
```bash
curl -X POST "http://localhost/api/contact/submit" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Product Demo Request",
    "message": "I would like to schedule a product demo.",
    "form_type": "demo"
  }'
```

---

### POST `/api/contact/quote`
Submit a detailed quote request.

#### Request Body:
```json
{
    "name": "Jane Smith",
    "email": "jane@company.com",
    "phone": "+1-555-987-6543",
    "company": "Tech Solutions Inc",
    "project_type": "Web Development",
    "budget_range": "$25,000-$50,000",
    "timeline": "6 months",
    "description": "We need a custom e-commerce platform...",
    "requirements": [
        "Mobile responsive",
        "Payment integration",
        "Inventory management"
    ],
    "preferred_contact": "email"
}
```

#### cURL Example:
```bash
curl -X POST "http://localhost/api/contact/quote" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@company.com",
    "phone": "+1-555-987-6543",
    "company": "Tech Solutions Inc",
    "project_type": "Mobile App Development",
    "description": "Need a mobile app for iOS and Android"
  }'
```

---

### POST `/api/contact/newsletter`
Subscribe to newsletter.

#### Request Body:
```json
{
    "email": "subscriber@example.com",
    "name": "John Subscriber",
    "interests": ["Web Development", "Mobile Apps", "Cloud Solutions"]
}
```

#### cURL Example:
```bash
curl -X POST "http://localhost/api/contact/newsletter" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "subscriber@example.com",
    "name": "John Subscriber",
    "interests": ["Technology Updates", "Product News"]
  }'
```

---

### GET `/api/contact/status/{submissionId}`
Get contact form submission status.

#### cURL Example:
```bash
curl -X GET "http://localhost/api/contact/status/670d1234567890abcdef1234" \
  -H "Accept: application/json"
```

---

## 🤝 Partner Registration API

### POST `/api/partners/register`
Submit a partner registration application.

#### Request Body:
```json
{
    "company_name": "Tech Partners LLC",
    "contact_person": "Sarah Johnson",
    "email": "sarah@techpartners.com",
    "phone": "+1-555-444-3333",
    "website": "https://techpartners.com",
    "address": "123 Business Ave",
    "city": "New York",
    "state": "NY",
    "country": "United States",
    "postal_code": "10001",
    "business_type": "Technology Consulting",
    "annual_revenue": 5000000,
    "employees_count": 25,
    "years_in_business": 8,
    "partnership_type": "reseller",
    "areas_of_interest": ["Software Solutions", "Cloud Services"],
    "target_markets": ["SMB", "Enterprise"],
    "why_partner": "We want to expand our solution portfolio...",
    "references": [
        {
            "name": "Mike Wilson",
            "company": "Reference Corp",
            "email": "mike@referencecorp.com",
            "phone": "+1-555-111-2222"
        }
    ]
}
```

#### Partnership Types:
- `reseller` - Reseller Partner
- `distributor` - Distribution Partner  
- `integrator` - System Integrator
- `consultant` - Solution Consultant
- `technology_partner` - Technology Partner
- `referral_partner` - Referral Partner

#### cURL Example:
```bash
curl -X POST "http://localhost/api/partners/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "company_name": "Tech Partners LLC",
    "contact_person": "Sarah Johnson",
    "email": "sarah@techpartners.com",
    "phone": "+1-555-444-3333",
    "address": "123 Business Ave",
    "city": "New York",
    "state": "NY",
    "country": "United States",
    "postal_code": "10001",
    "partnership_type": "reseller",
    "areas_of_interest": ["Software Solutions"],
    "why_partner": "We want to expand our solution portfolio"
  }'
```

---

### GET `/api/partners/status/{registrationId}`
Get partner registration status.

#### cURL Example:
```bash
curl -X GET "http://localhost/api/partners/status/670d1234567890abcdef1234" \
  -H "Accept: application/json"
```

---

### GET `/api/partners/types`
Get available partnership types with descriptions.

#### cURL Example:
```bash
curl -X GET "http://localhost/api/partners/types" \
  -H "Accept: application/json"
```

---

## 📩 Sales Requirement API

### POST `/api/sales/requirements`
Submit the "Send Us Your Requirement" form.

#### Request Body:
```json
{
  "name": "Test User",
  "email": "test@example.com",
  "phone_country_code": "+91",
  "phone": "9876543210",
  "state": "Karnataka",
  "country": "India",
  "requirement_type": "4G WiFi Router",
  "source": "General",
  "message": "Need 2 routers for office",
  "page_url": "https://example.com/sales",
  "referrer": "https://example.com/",
  "utm_source": "google",
  "utm_medium": "cpc",
  "utm_campaign": "q4-promo"
}
```

#### Required Fields:
- `phone` - string
- `country` - string
- `requirement_type` - one of the allowed types listed below
- `source` - one of the allowed sources listed below

#### Allowed Requirement Types:
- `Face + Fingerprint Device`
- `Face Device`
- `Aadhar Device`
- `Fingerprint Device`
- `4G WiFi Router`
- `4G/WiFi Cameras`
- `POE`
- `Accessories`
- `Support`
- `Others`

#### Allowed Sources:
- `General`
- `Social Media Ad`
- `Others`

#### Rate Limiting:
- Max 3 submissions per 5 minutes per IP. Exceeding this returns `429 Too Many Requests`.

#### Response (201 Created):
```json
{
  "success": true,
  "message": "Thank you. Your requirement has been submitted successfully.",
  "data": {
    "submission_id": "6710abc123def45678901234",
    "status": "new"
  }
}
```

#### Validation Error (400 Bad Request):
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "phone": ["The phone field is required."],
    "country": ["The country field is required."],
    "requirement_type": ["The selected requirement_type is invalid."],
    "source": ["The selected source is invalid."]
  }
}
```

#### Too Many Requests (429):
```json
{
  "success": false,
  "message": "Too many submissions. Please wait before submitting again."
}
```

#### cURL Example:
```bash
curl -X POST "http://localhost/api/sales/requirements" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone_country_code": "+91",
    "phone": "9876543210",
    "state": "Karnataka",
    "country": "India",
    "requirement_type": "4G WiFi Router",
    "source": "General",
    "message": "Need 2 routers for office"
  }'
```

#### Notes:
- `priority` is auto-determined by `requirement_type` (e.g., biometric devices -> `high`).
- The endpoint records `ip_address` and `user_agent` for rate limiting and analytics.
- Optional tracking fields (`page_url`, `referrer`, `utm_*`) are accepted and stored.

---

## 🔧 Additional cURL Examples

### Track Page Visit and Activity
```bash
# 1. Record initial visit
VISIT_RESPONSE=$(curl -s -X POST "http://localhost/api/analytics/visits" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "sess_demo_123",
    "url": "https://example.com/products",
    "page_title": "Products",
    "device_type": "desktop",
    "browser": "Chrome"
  }')

# 2. Extract visit_id from response
VISIT_ID=$(echo $VISIT_RESPONSE | jq -r '.data.visit_id')

# 3. Record user activity
curl -X POST "http://localhost/api/analytics/activities" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "sess_demo_123",
    "action": "click",
    "element": "button",
    "element_id": "buy-now",
    "page_url": "https://example.com/products"
  }'

# 4. Update visit with time spent
curl -X PUT "http://localhost/api/analytics/visits" \
  -H "Content-Type: application/json" \
  -d '{
    "visit_id": "'$VISIT_ID'",
    "time_on_page": 120000,
    "is_bounce": false
  }'
```

### Submit Contact Form and Check Status
```bash
# 1. Submit contact form
CONTACT_RESPONSE=$(curl -s -X POST "http://localhost/api/contact/submit" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Product Inquiry",
    "message": "Interested in your solutions",
    "form_type": "contact"
  }')

# 2. Extract submission_id
SUBMISSION_ID=$(echo $CONTACT_RESPONSE | jq -r '.data.submission_id')

# 3. Check submission status
curl -X GET "http://localhost/api/contact/status/$SUBMISSION_ID" \
  -H "Accept: application/json"
```

---

**Note:** Analytics and form endpoints are **WRITE-enabled** (POST/PUT requests) for data collection, while content endpoints remain **READ-only** (GET requests only) and return **public/published content** by default.
