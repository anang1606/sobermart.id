<?php

namespace Botble\Marketplace\Repositories\Eloquent;

use Botble\Ecommerce\Models\RequestGift;
use Botble\Marketplace\Repositories\Interfaces\RequestGiftInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class RequestGiftRepository extends RepositoriesAbstract implements RequestGiftInterface
{
    public function __construct(RequestGift $model)
    {
        $this->model = $model;
    }
}
