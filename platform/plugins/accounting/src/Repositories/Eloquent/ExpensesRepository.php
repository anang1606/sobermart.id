<?php

namespace Botble\Accounting\Repositories\Eloquent;

use Botble\Accounting\Models\Expense;
use Botble\Accounting\Repositories\Interfaces\ExpensesInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class ExpensesRepository extends RepositoriesAbstract implements ExpensesInterface
{
    public function __construct(Expense $model)
    {
        $this->model = $model;
    }
}
