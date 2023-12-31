<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Marketplace\Http\Requests\CheckStoreUrlRequest;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use EcommerceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RvMedia;
use SeoHelper;
use SlugHelper;
use Theme;

class PublicStoreController
{
    protected StoreInterface $storeRepository;

    protected SlugInterface $slugRepository;

    public function __construct(StoreInterface $storeRepository, SlugInterface $slugRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->slugRepository = $slugRepository;
    }

    public function getStores(Request $request)
    {
        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Stores'), route('public.stores'));

        SeoHelper::setTitle(__('Stores'))->setDescription(__('Stores'));

        $condition = ['status' => BaseStatusEnum::PUBLISHED];

        $search = BaseHelper::clean($request->input('q'));
        if ($search) {
            $condition[] = ['name', 'LIKE', '%' . $search . '%'];
        }

        $with = ['slugable'];
        if (EcommerceHelper::isReviewEnabled()) {
            $with['reviews'] = function ($query) {
                $query->where([
                    'ec_products.status' => BaseStatusEnum::PUBLISHED,
                    'ec_reviews.status' => BaseStatusEnum::PUBLISHED,
                ]);
            };
        }

        $stores = $this->storeRepository->advancedGet([
            'condition' => $condition,
            'order_by' => ['created_at' => 'desc'],
            'paginate' => [
                'per_page' => 12,
                'current_paged' => (int)$request->input('page'),
            ],
            'with' => $with,
            'withCount' => [
                'products' => function ($query) {
                    $query->where(['status' => BaseStatusEnum::PUBLISHED]);
                },
            ],
        ]);
        
        return Theme::scope('marketplace.stores', compact('stores'), 'plugins/marketplace::themes.stores')->render();
    }

    private function getAllEtalase ($store):array {
        $products = \DB::select("SELECT * FROM ec_products WHERE store_id = '$store->id' GROUP BY etalase");

        $etalase = [];
        foreach($products as $prod){
            $etalase[] = $prod->etalase;
        }
        $etalase = array_unique($etalase);

        return $etalase;
    }

    public function getStore(
        string $slug,
        Request $request,
        GetProductService $productService,
        BaseHttpResponse $response
    ) {
        $baseURL = str_replace('?','/',route('public.stores',$slug));
        $slug = $this->slugRepository->getFirstBy([
            'key' => $slug,
            'reference_type' => Store::class,
            'prefix' => SlugHelper::getPrefix(Store::class),
        ]);

        if (! $slug) {
            abort(404);
        }

        $condition = [
            'mp_stores.id' => $slug->reference_id,
            'mp_stores.status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && $request->input('preview')) {
            Arr::forget($condition, 'status');
        }

        $store = $this->storeRepository->getFirstBy($condition, ['*'], ['slugable', 'metadata']);

        if (! $store) {
            abort(404);
        }

        if ($store->slugable->key !== $slug->key) {
            return redirect()->to($store->url);
        }

        SeoHelper::setTitle($store->name)->setDescription($store->description);

        $meta = new SeoOpenGraph();
        if ($store->logo) {
            $meta->setImage(RvMedia::getImageUrl($store->logo));
        }
        $meta->setDescription($store->description);
        $meta->setUrl($store->url);
        $meta->setTitle($store->name);

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Stores'), route('public.stores'))
            ->add($store->name, $store->url);

        $with = [
            'slugable',
            'variations',
            'productLabels',
            'variationAttributeSwatchesForProductList',
            'store',
            'store.slugable',
        ];

        $products = $productService->getProduct($request, null, null, $with, [], ['store_id' => $store->id]);

        if ($request->ajax()) {
            $total = $products->total();
            $message = $total > 1 ? __(':total Products found', compact('total')) : __(
                ':total Product found',
                compact('total')
            );

            $view = Theme::getThemeNamespace('views.marketplace.stores.items');

            if (! view()->exists($view)) {
                $view = 'plugins/marketplace::themes.stores.items';
            }

            return $response
                ->setData(view($view, compact('products', 'store'))->render())
                ->setMessage($message);
        }
        $etalase = [];
        $etalase = $this->getAllEtalase($store);
        $isActive = 'all-product';

        return Theme::scope('marketplace.store', compact('store','baseURL','isActive','etalase', 'products'),
        'plugins/marketplace::themes.store')->render();
    }

    public function getStoreEtalase(
        string $slug,
        Request $request,
        GetProductService $productService,
        BaseHttpResponse $response,
        $etalaseKey
    ) {
        $baseURL = str_replace('?','/',route('public.stores',$slug));
        $slug = $this->slugRepository->getFirstBy([
            'key' => $slug,
            'reference_type' => Store::class,
            'prefix' => SlugHelper::getPrefix(Store::class),
        ]);

        if (! $slug) {
            abort(404);
        }

        $condition = [
            'mp_stores.id' => $slug->reference_id,
            'mp_stores.status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && $request->input('preview')) {
            Arr::forget($condition, 'status');
        }
        $store = $this->storeRepository->getFirstBy($condition, ['*'], ['slugable', 'metadata']);

        if (! $store) {
            abort(404);
        }

        if ($store->slugable->key !== $slug->key) {
            return redirect()->to($store->url);
        }

        SeoHelper::setTitle($store->name)->setDescription($store->description);

        $meta = new SeoOpenGraph();
        if ($store->logo) {
            $meta->setImage(RvMedia::getImageUrl($store->logo));
        }
        $meta->setDescription($store->description);
        $meta->setUrl($store->url);
        $meta->setTitle($store->name);

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Stores'), route('public.stores'))
            ->add($store->name, $store->url);

        $with = [
            'slugable',
            'variations',
            'productLabels',
            'variationAttributeSwatchesForProductList',
            'store',
            'store.slugable',
        ];

        $products = $productService->getProduct($request, null, null, $with, [], ['store_id' => $store->id],str_replace('-',' ',$etalaseKey));

        if ($request->ajax()) {
            $total = $products->total();
            $message = $total > 1 ? __(':total Products found', compact('total')) : __(
                ':total Product found',
                compact('total')
            );

            $view = Theme::getThemeNamespace('views.marketplace.stores.items');

            if (! view()->exists($view)) {
                $view = 'plugins/marketplace::themes.stores.items';
            }

            return $response
                ->setData(view($view, compact('products', 'store'))->render())
                ->setMessage($message);
        }
        $etalase = [];
        $etalase = $this->getAllEtalase($store);
        $isActive = $etalaseKey;

        return Theme::scope('marketplace.store', compact('store','baseURL','isActive','etalase', 'products'),
        'plugins/marketplace::themes.store')->render();
    }

    public function checkStoreUrl(CheckStoreUrlRequest $request, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            abort(404);
        }
        $slug = $request->input('url');
        $slug = Str::slug($slug, '-', ! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);

        $existing = SlugHelper::getSlug($slug, null, Store::class);

        $response->setData(['slug' => $slug]);

        if ($existing && $existing->reference_id != $request->input('reference_id')) {
            return $response
                ->setError()
                ->setMessage(__('Not Available'));
        }

        return $response->setMessage(__('Available'));
    }
}
