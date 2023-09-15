<?php

namespace Botble\Accounting\Http\Controllers;

use Auth;
use Botble\Accounting\Helpers\LabaRugi as HelpersLabaRugi;
use Botble\Accounting\Models\LabaRugi;
use Illuminate\Routing\Controller;

class LabaRugiCrontoller extends Controller
{
    public function index()
    {
        page_title()->setTitle('SPT PPh BADAN');

        $laba_rubi = LabaRugi::where([['parent', 0]])->get();

        $html = '';
        foreach ($laba_rubi as $key => $lb) {
            $rugi_laba = new HelpersLabaRugi($lb);
            $amount = ($lb->amount !== '0') ? number_format($lb->amount) : '';

            $html .= "<tr style='padding: 0;'>";
            $html .= "<td style='padding: 0;' width='800'>
                    <h4
                        style='text-transform: uppercase;font-weight: 600;font-size: 15px;text-decoration: underline;'>
                        $lb->keterangan
                    </h4>
                </td>";

            $html .= "<td colspan='3' style='text-align:right;'>
                    <h4 style='text-transform: uppercase;font-weight: 600;font-size: 15px;'>
                        " . $amount . "
                    </h4>
                </td>";
            $html .= "</tr>";
            $html .= $rugi_laba->htmlList();
        }

        $html .= "<tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
        $html .= "<tr style='padding: 0;'>";
        $html .= "<td width='250'></td>";
        $html .= "<td colspan='2' style='text-align: right;padding: 4px 0 0 48px;' width='250'>
                    <div
                        style='display: flex;flex-direction: column;align-items: flex-end;'>
                        <span style='font-size: 15px;'>
                            ". get_ecommerce_setting('store_city') .", " . date('d F Y') . "
                        </span>
                        <div style='display: flex;flex-direction: column;align-items: flex-start;width: 140px;'>
                            <span style='margin-top: 80px;font-size: 15px;text-decoration: underline;'>
                                " . Auth::user()->name. "
                            </span>
                        </div>
                    </div>
                </td>";
        $html .= "</tr>";
        $html .= '<tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>';

        $get_PPH = LabaRugi::on(\Config('db'))->where('parent', 99999)->first();
        $pph = new HelpersLabaRugi($get_PPH);

        $html .= "<tr style='line-height: 14px;'>";
        $html .= "<td style='padding: 4px 0 0 48px;' width='800'>
                    <h4
                        style='margin:0;font-size: 14px;font-weight: 600;text-transform: uppercase;text-decoration: underline;'>
                        $lb->keterangan
                    </h4>
                </td>";

        $html .= "<td colspan='3' style='text-align:right;'>
                    <h4 style='text-transform: uppercase;font-weight: 600;font-size: 15px;'>
                    </h4>
                </td>";
        $html .= "</tr>";
        $html .= $pph->htmlList();

        $data['data'] = $html;
        return view('plugins/accounting::laba-rugi.index',$data);
    }
}
