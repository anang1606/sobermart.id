<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Models\RequestGift;
use Botble\Marketplace\Forms\Fields\CustomImagesField;
use EcommerceHelper;

class RequestGiftForm extends FormAbstract
{
    public function buildForm(): void
    {
        $exists = $this->getModel() && $this->getModel()->id;

        if ($exists) {
            $model = $this->getModel();
        }

        $this
            ->setupModel(new RequestGift())
            ->withCustomFields()
            ->add('customer_name', 'text', [
                'label' => 'Nama Customer',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'readonly' => true
                ],
            ])
            ->add('paket_name', 'text', [
                'label' => 'Paket',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'readonly' => true
                ],
            ])
            ->add('nama', 'text', [
                'label' => 'Nama',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'readonly' => true
                ],
            ])
            ->add('label', 'text', [
                'label' => 'Hadiah',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'readonly' => true
                ],
            ])
            ->add('alamat', 'textarea', [
                'label' => 'Alamat Pengirim',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'readonly' => true,
                    'rows' => 4
                ],
            ])
            ->add('photo_ktp', 'mediaImage', [
                'label' => 'Photo KTP',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'readonly' => true
                ],
            ])
            ->add('photo_ktp_selfi', 'mediaImage', [
                'label' => 'Photo Selfie KTP',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'readonly' => true
                ],
            ]);

        $this
            ->add('notes', 'textarea', [
                'label' => 'Catatan',
                'label_attr' => ['class' => 'control-label'],
                'attr' => array_merge([
                    'rows' => 3,
                    'placeholder' => 'Silahkan masukan catatan untuk di kirim ke customer',
                    'data-counter' => 1200,
                ]),
            ]);

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

        $this
            ->setBreakFieldPoint('notes');
    }
}
