<?php

namespace Botble\AdsCategory\Providers;

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

class AdsCategoryServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(AdsCategoryInterface::class, function () {
            return new AdsCategoryCacheDecorator(new AdsCategoryRepository(new AdsCategory()));
        });

        Helper::autoload(__DIR__ . '/../../helpers');

        // AliasLoader::getInstance()->alias('AdsManager', AdsManagerFacade::class);
    }

    public function boot()
    {
        $this->setNamespace('plugins/ads-category')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugins-ads-category',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'Ads Category',
                'icon' => 'fas fa-bullhorn',
                'url' => route('category-ads.index'),
                'permissions' => ['ads.index'],
            ]);
        });

        if (function_exists('shortcode')) {
            add_shortcode('ads-category', 'Ads', 'Ads', function ($shortcode) {
                if (! $shortcode->key) {
                    return null;
                }

                return AdsManager::displayAds((string)$shortcode->key);
            });

            shortcode()->setAdminConfig('ads-category', function ($attributes) {
                $ads = $this->app->make(AdsCategoryInterface::class)
                    ->pluck('name', 'key', ['status' => BaseStatusEnum::PUBLISHED]);

                return view('plugins/ads::partials.ads-admin-config', compact('ads', 'attributes'))
                    ->render();
            });
        }

        // if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
        //     LanguageAdvancedManager::registerModule(Ads::class, [
        //         'name',
        //         'image',
        //         'url',
        //     ]);
        // }

        // add_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, function ($request, $data = null) {
        //     if (! $data instanceof Ads || ! in_array(Route::currentRouteName(), ['ads.create', 'ads.edit'])) {
        //         return false;
        //     }

        //     echo view('plugins/ads::partials.notification')
        //         ->render();

        //     return true;
        // }, 45, 2);
    }
}
