<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price = $this->price;
        $specialPrice = $this->special_price;

        // If product has variants, use primary variant pricing
        if ($this->primaryVariant) {
            $price = $this->primaryVariant->price;
            $specialPrice = $this->primaryVariant->special_price;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->when($request->routeIs('*.show'), $this->description),
            'short_description' => $this->short_description,
            'price' => $price,
            'special_price' => $specialPrice,
            'final_price' => $specialPrice > 0 ? $specialPrice : $price,
            'discount_percentage' => $specialPrice > 0 ? round((($price - $specialPrice) / $price) * 100) : 0,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'is_featured' => (bool) $this->is_featured,
            'is_new' => (bool) $this->is_new,
            'is_hot' => (bool) $this->is_hot,
            'rating' => round($this->reviews_avg_rating ?? 0, 1),
            'reviews_count' => $this->reviews_count ?? 0,
            'thumbnail' => $this->thumbnail ? asset($this->thumbnail) : null,
            'images' => $this->when($this->relationLoaded('images'), function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset($image->image),
                        'is_primary' => (bool) $image->is_primary,
                    ];
                });
            }),
            'store' => $this->when($this->relationLoaded('store'), function () {
                return [
                    'id' => $this->store->id,
                    'name' => $this->store->name,
                    'slug' => $this->store->slug,
                    'logo' => $this->store->logo ? asset($this->store->logo) : null,
                ];
            }),
            'brand' => $this->when($this->relationLoaded('brand') && $this->brand, function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                    'logo' => $this->brand->logo ? asset($this->brand->logo) : null,
                ];
            }),
            'categories' => $this->when($this->relationLoaded('categories'), function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                });
            }),
            'variants' => $this->when($this->relationLoaded('variants'), function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'price' => $variant->price,
                        'special_price' => $variant->special_price,
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                        'attributes' => $variant->attributeValues->map(function ($attrValue) {
                            return [
                                'attribute' => $attrValue->attribute->name,
                                'value' => $attrValue->value,
                            ];
                        }),
                    ];
                });
            }),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
