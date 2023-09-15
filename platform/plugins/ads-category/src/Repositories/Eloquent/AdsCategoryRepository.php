<?php

namespace Botble\AdsCategory\Repositories\Eloquent;

use Botble\AdsCategory\Models\AdsCategory;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\AdsCategory\Repositories\Interfaces\AdsCategoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AdsCategoryRepository extends RepositoriesAbstract implements AdsCategoryInterface
{
    public function __construct(AdsCategory $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        $data = $this->model
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->notExpired()
            ->with(['metadata']);

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
