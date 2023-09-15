<?php

namespace Botble\AdsCategory\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface AdsCategoryInterface extends RepositoryInterface
{
    public function getAll(): Collection;
}
