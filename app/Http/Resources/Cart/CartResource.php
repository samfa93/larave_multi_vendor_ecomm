<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $variant = $this->variant;

        $price = $variant ? ($variant->special_price > 0 ? $variant->special_price : $variant->price)
            : ($product->special_price > 0 ? $product->special_price : $product->price);

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_price' => $price,
            'total_price' => $price * $this->quantity,
            'thumbnail' => $product->thumbnail ? asset($product->thumbnail) : null,
            'variant' => $variant ? [
                'id' => $variant->id,
                'attributes' => $variant->attributeValues->map(function ($attrValue) {
                    return [
                        'attribute' => $attrValue->attribute->name,
                        'value' => $attrValue->value,
                    ];
                }),
            ] : null,
            'store' => [
                'id' => $product->store->id,
                'name' => $product->store->name,
            ],
            'in_stock' => $variant ? $variant->stock >= $this->quantity : $product->stock >= $this->quantity,
        ];
    }
}
