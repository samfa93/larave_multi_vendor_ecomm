<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{

    function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    function variant() : BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
