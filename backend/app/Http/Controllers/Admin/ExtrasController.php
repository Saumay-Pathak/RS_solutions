<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class ExtrasController extends Controller
{
    /**
     * Display the extras/settings page
     */
    public function index()
    {
        // Initialize defaults if no settings exist
        if (SiteSetting::count() === 0) {
            SiteSetting::initializeDefaults();
        }

        // Ensure newly added default settings exist without overwriting current values
        SiteSetting::firstOrCreate(
            ['key' => 'custom_activity_tracker'],
            [
                'value' => false,
                'type' => 'boolean',
                'label' => 'Custom Activity Tracker',
                'description' => 'Enable custom activity tracking system',
                'group' => 'system',
                'order' => 2
            ]
        );

        $settings = SiteSetting::getGroupedSettings();
        
        return view('admin.extras.index', compact('settings'));
    }

    /**
     * Update site settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $updated = 0;
        
        foreach ($request->settings as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            
            if ($setting) {
                // Handle different value types
                $processedValue = $this->processSettingValue($value, $setting->type);
                
                $setting->update(['value' => $processedValue]);
                $updated++;
            }
        }

        return back()->with('success', "Successfully updated {$updated} setting(s)!");
    }

    /**
     * Update a single setting via AJAX
     */
    public function updateSingle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $setting = SiteSetting::where('key', $request->key)->first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Setting not found'], 404);
        }

        $processedValue = $this->processSettingValue($request->value, $setting->type);
        $setting->update(['value' => $processedValue]);

        return response()->json([
            'success' => true, 
            'message' => 'Setting updated successfully',
            'setting' => $setting->fresh()
        ]);
    }

    /**
     * Reset all settings to defaults
     */
    public function resetDefaults(Request $request)
    {
        try {
            SiteSetting::initializeDefaults();
            
            return back()->with('success', 'All settings have been reset to defaults!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Get setting by key (for API)
     */
    public function getSetting($key)
    {
        $setting = SiteSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], 404);
        }

        return response()->json([
            'key' => $setting->key,
            'value' => $setting->value,
            'type' => $setting->type
        ]);
    }

    /**
     * Get all settings (for API)
     */
    public function getAllSettings()
    {
        $settings = SiteSetting::getAllSettings();
        return response()->json($settings);
    }

    /**
     * Export settings as JSON
     */
    public function export()
    {
        $settings = SiteSetting::all()->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
                'group' => $setting->group,
                'order' => $setting->order
            ];
        });

        $filename = 'site_settings_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($settings)
                        ->header('Content-Type', 'application/json')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings from JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->path());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file');
            }

            $imported = 0;
            
            foreach ($settings as $setting) {
                if (!isset($setting['key']) || !isset($setting['value'])) {
                    continue;
                }

                SiteSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'type' => $setting['type'] ?? 'boolean',
                        'label' => $setting['label'] ?? ucwords(str_replace('_', ' ', $setting['key'])),
                        'description' => $setting['description'] ?? '',
                        'group' => $setting['group'] ?? 'general',
                        'order' => $setting['order'] ?? 0
                    ]
                );
                
                $imported++;
            }

            return back()->with('success', "Successfully imported {$imported} setting(s)!");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process setting value based on its type
     */
    private function processSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            
            case 'integer':
                return (int) $value;
            
            case 'float':
                return (float) $value;
            
            case 'array':
                return is_array($value) ? $value : (json_decode($value, true) ?: []);
            
            case 'json':
                return is_array($value) ? $value : (json_decode($value, true) ?: []);
            
            case 'text':
            case 'textarea':
            case 'url':
            case 'email':
            default:
                return (string) $value;
        }
    }

    /**
     * Get group icons for display
     */
    private function getGroupIcons()
    {
        return [
            'analytics' => 'fas fa-chart-line',
            'popups' => 'fas fa-window-restore',
            'features' => 'fas fa-cogs',
            'maintenance' => 'fas fa-tools',
            'security' => 'fas fa-shield-alt',
            'notifications' => 'fas fa-bell',
            'performance' => 'fas fa-tachometer-alt',
            'general' => 'fas fa-sliders-h'
        ];
    }
}