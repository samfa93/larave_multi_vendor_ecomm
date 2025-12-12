<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $guarded = [];

    function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    function attributes() : BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_variant_attribute_value')->withPivot('attribute_value_id');
    }

    function attributeValues() : BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_value')->withPivot('attribute_id');
    }
}
