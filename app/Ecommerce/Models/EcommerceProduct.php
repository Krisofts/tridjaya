<?php

namespace App\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcommerceProduct extends Model
{
    use SoftDeletes;

    protected $table = 'ecommerce_products';

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'description',
        'price', 'price_sale', 'stock', 'weight',
        'image', 'images', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'price'      => 'integer',
        'price_sale' => 'integer',
        'stock'      => 'integer',
        'weight'     => 'integer',
        'images'     => 'array',
        'is_active'  => 'boolean',
        'is_featured'=> 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EcommerceCategory::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(EcommerceOrderItem::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getPriceSaleFormattedAttribute(): ?string
    {
        return $this->price_sale
            ? 'Rp ' . number_format($this->price_sale, 0, ',', '.')
            : null;
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->price_sale ?? $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->price_sale || $this->price == 0) return 0;
        return (int) round((($this->price - $this->price_sale) / $this->price) * 100);
    }
}