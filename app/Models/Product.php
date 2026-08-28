<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'weight_gram',
        'condition',
        'status',
        'sold_count',
        'avg_rating',
        'specs',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'weight_gram' => 'integer',
            'sold_count' => 'integer',
            'avg_rating' => 'decimal:1',
            'specs' => 'array',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function flashSale()
    {
        return $this->hasOne(FlashSale::class)
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helpers
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return asset('storage/' . $primary->image_path);
        }
        $first = $this->images->first();
        return $first ? asset('storage/' . $first->image_path) : asset('images/digital-hook-logo-white.png');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isOwnedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $storeUserId = $this->relationLoaded('store')
            ? $this->store?->user_id
            : Store::whereKey($this->store_id)->value('user_id');

        return (int) $storeUserId === (int) $user->getKey();
    }
}
