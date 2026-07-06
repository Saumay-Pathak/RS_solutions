# File Upload Configuration Guide

## Current File Size Limits

### Application Limits
- **Software Files**: 200MB
- **All Other Files** (Images, Documents, Videos): 20MB

### PHP Configuration Requirements
- `upload_max_filesize`: 200M
- `post_max_size`: 210M
- `max_execution_time`: 300 seconds
- `max_input_time`: 300 seconds
- `max_file_uploads`: 20

## Configuration Steps

### 1. Check Current PHP Settings

Access the verification file (after setup):
```
https://yourdomain.com/check-upload-limits.php?token=check_uploads_2024
```

Or check via PHP CLI:
```bash
php -i | grep -E "upload_max_filesize|post_max_size"
```

### 2. Update PHP Configuration

#### Option A: php.ini (Recommended - Works with all PHP setups)

Find your `php.ini` file:
```bash
php --ini
```

Edit `php.ini` and set:
```ini
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
max_file_uploads = 20
```

Restart your web server:
```bash
# Apache
sudo service apache2 restart
# or
sudo systemctl restart apache2

# Nginx with PHP-FPM
sudo service php-fpm restart
# or
sudo systemctl restart php7.4-fpm  # Adjust version as needed
```

#### Option B: .htaccess (Apache only, may not work with PHP-FPM)

The `.htaccess` file in `public/` directory already contains:
```apache
php_value upload_max_filesize 200M
php_value post_max_size 210M
php_value max_execution_time 300
php_value max_input_time 300
```

**Note**: This only works if:
- Running Apache (not Nginx)
- PHP runs as Apache module (not PHP-FPM/CGI)
- Server allows `php_value` in .htaccess

#### Option C: .user.ini (If hosting provider allows)

Create or edit `.user.ini` in the `public/` directory:
```ini
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
max_file_uploads = 20
```

### 3. Nginx Configuration (If using Nginx)

If using Nginx with PHP-FPM, edit your Nginx config file (usually `/etc/nginx/sites-available/default`):

```nginx
server {
    # ... other settings ...
    
    client_max_body_size 210M;
    
    location ~ \.php$ {
        # ... PHP-FPM settings ...
        fastcgi_param PHP_VALUE "upload_max_filesize=200M \n post_max_size=210M";
    }
}
```

Then restart Nginx:
```bash
sudo nginx -t  # Test configuration
sudo service nginx restart
```

## Verification

### 1. Check via Web Interface

Visit: `https://yourdomain.com/check-upload-limits.php?token=check_uploads_2024`

**Important**: Delete this file after verification for security!

### 2. Check via PHP Code

Create a temporary test file:
```php
<?php
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
?>
```

### 3. Check Application Logs

The application will log warnings if upload limits are too low:
```bash
tail -f storage/logs/laravel.log
```

Look for messages like:
```
PHP upload limits are too low
```

## Troubleshooting

### Issue: Upload fails even after configuration

1. **Check if .htaccess is working:**
   - `.htaccess` only works with Apache, not Nginx
   - `.htaccess` doesn't work with PHP-FPM/FastCGI
   - Solution: Use `php.ini` or `.user.ini`

2. **Check server error logs:**
   ```bash
   # Apache
   tail -f /var/log/apache2/error.log
   
   # Nginx
   tail -f /var/log/nginx/error.log
   
   # PHP-FPM
   tail -f /var/log/php-fpm/error.log
   ```

3. **Verify file permissions:**
   - Storage directory must be writable:
   ```bash
   chmod -R 775 storage
   chown -R www-data:www-data storage
   ```

4. **Check disk space:**
   ```bash
   df -h
   ```

### Issue: "The file exceeds your upload_max_filesize directive"

- This means PHP settings haven't taken effect
- Restart your web server/PHP-FPM after changing `php.ini`
- Check which `php.ini` file is being used: `php --ini`

### Issue: Large files upload but fail validation

- Check Laravel validation rules in controllers
- All controllers are configured correctly
- Software: 200MB, Others: 20MB

## Application-Level Safeguards

The application includes:

1. **AppServiceProvider**: Attempts to set PHP limits at runtime (if allowed)
2. **CheckUploadLimits Middleware**: Monitors and logs upload limit issues
3. **Laravel Validation**: Enforces limits at application level

## Security Notes

1. **Delete test files after verification:**
   - `public/check-upload-limits.php` should be removed in production
   
2. **Monitor disk usage:**
   - Large file uploads consume storage space
   - Set up disk quota monitoring if needed

3. **Rate limiting:**
   - Consider implementing rate limits for file uploads
   - Prevent abuse of large upload capacity

## Support

If you continue to experience issues:

1. Verify PHP settings via `phpinfo()`
2. Check web server configuration
3. Review application logs
4. Contact your hosting provider if you don't have server access

