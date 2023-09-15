<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Marketplace\Tables\EtalaseTable;
use Illuminate\Http\Request;
use Botble\Base\Http\Controllers\BaseController;
use MarketplaceHelper;
use Html;
use RvMedia;
use BaseHelper;
use Exception;

class EtalaseController extends BaseController
{

    protected ProductInterface $productRepository;

    public function __construct(
        ProductInterface $productRepository
    ) {
        $this->productRepository = $productRepository;

        Assets::setConfig(config('plugins.marketplace.assets', []));
    }

    public function index(EtalaseTable $table)
    {
        page_title()->setTitle(__('Etalase'));
        return $table->render(MarketplaceHelper::viewPath('dashboard.table.etalase'));
    }

    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $querys = $this->productRepository->getModel()
                ->select([
                    'id',
                    'etalase',
                ])
                ->where('etalase',$id)
                ->where('store_id', auth('customer')->user()->store->id)
                ->get();

            foreach($querys as $query){
                $get_products = $this->productRepository->getModel()
                ->select([
                    'id',
                    'etalase',
                    'name',
                ])
                ->where('id',$query->id)
                ->first();

                $get_products->etalase = '';
                $get_products->name = $get_products->name;
                $get_products->save();
            }
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function update(Request $request,BaseHttpResponse $response){
        $data = (object)$request->input();
        try {
            $querys = $this->productRepository->getModel()
                ->select([
                    'id',
                    'etalase',
                ])
                ->where('etalase',$data->old_etalase)
                ->where('store_id', auth('customer')->user()->store->id)
                ->get();

            foreach($querys as $query){
                $get_products = $this->productRepository->getModel()
                ->select([
                    'id',
                    'etalase',
                    'name',
                ])
                ->where('id',$query->id)
                ->first();

                $get_products->etalase = $data->name_etalase;
                $get_products->name = $get_products->name;
                $get_products->save();
            }
            return $response
            ->setPreviousUrl(route('marketplace.vendor.etalase.index'))
            ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function etalaseDetails(Request $request){
        $request->group = strip_tags($request->group);
        $query = $this->productRepository->getModel()
            ->select([
                'id',
                'name',
                'order',
                'created_at',
                'status',
                'sku',
                'images',
                'price',
                'sale_price',
                'sale_type',
                'start_date',
                'end_date',
                'etalase',
                'quantity',
                'with_storehouse_management',
                'product_type',
            ])
            ->where([['is_variation', 0],['etalase',($request->group === 'Semua Product') ? '' : $request->group]])
            ->where('store_id', auth('customer')->user()->store->id)
            ->get();

        $table = '<table>';
        $table .= '<thead>
                <tr>
                    <th style="text-align:left;padding: 5px 12px !important;" width="40px">No.</th>
                    <th style="text-align:left;" width="80px">Thumbhnail</th>
                    <th style="text-align:left;" width="57%">Name</th>
                    <th style="text-align:left;" width="15%">Price</th>
                    <th style="text-align:left;padding: 5px 12px !important;" width="40px">QTY</th>
                    <th style="text-align:left;">SKU</th>
                </tr>
        </thead>';

        $table .= '<tbody>';
        $no = 1;
        foreach($query as $product){
            $table .= '<tr>';
            // $table .= "<td>$product->id</td>";
            $table .= "<td style='text-align:left;'>".$no++."</td>";
            $table .= "<td style='text-align:left;'>".Html::image(
                RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()),
                trans('core/base::tables.image'),[]
                )."</td>";
            $table .= "<td style='text-align:left;'>".Html::link(route('marketplace.vendor.products.edit', $product->id), BaseHelper::clean($product->name))."</td>";
            $table .= "<td style='text-align:left;'>".$product->price_in_table."</td>";
            $table .= "<td style='text-align:left;'>".($product->with_storehouse_management ? $product->quantity : '&#8734;')."</td>";
            $table .= "<td style='text-align:left;'>".BaseHelper::clean($product->sku ?: '&mdash;')."</td>";
            $table .= '</tr>';
        }
        $table .='</tbody>';

        $table .= '</table>';

        return $table;
    }
}
