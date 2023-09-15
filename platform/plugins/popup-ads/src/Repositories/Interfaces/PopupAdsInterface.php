<?php

namespace Botble\PopupAds\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PopupAdsInterface extends RepositoryInterface
{
    public function getAll(): Collection;
}
