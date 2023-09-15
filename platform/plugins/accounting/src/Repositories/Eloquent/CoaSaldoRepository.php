<?php

namespace Botble\Accounting\Repositories\Eloquent;

use Botble\Accounting\Models\CoaSaldo;
use Botble\Accounting\Repositories\Interfaces\CoaInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class CoaSaldoRepository extends RepositoriesAbstract implements CoaInterface
{
    public function __construct(CoaSaldo $model)
    {
        $this->model = $model;
    }
}
