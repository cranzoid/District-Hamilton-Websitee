<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MenuItem;
use App\Models\Category;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml file';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;

        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/menu', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/daily-offers', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/events', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/gift-cards', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/terms', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => '/privacy', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->generateUrlEntry(url($page['url']), $page['changefreq'], $page['priority']);
        }

        try {
            $categories = Category::query()
                ->when(\Schema::hasColumn('categories', 'is_visible'), fn($q) => $q->where('is_visible', true))
                ->get();
        } catch (\Throwable $e) {
            $categories = Category::all();
        }

        foreach ($categories as $category) {
            if (empty($category->slug)) continue;
            $lastmod = $category->updated_at ?? Carbon::now();
            $sitemap .= $this->generateUrlEntry(url("/menu/category/{$category->slug}"), 'weekly', '0.7', $lastmod);
        }

        $menuItems = MenuItem::query()
            ->when(\Schema::hasColumn('menu_items', 'is_visible'), fn($q) => $q->where('is_visible', true))
            ->get();

        foreach ($menuItems as $item) {
            if (empty($item->slug)) continue;
            $lastmod = $item->updated_at ?? Carbon::now();
            $sitemap .= $this->generateUrlEntry(url("/menu/item/{$item->slug}"), 'weekly', '0.6', $lastmod);
        }

        $sitemap .= '</urlset>';

        file_put_contents(public_path('sitemap.xml'), $sitemap);

        $count = count($staticPages) + $categories->count() + $menuItems->count();
        $this->info("Sitemap generated with {$count} URLs.");
    }

    private function generateUrlEntry($url, $changefreq, $priority, $lastmod = null)
    {
        $lastmod = $lastmod ? Carbon::parse($lastmod)->toAtomString() : Carbon::now()->toAtomString();
        return "    <url>\n" .
               "        <loc>" . htmlspecialchars($url, ENT_XML1) . "</loc>\n" .
               "        <lastmod>" . $lastmod . "</lastmod>\n" .
               "        <changefreq>" . $changefreq . "</changefreq>\n" .
               "        <priority>" . $priority . "</priority>\n" .
               "    </url>\n";
    }
}
