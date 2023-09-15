<?php

namespace Botble\Shortcode\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Shortcode\Http\Requests\GetShortcodeDataRequest;
use Botble\Support\Http\Requests\Request;
use Closure;
use DB;
use Illuminate\Support\Arr;

class ShortcodeController extends BaseController
{
    public function ajaxGetAdminConfig(?string $key, GetShortcodeDataRequest $request, BaseHttpResponse $response)
    {
        $registered = shortcode()->getAll();

        $data = Arr::get($registered, $key . '.admin_config');

        $attributes = [];
        $content = null;

        if ($code = $request->input('code')) {
            $compiler = shortcode()->getCompiler();
            $attributes = $compiler->getAttributes(html_entity_decode($code));
            $content = $compiler->getContent();
        }

        if ($data instanceof Closure) {
            $data = call_user_func($data, $attributes, $content);
        }

        $data = apply_filters(SHORTCODE_REGISTER_CONTENT_IN_ADMIN, $data, $key, $attributes);

        return $response->setData($data);
    }

    public function getCategory2(GetShortcodeDataRequest $request,BaseHttpResponse $response){
        $get_category = DB::select("SELECT * FROM ec_product_categories2 WHERE parent_id = '$request->category'");

        return $response->setData($get_category);
    }

    public function getCategory3(GetShortcodeDataRequest $request,BaseHttpResponse $response){
        $get_category = DB::select("SELECT * FROM ec_product_categories3 WHERE parent_id = '$request->category'");

        return $response->setData($get_category);
    }
}
