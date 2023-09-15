<?php

namespace Botble\Accounting\Repositories\Eloquent;

use Botble\Accounting\Models\Coa;
use Botble\Accounting\Repositories\Interfaces\CoaInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class CoaRepository extends RepositoriesAbstract implements CoaInterface
{
    public function __construct(Coa $model)
    {
        $this->model = $model;
    }
}
