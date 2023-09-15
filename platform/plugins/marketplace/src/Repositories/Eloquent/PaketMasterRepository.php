<?php

namespace Botble\Marketplace\Repositories\Eloquent;

use Botble\Ecommerce\Models\PaketMaster;
use Botble\Marketplace\Repositories\Interfaces\PaketMasterInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class PaketMasterRepository extends RepositoriesAbstract implements PaketMasterInterface
{
    public function __construct(PaketMaster $model)
    {
        $this->model = $model;
    }
}