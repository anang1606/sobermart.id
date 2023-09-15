<?php

namespace Botble\PopupAds\Repositories\Eloquent;

use Botble\PopupAds\Models\PopupAds;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\PopupAds\Repositories\Interfaces\PopupAdsInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Eloquent\Collection;

class PopupAdsRepository extends RepositoriesAbstract implements PopupAdsInterface
{
    public function __construct(PopupAds $model)
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
