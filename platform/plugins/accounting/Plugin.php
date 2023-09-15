<?php

namespace Botble\Accounting;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('accounting');

        Setting::query()
            ->whereIn('key', [
            ])
            ->delete();
    }
}
