<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Models\MemberWithdrawal;
use Botble\Marketplace\Enums\PayoutPaymentMethodsEnum;
use Botble\Marketplace\Http\Requests\MemberWithdrawalRequest;
use Botble\Marketplace\Http\Requests\WithdrawalRequest;
use Botble\Marketplace\Models\Withdrawal;

class MemberWithdrawalForm extends FormAbstract
{
    public function buildForm(): void
    {
        $symbol = ' (' . get_application_currency()->symbol . ')';

        $this
            ->setupModel(new MemberWithdrawal())
            ->setValidatorClass((auth('customer')->check()) ? MemberWithdrawalRequest::class : '')
            ->withCustomFields()
            ->add('amount', 'text', [
                'label' => trans('plugins/marketplace::withdrawal.forms.amount') . $symbol,
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('payment_channel', 'customSelect', [
                'label' => trans('plugins/marketplace::withdrawal.forms.payment_channel'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => PayoutPaymentMethodsEnum::labels(),
                'attr' => $this->model->transaction_id ? ['disabled' => 'disabled'] : [],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('payoutInfo', 'html', [
                'html' => view('plugins/marketplace::withdrawals.payout-info', [
                    'bankInfo' => $this->getModel()->bank_info,
                    'taxInfo' => $this->getModel()->customer->tax_info,
                    'paymentChannel' => $this->getModel()->payment_channel,
                    'title' => __('Payout account'),
                ])->render(),
            ])
            ->add('images[]', 'mediaImages', [
                'label' => trans('plugins/ecommerce::products.form.image'),
                'label_attr' => ['class' => 'control-label'],
                'values' => $this->getModel() ? $this->getModel()->images : [],
            ]);

            if ($this->getModel()->canEditStatus()) {
                $this
                    ->add('status', 'customSelect', [
                        'label' => trans('core/base::tables.status'),
                        'label_attr' => ['class' => 'control-label required'],
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'choices' => $this->getModel()->getNextStatuses(),
                        'help_block' => [
                            'text' => $this->getModel()->getStatusHelper(),
                        ],
                    ]);
            } else {
                $this
                    ->add('status', 'html', [
                        'html' => $this->getModel()->status->toHtml(),
                    ]);
            }

        $this->setBreakFieldPoint('status');
    }
}
