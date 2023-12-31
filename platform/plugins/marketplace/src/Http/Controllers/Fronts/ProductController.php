<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Http\Requests\ProductVersionRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Repositories\Interfaces\GlobalOptionInterface;
use Botble\Ecommerce\Repositories\Interfaces\GroupedProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Services\Products\StoreAttributesOfProductService;
use Botble\Ecommerce\Services\Products\StoreProductService;
use Botble\Ecommerce\Services\StoreProductTagService;
use Botble\Ecommerce\Traits\ProductActionsTrait;
use Botble\Marketplace\Forms\ProductForm;
use Botble\Marketplace\Tables\ProductTable;
use EcommerceHelper;
use EmailHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use MarketplaceHelper;
use Botble\Marketplace\Models\ProductCategory;
use DB;
use Botble\Ecommerce\Models\ProductCategoryProduct;



class ProductController extends BaseController
{
    use ProductActionsTrait {
        ProductActionsTrait::postAddVersion as basePostAddVersion;
        ProductActionsTrait::postUpdateVersion as basePostUpdateVersion;
        ProductActionsTrait::deleteVersionItem as baseDeleteVersionItem;
    }

    public function index(ProductTable $table)
    {
        page_title()->setTitle(__('Products'));

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'));
    }

    public function create(FormBuilder $formBuilder, Request $request)
    {
        if (EcommerceHelper::isEnabledSupportDigitalProducts()) {
            if ($request->input('product_type') == ProductTypeEnum::DIGITAL) {
                page_title()->setTitle(trans('plugins/ecommerce::products.create_product_type.digital'));
            } else {
                page_title()->setTitle(trans('plugins/ecommerce::products.create_product_type.physical'));
            }
        } else {
            page_title()->setTitle(trans('plugins/ecommerce::products.create'));
        }

        return $formBuilder->create(ProductForm::class)->renderForm();
    }

    public function store(
        ProductRequest $request,
        StoreProductService $service,
        BaseHttpResponse $response,
        ProductVariationInterface $variationRepository,
        ProductVariationItemInterface $productVariationItemRepository,
        GroupedProductInterface $groupedProductRepository,
        StoreAttributesOfProductService $storeAttributesOfProductService,
        StoreProductTagService $storeProductTagService
    ) {
        $request = $this->processRequestData($request);

        $product = $this->productRepository->getModel();

        $product->status = MarketplaceHelper::getSetting(
                'enable_product_approval',
            1
        ) ? BaseStatusEnum::PENDING : BaseStatusEnum::PUBLISHED;

        if (EcommerceHelper::isEnabledSupportDigitalProducts() && $request->input('product_type')) {
            $product->product_type = $request->input('product_type');
        }

        $product = $service->execute($request, $product);

        $product->store_id = auth('customer')->user()->store->id;
        $product->created_by_id = auth('customer')->id();
        $product->created_by_type = Customer::class;
        $product->kategori1 = $request->input('kategori1');
        $product->kategori2 = $request->input('kategori2');
        $product->kategori3 = $request->input('kategori3');
        $product->save();

        $storeProductTagService->execute($request, $product);

        $addedAttributes = $request->input('added_attributes', []);

        if ($request->input('is_added_attributes') == 1 && $addedAttributes) {
            $storeAttributesOfProductService->execute($product, array_keys($addedAttributes), array_values($addedAttributes));

            $variation = $variationRepository->create([
                'configurable_product_id' => $product->id,
            ]);

            foreach ($addedAttributes as $attribute) {
                $productVariationItemRepository->createOrUpdate([
                    'attribute_id' => $attribute,
                    'variation_id' => $variation->id,
                ]);
            }

            $variation = $variation->toArray();

            $variation['variation_default_id'] = $variation['id'];

            $variation['sku'] = $product->sku ?? time();
            foreach ($addedAttributes as $attributeId) {
                $attribute = $this->productAttributeRepository->findById($attributeId);
                if ($attribute) {
                    $variation['sku'] .= '-' . $attribute->slug;
                    $variation['attribute_title'] = $attribute->title;
                }
            }

            $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $product->id, $response);
        }

        if ($request->has('grouped_products')) {
            $groupedProductRepository->createGroupedProducts($product->id, array_map(function ($item) {
                return [
                    'id' => $item,
                    'qty' => 1,
                ];
            }, array_filter(explode(',', $request->input('grouped_products', '')))));
        }

        if (MarketplaceHelper::getSetting('enable_product_approval', 1)) {
            EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'product_name' => $product->name,
                    'product_url' => route('products.edit', $product->id),
                    'store_name' => auth('customer')->user()->store->name,
                ])
                ->sendUsingTemplate('pending-product-approval');
        }

		if ( strlen($request->input('kategori1')) > 0)
		$dtkategori = DB::select(DB::raw("INSERT INTO ec_product_category_product (category_id,product_id) VALUES(:idkategori,:idproduk)"),
		array("idkategori" => $request->input('kategori1'),"idproduk" => $product->id,));

		if ( strlen($request->input('kategori2')) > 0)
		$dtkategori = DB::select(DB::raw("INSERT INTO ec_product_category_product (category_id,product_id) VALUES(:idkategori,:idproduk)"),
		array("idkategori" => $request->input('kategori2'),"idproduk" => $product->id,));

		if ( strlen($request->input('kategori3')) > 0)
		$dtkategori = DB::select(DB::raw("INSERT INTO ec_product_category_product (category_id,product_id) VALUES(:idkategori,:idproduk)"),
		array("idkategori" => $request->input('kategori3'),"idproduk" => $product->id,));

        return $response
            ->setPreviousUrl(route('marketplace.vendor.products.index'))
            ->setNextUrl(route('marketplace.vendor.products.edit', $product->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int $id, FormBuilder $formBuilder)
    {
        $product = (object)$this->productRepository->findOrFail($id);

        $kategori1 = ($product->kategori1 === null) ? '' :(object)DB::select("select id,name from ec_product_categories
        WHERE
        id='$product->kategori1'")[0];
        $kategori2 = ($product->kategori2 === null) ? '' :(object)DB::select("select id,name from ec_product_categories
        WHERE id='$product->kategori2'")[0];
        $kategori3 = ($product->kategori3 === null) ? '' :(object)DB::select("select id,name from ec_product_categories
        WHERE id='$product->kategori3'")[0];

        $product->kategori1_id = ($product->kategori1 === null) ? '' : $kategori1->id;
        $product->kategori1_name = ($product->kategori1 === null) ? '' : $kategori1->name;

        $product->kategori2_id = ($product->kategori2 === null) ? '' : $kategori2->id;
        $product->kategori2_name = ($product->kategori2 === null) ? '' : $kategori2->name;

        $product->kategori3_id = ($product->kategori3 === null) ? '' : $kategori3->id;
        $product->kategori3_name = ($product->kategori3 === null) ? '' : $kategori3->name;

        $product->kategori1 = ($product->kategori1 === null) ? 'false' : 'true';
        $product->kategori2 = ($product->kategori2 === null) ? 'false' : 'true';
        $product->kategori3 = ($product->kategori3 === null) ? 'false' : 'true';

        if ($product->is_variation || $product->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }
        // return $product;
        page_title()->setTitle(trans('plugins/ecommerce::products.edit', ['name' => $product->name]));
        return $formBuilder
            ->create(ProductForm::class, ['model' => $product])
            ->renderForm();
    }

    public function update(
        int $id,
        ProductRequest $request,
        StoreProductService $service,
        GroupedProductInterface $groupedProductRepository,
        BaseHttpResponse $response,
        ProductVariationInterface $variationRepository,
        ProductVariationItemInterface $productVariationItemRepository,
        StoreProductTagService $storeProductTagService) {

        $product = $this->productRepository->findOrFail($id);

        if ($product->is_variation || $product->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $request = $this->processRequestData($request);
        $product->store_id = auth('customer')->user()->store->id;

        $product = $service->execute($request, $product);

        $storeProductTagService->execute($request, $product);

        $product->kategori1 = $request->input('kategori1');
        $product->kategori2 = $request->input('kategori2');
        $product->kategori3 = $request->input('kategori3');
        $product->save();

        $variationRepository
            ->getModel()
            ->where('configurable_product_id', $product->id)
            ->update(['is_default' => 0]);

        $defaultVariation = $variationRepository->findById($request->input('variation_default_id'));
        if ($defaultVariation) {
            $defaultVariation->is_default = true;
            $defaultVariation->save();
        }

        $addedAttributes = $request->input('added_attributes', []);

        if ($request->input('is_added_attributes') == 1 && $addedAttributes) {
            $result = $variationRepository->getVariationByAttributesOrCreate($id, $addedAttributes);

            /**
             * @var Collection $variation
             */
            $variation = $result['variation'];

            foreach ($addedAttributes as $attribute) {
                $productVariationItemRepository->createOrUpdate([
                    'attribute_id' => $attribute,
                    'variation_id' => $variation->id,
                ]);
            }

            $variation = $variation->toArray();
            $variation['variation_default_id'] = $variation['id'];

            $product->productAttributeSets()->sync(array_keys($addedAttributes));

            $variation['sku'] = $product->sku ?? time();
            foreach (array_keys($addedAttributes) as $attributeId) {
                $attribute = $this->productAttributeRepository->findById($attributeId);
                if ($attribute) {
                    $variation['sku'] .= '-' . $attribute->slug;
                    $variation['attribute_title'] = $attribute->title;
                }
            }

            $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $product->id, $response);
        } elseif ($product->variations()->count() === 0) {
            $product->productAttributeSets()->detach();
        }

        if ($request->has('grouped_products')) {
            $groupedProductRepository->createGroupedProducts($product->id, array_map(function ($item) {
                return [
                    'id' => $item,
                    'qty' => 1,
                ];
            }, array_filter(explode(',', $request->input('grouped_products', '')))));
        }
        // return $product;
		$dtkategori = DB::select(DB::raw("select * from ec_product_category_product WHERE product_id = :idproduk"), array("idproduk" => $id,));
		if( $dtkategori == true )
		{
            $KategoriProduk=ProductCategoryProduct::find($id);
			$KategoriProduk->delete(); //returns true/false
		}

		if ( strlen($request->input('kategori1')) > 0)
		{
			$getProductCategory1 =ProductCategory::find($request->input('kategori1'));

			$dtkategori1 = DB::select(DB::raw("INSERT INTO ec_product_category_product (category_id,product_id) VALUES(:idkategori,:idproduk)"),
			array("idkategori" => $request->input('kategori1'),"idproduk" => $id,));

		}


		if ( strlen($request->input('kategori2')) > 0)
		{
			$getProductCategory2 =ProductCategory::find($request->input('kategori2'));

			$dtkategori2 = DB::select(DB::raw("INSERT INTO ec_product_category_product (category_id,product_id) VALUES(:idkategori,:idproduk)"),
			array("idkategori" => $request->input('kategori2'),"idproduk" => $id,));

		}

		if ( strlen($request->input('kategori3')) > 0)
		{
			$getProductCategory3 =ProductCategory::find($request->input('kategori3'));

			// $dtkategoriprod3 = DB::select(DB::raw("UPDATE ec_products SET kategori3 = :namakategori WHERE id =:idproduk"),
			// array("namakategori" => $getProductCategory3->name,"idproduk" => $id,));

			$dtkategori3 = DB::select(DB::raw("INSERT INTO ec_product_category_product (category_id,product_id) VALUES(:idkategori,:idproduk)"),
			array("idkategori" => $request->input('kategori3'),"idproduk" => $id,));

		}

        return $response
            ->setPreviousUrl(route('marketplace.vendor.products.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    protected function processRequestData(Request $request): Request
    {
        $shortcodeCompiler = shortcode()->getCompiler();

        $request->merge([
            'content' => $shortcodeCompiler->strip($request->input('content'), $shortcodeCompiler->whitelistShortcodes()),
            'images' => json_decode($request->input('images')),
        ]);

        $except = [
            'is_featured',
            'status',
        ];

        foreach ($except as $item) {
            $request->request->remove($item);
        }

        return $request;
    }

    public function getRelationBoxes($id, BaseHttpResponse $response)
    {
        $product = null;
        if ($id) {
            $product = $this->productRepository->findById($id);
        }

        $dataUrl = route(
            'marketplace.vendor.products.get-list-product-for-search',
            ['product_id' => $product ? $product->id : 0]
        );

        return $response->setData(view(
            'plugins/ecommerce::products.partials.extras',
            compact('product', 'dataUrl')
        )->render());
    }

    public function postAddVersion(
        ProductVersionRequest $request,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $request->merge([
            'images' => json_decode($request->input('images', '[]')),
        ]);

        return $this->basePostAddVersion($request, $productVariation, $id, $response);
    }

    public function postUpdateVersion(
        ProductVersionRequest $request,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $request->merge([
            'images' => json_decode($request->input('images', '[]')),
        ]);

        return $this->basePostUpdateVersion($request, $productVariation, $id, $response);
    }

    public function getVersionForm(
        $id,
        Request $request,
        ProductVariationInterface $productVariation,
        BaseHttpResponse $response,
        ProductAttributeSetInterface $productAttributeSetRepository,
        ProductVariationItemInterface $productVariationItemRepository
    ) {
        $product = null;
        $variation = null;
        $productVariationsInfo = [];

        if ($id) {
            $variation = $productVariation->findOrFail($id);
            $product = $this->productRepository->findOrFail($variation->product_id);
            $productVariationsInfo = $productVariationItemRepository->getVariationsInfo([$id]);
        }

        $productId = $variation ? $variation->configurable_product_id : $request->input('product_id');

        if ($productId) {
            $productAttributeSets = $productAttributeSetRepository->getByProductId($productId);
        } else {
            $productAttributeSets = $productAttributeSetRepository->getAllWithSelected($productId);
        }

        $originalProduct = $product;

        // return $product;
        return $response
            ->setData(
                MarketplaceHelper::view('dashboard.products.product-variation-form', compact(
                    'productAttributeSets',
                    'product',
                    'productVariationsInfo',
                    'originalProduct'
                ))->render()
            );
    }

    protected function deleteVersionItem(
        ProductVariationInterface $productVariation,
        ProductVariationItemInterface $productVariationItem,
        $variationId
    ) {
        $variation = $productVariation->findOrFail($variationId);

        $product = $variation->product()->first();

        if (! $product || $product->original_product->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        return $this->baseDeleteVersionItem($productVariation, $productVariationItem, $variationId);
    }

    public function getListProductForSearch(Request $request, BaseHttpResponse $response)
    {
        $availableProducts = $this->productRepository
            ->advancedGet([
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                    ['is_variation', '<>', 1],
                    ['id', '<>', $request->input('product_id', 0)],
                    ['name', 'LIKE', '%' . $request->input('keyword') . '%'],
                    'store_id' => auth('customer')->user()->store->id,
                ],
                'select' => [
                    'id',
                    'name',
                    'images',
                    'image',
                    'price',
                ],
                'paginate' => [
                    'per_page' => 5,
                    'type' => 'simplePaginate',
                    'current_paged' => (int)$request->input('page', 1),
                ],
            ]);

        $includeVariation = $request->input('include_variation', 0);

        return $response->setData(
            view('plugins/ecommerce::products.partials.panel-search-data', compact(
                'availableProducts',
                'includeVariation'
            ))->render()
        );
    }

    public function ajaxProductOptionInfo(
        Request $request,
        BaseHttpResponse $response,
        GlobalOptionInterface $globalOptionRepository
    ): BaseHttpResponse {
        $optionsValues = $globalOptionRepository->findOrFail($request->input('id'), ['values']);

        return $response->setData($optionsValues);
    }

	public function getKategori1()
	{
		$kategorilist = DB::select("select id,name from ec_product_categories1 WHERE parent_id = 0");
		if( $kategorilist == true )
				$data=array("status"=>"OK","message"=>"Kategori ada","listkategori"=>$kategorilist,"result"=>1);
		else
			$data = array("status"=>"error","message"=>"Data aktegori tidak ada","result"=>0);
		echo json_encode($data);
	}

	public function getKategori2(Request $request)
	{
		$idparent =  $request->input('_xhks');

		$kategorilist = DB::select(DB::raw("select id,name from ec_product_categories2 WHERE parent_id = :idparent"), array("idparent" => $idparent,));
		if( $kategorilist == true )
				$data=array("status"=>"OK","message"=>"Kategori ada","listkategori"=>$kategorilist,"result"=>1);
		else
			$data = array("status"=>"error","message"=>"Data aktegori tidak ada","result"=>0);
		echo json_encode($data);
	}

	public function getKategori3(Request $request)
	{
		$idparent =  $request->input('_xhks');

		$kategorilist = DB::select(DB::raw("select id,name from ec_product_categories3 WHERE parent_id = :idparent"), array("idparent" => $idparent,));
		if( $kategorilist == true )
				$data=array("status"=>"OK","message"=>"Kategori ada","listkategori"=>$kategorilist,"result"=>1);
		else
			$data = array("status"=>"error","message"=>"Data aktegori tidak ada","result"=>0);
		echo json_encode($data);
	}

	public function getEtalase(Request $request)
	{
		$kdpos =  $request->input('_xhks');
		if (strlen($kdpos) == 5)
		{
			$kelurahanlist = DB::select(DB::raw("select kelurahan  from kodepos WHERE kodepos = :kdpos"), array("kdpos" => $kdpos,));
			if( $kelurahanlist == true )
			{
				$wilayah = DB::select(DB::raw("select provinsi,kota,kecamatan from kodepos WHERE kodepos = :kdpos
				group by provinsi,kota,kecamatan"), array("kdpos" => $kdpos,));

				$data=array("status"=>"OK","message"=>"Upload Sukses","listkelurahan"=>$kelurahanlist,"kecamatan"=>$wilayah[0]->kecamatan,
				"kota"=>$wilayah[0]->kota,"provinsi"=>$wilayah[0]->provinsi,"result"=>1);
			}
			else
				$data = array("status"=>"error","message"=>"Data wilayah dengan Kode Pos tersebut tidak ada","result"=>0);
		}
		else
				$data = array("status"=>"error","message"=>"input Kode Pos 5 digit, Mohon ulangi lagi!","result"=>0);
		echo json_encode($data);
	}
}
