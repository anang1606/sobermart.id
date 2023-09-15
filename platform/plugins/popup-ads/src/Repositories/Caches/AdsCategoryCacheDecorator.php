<?php

namespace Botble\AdsCategory\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\AdsCategory\Repositories\Interfaces\AdsCategoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AdsCategoryCacheDecorator extends CacheAbstractDecorator implements AdsCategoryInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
