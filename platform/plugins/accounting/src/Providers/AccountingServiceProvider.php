<?php

// AccountingServiceProvider.php
namespace Botble\Accounting\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this
            ->setNamespace('plugins/accounting')
            ->loadHelpers();
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishViews()
            ->loadRoutes()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-accounting',
                    'priority' => 800,
                    'parent_id' => null,
                    'name' => 'Accounting',
                    'icon' => 'fas fa-pen',
                    'url' => route('coa.index'),
                    'permissions' => ['coa.index'],
                ])
                ->registerItem([
                    'id' => 'coa',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Coa Akunting',
                    'icon' => null,
                    'url' => route('coa.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'coa-saldo',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Saldo Akun',
                    'icon' => null,
                    'url' => route('coa-saldo.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'expense',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Pengeluaran',
                    'icon' => null,
                    'url' => route('expense.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'buku-besar',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Buku Besar',
                    'icon' => null,
                    'url' => route('buku-besar.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'neraca-lajur',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Neraca Lajur',
                    'icon' => null,
                    'url' => route('neraca-lajur.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'rugi-laba',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Rugi Laba',
                    'icon' => null,
                    'url' => route('rugi-laba.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'posisi-keuangan',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Posisi Keuangan',
                    'icon' => null,
                    'url' => route('posisi-keuangan.index'),
                    'permissions' => ['accounting.index'],
                ])
                ->registerItem([
                    'id' => 'ongkos-kirim',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-accounting',
                    'name' => 'Laporan Ongkos Kirim',
                    'icon' => null,
                    'url' => route('ongkos-kirim.index'),
                    'permissions' => ['accounting.index'],
                ]);
        });
    }
}
