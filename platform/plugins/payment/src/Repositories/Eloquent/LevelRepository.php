<?php

namespace Botble\Payment\Repositories\Eloquent;

use Botble\Payment\Models\Level;
use Botble\Payment\Repositories\Interfaces\LevelInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class LevelRepository extends RepositoriesAbstract implements LevelInterface
{
    public function __construct(Level $model)
    {
        $this->model = $model;
    }
}