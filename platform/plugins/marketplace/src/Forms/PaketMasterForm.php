<?php

namespace Botble\Marketplace\Forms;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Marketplace\Repositories\Eloquent\PaketMasterRepository;
use Botble\Marketplace\Http\Requests\PaketMasterRequest;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Botble\Ecommerce\Models\PaketMaster;
use EcommerceHelper;

class PaketMasterForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addScripts(['jquery-ui'])->addScriptsDirectly([
            'vendor/core/plugins/ecommerce/js/paket-master-option.js',
        ]);

        $this
            ->setupModel(new PaketMaster())
            ->setValidatorClass(PaketMasterRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                ],
            ])
            ->add('nominal', 'number', [
                'label' => 'Nominal',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Nominal',
                ],
            ])
            ->add('fee_commissions', 'text', [
                'label' => 'Commissions',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Commissions',
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('image', 'mediaImage', [
                'label' => trans('core/base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('image')
            ->addMetaBoxes([
                'product_options_box' => [
                    'id' => 'product_options_box',
                    'title' => 'Gift',
                    'content' => view(
                        'plugins/ecommerce::paket-option.index',
                        ['values' => $this->model->values]
                    )->render(),
                ],
            ]);
    }
}
