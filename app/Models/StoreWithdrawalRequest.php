<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreWithdrawalRequest extends Model
{
    function store() : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
