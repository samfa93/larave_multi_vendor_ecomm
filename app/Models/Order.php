<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $casts = [
        'billing_info' => 'array',
        'shipping_info' => 'array',
    ];


    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    function orderProducts() : HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    function orderHistory() : HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
