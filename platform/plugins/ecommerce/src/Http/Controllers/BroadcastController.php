<?php
namespace Botble\Ecommerce\Http\Controllers;

use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Forms\BroadcastForm;
use Botble\Ecommerce\Http\Requests\BroadcastRequest;
use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastCustomer;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Tables\BroadcastTable;
use Exception;
use RvMedia;
use Illuminate\Http\Request;

class BroadcastController extends BaseController
{
    protected CustomerInterface $customerRepository;
    protected ProductInterface $productRepository;

    public function __construct(CustomerInterface $customerRepository,ProductInterface $productRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }
    public function index(BroadcastTable $dataTable){
        page_title()->setTitle('Broadcast');
        return $dataTable->renderTable();
    }
    public function create(FormBuilder $formBuilder){
        page_title()->setTitle('Broadcast');

        return $formBuilder->create(BroadcastForm::class)->renderForm();
    }

    public function edit(int $id, FormBuilder $formBuilder)
    {
        $brand = Broadcast::find($id);
        page_title()->setTitle('Broadcast' . ' "' . $brand->title . '"');

        return $formBuilder->create(BroadcastForm::class, ['model' => $brand])->renderForm();
    }

    public function store(BroadcastRequest $request,BaseHttpResponse $response){
        $input = (object)$request->input();

        $new_broadcast = new Broadcast;
        $new_broadcast->title = $input->title;
        $new_broadcast->description = $input->description;
        $new_broadcast->type = $input->type;
        $new_broadcast->target = $input->target;
        $new_broadcast->image = $input->image;
        $new_broadcast->website = $input->website;
        $new_broadcast->product_id = $input->products;
        if($new_broadcast->save()){
            if($input->customer){
                $customers = json_decode($input->customer);
                foreach($customers as $customer){
                    $search_customer = Customer::where('name',$customer->value)->first();
                    if($search_customer){
                        $insert_customer = new BroadcastCustomer;
                        $insert_customer->customer_id = $search_customer->id;
                        $insert_customer->broadcast_id = $new_broadcast->id;
                        $insert_customer->save();
                    }
                }
            }
        }

        return $response
            ->setPreviousUrl(route('ecommerce.broadcast'))
            ->setNextUrl(route('ecommerce.broadcast'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function getAllCustomer()
    {
        return $this->customerRepository->pluck('name');
    }

    public function getRelationsBoxes(Request $request, BaseHttpResponse $response){
        $searchTerm = $request->term;

        $products = Product::where([['name','LIKE','%'.$searchTerm.'%'],['is_variation',0]])
        ->limit(10)
        ->get();

        foreach($products as $product){
            $product->image = RvMedia::url($product->image);
        }

        return $response->setData($products);
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            Broadcast::find($id)->delete();
            BroadcastCustomer::where('broadcast_id', $id)->delete();

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

}
