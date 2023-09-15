<?php

namespace Botble\Accounting\Forms;

use Botble\Accounting\Http\Requests\ExpensesRequest;
use Botble\Accounting\Models\Coa;
use Botble\Accounting\Models\Expense;
use Botble\Base\Forms\FormAbstract;

class ExpenseForm extends FormAbstract
{
    public function buildForm(): void
    {
        $coakredits  = Coa::where('typecoa', 'NOT LIKE', 'Level%')
            ->where('idcoa', 'like', '11%')
            ->where('idcoa', '<', '113000')
            ->get();

        $coadebits  = Coa::where('typecoa', 'NOT LIKE', 'Level%')
            ->where('idcoa', 'like', '6%')
            ->get();

        $selectCoaKredit = [];
        foreach ($coakredits as $coakredit) {
            $selectCoaKredit[$coakredit->idcoa] = '(' . $coakredit->idcoa . ') ' . $coakredit->namacoa . ' - ' . $coakredit->typecoa;
        }

        $selectCoaDebit = [];
        foreach ($coadebits as $coadebit) {
            $selectCoaDebit[$coadebit->idcoa] = '(' . $coadebit->idcoa . ') ' . $coadebit->namacoa . ' - ' . $coadebit->typecoa;
        }

        $this
            ->setupModel(new Expense())
            ->withCustomFields()
            ->setValidatorClass(ExpensesRequest::class)
            ->add('date', 'date', [
                'label' => 'Tanggal',
                'label_attr' => ['class' => 'control-label required'],
            ])
            ->add('kode_reff', 'text', [
                'label' => 'No Refferensi',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'No Refferensi',
                ],
            ])
            ->add('amount', 'number', [
                'label' => 'Jumlah Pengeluaran',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Jumlah Pengeluaran',
                ],
            ])
            ->add('note', 'textarea', [
                'label' => 'Keterangan Pengeluaran',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => 'Keterangan Pengeluaran',
                ],
            ])
            ->add('coadebit', 'customSelect', [
                'label' => 'Debit',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $selectCoaDebit,
            ])
            ->add('coakredit', 'customSelect', [
                'label' => 'Kredit',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $selectCoaKredit,
            ]);
    }
}
