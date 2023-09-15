<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Broadcast extends BaseModel
{
    public function customer(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'broadcast_customers');
    }
}
