<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Store extends Model
{
    protected $fillable = [
        'id',
        'seller_id',
        'logo',
        'banner',
        'name',
        'email',
        'phone',
        'address',
        'short_description',
        'long_description'
    ];

    function wallet() : HasOne
    {
        return $this->hasOne(StoreWallet::class, 'store_id');
    }

    function products() : HasMany
    {
        return $this->hasMany(Product::class);
    }

    function reviews() : HasManyThrough
    {
        return $this->hasManyThrough(ProductReview::class, Product::class, 'store_id', 'product_id', 'id', 'id');
    }
}
