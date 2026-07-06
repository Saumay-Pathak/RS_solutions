<?php
/**
 * PHP Upload Limits Checker
 * 
 * This file helps verify that PHP upload limits are configured correctly.
 * Access this file via: https://yourdomain.com/check-upload-limits.php
 * 
 * IMPORTANT: Delete this file after verification for security reasons.
 */

// Security: Only allow access in development or with specific token
$allowedToken = 'check_uploads_2024'; // Change this or remove for production
$token = $_GET['token'] ?? '';

if ($token !== $allowedToken && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
    http_response_code(403);
    die('Access Denied');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Upload Limits Checker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 PHP Upload Limits Checker</h1>
        
        <?php
        function parseSize($size) {
            $size = trim($size);
            if (empty($size)) return 0;
            $last = strtolower($size[strlen($size) - 1]);
            $size = (int) $size;
            switch ($last) {
                case 'g': $size *= 1024;
                case 'm': $size *= 1024;
                case 'k': $size *= 1024;
            }
            return $size;
        }
        
        function formatBytes($bytes) {
            if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
            if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
            if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
            return $bytes . ' bytes';
        }
        
        $uploadMaxFilesize = parseSize(ini_get('upload_max_filesize'));
        $postMaxSize = parseSize(ini_get('post_max_size'));
        $maxFileUploads = ini_get('max_file_uploads');
        $maxExecutionTime = ini_get('max_execution_time');
        $maxInputTime = ini_get('max_input_time');
        
        $requiredUploadMax = 200 * 1024 * 1024; // 200MB
        $requiredPostMax = 210 * 1024 * 1024; // 210MB
        
        $uploadOk = $uploadMaxFilesize >= $requiredUploadMax;
        $postOk = $postMaxSize >= $requiredPostMax;
        $allOk = $uploadOk && $postOk;
        ?>
        
        <div class="status <?php echo $allOk ? 'ok' : 'error'; ?>">
            <strong><?php echo $allOk ? '✅ All upload limits are properly configured!' : '⚠️ Upload limits need attention!'; ?></strong>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Setting</th>
                    <th>Current Value</th>
                    <th>Required Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>upload_max_filesize</strong></td>
                    <td><?php echo ini_get('upload_max_filesize'); ?> (<?php echo formatBytes($uploadMaxFilesize); ?>)</td>
                    <td>200M (<?php echo formatBytes($requiredUploadMax); ?>)</td>
                    <td>
                        <?php if ($uploadOk): ?>
                            <span style="color: green;">✅ OK</span>
                        <?php else: ?>
                            <span style="color: red;">❌ TOO LOW</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>post_max_size</strong></td>
                    <td><?php echo ini_get('post_max_size'); ?> (<?php echo formatBytes($postMaxSize); ?>)</td>
                    <td>210M (<?php echo formatBytes($requiredPostMax); ?>)</td>
                    <td>
                        <?php if ($postOk): ?>
                            <span style="color: green;">✅ OK</span>
                        <?php else: ?>
                            <span style="color: red;">❌ TOO LOW</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>max_file_uploads</strong></td>
                    <td><?php echo $maxFileUploads; ?></td>
                    <td>20+</td>
                    <td>
                        <?php if ($maxFileUploads >= 20): ?>
                            <span style="color: green;">✅ OK</span>
                        <?php else: ?>
                            <span style="color: orange;">⚠️ WARNING</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>max_execution_time</strong></td>
                    <td><?php echo $maxExecutionTime; ?> seconds</td>
                    <td>300+ seconds</td>
                    <td>
                        <?php if ($maxExecutionTime >= 300 || $maxExecutionTime == 0): ?>
                            <span style="color: green;">✅ OK</span>
                        <?php else: ?>
                            <span style="color: orange;">⚠️ WARNING</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>max_input_time</strong></td>
                    <td><?php echo $maxInputTime; ?> seconds</td>
                    <td>300+ seconds</td>
                    <td>
                        <?php if ($maxInputTime >= 300 || $maxInputTime == -1): ?>
                            <span style="color: green;">✅ OK</span>
                        <?php else: ?>
                            <span style="color: orange;">⚠️ WARNING</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php if (!$allOk): ?>
            <div class="info">
                <h3>🔧 How to Fix:</h3>
                <p><strong>Option 1: Update php.ini (Recommended)</strong></p>
                <pre style="background: #f4f4f4; padding: 10px; border-radius: 4px;">
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
max_file_uploads = 20</pre>
                
                <p><strong>Option 2: Update .htaccess (Already configured, but may not work with PHP-FPM)</strong></p>
                <p>The .htaccess file already has these settings, but if you're using PHP-FPM or FastCGI, you need to update php.ini directly.</p>
                
                <p><strong>Option 3: Contact Your Hosting Provider</strong></p>
                <p>If you don't have access to php.ini, contact your hosting provider and ask them to increase these limits.</p>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <h3>📋 Application File Size Limits:</h3>
            <ul>
                <li><strong>Software Files:</strong> 200MB</li>
                <li><strong>All Other Files (Images, Documents, Videos):</strong> 20MB</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>Note:</strong> Delete this file after verification for security reasons.</p>
            <p>Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?> | PHP Version: <?php echo PHP_VERSION; ?></p>
        </div>
    </div>
</body>
</html>

