<?php

namespace Botble\Accounting\Forms;

use Botble\Accounting\Models\CoaSaldo;
use Botble\Base\Forms\FormAbstract;

class CoaSaldoForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new CoaSaldo())
            ->withCustomFields()
            ->add('tahun', 'number', [
                'label' => 'Periode',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Periode',
                    'readonly' => true,
                ],
            ])
            ->add('namacoa', 'text', [
                'label' => 'Nama Akun',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Nama Akun',
                    'readonly' => true,
                ],
            ])
            ->add('kredit', 'number', [
                'label' => 'Kredit',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Kredit',
                ],
            ])
            ->add('debit', 'number', [
                'label' => 'Debit',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Debit',
                ],
            ]);
    }
}
