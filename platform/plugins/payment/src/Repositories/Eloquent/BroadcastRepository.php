<?php

namespace Botble\Payment\Repositories\Eloquent;

use Botble\Ecommerce\Models\Broadcast;
use Botble\Payment\Repositories\Interfaces\BroadcastInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
class BroadcastRepository extends RepositoriesAbstract implements BroadcastInterface
{
    public function __construct(Broadcast $model)
    {
        $this->model = $model;
    }
}
