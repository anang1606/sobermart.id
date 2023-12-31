<?php

namespace Theme\Farmart\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\WishlistInterface;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Theme\Http\Controllers\PublicController;
use Cart;
use EcommerceHelper;
use EmailHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Theme;
use Theme\Farmart\Http\Resources\BrandResource;
use Theme\Farmart\Http\Resources\ProductCategoryResource;
use Theme\Farmart\Http\Resources\ReviewResource;
use Botble\Ecommerce\Models\Review;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\Cart as CartModel;
use Botble\Ecommerce\Models\OptionValue;

class FarmartController extends PublicController
{
    protected BaseHttpResponse $httpResponse;

    public function __construct(BaseHttpResponse $response)
    {
        $this->httpResponse = $response;

        $this->middleware(function ($request, $next) {
            if (! $request->ajax()) {
                return $this->httpResponse->setNextUrl(route('public.index'));
            }

            return $next($request);
        })->only([
            'ajaxGetProducts',
            'ajaxGetFeaturedProductCategories',
            'ajaxGetFeaturedBrands',
            'ajaxGetFlashSale',
            'ajaxGetFeaturedProducts',
            'ajaxGetProductsByCategoryId',
            'ajaxCart',
            'ajaxGetQuickView',
            'ajaxAddProductToWishlist',
            'ajaxGetRelatedProducts',
            'ajaxSearchProducts',
            'ajaxGetProductReviews',
            'ajaxGetProductCategories',
            'ajaxGetRecentlyViewedProducts',
            'ajaxContactSeller',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getIndex()
    {
        return parent::getIndex();
    }

    /**
     * {@inheritDoc}
     */
    public function getView($key = null)
    {
        return parent::getView($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getSiteMap()
    {
        return parent::getSiteMap();
    }

    /**
     * @param array $productIds
     * @return array
     */
    protected function getWishlistIds(array $productIds = []): array
    {
        if (! EcommerceHelper::isWishlistEnabled()) {
            return [];
        }

        if (auth('customer')->check()) {
            return auth('customer')->user()->wishlist()->whereIn('product_id', $productIds)->pluck('product_id')->all();
        }

        return collect(Cart::instance('wishlist')->content())
            ->sortBy([['updated_at', 'desc']])
            ->whereIn('id', $productIds)
            ->pluck('id')
            ->all();
    }

    /**
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function ajaxGetProducts(Request $request)
    {
        $products = get_products_by_collections([
                'collections' => [
                    'by' => 'id',
                    'value_in' => [(int)$request->input('collection_id')],
                ],
                'take' => (int)$request->input('limit', 10),
                'with' => [
                    'slugable',
                    'variations',
                    'productCollections',
                    'variationAttributeSwatchesForProductList',
                ],
            ] + EcommerceHelper::withReviewsParams());

        $wishlistIds = $this->getWishlistIds($products->pluck('id')->all());

        $is_flashsale = true;
        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('ecommerce.product-item', compact('product', 'wishlistIds'));
        }

        return $this->httpResponse->setData($data);
    }

    /**
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedProductCategories()
    {
        $categories = get_featured_product_categories(['take' => null]);

        return $this->httpResponse->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedBrands()
    {
        $brands = get_featured_brands();

        return $this->httpResponse->setData(BrandResource::collection($brands));
    }

    /**
     * @param int $id
     * @param FlashSaleInterface $flashSaleRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetFlashSale($id, FlashSaleInterface $flashSaleRepository)
    {
        $flashSale = $flashSaleRepository->getModel()
            ->notExpired()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with([
                'products' => function ($query) {
                    $reviewParams = EcommerceHelper::withReviewsParams();

                    if (EcommerceHelper::isReviewEnabled()) {
                        $query->withAvg($reviewParams['withAvg'][0], $reviewParams['withAvg'][1]);
                    }

                    return $query
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->withCount($reviewParams['withCount']);
                },
            ])
            ->first();

        if (! $flashSale) {
            return $this->httpResponse->setData([]);
        }

        $data = [];
        $isFlashSale = true;
        $wishlistIds = $this->getWishlistIds($flashSale->products->pluck('id')->all());

        foreach ($flashSale->products as $product) {
            if (! EcommerceHelper::showOutOfStockProducts() && $product->isOutOfStock()) {
                continue;
            }

            $data[] = Theme::partial('ecommerce.product-item', compact('product', 'flashSale', 'isFlashSale', 'wishlistIds'));
        }

        return $this->httpResponse->setData($data);
    }

    /**
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedProducts(Request $request)
    {
        $data = [];

        $products = get_featured_products([
                'take' => (int)$request->input('limit', 10),
                'with' => [
                    'slugable',
                    'variations',
                    'productCollections',
                    'variationAttributeSwatchesForProductList',
                ],
            ] + EcommerceHelper::withReviewsParams());

        $wishlistIds = $this->getWishlistIds($products->pluck('id')->all());

        foreach ($products as $product) {
            $data[] = Theme::partial('ecommerce.product-item', compact('product', 'wishlistIds'));
        }

        return $this->httpResponse->setData($data);
    }

    /**
     * @param Request $request
     * @param ProductInterface $productRepository
     * @param ProductCategoryInterface $productCategoryRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductsByCategoryId(
        Request $request,
        ProductInterface $productRepository,
        ProductCategoryInterface $productCategoryRepository
    ) {
        $categoryId = $request->input('category_id');

        if (! $categoryId) {
            return $this->httpResponse;
        }

        $category = $productCategoryRepository->findOrFail($categoryId);

        $products = $productRepository->getProductsByCategories([
                'categories' => [
                    'by' => 'id',
                    'value_in' => array_merge([$category->id], $category->activeChildren->pluck('id')->all()),
                ],
                'take' => (int)$request->input('limit', 10),
            ] + EcommerceHelper::withReviewsParams());

        $wishlistIds = $this->getWishlistIds($products->pluck('id')->all());

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('ecommerce.product-item', compact('product', 'wishlistIds'));
        }

        return $this->httpResponse->setData($data);
    }

    /**
     * @return BaseHttpResponse
     */
    public function ajaxCart()
    {
        $getCarts = CartModel::where([['customer_id',auth('customer')->id()],['is_buynow',0]])->get();

        $cart = [];
        $count_cart = 0;
        $total_price = 0;
        foreach($getCarts as $cartItem){
            $product = Product::where([['id',$cartItem->product_id],['status',BaseStatusEnum::PUBLISHED]])->first();
            if($product){
                $count_cart++;
                $product->variant_config = $product->is_variation ? ProductVariation::where('product_id',
                $cartItem->product_id)->with('productAttributes.productAttributeSet')->first() : '';
                $total_price += ($product->front_sale_price * $cartItem->qty) + $product->original_product->total_taxes_percentage;

                if ($cartItem->options !== '') {
                    $options = explode(';', $cartItem->options);
                    $objects = [];
                    foreach ($options as $option) {
                        $object = json_decode($option);
                        $objects[] = $object;
                    }
                    $options = array();
                    foreach ($objects as $obj) {
                        $option = OptionValue::where([[
                            'option_id',
                            $obj->option_id
                        ], ['id', $obj->id]])->with('option')->first();

                        $options[] = $option;
                    }
                    $cartItem->option = $options;
                } else {
                    $cartItem->option = [];
                }

                $cartItem->product = $product;

                $cart[] = $cartItem;
            }
        }
        return $this->httpResponse->setData([
            'count' => $count_cart,
            // 'total_price' => format_price($total_price),
            'html' => Theme::partial('cart-mini.list',compact('cart')),
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return BaseHttpResponse
     */
    public function ajaxGetQuickView(Request $request, $id = null)
    {
        if (! $id) {
            $id = (int)$request->input('product_id');
        }

        $product = null;

        if ($id) {
            $product = get_products([
                    'condition' => [
                        'ec_products.id' => $id,
                        'ec_products.status' => BaseStatusEnum::PUBLISHED,
                    ],
                    'take' => 1,
                    'with' => [
                        'slugable',
                        'tags',
                        'tags.slugable',
                        'options' => function ($query) {
                            return $query->with('values');
                        },
                    ],
                ] + EcommerceHelper::withReviewsParams());
        }

        if (! $product) {
            return $this->httpResponse->setError()->setMessage(__('This product is not available.'));
        }

        [$productImages, $productVariation, $selectedAttrs] = EcommerceHelper::getProductVariationInfo($product);

        $wishlistIds = $this->getWishlistIds([$product->id]);

        return $this
            ->httpResponse
            ->setData(Theme::partial('ecommerce.quick-view', compact('product', 'selectedAttrs', 'productImages', 'productVariation', 'wishlistIds')));
    }

    /**
     * @param Request $request
     * @param ProductInterface $productRepository
     * @param WishlistInterface $wishlistRepository
     * @param $productId
     * @return BaseHttpResponse
     * @throws \Exception
     */
    public function ajaxAddProductToWishlist(Request $request, ProductInterface $productRepository, WishlistInterface $wishlistRepository, $productId = null)
    {
        if (! EcommerceHelper::isWishlistEnabled()) {
            abort(404);
        }
        if (! $productId) {
            $productId = $request->input('product_id');
        }

        if (! $productId) {
            return $this->httpResponse->setError()->setMessage(__('This product is not available.'));
        }
        $product = $productRepository->findOrFail($productId);

        $messageAdded = __('Added product :product successfully!', ['product' => $product->name]);
        $messageRemoved = __('Removed product :product from wishlist successfully!', ['product' => $product->name]);

        if (! auth('customer')->check()) {
            $duplicates = Cart::instance('wishlist')->search(function ($cartItem) use ($productId) {
                return $cartItem->id == $productId;
            });

            if (! $duplicates->isEmpty()) {
                $added = false;
                Cart::instance('wishlist')->search(function ($cartItem, $rowId) use ($productId) {
                    if ($cartItem->id == $productId) {
                        Cart::instance('wishlist')->remove($rowId);

                        return true;
                    }

                    return false;
                });
            } else {
                $added = true;
                Cart::instance('wishlist')
                    ->add($productId, $product->name, 1, $product->front_sale_price)
                    ->associate(Product::class);
            }

            return $this->httpResponse
                ->setMessage($added ? $messageAdded : $messageRemoved)
                ->setData([
                    'count' => Cart::instance('wishlist')->count(),
                    'added' => $added,
                ]);
        }

        $customer = auth('customer')->user();

        if (is_added_to_wishlist($productId)) {
            $added = false;
            $wishlistRepository->deleteBy([
                'product_id' => $productId,
                'customer_id' => $customer->getKey(),
            ]);
        } else {
            $added = true;
            $wishlistRepository->createOrUpdate([
                'product_id' => $productId,
                'customer_id' => $customer->getKey(),
            ]);
        }

        return $this->httpResponse
            ->setMessage($added ? $messageAdded : $messageRemoved)
            ->setData([
                'count' => $customer->wishlist()->count(),
                'added' => $added,
            ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetRelatedProducts(
        $id,
        Request $request,
        ProductInterface $productRepository
    ) {
        $product = $productRepository->findOrFail($id);

        $products = get_related_products($product, $request->input('limit'));

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('ecommerce.product-item', compact('product'));
        }

        return $this->httpResponse->setData($data);
    }

    /**
     * @param Request $request
     * @param GetProductService $productService
     * @return BaseHttpResponse
     */
    public function ajaxSearchProducts(Request $request, GetProductService $productService)
    {
        $request->merge(['num' => 12]);

        $with = [
            'slugable',
            'variations',
            'productCollections',
            'variationAttributeSwatchesForProductList',
        ];

        $products = $productService->getProduct($request, null, null, $with);

        $queries = $request->input();
        foreach ($queries as $key => $query) {
            if (! $query || $key == 'num' || (is_array($query) && ! Arr::get($query, 0))) {
                unset($queries[$key]);
            }
        }

        $total = $products->count();
        $message = $total != 1 ? __(':total Products found', compact('total')) : __(':total Product found', compact('total'));

        return $this->httpResponse
            ->setData(Theme::partial('ajax-search-results', compact('products', 'queries')))
            ->setMessage($message);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductReviews(
        $id,
        Request $request,
        ProductInterface $productRepository
    ) {
        $product = $productRepository->getFirstBy([
            'id' => $id,
            'status' => BaseStatusEnum::PUBLISHED,
            'is_variation' => 0,
        ], [], ['variations']);

        if (! $product) {
            abort(404);
        }

        $star = (int)$request->input('star');
        $perPage = (int)$request->input('per_page', 10);

        $reviews = EcommerceHelper::getProductReviews($product, $star, $perPage);

        if ($star) {
            $message = __(':total review(s) ":star star" for ":product"', [
                'total' => $reviews->total(),
                'product' => $product->name,
                'star' => $star,
            ]);
        } else {
            $message = __(':total review(s) for ":product"', [
                'total' => $reviews->total(),
                'product' => $product->name,
            ]);
        }

        foreach($reviews as $review) {
            $get_parent = Review::where('parent_id', $review->id)->get();
            if(count($get_parent) > 0){
                $vendor = array();
                foreach($get_parent as $parent){
                    $get_vendor = \DB::select("SELECT * FROM mp_stores WHERE id = '$parent->customer_id'")[0];
                    if($get_vendor){
                        $vendor = $get_vendor;
                    }
                    $parent->vendor = (object)$vendor;
                }
            }
            $review->parent = $get_parent;
        }

        return $this->httpResponse
            ->setData(ReviewResource::collection($reviews))
            ->setMessage($message)
            ->toApiResponse();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductCategoryInterface $productCategoryRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductCategories(
        Request $request,
        BaseHttpResponse $response,
        ProductCategoryInterface $productCategoryRepository
    ) {
        $categoryIds = $request->input('categories', []);
        if (empty($categoryIds)) {
            return $response;
        }

        $categories = $productCategoryRepository->advancedGet([
            'condition' => [
                'status' => BaseStatusEnum::PUBLISHED,
                ['id', 'IN', $categoryIds],
            ],
            'with' => ['slugable'],
        ]);

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @return BaseHttpResponse
     */
    public function ajaxGetRecentlyViewedProducts(ProductInterface $productRepository)
    {
        if (! EcommerceHelper::isEnabledCustomerRecentlyViewedProducts()) {
            abort(404);
        }

        $queryParams = [
                'with' => ['slugable'],
                'take' => 12,
            ] + EcommerceHelper::withReviewsParams();

        if (auth('customer')->check()) {
            $products = $productRepository->getProductsRecentlyViewed(auth('customer')->id(), $queryParams);
        } else {
            $products = collect();

            $itemIds = collect(Cart::instance('recently_viewed')->content())
                ->sortBy([['updated_at', 'desc']])
                ->take(12)
                ->pluck('id')
                ->all();

            if ($itemIds) {
                $products = $productRepository->getProductsByIds($itemIds, $queryParams);
            }
        }

        return $this->httpResponse
            ->setData(Theme::partial('ecommerce.recently-viewed-products', compact('products')));
    }

    /**
     * @param Theme\Farmart\Http\Requests\ContactSellerRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function ajaxContactSeller(Theme\Farmart\Http\Requests\ContactSellerRequest $request, BaseHttpResponse $response)
    {
        $name = $request->input('name');
        $email = $request->input('email');

        if (auth('customer')->check()) {
            $name = auth('customer')->user()->name;
            $email = auth('customer')->user()->email;
        }

        EmailHandler::setModule(Theme::getThemeName())
            ->setVariableValues([
                'contact_message' => $request->input('content'),
                'customer_name' => $name,
                'customer_email' => $email,
            ])
            ->sendUsingTemplate('contact-seller', $email, [], false, 'themes');

        return $response->setMessage(__('Send message successfully!'));
    }

    public function getCategorySub(Request $request,BaseHttpResponse $response){
        $id = base64_decode($request->__ex_cat);
        $category = \DB::select("SELECT * FROM ec_product_categories1 WHERE id='$id'");
        if(count($category)){
            $category = $category[0];
            $html = '';
            $html .= '<div class="css-gvoll6">';
            $html .= '<img src="'.\RvMedia::getImageUrl($category->image).'" class="css-zk2hyh" alt="'.$category->name.'" />';
            $html .= "<div class='css-5j1t1q'>$category->name</div>";
            $html .= '</div>';

            $html .= '<div class="css-11p7ov6">';
            $html .= '<div class="css-s0g7na">';
            $html .= $this->getCategoryLevel1($category->id);
            $html .= '</div>';
            $html .= '</div>';

            // return $html;
            return $response->setData($html);
        }
    }

    private function getCategoryLevel1 ($id){
        $categorys = \DB::select("SELECT * FROM ec_product_categories2 WHERE parent_id='$id'");
        if(count($categorys)){
            $html = '';
            foreach($categorys as $category){
                $slugs = \DB::select("SELECT * FROM slugs WHERE prefix='product-categories' AND reference_id='$category->id'");
                $url = (count($slugs) > 0) ? '/product-categories/' .$slugs[0]->key : '/';

                $html .= '<div class="css-1owj1eu">';
                $html .= "<div><a href='$url' class='css-1qaqbbz'>$category->name</a></div>";
                $html .= '<div class="css-1wode1h">';
                $html .= $this->getCategoryLevel2($category->id);
                $html .= '</div>';
                $html .= '</div>';
            }

            return $html;
        }
    }

    private function getCategoryLevel2($id){
        $categorys = \DB::select("SELECT * FROM ec_product_categories3 WHERE parent_id='$id'");
        if(count($categorys)){
            $html = '';
            foreach($categorys as $category){
                $slugs = \DB::select("SELECT * FROM slugs WHERE prefix='product-categories' AND reference_id='$category->id'");
                $url = (count($slugs) > 0) ? '/product-categories/' .$slugs[0]->key : '/';

                $html .= "<a href='$url' class='css-1nykm5o'>$category->name</a>";
            }

            return $html;
        }
    }
}
