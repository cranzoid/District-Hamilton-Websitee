<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_en',
        'name_fr',
        'slug',
        'description_en',
        'description_fr',
        'price',
        'preparation_time_minutes',
        'image_path',
        'is_pickup_only',
        'is_available',
        'is_visible',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_pickup_only' => 'boolean',
        'is_available' => 'boolean',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'preparation_time_minutes' => 'integer',
    ];

    /**
     * Get the category that owns the menu item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for this menu item.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(MenuItemTag::class);
    }

    /**
     * Get the add-ons for this menu item.
     */
    public function addOns(): HasMany
    {
        return $this->hasMany(AddOn::class);
    }

    /**
     * Get the order items for this menu item.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get localized name based on current app locale.
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'fr' && $this->name_fr ? $this->name_fr : $this->name_en;
    }

    /**
     * Get localized description based on current app locale.
     */
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'fr' && $this->description_fr ? $this->description_fr : $this->description_en;
    }

    public static function getFeaturedItems()
    {
        return self::where('is_featured', true)
            ->where('is_visible', true)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->get();
    }
}
