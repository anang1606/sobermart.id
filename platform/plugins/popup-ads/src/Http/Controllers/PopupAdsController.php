<?php

namespace Botble\PopupAds\Http\Controllers;

use Botble\PopupAds\Http\Requests\PopupAdsRequest;
use Botble\PopupAds\Tables\PopupAdsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\PopupAds\Forms\PopupAdsForm;
use Botble\PopupAds\Models\PopupAds;
use Botble\PopupAds\Repositories\Eloquent\PopupAdsRepository;
use DB;
use Exception;
use Illuminate\Http\Request;

class PopupAdsController extends BaseController
{
    protected PopupAdsRepository $adsRepository;

    public function __construct(PopupAdsRepository $adsRepository)
    {
        $this->adsRepository = $adsRepository;
    }

    public function index(PopupAdsTable $table)
    {
        page_title()->setTitle(trans('plugins/ads::ads.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ads::ads.create'));

        return $formBuilder->create(PopupAdsForm::class)->renderForm();
    }

    public function store(PopupAdsRequest $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $createAds = new PopupAds;
            $createAds->name = $request->name;
            $createAds->expired_at = $request->expired_at;
            $createAds->key = $request->key;
            $createAds->image = $request->image;
            $createAds->url = $request->url;
            $createAds->order = $request->order;
            $createAds->status = $request->status;
            if($createAds->save()){
                DB::commit();
            }
            return $response
                ->setPreviousUrl(route('popup-ads.index'))
                ->setNextUrl(route('popup-ads.edit', $createAds->id))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $ex) {
            DB::rollBack();
            return $response->setMessage('Terjadi kesalahan, Silahkan coba lagi.');
        }
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $ads = PopupAds::find($id);

        page_title()->setTitle(trans('plugins/ads::ads.edit') . ' "' . $ads->name . '"');

        return $formBuilder->create(PopupAdsForm::class, ['model' => $ads])->renderForm();
    }

    public function update(int $id, PopupAdsRequest $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $createAds = PopupAds::find($id);
            $createAds->name = $request->name;
            $createAds->expired_at = $request->expired_at;
            $createAds->key = $request->key;
            $createAds->image = $request->image;
            $createAds->url = $request->url;
            $createAds->order = $request->order;
            $createAds->status = $request->status;
            if($createAds->save()){
                DB::commit();
            }
            return $response
            ->setPreviousUrl(route('popup-ads.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Exception $ex) {
            DB::rollBack();
            return $response->setMessage('Terjadi kesalahan, Silahkan coba lagi.');
        }
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $ads = PopupAds::findOrFail($id);
            $ads->delete();

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $ads = $this->adsRepository->findOrFail($id);
            $this->adsRepository->delete($ads);
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
