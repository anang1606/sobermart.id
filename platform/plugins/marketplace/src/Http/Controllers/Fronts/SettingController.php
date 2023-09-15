<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\JneBranch;
use Botble\Ecommerce\Models\JneOrigin;
use Botble\Ecommerce\Models\JneSupport;
use Botble\Marketplace\Http\Requests\SettingRequest;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Models\KodePos;
use Botble\Marketplace\Repositories\Interfaces\StoreInterface;
use Illuminate\Contracts\Config\Repository;
use MarketplaceHelper;
use Illuminate\Http\Request;
use RvMedia;
use SlugHelper;
use DB;
use Illuminate\Support\Facades\Cache;

class SettingController
{
    public function __construct(Repository $config)
    {
        Assets::setConfig($config->get('plugins.marketplace.assets', []));
    }

    public function index()
    {
        page_title()->setTitle(__('Settings'));

        Assets::addScriptsDirectly('vendor/core/plugins/location/js/location.js');

        $store = auth('customer')->user()->store;

        return MarketplaceHelper::view('dashboard.settings', compact('store'));
    }

    public function saveSettings(SettingRequest $request, StoreInterface $storeRepository, BaseHttpResponse $response)
    {

        $kota_kabupaten = explode(' ',$request->city,2);

        $origin = '';
        if(strtolower($kota_kabupaten[0]) == 'kota'){
            $get_origin = JneOrigin::where('origin_name',$kota_kabupaten[1])->get();
            if(count($get_origin) > 0){
                if(count($get_origin) > 1){
                    foreach($get_origin as $go){
                        if(strtolower($go->origin_province) === strtolower($request->kelurahan) || strtolower($go->origin_province) === strtolower($request->kecamatan)){
                            $origin = $go;
                        }else{
                            $origin = $get_origin[0];
                        }
                    }
                }else{
                    $origin = $get_origin[0];
                }
            }
        }else{
            $get_origin = JneOrigin::where('origin_name',$kota_kabupaten[1])->get();
            if(count($get_origin) > 0){
                if(count($get_origin) > 1){
                    foreach($get_origin as $go){
                        if(strtolower($go->origin_province) === strtolower($request->kelurahan) || strtolower($go->origin_province) === strtolower($request->kecamatan)){
                            $origin = $go;
                        }else{
                            $origin = $get_origin[0];
                        }
                    }
                }else{
                    $origin = $get_origin[0];
                }
            }
        }

        $searchTerm = substr($origin->origin_code,0,3);
        $get_branch = JneBranch::where('branch_code','like',$searchTerm.'%')->first();

        $request['origin_shipment'] = $origin->origin_code;
        $request['branch_shipment'] = $get_branch->branch_code;

        // return $request->input();

        $store = auth('customer')->user()->store;

        $existing = SlugHelper::getSlug($request->input('slug'), SlugHelper::getPrefix(Store::class), Store::class);

        if ($existing && $existing->reference_id != $store->id) {
            return $response->setError()->setMessage(__('Shop URL is existing. Please choose another one!'));
        }

        if ($request->hasFile('logo_input')) {
            $result = RvMedia::handleUpload($request->file('logo_input'), 0, 'stores');
            if (! $result['error']) {
                $file = $result['data'];
                $request->merge(['logo' => $file->url]);
            }
        }

		if ($request->hasFile('ktp_input')) {
            $result = RvMedia::handleUpload($request->file('ktp_input'), 0, 'stores');
            if (! $result['error']) {
                $file = $result['data'];
                $request->merge(['ktp' => $file->url]);
            }
        }

		if ($request->hasFile('covers_input')) {
            $result = RvMedia::handleUpload($request->file('covers_input'), 0, 'stores');
            if (! $result['error']) {
                $file = $result['data'];
                $request->merge(['covers' => $file->url]);
            }
        }

        $store->fill($request->input());



        $storeRepository->createOrUpdate($store);

        $customer = $store->customer;

        if ($customer && $customer->id) {
            $vendorInfo = $customer->vendorInfo;
            $vendorInfo->payout_payment_method = $request->input('payout_payment_method');
            $vendorInfo->bank_info = $request->input('bank_info', []);
            $vendorInfo->tax_info = $request->input('tax_info', []);
            $vendorInfo->save();
        }

        event(new UpdatedContentEvent(STORE_MODULE_SCREEN_NAME, $request, $store));

        return $response
            ->setNextUrl(route('marketplace.vendor.settings'))
            ->setMessage(__('Update successfully!'));
    }

	public function getwilayah(Request $request)
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
