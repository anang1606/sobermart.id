<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\UpdateOrderRequest;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Marketplace\Http\Requests\UpdateShippingStatusRequest;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvoiceHelper;
use MarketplaceHelper;
use OrderHelper;

class RevenueAllController extends BaseController
{   
	public function index(Request $request)
    {
        page_title()->setTitle(__('Revenue'));


        $orders = auth('customer')->user()->store->orders()->get();
		return view('plugins/ecommerce::revenueall.index',compact('orders'));
		//return view('packages/theme::revenueall.index');
		//return view('views.marketplace.revenueall.index', compact('orders'));

        //return $table->render(MarketplaceHelper::viewPath('revenueall.index'), compact('orders'));
    }
    
    /*public function getGenerateInvoice(int $orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        return InvoiceHelper::downloadInvoice($order->invoice);
    }*/

        
}
