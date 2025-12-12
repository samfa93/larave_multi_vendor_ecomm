<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected $casts = ['variant' => 'array'];

    function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
