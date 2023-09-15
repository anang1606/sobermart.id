<?php

namespace Botble\PopupAds\Http\Controllers;

use Botble\PopupAds\Models\PopupAds;
use Botble\PopupAds\Repositories\Eloquent\PopupAdsRepository;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use RvMedia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
    protected PopupAdsRepository $adsRepository;

    public function __construct(PopupAdsRepository $adsRepository)
    {
        $this->adsRepository = $adsRepository;
    }

    public function getAdsClick(string $key, BaseHttpResponse $response)
    {
        $ads = PopupAds::where('key',$key)->first();
        if (! $ads || ! $ads->url) {
            return $response->setNextUrl(route('public.single'));
        }

        $ads->clicked++;
        $ads->save();

        return $response->setNextUrl($ads->url);
    }

    public function fetchBannerPopups(Request $request,BaseHttpResponse $response){
        $ads = PopupAds::whereDate('expired_at','>=',Carbon::now())
        ->orderBy('order','ASC')
        ->inRandomOrder()
        ->get();
        if(count($ads) > 0){
            foreach($ads as $ad){
                $ad->url = route('public.popupsbanner',$ad->key);
                $ad->image = RvMedia::getImageUrl($ad->image);
            }
            return $response->setData($ads);
        }
        return $response->setError();
    }

}
