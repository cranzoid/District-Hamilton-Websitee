<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index()
    {
        return $this->renderSection(Category::SECTION_FOOD, 'Our Menu', 'menu.index');
    }

    public function drinks()
    {
        return $this->renderSection(Category::SECTION_DRINK, 'Drinks Menu', 'menu.index');
    }

    protected function renderSection(string $section, string $heading, string $view)
    {
        $categories = Category::where('is_visible', true)
            ->where('section', $section)
            ->with(['menuItems' => function ($query) {
                $query->where('is_visible', true)
                    ->with('tags')
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view($view, [
            'categories' => $categories,
            'section' => $section,
            'heading' => $heading,
        ]);
    }

    public function category(Category $category)
    {
        if (!$category->is_visible) {
            abort(404);
        }

        $category->load(['menuItems' => function ($query) {
            $query->where('is_visible', true)
                ->with('tags')
                ->orderBy('sort_order');
        }]);

        $categories = Category::where('is_visible', true)
            ->where('section', $category->section)
            ->orderBy('sort_order')
            ->get();

        return view('menu.category', [
            'category' => $category,
            'categories' => $categories,
            'section' => $category->section,
        ]);
    }

    public function show(string $slug)
    {
        $item = MenuItem::where('slug', $slug)
            ->where('is_visible', true)
            ->with(['category', 'tags', 'addOns' => function ($query) {
                $query->where('is_available', true)
                    ->orderBy('sort_order');
            }])
            ->firstOrFail();

        $relatedItems = MenuItem::where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->where('is_visible', true)
            ->where('is_available', true)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('menu.show', compact('item', 'relatedItems'));
    }
}
