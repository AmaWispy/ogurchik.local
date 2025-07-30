<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class PageObserver
{
    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        $this->updateRobotsTxt();
        $this->updateSitemap();
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        $this->updateRobotsTxt();
        $this->updateSitemap();
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        // Если изменился slug или статус активности
        if ($page->wasChanged('slug') || $page->wasChanged('is_active')) {
            $this->updateRobotsTxt();
            $this->updateSitemap();
        }
    }

    /**
     * Update robots.txt file with all active pages
     */
    private function updateRobotsTxt(): void
    {
        $content = "User-agent: *\nDisallow: /admin\n\n# Sitemap\nSitemap: " . config('app.url') . "/sitemap.xml\n\n# Pages\n";
        
        // Добавляем все активные страницы
        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $content .= "Allow: /pages/{$page->slug}\n";
        }

        // Записываем файл
        File::put(public_path('robots.txt'), $content);
    }

    /**
     * Update sitemap.xml file
     */
    private function updateSitemap(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Добавляем главную страницу
        $content .= $this->getSitemapUrl(config('app.url'), '1.0', 'daily');

        // Добавляем все активные страницы
        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $url = config('app.url') . '/pages/' . $page->slug;
            $lastmod = $page->updated_at->format('Y-m-d');
            $content .= $this->getSitemapUrl($url, '0.8', 'weekly', $lastmod);
        }

        $content .= '</urlset>';

        // Записываем файл
        File::put(public_path('sitemap.xml'), $content);
    }

    /**
     * Generate sitemap URL entry
     */
    private function getSitemapUrl(string $url, string $priority = '0.5', string $changefreq = 'monthly', ?string $lastmod = null): string
    {
        $entry = "    <url>\n";
        $entry .= "        <loc>" . htmlspecialchars($url) . "</loc>\n";
        if ($lastmod) {
            $entry .= "        <lastmod>" . $lastmod . "</lastmod>\n";
        }
        $entry .= "        <changefreq>" . $changefreq . "</changefreq>\n";
        $entry .= "        <priority>" . $priority . "</priority>\n";
        $entry .= "    </url>\n";
        return $entry;
    }
} 