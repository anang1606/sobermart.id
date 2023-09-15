<?php

namespace Botble\AdsCategory\Http\Controllers;

use Botble\AdsCategory\Forms\AdsCategoryForm;
use Botble\AdsCategory\Http\Requests\AdsCategoryRequest;
use Botble\AdsCategory\Models\AdsCategory;
use Botble\AdsCategory\Repositories\Eloquent\AdsCategoryRepository;
use Botble\AdsCategory\Tables\AdsCategoryTable;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use DB;
use Exception;
use Illuminate\Http\Request;

class AdsCategoryController extends BaseController
{
    protected AdsCategoryRepository $adsRepository;

    public function __construct(AdsCategoryRepository $adsRepository)
    {
        $this->adsRepository = $adsRepository;
    }

    public function index(AdsCategoryTable $table)
    {
        page_title()->setTitle(trans('plugins/ads::ads.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ads::ads.create'));

        return $formBuilder->create(AdsCategoryForm::class)->renderForm();
    }

    public function store(AdsCategoryRequest $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $createAds = new AdsCategory;
            $createAds->name = $request->name;
            $createAds->expired_at = $request->expired_at;
            $createAds->key = $request->key;
            $createAds->image = $request->image;
            $createAds->url = $request->url;
            $createAds->order = $request->order;
            $createAds->status = $request->status;
            $createAds->categories_id = $request->categories_id;
            if($createAds->save()){
                DB::commit();
                event(new CreatedContentEvent(ADS_CATEGORY_MODULE_SCREEN_NAME, $request, $createAds));
            }
            return $response
                ->setPreviousUrl(route('category-ads.index'))
                ->setNextUrl(route('category-ads.edit', $createAds->id))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $ex) {
            DB::rollBack();
            return $response->setMessage('Terjadi kesalahan, Silahkan coba lagi.');
        }
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $ads = AdsCategory::find($id);

        page_title()->setTitle(trans('plugins/ads::ads.edit') . ' "' . $ads->name . '"');

        return $formBuilder->create(AdsCategoryForm::class, ['model' => $ads])->renderForm();
    }

    public function update(int $id, AdsCategoryRequest $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $createAds = AdsCategory::find($id);
            $createAds->name = $request->name;
            $createAds->expired_at = $request->expired_at;
            $createAds->key = $request->key;
            $createAds->image = $request->image;
            $createAds->url = $request->url;
            $createAds->order = $request->order;
            $createAds->status = $request->status;
            $createAds->categories_id = $request->categories_id;
            if($createAds->save()){
                DB::commit();
                event(new UpdatedContentEvent(ADS_CATEGORY_MODULE_SCREEN_NAME, $request, $createAds));
            }
            return $response
            ->setPreviousUrl(route('category-ads.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Exception $ex) {
            DB::rollBack();
            return $response->setMessage('Terjadi kesalahan, Silahkan coba lagi.');
        }
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $ads = AdsCategory::findOrFail($id);
            $ads->delete();

            event(new DeletedContentEvent(ADS_CATEGORY_MODULE_SCREEN_NAME, $request, $ads));

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
            event(new DeletedContentEvent(ADS_CATEGORY_MODULE_SCREEN_NAME, $request, $ads));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
