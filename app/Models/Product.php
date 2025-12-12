<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->orderBy('order');
    }

    function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values')->withPivot('attribute_value_id');
    }
    function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values')->withPivot('attribute_id');
    }

    function attributeWithValues(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values')
            ->distinct()
            ->orderBy('id', 'asc')
            ->with(['values' => function ($query) {
                $query->whereIn('id', function ($subquery) {
                    $subquery->select('attribute_value_id')
                        ->from('product_attribute_values')
                        ->where('product_id', $this->id)
                        ->orderBy('id', 'asc');
                });
            }]);
    }

    function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    function primaryVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', 1);
    }

    function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    function files(): HasMany
    {
        return $this->hasMany(ProductFile::class);
    }

    function getEffectivePriceAndStock(): array
    {
        $getPriceData = function ($id = null, $price, $special_price, $in_stock, $qty) {
            return [
                'id' => $id,
                'price' => $special_price > 0 ? $special_price : $price,
                'old_price' => $special_price > 0 ? $price : null,
                'in_stock' => $in_stock,
                'qty' => $qty
            ];
        };

        if ($this->variants()->exists()) {
            $variants = $this->variants()->where('in_stock', 1)->orderBy('is_default', 'asc')->get();

            foreach ($variants as $variant) {
                if ($variant->manage_stock) {
                    if ($variant->qty < 1) continue;

                    return $getPriceData(
                        $variant->id,
                        $variant->price,
                        $variant->special_price,
                        $variant->in_stock,
                        $variant->qty
                    );
                }

                // Stock is not managed, treat as unlimited
                return $getPriceData(
                    $variant->id,
                    $variant->price,
                    $variant->special_price,
                    $variant->in_stock,
                    'Unlimited'
                );
            }
            return $getPriceData(null, 0, 0, false, null);
        }

        // No variants exist, fallback to product-level stock
        $stockManaged = $this->manage_stock == 'yes';
        $inStock = $this->in_stock && (!$stockManaged || $this->qty > 0);

        $qty = $stockManaged ? ($this->qty > 0 ? $this->qty : 'null')
            : ($this->in_stock ? 'Unlimited' : null);

        return $getPriceData(null, $this->price, $this->special_price, $inStock, $qty);
    }


    function getVariantOrProductPriceAndStock(?int $variantId = null): array
    {
        $getPriceData = function ($id = null, $price, $special_price, $in_stock, $qty) {
            return [
                'id' => $id,
                'price' => $special_price > 0 ? $special_price : $price,
                'old_price' => $special_price > 0 ? $price : null,
                'in_stock' => $in_stock,
                'qty' => $qty
            ];
        };

        if ($variantId) {
            $variant = $this->variants()->where('id', $variantId)->first();

            if ($variant) {
                if ($variant->manage_stock && $variant->in_stock) {
                    if ($variant->qty < 1) {
                        return $getPriceData($variant->id, $variant->price, $variant->special_price, false, null);
                    }

                    return $getPriceData(
                        $variant->id,
                        $variant->price,
                        $variant->special_price,
                        $variant->in_stock,
                        $variant->qty
                    );
                }

                if(!$variant->manage_stock && $variant->in_stock) {
                    return $getPriceData(
                        $variant->id,
                        $variant->price,
                        $variant->special_price,
                        $variant->in_stock,
                        'Unlimited'
                    );
                }

                return $getPriceData(
                        $variant->id,
                        $variant->price,
                        $variant->special_price,
                        false,
                        null
                );

            }

        }

        // No variants exist, fallback to product-level stock
        $stockManaged = $this->manage_stock == 'yes';
        $inStock = $this->in_stock && (!$stockManaged || $this->qty > 0);

        $qty = $stockManaged ? ($this->qty > 0 ? $this->qty : 'null')
            : ($this->in_stock ? 'Unlimited' : null);

        return $getPriceData(null, $this->price, $this->special_price, $inStock, $qty);
    }

    function reviews() : HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    function rating() : float
    {
        if(!$this->reviews()->exists()) return 0;

        return round($this->reviews()->avg('rating'), 1);
    }
}
