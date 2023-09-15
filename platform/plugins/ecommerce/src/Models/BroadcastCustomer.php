<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Broadcast;

class BroadcastCustomer extends BaseModel
{
    public function broadcast(){
        return $this->hasOne(Broadcast::class, 'id', 'broadcast_id');
    }
}
