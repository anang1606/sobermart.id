<?php

namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\MemberWithdrawal;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Marketplace\Forms\WithdrawalForm;
use Botble\Marketplace\Forms\MemberWithdrawalForm;
use Botble\Marketplace\Http\Requests\MemberWithdrawalRequest;
use Botble\Marketplace\Http\Requests\WithdrawalRequest;
use Botble\Marketplace\Repositories\Interfaces\WithdrawalInterface;
use Botble\Marketplace\Tables\WithdrawalTable;
use Botble\Marketplace\Tables\WithdrawalMemberTable;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends BaseController
{
    protected WithdrawalInterface $withdrawalRepository;

    public function __construct(WithdrawalInterface $withdrawalRepository)
    {
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function index(WithdrawalTable $table)
    {
        page_title()->setTitle(trans('plugins/marketplace::withdrawal.name'));

        return $table->renderTable();
    }

    public function member(WithdrawalMemberTable $table){
        page_title()->setTitle(trans('plugins/marketplace::withdrawal.name'));

        return $table->renderTable();
    }

    public function memberEdit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $withdrawal = MemberWithdrawal::where('id',$id)->first();;

        page_title()->setTitle(trans('plugins/marketplace::withdrawal.edit') . ' "' . $withdrawal->customer->name . '"');

        return $formBuilder->create(MemberWithdrawalForm::class, ['model' => $withdrawal])->renderForm();
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $withdrawal = $this->withdrawalRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $withdrawal));

        page_title()->setTitle(trans('plugins/marketplace::withdrawal.edit') . ' "' . $withdrawal->customer->name . '"');

        return $formBuilder->create(WithdrawalForm::class, ['model' => $withdrawal])->renderForm();
    }

    public function memberPost(int $id, Request $request, BaseHttpResponse $response)
    {
        $withdrawal = MemberWithdrawal::where('id',$id)->first();;

        $data = [
            'images' => array_filter($request->input('images', [])),
            'user_id' => Auth::id(),
            'description' => $request->input('description'),
            'payment_channel' => $request->input('payment_channel'),
        ];

        if ($withdrawal->canEditStatus()) {
            $data['status'] = $request->input('status');

            if ($withdrawal->status == WithdrawalStatusEnum::PROCESSING &&
                $data['status'] == WithdrawalStatusEnum::COMPLETED
            ) {
                $store = $withdrawal->customer->store;

                EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'store_name' => $store->name,
                        'withdrawal_amount' => format_price($withdrawal->amount),
                    ])
                    ->sendUsingTemplate('withdrawal-approved', $store->email);
            }else if($data['status'] == WithdrawalStatusEnum::CANCELED){
                $customer = Customer::where('id',$withdrawal->customer_id)->first();
                if($customer){
                    $customer->commissions += (int)$withdrawal->amount;
                    $customer->save();
                }
            }
        }

        $withdrawal->fill($data);

        $withdrawal->save();

        event(new UpdatedContentEvent(WITHDRAWAL_MODULE_SCREEN_NAME, $request, $withdrawal));

        return $response
            ->setPreviousUrl(route('marketplace.member.withdrawal'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function update(int $id, WithdrawalRequest $request, BaseHttpResponse $response)
    {
        $withdrawal = $this->withdrawalRepository->findOrFail($id);

        $data = [
            'images' => array_filter($request->input('images', [])),
            'user_id' => Auth::id(),
            'description' => $request->input('description'),
            'payment_channel' => $request->input('payment_channel'),
            'transaction_id' => $request->input('transaction_id'),
        ];

        if ($withdrawal->canEditStatus()) {
            $data['status'] = $request->input('status');

            if ($withdrawal->status == WithdrawalStatusEnum::PROCESSING &&
                $data['status'] == WithdrawalStatusEnum::COMPLETED
            ) {
                $store = $withdrawal->customer->store;

                EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'store_name' => $store->name,
                        'withdrawal_amount' => format_price($withdrawal->amount),
                    ])
                    ->sendUsingTemplate('withdrawal-approved', $store->email);
            }
        }

        $withdrawal->fill($data);

        $this->withdrawalRepository->createOrUpdate($withdrawal);

        event(new UpdatedContentEvent(WITHDRAWAL_MODULE_SCREEN_NAME, $request, $withdrawal));

        return $response
            ->setPreviousUrl(route('marketplace.withdrawal.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $withdrawal = $this->withdrawalRepository->findOrFail($id);

            $this->withdrawalRepository->delete($withdrawal);

            event(new DeletedContentEvent(WITHDRAWAL_MODULE_SCREEN_NAME, $request, $withdrawal));

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
            $withdrawal = $this->withdrawalRepository->findOrFail($id);
            $this->withdrawalRepository->delete($withdrawal);
            event(new DeletedContentEvent(WITHDRAWAL_MODULE_SCREEN_NAME, $request, $withdrawal));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
