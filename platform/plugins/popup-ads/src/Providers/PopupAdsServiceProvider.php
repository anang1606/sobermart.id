<?php

namespace Botble\PopupAds\Providers;

use AdsManager;
use Botble\Ads\Facades\AdsManagerFacade;
use Botble\AdsCategory\Repositories\Caches\AdsCategoryCacheDecorator;
use Botble\AdsCategory\Models\AdsCategory;
use Botble\AdsCategory\Repositories\Eloquent\AdsCategoryRepository;
use Botble\AdsCategory\Repositories\Interfaces\AdsCategoryInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Event;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PopupAdsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(AdsCategoryInterface::class, function () {
            return new AdsCategoryCacheDecorator(new AdsCategoryRepository(new AdsCategory()));
        });
    }

    public function boot()
    {
        $this->setNamespace('plugins/popup-ads')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugins-popup-ads',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'Popup Ads',
                'icon' => 'fas fa-bullhorn',
                'url' => route('popup-ads.index'),
                'permissions' => ['ads.index'],
            ]);
        });
    }
}
