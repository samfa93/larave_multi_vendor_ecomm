<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreWithdrawMethod extends Model
{
    function withdrawMethod() : BelongsTo
    {
        return $this->belongsTo(WithdrawMethod::class);
    }
}
