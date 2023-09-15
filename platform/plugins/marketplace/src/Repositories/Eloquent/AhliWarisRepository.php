<?php

namespace Botble\Marketplace\Repositories\Eloquent;

use Botble\Ecommerce\Models\AhliWaris;
use Botble\Marketplace\Repositories\Interfaces\AhliWarisInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class AhliWarisRepository extends RepositoriesAbstract implements AhliWarisInterface
{
    public function __construct(AhliWaris $model)
    {
        $this->model = $model;
    }
}
