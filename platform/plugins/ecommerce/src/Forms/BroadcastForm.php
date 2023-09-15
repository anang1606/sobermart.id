<?php

namespace Botble\Ecommerce\Forms;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\Product;

class BroadcastForm extends FormAbstract
{
    public function buildForm()
    {
        Assets::addScripts([
            'jquery-ui',
        ])
        ->addStylesDirectly([
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
        ])
        ->addScriptsDirectly([
            'vendor/core/plugins/ecommerce/js/global-broadcast.js',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
        ]);

        $tags = null;

        if ($this->getModel()) {
            $tags = $this->getModel()->customer()->pluck('name')->all();
            $tags = implode(',', $tags);
        }

        $productId = $this->getModel() ? $this->getModel()->id : null;

        $products = [];
        $get_products = Product::where('status',BaseStatusEnum::PUBLISHED)->get();
        foreach($get_products as $product){
            $products[] = [
                $product->id => $product->name
            ];
        }

        $this
            ->setupModel(new Broadcast())
            // ->setValidatorClass(BrandRequest::class)
            ->withCustomFields()
            ->addCustomField('tags', TagField::class)
            ->add('title', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'editor', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('plugins/ecommerce::products.form.description'),
                ],
            ])
            ->add('website', 'text', [
                'label' => trans('plugins/ecommerce::brands.form.website'),
                'label_attr' => ['class' => 'control-label general-type'],
                'attr' => [
                    'placeholder' => 'Ex: https://example.com',
                    'class' => 'form-control general-type'
                ],
            ])
            ->add('products', 'select', [
                'label' => trans('plugins/ecommerce::flash-sale.products'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control product-type',
                    'data-url' => route('ecommerce.broadcast.get-relations-boxes')
                ],
            ])
            ->add('customer', 'tags', [
                'label' => 'Customer',
                'label_attr' => ['class' => 'control-label target-customer'],
                'value' => $tags,
                'attr' => [
                    'placeholder' => 'Customer',
                    'data-url' => route('ecommerce.broadcast.all.cutomer'),
                    'class' => 'target-customer'
                ],
            ])
            ->add('short_code', 'customSelect', [
                'label' => 'Short Code',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control short-code-broadcast',
                    'data-result' => 'description'
                ],
                'choices' => [
                    'customer' => 'Customer',
                    'product' => 'Product',
                ],
                'help_block' => [
                    'text' => 'Jika kamu ingin menambahkan "Nama Customer" atau "Nama Product" untuk pesan anda silahkan pilih salah satu.',
                ],
            ])
            ->add('type', 'customSelect', [
                'label' => trans('plugins/ecommerce::product-option.option_type'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => ['class' => 'form-control option-type'],
                'choices' => [
                    'general' => 'General',
                    'product' => 'Product',
                ],
            ])
            ->add('target', 'customSelect', [
                'label' => 'Target',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => ['class' => 'form-control target-type'],
                'choices' => [
                    'all' => 'All',
                    'user' => 'Customer',
                ],
            ])
            ->add('image', 'mediaImage', [
                'label' => trans('plugins/ecommerce::brands.logo'),
                'label_attr' => ['class' => 'control-label'],
            ])
            // ->addMetaBoxes([
            //     'with_related' => [
            //         'title' => null,
            //         'content' => '<div class="wrap-relation-product" data-target="' . route(
            //             'ecommerce.broadcast.get-relations-boxes'
            //         ) . '"></div>',
            //         'wrap' => false,
            //         'priority' => 9999,
            //     ],
            // ])
            ->setBreakFieldPoint('short_code');
    }
}
