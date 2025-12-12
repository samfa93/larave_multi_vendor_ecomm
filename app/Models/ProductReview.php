<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{

    function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
