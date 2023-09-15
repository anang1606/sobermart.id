<?php

namespace Botble\Payment\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Botble\Payment\Repositories\Caches\PaymentCacheDecorator;
use Botble\Payment\Repositories\Eloquent\PaymentRepository;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;

class PaymentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this
            ->setNamespace('plugins/payment')
            ->loadHelpers();

        $this->app->singleton(PaymentInterface::class, function () {
            return new PaymentCacheDecorator(new PaymentRepository(new Payment()));
        });
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['payment', 'permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-payments',
                    'priority' => 800,
                    'parent_id' => null,
                    'name' => 'plugins/payment::payment.name',
                    'icon' => 'fas fa-credit-card',
                    'url' => route('payment.index'),
                    'permissions' => ['payment.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-payments-all',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-payments',
                    'name' => 'plugins/payment::payment.transactions',
                    'icon' => null,
                    'url' => route('payment.index'),
                    'permissions' => ['payment.index'],
                ])
                ->registerItem([
                    'id' => 'payments-paket',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-payments',
                    'name' => 'Paket',
                    'icon' => null,
                    'url' => route('payment.paket'),
                    'permissions' => ['payment.index'],
                ])
                ->registerItem([
                    'id' => 'payments-bank',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-payments',
                    'name' => 'Bank',
                    'icon' => null,
                    'url' => route('bank.index'),
                    'permissions' => ['payment.index'],
                ])
                ->registerItem([
                    'id' => 'accumulation',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-payments',
                    'name' => 'Akumulasi Perhitungan',
                    'icon' => null,
                    'url' => route('accumulation.index'),
                    'permissions' => ['payment.index'],
                ]);
                // ->registerItem([
                //     'id' => 'cms-plugins-payment-methods',
                //     'priority' => 1,
                //     'parent_id' => 'cms-plugins-payments',
                //     'name' => 'plugins/payment::payment.payment_methods',
                //     'icon' => null,
                //     'url' => route('payments.methods'),
                //     'permissions' => ['payments.settings'],
                // ]);
        });
    }
}
