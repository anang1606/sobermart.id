<?php

namespace Botble\AdsCategory\Http\Controllers;

use Botble\AdsCategory\Models\AdsCategory;
use Botble\AdsCategory\Repositories\Eloquent\AdsCategoryRepository;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;

class PublicController extends BaseController
{
    protected AdsCategoryRepository $adsRepository;

    public function __construct(AdsCategoryRepository $adsRepository)
    {
        $this->adsRepository = $adsRepository;
    }

    public function getAdsClick(string $key, BaseHttpResponse $response)
    {
        $ads = AdsCategory::where('key',$key)->first();
        if (! $ads || ! $ads->url) {
            return $response->setNextUrl(route('public.single'));
        }

        $ads->clicked++;
        $ads->save();

        return $response->setNextUrl($ads->url);
    }
}
