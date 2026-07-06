# Content Management System (CMS) - Laravel + MongoDB

## 🚀 Overview

A comprehensive Content Management System built with Laravel and MongoDB, featuring user management, product catalog, blog system, and dynamic page editor.

## 📋 Features

### ✅ Completed Features

### 1. **User Management System**
- Role-based access control (RBAC)
- User registration, authentication, and profile management
- Three default roles: Administrator, Content Editor, Viewer
- Page-level access permissions
- User status management (Active/Inactive)

### 2. **Product Management System**
- Hierarchical category system (parent/child categories)
- Product catalog with rich descriptions
- Multiple product features (dynamically add/remove)
- Product specifications with title-value pairs
- Multiple image uploads per product
- Product catalog document upload (PDF, DOC, DOCX)
- Category mapping and SEO optimization

### 3. **Blog Management System**
- Rich text blog post editor
- Featured image uploads
- Author assignment and management
- Tag system with multiple tags per post
- Reading time calculation
- Published/Draft status management
- Category-based blog organization

### 4. **Dynamic Page Editor**
- Page templates (Home, About, Solutions, Support, Software)
- Section-based content management
- Custom CSS and JavaScript per page
- SEO meta fields for all pages
- Dynamic content sections

### 5. **Admin Dashboard**
- Comprehensive statistics overview
- Recent activity feeds
- Quick action buttons
- Global search functionality
- Responsive design using theme components

## 🛠 Technical Stack

- **Backend**: Laravel 11.x
- **Database**: MongoDB with Laravel MongoDB package
- **Frontend**: Bootstrap 5 + Vuexy Theme Components
- **Icons**: Tabler Icons
- **Rich Text Editor**: Quill.js
- **File Upload**: Dropzone.js
- **Authentication**: Laravel's built-in auth with MongoDB

## 📁 Project Structure

```
app/
├── Http/Controllers/Admin/
│   ├── AdminController.php       # Dashboard and stats
│   ├── UserController.php        # User management
│   ├── RoleController.php        # Role management
│   ├── CategoryController.php    # Category management
│   ├── ProductController.php     # Product management
│   ├── BlogController.php        # Blog management
│   └── PageController.php        # Page management
├── Models/
│   ├── User.php                  # User model with RBAC
│   ├── Role.php                  # Role model
│   ├── Category.php              # Category model
│   ├── Product.php               # Product model
│   ├── Blog.php                  # Blog model
│   └── Page.php                  # Page model
resources/views/
├── layouts/
│   ├── app.blade.php             # Main layout
│   ├── menu-cms.blade.php        # CMS navigation menu
│   ├── navbar.blade.php          # Top navigation
│   └── footer.blade.php          # Footer component
└── admin/                        # Admin panel views
    ├── dashboard.blade.php       # Dashboard
    ├── users/                    # User management views
    ├── roles/                    # Role management views
    ├── categories/               # Category management views
    ├── products/                 # Product management views
    ├── blogs/                    # Blog management views
    └── pages/                    # Page management views
```

## 🚀 Installation & Setup

### 1. Clone and Install Dependencies
```bash
composer install
```

### 2. Environment Configuration
Update `.env` file with MongoDB settings:
```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=cms_laravel
DB_USERNAME=
DB_PASSWORD=
```

### 3. Run Database Seeder
```bash
php artisan db:seed
```

### 4. Start the Application
```bash
php artisan serve
```

## 👤 Default Login Credentials

### Administrator
- **Email**: `admin@cms.local`
- **Password**: `password`
- **Permissions**: Full system access

### Content Editor
- **Email**: `editor@cms.local`
- **Password**: `password`
- **Permissions**: Content management only

## 📊 Database Schema

### Collections Overview
- **users** - User accounts with role assignments
- **roles** - Role definitions with permissions
- **categories** - Product categories (hierarchical)
- **products** - Product catalog with features/specs
- **blogs** - Blog posts with rich content
- **pages** - Dynamic pages with sections

### Key Features in Models
- **Soft relationships** between MongoDB documents
- **Array fields** for features, specifications, tags
- **File handling** for images and documents
- **SEO optimization** fields in all content models

## 🎨 UI Components Used

### Theme Components
- **Cards**: Statistics, forms, and content displays
- **DataTables**: Listing pages with filtering
- **Forms**: Advanced form controls and validation
- **Modals**: Confirmation dialogs and quick actions
- **Dropdowns**: Action menus and filters
- **Badges**: Status indicators and labels
- **Progress Bars**: File uploads and loading states

### Advanced Features
- **Select2**: Enhanced dropdown selections
- **Quill Editor**: Rich text editing
- **Dropzone**: Drag-and-drop file uploads
- **Image Preview**: Real-time image previews
- **Dynamic Forms**: Add/remove form fields

## 📱 Responsive Design

The CMS is fully responsive and works on:
- Desktop computers (1920px+)
- Laptops (1366px+)
- Tablets (768px+)
- Mobile devices (320px+)

## 🔐 Security Features

- **Role-based Access Control (RBAC)**
- **Page-level Permissions**
- **Input Validation** on all forms
- **File Upload Security** with type restrictions
- **CSRF Protection** on all forms
- **Password Hashing** with bcrypt

## 🛣 Routes Structure

```
/admin/dashboard          # Main dashboard
/admin/users             # User management
/admin/roles             # Role management
/admin/categories        # Category management
/admin/products          # Product management
/admin/blogs             # Blog management
/admin/pages             # Page management
/admin/profile           # User profile
```

## 🔧 Customization

### Adding New Permissions
Edit `RoleController.php` and add to `$availablePermissions` array.

### Adding New Page Templates
Create new template files and update `PageController.php` templates array.

### Adding New User Fields
Update `User.php` model `$fillable` array and create corresponding form fields.

## 📈 Performance Optimization

- **MongoDB Indexing** on frequently queried fields
- **Eager Loading** for relationships
- **Pagination** on all listing pages
- **Image Optimization** recommendations
- **Caching** for static content

## 🐛 Known Issues & Solutions

1. **File Upload Size Limit**: Check `php.ini` settings for `upload_max_filesize`
2. **MongoDB Connection**: Ensure MongoDB service is running
3. **Image Display**: Use `Storage::url()` for proper image URLs

## 🔮 Future Enhancements

- [ ] Multi-language support
- [ ] Advanced SEO tools
- [ ] Email notifications
- [ ] File manager with folders
- [ ] Backup and restore functionality
- [ ] Advanced user permissions
- [ ] API endpoints for mobile app
- [ ] Real-time notifications

## 📞 Support

For support and questions, please check the documentation or create an issue in the repository.

---

**Built with ❤️ using Laravel, MongoDB, and Vuexy Theme Components**