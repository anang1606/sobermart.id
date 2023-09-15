<?php

namespace Botble\Payment\Repositories\Eloquent;

use Botble\Ecommerce\Models\BankList;
use Botble\Payment\Repositories\Interfaces\BankListInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class BankListRepository extends RepositoriesAbstract implements BankListInterface
{
    public function __construct(BankList $model)
    {
        $this->model = $model;
    }
}