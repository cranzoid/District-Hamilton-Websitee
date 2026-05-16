<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('data/menu.json')), true);

        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        MenuItem::truncate();
        Category::truncate();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        foreach ($data['categories'] as $cat) {
            Category::create([
                'slug'           => $cat['slug'],
                'section'        => $cat['section'],
                'name_en'        => $cat['name_en'],
                'name_fr'        => $cat['name_fr'] ?? null,
                'description_en' => $cat['description_en'] ?? null,
                'description_fr' => $cat['description_fr'] ?? null,
                'sort_order'     => $cat['sort_order'],
                'is_visible'     => $cat['is_visible'] ?? true,
            ]);
        }

        $categoryMap = Category::pluck('id', 'slug');

        foreach ($data['items'] as $item) {
            $categoryId = $categoryMap[$item['category_slug']] ?? null;
            if (!$categoryId) {
                continue;
            }

            MenuItem::create([
                'category_id'    => $categoryId,
                'slug'           => $item['slug'],
                'name_en'        => $item['name_en'],
                'name_fr'        => $item['name_fr'] ?? null,
                'description_en' => $item['description_en'] ?? null,
                'description_fr' => $item['description_fr'] ?? null,
                'price'          => $item['price'],
                'sort_order'     => $item['sort_order'],
                'is_visible'     => $item['is_visible'] ?? true,
                'is_available'   => true,
                'is_featured'    => false,
            ]);
        }

        $this->command->info('Menu seeded: ' . Category::count() . ' categories, ' . MenuItem::count() . ' items.');
    }
}
