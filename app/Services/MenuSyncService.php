<?php

namespace App\Services;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MenuSyncService
{
    public const ITEM_OWNED_FIELDS = [
        'name_en', 'name_fr', 'description_en', 'description_fr',
        'price', 'sort_order', 'is_visible',
    ];

    public const CATEGORY_OWNED_FIELDS = [
        'name_en', 'name_fr', 'description_en', 'description_fr',
        'section', 'sort_order', 'is_visible',
    ];

    public function load(string $path): array
    {
        if (! file_exists($path)) {
            throw new RuntimeException("Menu snapshot not found: {$path}");
        }

        $raw = file_get_contents($path);
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        return [
            'categories' => $data['categories'] ?? [],
            'items' => $data['items'] ?? [],
        ];
    }

    public function save(string $path, array $data): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $payload = [
            'categories' => array_values($data['categories'] ?? []),
            'items' => array_values($data['items'] ?? []),
        ];

        file_put_contents(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n"
        );
    }

    public function exportFromDb(): array
    {
        return [
            'categories' => Category::orderBy('sort_order')->get()->map(fn (Category $c) => [
                'slug' => $c->slug,
                'section' => $c->section ?? Category::SECTION_FOOD,
                'name_en' => $c->name_en,
                'name_fr' => $c->name_fr,
                'description_en' => $c->description_en,
                'description_fr' => $c->description_fr,
                'sort_order' => (int) $c->sort_order,
                'is_visible' => (bool) $c->is_visible,
            ])->all(),
            'items' => MenuItem::with('category')->orderBy('sort_order')->get()->map(fn (MenuItem $i) => [
                'slug' => $i->slug,
                'category_slug' => $i->category?->slug,
                'name_en' => $i->name_en,
                'name_fr' => $i->name_fr,
                'description_en' => $i->description_en,
                'description_fr' => $i->description_fr,
                'price' => (float) $i->price,
                'sort_order' => (int) $i->sort_order,
                'is_visible' => (bool) $i->is_visible,
            ])->all(),
        ];
    }

    public function diff(array $data): array
    {
        $currentCategories = Category::all()->keyBy('slug');
        $currentItems = MenuItem::all()->keyBy('slug');

        $snapshotItemSlugs = array_column($data['items'] ?? [], 'slug');

        $addedCategories = [];
        $updatedCategories = [];
        foreach ($data['categories'] ?? [] as $cat) {
            $existing = $currentCategories->get($cat['slug'] ?? null);
            if (! $existing) {
                $addedCategories[] = $cat['slug'];
                continue;
            }
            if ($this->categoryDiffers($existing, $cat)) {
                $updatedCategories[] = $cat['slug'];
            }
        }

        $added = [];
        $updated = [];
        foreach ($data['items'] ?? [] as $item) {
            $existing = $currentItems->get($item['slug'] ?? null);
            if (! $existing) {
                $added[] = $item['slug'];
                continue;
            }
            if ($this->itemDiffers($existing, $item)) {
                $updated[] = $item['slug'];
            }
        }

        $deactivated = $currentItems
            ->filter(fn (MenuItem $i) => $i->is_visible && ! in_array($i->slug, $snapshotItemSlugs, true))
            ->pluck('slug')
            ->all();

        return compact('addedCategories', 'updatedCategories', 'added', 'updated', 'deactivated');
    }

    public function apply(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $categoriesBySlug = [];
            foreach ($data['categories'] ?? [] as $cat) {
                $slug = $cat['slug'] ?? null;
                if (! $slug) {
                    throw new RuntimeException('Category entry missing slug.');
                }

                $category = Category::firstOrNew(['slug' => $slug]);
                if (! $category->exists) {
                    $category->is_deliverable = $cat['is_deliverable'] ?? true;
                }
                foreach (self::CATEGORY_OWNED_FIELDS as $field) {
                    if (array_key_exists($field, $cat)) {
                        $category->{$field} = $cat[$field];
                    }
                }
                $category->save();
                $categoriesBySlug[$slug] = $category->id;
            }

            $snapshotItemSlugs = [];
            $addedItems = [];
            $updatedItems = [];
            foreach ($data['items'] ?? [] as $item) {
                $slug = $item['slug'] ?? null;
                if (! $slug) {
                    throw new RuntimeException('Item entry missing slug.');
                }
                $snapshotItemSlugs[] = $slug;

                $categorySlug = $item['category_slug'] ?? null;
                $categoryId = $categoriesBySlug[$categorySlug] ?? null;
                if (! $categoryId && $categorySlug) {
                    $categoryId = Category::where('slug', $categorySlug)->value('id');
                }
                if (! $categoryId) {
                    throw new RuntimeException(
                        "Item {$slug} references unknown category '{$categorySlug}'"
                    );
                }

                $existing = MenuItem::where('slug', $slug)->first();
                $ownedPayload = [];
                foreach (self::ITEM_OWNED_FIELDS as $field) {
                    if (array_key_exists($field, $item)) {
                        $ownedPayload[$field] = $item[$field];
                    }
                }

                if ($existing) {
                    $existing->fill($ownedPayload);
                    $existing->category_id = $categoryId;
                    $existing->save();
                    $updatedItems[] = $slug;
                } else {
                    MenuItem::create(array_merge([
                        'preparation_time_minutes' => 15,
                        'is_pickup_only' => false,
                        'is_available' => true,
                        'is_featured' => false,
                    ], $ownedPayload, [
                        'slug' => $slug,
                        'category_id' => $categoryId,
                    ]));
                    $addedItems[] = $slug;
                }
            }

            $hidden = [];
            if (! empty($snapshotItemSlugs)) {
                $toHide = MenuItem::whereNotIn('slug', $snapshotItemSlugs)
                    ->where('is_visible', true)
                    ->get();
                foreach ($toHide as $row) {
                    $row->is_visible = false;
                    $row->save();
                    $hidden[] = $row->slug;
                }
            }

            return [
                'added' => $addedItems,
                'updated' => $updatedItems,
                'hidden' => $hidden,
            ];
        });
    }

    protected function categoryDiffers(Category $existing, array $incoming): bool
    {
        foreach (self::CATEGORY_OWNED_FIELDS as $field) {
            if (! array_key_exists($field, $incoming)) {
                continue;
            }
            if ((string) $existing->{$field} !== (string) $incoming[$field]) {
                return true;
            }
        }

        return false;
    }

    protected function itemDiffers(MenuItem $existing, array $incoming): bool
    {
        foreach (self::ITEM_OWNED_FIELDS as $field) {
            if (! array_key_exists($field, $incoming)) {
                continue;
            }
            if ($field === 'price') {
                if ((float) $existing->price !== (float) $incoming['price']) {
                    return true;
                }
            } elseif ((string) $existing->{$field} !== (string) $incoming[$field]) {
                return true;
            }
        }

        return false;
    }
}
