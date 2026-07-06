<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUploadLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check PHP upload limits for file upload requests
        if ($request->isMethod('POST') && ($request->hasFile('file') || $request->hasFile('image') || $request->hasFile('images'))) {
            $uploadMaxFilesize = $this->parseSize(ini_get('upload_max_filesize'));
            $postMaxSize = $this->parseSize(ini_get('post_max_size'));
            
            // Required limits: 200MB for upload, 210MB for post
            $requiredUploadMax = 200 * 1024 * 1024;
            $requiredPostMax = 210 * 1024 * 1024;
            
            if ($uploadMaxFilesize < $requiredUploadMax || $postMaxSize < $requiredPostMax) {
                \Log::warning('PHP upload limits are too low', [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'required_upload' => '200M',
                    'required_post' => '210M'
                ]);
                
                // Add a flash message warning (non-blocking, but logs the issue)
                // The request will continue, but large uploads may fail
            }
        }
        
        return $next($request);
    }
    
    /**
     * Parse size string (e.g., "200M", "8M") to bytes
     */
    private function parseSize(string $size): int
    {
        $size = trim($size);
        if (empty($size)) {
            return 0;
        }
        
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;
        
        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }
}

