<?php

namespace Botble\Accounting\Http\Controllers;

use Botble\Accounting\Forms\ExpenseForm;
use Botble\Accounting\Models\CoaJurnal;
use Botble\Accounting\Models\Expense;
use Botble\Accounting\Tables\ExpensesTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ExpenseCrontoller extends Controller
{
    public function index(ExpensesTable $table)
    {
        page_title()->setTitle('Pengeluaran');

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        return $formBuilder->create(ExpenseForm::class)->renderForm();
    }

    public function store(Request $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $kas = [
                [
                    'kode_reff' => $request->kode_reff,
                    'tanggal'   => date('Y-m-d', strtotime($request->date)),
                    'idcoa' => $request->coadebit,
                    'keterangan' => $request->note . ' ' . '(Ket: Kas Keluar)',
                    'debit' => $request->amount,
                    'kredit' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'kode_reff' => $request->kode_reff,
                    'tanggal'   => date('Y-m-d', strtotime($request->date)),
                    'idcoa' => $request->coakredit,
                    'keterangan' => $request->note . ' ' . '(Ket: Kas Keluar)',
                    'debit' => 0,
                    'kredit' => $request->amount,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];

            $save_jurnal = CoaJurnal::insert($kas);
            if ($save_jurnal) {
            }
            $code = $this->generate_code($length = 10, $sm_alpha = false, $lg_alpha = false, $number = true, $specialchar = false);
            $expenses = new Expense;
            $expenses->kode_jurnal  = $code;
            $expenses->kode_reff    = $request->kode_reff;
            $expenses->date      = date('Y-m-d', strtotime($request->date));
            $expenses->coadebit  = $request->coadebit;
            $expenses->coakredit = $request->coakredit;
            $expenses->note   = $request->note;
            $expenses->amount        = $request->amount;
            $expenses->user_id        = auth()->user()->id;
            $expenses->save();
            DB::commit();
            return $response
                ->setPreviousUrl(route('expense.index'))
                ->setMessage(trans('core/base::notices.update_success_message'));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $response->setError()->setMessage('Terjadi kesalahan, Silahkan coba lagi.');
        }
    }

    private function generate_code($length = 6, $sm_alpha = false, $lg_alpha = true, $number = false, $specialchar = false)
    {
        $characters  = " ";
        if ($sm_alpha) {
            $characters  .= "abcdefghijkmnopqrstuvwxyz";
        }
        if ($lg_alpha) {
            $characters  .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        if ($number) {
            $characters  .= "0123456789";
        }
        if ($specialchar) {
            $characters  .= "!@#$%&*?";
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(1, $charactersLength - 1)];
        }
        return $randomString;
    }
}
