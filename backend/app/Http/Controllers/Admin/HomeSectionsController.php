<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeSectionsController extends Controller
{
    private function defaultSections()
    {
        return [
            ['key' => 'hero', 'component' => 'HeroSection', 'title' => 'Hero Section', 'enabled' => true, 'order' => 1],
            ['key' => 'spotlight', 'component' => 'SpotlightSection', 'title' => 'Spotlight Section', 'enabled' => true, 'order' => 2],
            ['key' => 'services', 'component' => 'ServicesSections', 'title' => 'Services Sections', 'enabled' => true, 'order' => 3],
            ['key' => 'features', 'component' => 'FeaturesSection', 'title' => 'Features Section', 'enabled' => true, 'order' => 4],
            ['key' => 'stats', 'component' => 'StatsCounter', 'title' => 'Stats Counter', 'enabled' => true, 'order' => 5],
            ['key' => 'solutions', 'component' => 'SolutionsSection', 'title' => 'Solutions Section', 'enabled' => true, 'order' => 6],
            ['key' => 'testimonials', 'component' => 'TestimonialCarousel', 'title' => 'Testimonial Carousel', 'enabled' => true, 'order' => 7],
            ['key' => 'blog', 'component' => 'BlogSection', 'title' => 'Blog Section', 'enabled' => true, 'order' => 8],
            ['key' => 'contact', 'component' => 'ContactSection', 'title' => 'Contact Section', 'enabled' => true, 'order' => 9],
            // Newly added: Certifications Section
            ['key' => 'certifications', 'component' => 'CertificationsSection', 'title' => 'Certifications Section', 'enabled' => true, 'order' => 10],
            // Newly added: Our Clients Section
            ['key' => 'our_clients', 'component' => 'OurClientsSection', 'title' => 'Our Clients Section', 'enabled' => true, 'order' => 11],
        ];
    }

    public function edit()
    {
        $savedSections = SiteSetting::getValue('home_sections', $this->defaultSections());
        $defaults = collect($this->defaultSections())->keyBy('key');

        // Normalize saved items
        $current = collect($savedSections)->map(function ($item, $index) {
            return [
                'key' => $item['key'] ?? (is_string($item) ? $item : 'section_' . $index),
                'component' => $item['component'] ?? $item['title'] ?? 'Section',
                'title' => $item['title'] ?? ucfirst(str_replace('_', ' ', $item['key'] ?? 'Section')),
                'enabled' => (bool)($item['enabled'] ?? true),
                'order' => (int)($item['order'] ?? ($index + 1)),
            ];
        })->keyBy('key');

        // Merge in any new default sections that are missing
        $merged = $current->toBase();
        foreach ($defaults as $key => $def) {
            if (!$merged->has($key)) {
                $merged->put($key, $def);
            }
        }

        // Sort by order, then reindex and fix sequential order numbers
        $sections = $merged->values()->sortBy('order')->values()->map(function ($item, $idx) {
            $item['order'] = $idx + 1;
            return $item;
        })->toArray();

        return view('admin.home-sections.edit', compact('sections'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.key' => 'required|string',
            'sections.*.component' => 'required|string',
            'sections.*.title' => 'required|string',
            'sections.*.enabled' => 'required|boolean',
            'sections.*.order' => 'required|integer|min:1',
        ]);

        $sections = collect($validated['sections'])
            ->sortBy('order')
            ->values()
            ->toArray();

        SiteSetting::setValue(
            'home_sections',
            $sections,
            'json',
            'Home Sections',
            'Order and enable homepage sections',
            'site'
        );

        return redirect()->route('admin.home-sections.edit')
            ->with('success', 'Home sections updated successfully.');
    }
}
