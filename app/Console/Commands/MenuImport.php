<?php

namespace App\Console\Commands;

use App\Services\MenuSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;

class MenuImport extends Command
{
    protected $signature = 'menu:import
        {pdf : Path to the vendor menu PDF}
        {--out=database/data/menu.json : Output JSON path (relative to base_path)}
        {--write-only : Parse and write JSON but do not apply to the DB}
        {--merge : Merge with the existing snapshot instead of replacing (preserves translations + curated sort order)}';

    protected $description = 'Parse a menu PDF, update the committed snapshot JSON, and (optionally) apply to the DB.';

    public function handle(MenuSyncService $service): int
    {
        $pdfPath = $this->argument('pdf');
        if (! file_exists($pdfPath)) {
            $this->error("PDF not found: {$pdfPath}");

            return self::FAILURE;
        }

        $this->info("Parsing {$pdfPath}...");
        $parsed = $this->parsePdf($pdfPath);

        $this->info(sprintf(
            'Extracted %d categories and %d items.',
            count($parsed['categories']),
            count($parsed['items']),
        ));

        $out = base_path($this->option('out'));
        $data = $parsed;

        if ($this->option('merge') && file_exists($out)) {
            $data = $this->mergeWithExisting($service->load($out), $parsed);
            $this->info('Merged with existing snapshot (preserved translations and curated sort order where present).');
        }

        $service->save($out, $data);
        $this->info("Wrote snapshot to {$out}");

        if ($this->option('write-only')) {
            $this->line('Skipping DB apply. Commit the JSON and run `menu:sync` on deploy.');

            return self::SUCCESS;
        }

        return $this->call('menu:sync', ['--path' => $this->option('out')]);
    }

    /**
     * Heuristic PDF extraction.
     *
     * Tuned for a single-column layout where:
     *   - Category headings are short, uppercase lines.
     *   - Item rows end in a price token (`$12.00` or `12.00`).
     *   - An optional description follows on the next line(s).
     *
     * If the vendor's PDF layout departs from this, override by editing this
     * method or adding a vendor-specific parser class.
     */
    protected function parsePdf(string $path): array
    {
        $parser = new PdfParser;
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        if (trim($text) === '') {
            throw new RuntimeException('No extractable text in PDF (likely an image-only scan).');
        }

        $lines = preg_split('/\R+/u', $text) ?: [];
        $lines = array_values(array_filter(
            array_map(fn ($l) => trim(preg_replace('/\s+/u', ' ', $l)), $lines),
            fn ($l) => $l !== ''
        ));

        $categories = [];
        $items = [];
        $currentCategory = null;
        $categoryOrder = 0;
        $itemOrder = 0;

        $pricePattern = '/^(?<name>.+?)[\s\.\-–—·•]*\$?\s*(?<price>\d{1,3}(?:\.\d{2}))\s*$/u';
        $categoryPattern = '/^[A-Z0-9][A-Z0-9 &\'\-\/\.]{2,39}$/u';

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            if (preg_match($pricePattern, $line, $m)) {
                $name = trim($m['name'], " .·•\t-–—");
                $price = (float) $m['price'];
                if ($price <= 0 || $name === '' || mb_strlen($name) > 120) {
                    continue;
                }

                $description = null;
                if (isset($lines[$i + 1])
                    && ! preg_match($pricePattern, $lines[$i + 1])
                    && ! $this->looksLikeCategory($lines[$i + 1], $categoryPattern)) {
                    $description = $lines[$i + 1];
                    $i++;
                }

                $items[] = [
                    'slug' => Str::slug($name),
                    'category_slug' => $currentCategory ?? 'uncategorized',
                    'name_en' => $name,
                    'name_fr' => null,
                    'description_en' => $description,
                    'description_fr' => null,
                    'price' => $price,
                    'sort_order' => ++$itemOrder,
                    'is_visible' => true,
                ];

                continue;
            }

            if ($this->looksLikeCategory($line, $categoryPattern)) {
                $slug = Str::slug($line);
                if (! in_array($slug, array_column($categories, 'slug'), true)) {
                    $categories[] = [
                        'slug' => $slug,
                        'name_en' => Str::title(mb_strtolower($line)),
                        'name_fr' => null,
                        'description_en' => null,
                        'description_fr' => null,
                        'sort_order' => ++$categoryOrder,
                        'is_visible' => true,
                    ];
                }
                $currentCategory = $slug;
            }
        }

        $usesUncategorized = in_array('uncategorized', array_column($items, 'category_slug'), true);
        $hasUncategorized = in_array('uncategorized', array_column($categories, 'slug'), true);
        if ($usesUncategorized && ! $hasUncategorized) {
            array_unshift($categories, [
                'slug' => 'uncategorized',
                'name_en' => 'Uncategorized',
                'name_fr' => null,
                'description_en' => null,
                'description_fr' => null,
                'sort_order' => 0,
                'is_visible' => true,
            ]);
        }

        return compact('categories', 'items');
    }

    protected function looksLikeCategory(string $line, string $categoryPattern): bool
    {
        return preg_match($categoryPattern, $line)
            && mb_strlen($line) <= 40
            && ! preg_match('/\d{1,3}\.\d{2}/u', $line);
    }

    /**
     * Preserve human-curated fields (name_fr/description_fr + custom sort_order)
     * from the existing snapshot where the slug still exists.
     */
    protected function mergeWithExisting(array $existing, array $parsed): array
    {
        $existingCats = collect($existing['categories'] ?? [])->keyBy('slug');
        $existingItems = collect($existing['items'] ?? [])->keyBy('slug');

        $parsed['categories'] = array_map(function (array $cat) use ($existingCats) {
            $prev = $existingCats->get($cat['slug']);
            if ($prev) {
                $cat['name_fr'] = $prev['name_fr'] ?? $cat['name_fr'];
                $cat['description_fr'] = $prev['description_fr'] ?? $cat['description_fr'];
            }

            return $cat;
        }, $parsed['categories']);

        $parsed['items'] = array_map(function (array $item) use ($existingItems) {
            $prev = $existingItems->get($item['slug']);
            if ($prev) {
                $item['name_fr'] = $prev['name_fr'] ?? $item['name_fr'];
                $item['description_fr'] = $prev['description_fr'] ?? $item['description_fr'];
            }

            return $item;
        }, $parsed['items']);

        return $parsed;
    }
}
